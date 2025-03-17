<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\UsulanBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use App\Models\Dosen;

class DosenController extends Controller
{
    protected $googleCalendarController;

    public function __construct(GoogleCalendarController $googleCalendarController)
    {
        $this->googleCalendarController = $googleCalendarController;
    }
    public function index(Request $request)
    {
        try {
            $activeTab = $request->query('tab', 'usulan');
            $perPage = $request->query('per_page', 10);
            $nip = Auth::user()->nip;

            // Default values
            $usulan = collect();
            $jadwal = collect();
            $riwayat = collect();

            // Load data based on active tab
            switch ($activeTab) {
                case 'usulan':
                    $usulan = DB::table('usulan_bimbingans as ub')
                        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                        ->join('bimbingankonsultasi.jadwal_bimbingans as jb', function ($join) {
                            $join->on('ub.event_id', '=', 'jb.event_id')
                                ->on('ub.nip', '=', 'jb.nip');
                        })
                        ->select(
                            'ub.*',
                            'm.nama as mahasiswa_nama',
                            'jb.lokasi as lokasi_default',
                            DB::raw('(SELECT COUNT(*) FROM usulan_bimbingans 
                                    WHERE event_id = ub.event_id 
                                    AND status = "DISETUJUI") as total_antrian')
                        )
                        ->where('jb.nip', $nip)
                        ->where('jb.status', 'tersedia')
                        ->where('ub.status', 'USULAN')
                        ->orderBy('jb.waktu_mulai', 'asc')
                        ->orderBy('ub.created_at', 'desc')
                        ->paginate($perPage);
                    break;

                case 'jadwal':
                    $jadwal = DB::table('usulan_bimbingans as ub')
                        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                        ->where('ub.nip', $nip)
                        ->where('status', 'DISETUJUI')
                        ->select(
                            'ub.*',
                            'm.nama as mahasiswa_nama',
                            DB::raw('(SELECT COUNT(*) FROM usulan_bimbingans 
                                    WHERE event_id = ub.event_id 
                                    AND status = "DISETUJUI" 
                                    AND nomor_antrian <= ub.nomor_antrian) as posisi_antrian'),
                            DB::raw('(SELECT COUNT(*) FROM usulan_bimbingans 
                                    WHERE event_id = ub.event_id 
                                    AND status = "DISETUJUI") as total_antrian')
                        )
                        ->orderBy('ub.tanggal', 'desc')
                        ->orderBy('ub.waktu_mulai', 'asc')
                        ->paginate($perPage);
                    break;

                case 'riwayat':
                    $riwayat = DB::table('usulan_bimbingans as ub')
                        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                        ->where('ub.nip', $nip)
                        ->whereIn('ub.status', ['SELESAI', 'DITOLAK', 'DIBATALKAN'])
                        ->select('ub.*', 'm.nama as mahasiswa_nama')
                        ->orderBy('ub.tanggal', 'desc')
                        ->orderBy('ub.waktu_mulai', 'desc')
                        ->paginate($perPage);
                    break;
            }

            return view('bimbingan.dosen.persetujuan', compact(
                'activeTab',
                'usulan',
                'jadwal',
                'riwayat'
            ));
        } catch (\Exception $e) {
            Log::error('Error in dosen index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    public function getDetailBimbingan($id)
    {
        try {
            $usulan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->join('prodi as p', 'm.prodi_id', '=', 'p.id')
                ->join('konsentrasi as k', 'm.konsentrasi_id', '=', 'k.id')
                ->join('dosens as d', 'ub.nip', '=', 'd.nip')
                ->select(
                    'ub.*',
                    'm.nama as mahasiswa_nama',
                    'p.nama_prodi',
                    'k.nama_konsentrasi',
                    'd.nama as dosen_nama'
                )
                ->where('ub.id', $id)
                ->firstOrFail();

            // Format tanggal ke format Indonesia
            $tanggal = Carbon::parse($usulan->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
            $waktuMulai = Carbon::parse($usulan->waktu_mulai)->format('H.i');
            $waktuSelesai = Carbon::parse($usulan->waktu_selesai)->format('H.i');

            // Set warna badge status
            switch ($usulan->status) {
                case 'DISETUJUI':
                    $statusBadgeClass = 'bg-success';
                    break;
                case 'DITOLAK':
                    $statusBadgeClass = 'bg-danger';
                    break;
                case 'USULAN':
                    $statusBadgeClass = 'bg-info';
                    break;
                case 'SELESAI':
                    $statusBadgeClass = 'bg-primary';
                    break;
                case 'DIBATALKAN':  // Tambahkan kasus untuk DIBATALKAN
                    $statusBadgeClass = 'bg-secondary';  // Gunakan warna abu-abu untuk status dibatalkan
                    break;
                default:
                    $statusBadgeClass = '';
                    break;
            }
            return view('bimbingan.aksiInformasi', compact(
                'usulan',
                'tanggal',
                'waktuMulai',
                'waktuSelesai',
                'statusBadgeClass'
            ));
        } catch (\Exception $e) {
            Log::error('Error di getDetailBimbingan: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data usulan bimbingan');
        }
    }

    public function getRiwayatDetail($id)
    {
        try {
            $riwayat = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.id', $id)
                ->where('ub.status', 'SELESAI')
                ->select('ub.*', 'm.nama as mahasiswa_nama')
                ->firstOrFail();

            $tanggal = Carbon::parse($riwayat->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
            $waktuMulai = Carbon::parse($riwayat->waktu_mulai)->format('H:i');
            $waktuSelesai = Carbon::parse($riwayat->waktu_selesai)->format('H:i');

            return view('bimbingan.riwayatdosen', compact(
                'riwayat',
                'tanggal',
                'waktuMulai',
                'waktuSelesai'
            ));
        } catch (\Exception $e) {
            Log::error('Error getting riwayat detail: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail riwayat bimbingan');
        }
    }

    public function editUsulan($id)
    {
        try {
            $usulan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.id', $id)
                ->where('ub.status', 'DISETUJUI')
                ->select('ub.*', 'm.nama as mahasiswa_nama')
                ->firstOrFail();

            return view('bimbingan.dosen.editusulan', compact('usulan'));
        } catch (\Exception $e) {
            Log::error('Error in editUsulan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data usulan untuk diedit');
        }
    }

    public function updateUsulan(Request $request, $id)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'waktu_mulai' => 'required|date_format:H:i',
                'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
                'lokasi' => 'required|string|max:255'
            ]);

            DB::table('usulan_bimbingans')
                ->where('id', $id)
                ->update([
                    'tanggal' => $request->tanggal,
                    'waktu_mulai' => $request->waktu_mulai,
                    'waktu_selesai' => $request->waktu_selesai,
                    'lokasi' => $request->lokasi,
                    'updated_at' => now()
                ]);

            return redirect()
                ->route('dosen.persetujuanbimbingan', ['tab' => 'usulan'])
                ->with('success', 'Usulan bimbingan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error in updateUsulan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui usulan bimbingan');
        }
    }

    public function terima(Request $request, $id)
    {
        try {
            $usulan = UsulanBimbingan::with('mahasiswa')->findOrFail($id);

            $jadwal = JadwalBimbingan::where('event_id', $usulan->event_id)
                ->where('status', 'tersedia')
                ->first();

            if (!$jadwal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal bimbingan sudah tidak tersedia'
                ], 400);
            }

            DB::beginTransaction();

            if ($usulan->setujui($request->lokasi)) {
                try {
                    // Debug log untuk memeriksa event_id
                    Log::info('Mencari event dengan ID: ' . $usulan->event_id);

                    // Cari event di calendar dosen
                    $events = $this->googleCalendarController->getEvents();

                    // Debug log untuk melihat response events
                    Log::info('Events response:', ['events' => $events]);

                    if (!$events || !isset($events->original)) {
                        throw new \Exception('Tidak bisa mengambil events dari Google Calendar');
                    }

                    $event = collect($events->original)->first(function ($event) use ($usulan) {
                        return isset($event['id']) && $event['id'] === $usulan->event_id;
                    });

                    if ($event) {
                        $existingAttendees = $event['attendees'] ?? [];
                        Log::info('Existing attendees:', ['attendees' => $existingAttendees]);

                        $mahasiswaEmail = $usulan->mahasiswa->email;
                        $emailExists = collect($existingAttendees)->contains('email', $mahasiswaEmail);

                        if (!$emailExists) {
                            Log::info('Menambahkan attendee baru:', ['email' => $mahasiswaEmail]);

                            $existingAttendees[] = [
                                'email' => $mahasiswaEmail,
                                'responseStatus' => 'needsAction'
                            ];

                            $description = "Status: Disetujui\n" .
                                "Dosen: {$usulan->dosen->nama}\n" .
                                "Mahasiswa: {$usulan->mahasiswa->nama}\n" .
                                "NIM: {$usulan->nim}\n" .
                                "Nomor Antrian: {$usulan->nomor_antrian}\n" .
                                "Lokasi: {$request->lokasi}\n";

                            $this->googleCalendarController->updateEventAttendees(
                                $usulan->event_id,
                                $existingAttendees,
                                [
                                    'description' => $description,
                                    'sendUpdates' => 'all',
                                    'reminders' => [
                                        'useDefault' => false,
                                        'overrides' => [
                                            ['method' => 'email', 'minutes' => 24 * 60],
                                            ['method' => 'popup', 'minutes' => 30]
                                        ]
                                    ]
                                ]
                            );

                            Log::info('Berhasil menambahkan attendee dengan notifikasi');
                        }

                        DB::commit();
                        return response()->json([
                            'success' => true,
                            'message' => 'Usulan bimbingan berhasil disetujui dan undangan telah dikirim'
                        ]);
                    }

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Usulan bimbingan berhasil disetujui (tanpa notifikasi calendar)'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Google Calendar Error Detail:', [
                        'message' => $e->getMessage(),
                        'event_id' => $usulan->event_id,
                        'trace' => $e->getTraceAsString()
                    ]);

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Usulan bimbingan berhasil disetujui (tanpa notifikasi calendar)'
                    ]);
                }
            }

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui usulan bimbingan'
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in approve consultation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses usulan'
            ], 500);
        }
    }

    public function tolak(Request $request, $id)
    {
        try {
            $usulan = UsulanBimbingan::findOrFail($id);

            $usulan->update([
                'status' => 'DITOLAK',
                'keterangan' => $request->keterangan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usulan bimbingan berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses usulan'
            ], 500);
        }
    }
    public function selesaikan($id)
    {
        Log::info('Fungsi selesaikan dipanggil dengan ID: ' . $id);
        try {
            $usulan = UsulanBimbingan::findOrFail($id);

            if ($usulan->status !== 'DISETUJUI') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya bimbingan yang disetujui yang dapat diselesaikan'
                ], 422);
            }

            $usulan->update([
                'status' => 'SELESAI'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bimbingan berhasil diselesaikan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in selesaikan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan bimbingan'
            ], 500);
        }
    }
    public function batalkanPersetujuan($id, Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'alasan' => 'required|string'
            ]);

            // Cari data bimbingan
            $bimbingan = UsulanBimbingan::findOrFail($id);

            // Pastikan status saat ini adalah DISETUJUI
            if ($bimbingan->status !== 'DISETUJUI') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya bimbingan yang telah disetujui yang dapat dibatalkan'
                ], 400);
            }

            // Update status dan tambahkan alasan
            $bimbingan->status = 'DIBATALKAN';
            $bimbingan->keterangan = $request->alasan;
            $bimbingan->updated_at = now();
            $bimbingan->save();

            // Log pembatalan
            Log::info('Persetujuan bimbingan dibatalkan:', [
                'id' => $id,
                'dosen' => Auth::guard('dosen')->user()->nip,
                'alasan' => $request->alasan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Persetujuan bimbingan berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat membatalkan persetujuan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
