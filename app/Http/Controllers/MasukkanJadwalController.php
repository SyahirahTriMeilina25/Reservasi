<?php

namespace App\Http\Controllers;

use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\JadwalBimbingan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MasukkanJadwalController extends Controller
{
    protected $googleCalendarController;

    public function __construct(GoogleCalendarController $googleCalendarController)
    {   
        $this->googleCalendarController = $googleCalendarController;
    }

    /**
     * Menampilkan halaman masukkan jadwal
     */
    public function index()
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Coba refresh token terlebih dahulu
        $isConnected = false;
        if ($dosen->hasGoogleCalendarConnected()) {
            $isConnected = app(GoogleCalendarController::class)->validateAndRefreshToken();
        }
        
        // Logging untuk membantu debug
        Log::info('Google Calendar Status (Dosen):', [
            'has_tokens' => $dosen->hasGoogleCalendarConnected(),
            'is_expired' => $dosen->isGoogleTokenExpired(),
            'token_created' => $dosen->google_token_created_at,
            'expires_in' => $dosen->google_token_expires_in,
            'expiry_time' => $dosen->getTokenExpiryTime()?->format('Y-m-d H:i:s'),
            'has_access_token' => !empty($dosen->google_access_token),
            'has_refresh_token' => !empty($dosen->google_refresh_token),
            'is_connected' => $isConnected,
            'dosen_nip' => $dosen->nip
        ]);
        
        return view('bimbingan.dosen.masukkanjadwal', [
            'isConnected' => $isConnected,
            'email' => $dosen->email
        ]);
    }

    /**
     * Menyimpan jadwal baru
     */
    public function store(Request $request)
    {
        try {
            // Refresh token jika diperlukan
            if (!$this->googleCalendarController->validateAndRefreshToken()) {
                Log::error('Google Calendar authentication failed for dosen:', [
                    'nip' => Auth::guard('dosen')->user()->nip
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat terhubung ke Google Calendar. Silakan hubungkan ulang.'
                ], 401);
            }

            Log::info('Incoming request data:', $request->all());

            // Validasi request
            $validated = $request->validate([
                'start' => 'required|date',
                'end' => 'required|date|after:start',
                'description' => 'nullable|string',
            ]);

            // Parse dates with explicit timezone
            $start = Carbon::parse($request->start)->setTimezone('Asia/Jakarta');
            $end = Carbon::parse($request->end)->setTimezone('Asia/Jakarta');

            $dosen = Auth::guard('dosen')->user();
            
            // Buat event di Google Calendar
            $description = "Status: Tersedia\n" .
                    "Dosen: {$dosen->nama}\n" .
                    "NIP: {$dosen->nip}\n" .
                    "Email: {$dosen->email}\n" .
                    ($request->description ? "Catatan: {$request->description}" : "");

            $eventData = [
                'summary' => 'Jadwal Bimbingan',
                'description' => $description,
                'start' => $start,
                'end' => $end,
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'email', 'minutes' => 24 * 60],
                        ['method' => 'popup', 'minutes' => 30],
                    ],
                ],
            ];

            DB::beginTransaction();
            try {
                // Buat event di Google Calendar
                $createdEvent = $this->googleCalendarController->createEvent($eventData);
                
                // Simpan ke database
                $jadwal = JadwalBimbingan::create([
                    'event_id' => $createdEvent->id,
                    'nip' => $dosen->nip,
                    'waktu_mulai' => $start,
                    'waktu_selesai' => $end,
                    'catatan' => $request->description,
                    'status' => 'tersedia'
                ]);

                DB::commit();

                Log::info('Successfully created schedule:', [
                    'event_id' => $createdEvent->id,
                    'dosen_nip' => $dosen->nip
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal berhasil ditambahkan!',
                    'data' => [
                        'id' => $createdEvent->id,
                        'title' => 'Jadwal Bimbingan',
                        'start' => $start->toIso8601String(),
                        'end' => $end->toIso8601String(),
                        'description' => $request->description,
                        'status' => 'Tersedia',
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error adding event: ' . $e->getMessage(), [
                'dosen_nip' => Auth::guard('dosen')->user()->nip,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus jadwal
     */
    public function destroy($eventId)
    {
        try {
            // Refresh token jika diperlukan
            if (!$this->googleCalendarController->validateAndRefreshToken()) {
                Log::error('Google Calendar authentication failed for dosen on delete:', [
                    'nip' => Auth::guard('dosen')->user()->nip,
                    'event_id' => $eventId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat terhubung ke Google Calendar. Silakan hubungkan ulang.'
                ], 401);
            }

            $jadwal = JadwalBimbingan::where('event_id', $eventId)->first();
            if (!$jadwal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();
            try {
                // Hapus dari Google Calendar dulu
                $this->googleCalendarController->deleteEvent($eventId);
                
                // Jika berhasil hapus dari Google Calendar, hapus dari database
                $jadwal->delete();

                DB::commit();

                Log::info('Successfully deleted schedule:', [
                    'event_id' => $eventId,
                    'dosen_nip' => Auth::guard('dosen')->user()->nip
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal berhasil dihapus dari sistem dan Google Calendar!'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error deleting schedule: ' . $e->getMessage(), [
                'event_id' => $eventId,
                'dosen_nip' => Auth::guard('dosen')->user()->nip,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}