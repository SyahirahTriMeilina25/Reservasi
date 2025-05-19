<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventReminder;
use Google_Service_Calendar_EventReminders;
use Google_Service_Oauth2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\JadwalBimbingan;
use Carbon\Carbon;

class GoogleCalendarController extends Controller
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();

        try {
            $credentialsPath = storage_path('app/google-calendar/credentials.json');
            if (!file_exists($credentialsPath)) {
                throw new \Exception('Google Calendar credentials file not found');
            }

            $this->client->setAuthConfig($credentialsPath);
            $this->client->setApplicationName('Sistem Bimbingan');
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');
            $this->client->setIncludeGrantedScopes(true);

            $this->client->addScope(Google_Service_Calendar::CALENDAR);
            $this->client->addScope('email');
            $this->client->addScope('profile');
        } catch (\Exception $e) {
            Log::error('Error in GoogleCalendarController constructor: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengatur callback URL berdasarkan guard yang aktif
     */
    protected function setCallbackUrl()
    {
        $guard = $this->getCurrentGuard();
        $callbackUrl = url("/$guard/google/callback");
        $this->client->setRedirectUri($callbackUrl);
        Log::info("Setting callback URL to: $callbackUrl");
    }

    /**
     * Menghubungkan dengan Google Calendar
     */
    public function connect(Request $request)
    {
        try {
            $guard = $this->getCurrentGuard();
            $user = Auth::guard($guard)->user();

            // Simpan URL asal (referer)
            $origin_url = $request->header('referer');

            if ($user->hasGoogleCalendarConnected()) {
                return $this->redirectToIndex('Anda sudah terhubung dengan Google Calendar', 'warning');
            }

            // Set state untuk validasi dengan tambahan origin_url
            $state = base64_encode(json_encode([
                'id' => $user->getAuthIdentifier(),
                'guard' => $guard,
                'timestamp' => time(),
                'origin_url' => $origin_url // Tambahkan URL asal
            ]));

            $this->setCallbackUrl();

            // Tambahkan konfigurasi untuk memaksa menggunakan email yang sesuai
            $this->client->setState($state);
            $this->client->setLoginHint($user->email); // Paksa menggunakan email user
            $this->client->setPrompt('select_account consent'); // Paksa pilih akun dan minta consent
            $this->client->setHostedDomain($user->email); // Batasi domain email (opsional)

            $authUrl = $this->client->createAuthUrl();
            return redirect()->to($authUrl);
        } catch (\Exception $e) {
            Log::error('Gagal menghubungkan ke Google Calendar: ' . $e->getMessage());
            return $this->redirectToIndex('Terjadi kesalahan saat menghubungkan ke Google Calendar', 'error');
        }
    }

    /**
     * Handle callback dari Google OAuth
     */
    /**
     * Handle callback dari Google OAuth
     */
    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            return $this->redirectToIndex('Gagal terhubung ke Google Calendar.', 'error');
        }

        try {
            $this->setCallbackUrl();
            $state = json_decode(base64_decode($request->state), true);

            if (!$state || !isset($state['guard']) || !isset($state['id'])) {
                throw new \Exception('Invalid state parameter');
            }

            $guard = $state['guard'];
            $user = Auth::guard($guard)->user();

            if ($user->getAuthIdentifier() !== $state['id']) {
                throw new \Exception('Invalid user');
            }

            if ((time() - $state['timestamp']) > 3600) {
                throw new \Exception('State expired');
            }

            $token = $this->client->fetchAccessTokenWithAuthCode($request->code);

            $this->client->setAccessToken($token);
            $oauth2 = new Google_Service_Oauth2($this->client);
            $userInfo = $oauth2->userinfo->get();

            if ($userInfo->email !== $user->email) {
                throw new \Exception(sprintf(
                    'Email tidak sesuai. Gunakan akun Google dengan email yang terdaftar di sistem (%s)',
                    $user->email
                ));
            }

            $user->updateGoogleToken(
                $token['access_token'],
                $token['refresh_token'] ?? null,
                $token['expires_in']
            );

            // Dapatkan URL asal dari state
            $originUrl = $state['origin_url'] ?? null;

            // Deteksi apakah ini dari popup berdasarkan parameter
            $isPopup = $request->input('popup') === 'true';

            if ($isPopup) {
                // Jika dari popup, kembalikan script JavaScript untuk menutup popup
                $script = "
            <html>
            <head>
                <title>Menghubungkan...</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
                    h3 { color: #4CAF50; }
                </style>
            </head>
            <body>
                <h3>Berhasil terhubung! Menutup jendela...</h3>
                <script>
                if (window.opener) {
                    window.opener.postMessage({ 
                        success: true, 
                        message: 'Google Calendar berhasil terhubung!',
                        origin_url: " . json_encode($originUrl) . "
                    }, '*');
                    
                    setTimeout(function() {
                        window.close();
                    }, 1000);
                }
                </script>
            </body>
            </html>";

                return response($script);
            }

            // Jika bukan dari popup, gunakan redirect normal
            if ($originUrl && strpos($originUrl, 'persetujuan') !== false && strpos($originUrl, 'tab=jadwal') !== false) {
                // Jika berasal dari halaman persetujuan tab jadwal, kembalikan ke sana
                return redirect($originUrl)
                    ->with('success', 'Google Calendar berhasil terhubung!')
                    ->with('first_connection', true);
            }

            // Jika tidak ada URL sebelumnya, gunakan default berdasarkan guard
            $routeName = match ($guard) {
                'dosen' => 'dosen.persetujuan',
                'mahasiswa' => 'mahasiswa.usulanbimbingan',
                default => 'login'
            };

            $routeParams = ['tab' => 'jadwal'];
            return redirect()->route($routeName, $routeParams)
                ->with('success', 'Google Calendar berhasil terhubung!')
                ->with('first_connection', true);
        } catch (\Exception $e) {
            $errorMessage = $this->getErrorMessage($e->getMessage());

            // Coba kembali ke halaman sebelumnya jika ada
            $referer = $request->session()->get('_previous.url');

            if ($referer) {
                return redirect($referer)
                    ->with('error', $errorMessage);
            }

            // Fallback ke route default jika tidak ada referer
            $routeName = match (Auth::getDefaultDriver()) {
                'dosen' => 'dosen.persetujuan',
                'mahasiswa' => 'mahasiswa.usulanbimbingan',
                default => 'login'
            };

            Log::error('Error Google Calendar: ' . $e->getMessage());

            return redirect()->route($routeName, ['tab' => 'jadwal'])
                ->with('error', $errorMessage);
        }
    }

    /**
     * Mendapatkan events dari Google Calendar
     */
    public function getEvents(Request $request)
    {
        try {
            // Ambil parameter dari request
            $startStr = $request->query('start');
            $endStr = $request->query('end');
            $filterDuplicates = $request->query('filter_duplicates', true); // Parameter untuk mengaktifkan filter duplikasi

            Log::info('Request parameters for events:', [
                'start' => $startStr,
                'end' => $endStr,
                'filter_duplicates' => $filterDuplicates
            ]);

            // Parse tanggal dengan penanganan error yang lebih baik
            try {
                // Perbaikan format tanggal - ganti spasi dengan + untuk timezone
                if ($startStr && strpos($startStr, ' ') !== false) {
                    $startStr = str_replace(' ', '+', $startStr);
                }
                $start = $startStr ? Carbon::parse($startStr)->setTimezone('Asia/Jakarta') : Carbon::now()->startOfMonth();
            } catch (\Exception $e) {
                Log::warning('Error parsing start date, using fallback:', [
                    'input' => $startStr,
                    'error' => $e->getMessage()
                ]);
                $start = Carbon::now()->startOfMonth();
            }

            try {
                // Perbaikan format tanggal - ganti spasi dengan + untuk timezone
                if ($endStr && strpos($endStr, ' ') !== false) {
                    $endStr = str_replace(' ', '+', $endStr);
                }
                $end = $endStr ? Carbon::parse($endStr)->setTimezone('Asia/Jakarta') : Carbon::now()->endOfYear();
            } catch (\Exception $e) {
                Log::warning('Error parsing end date, using fallback:', [
                    'input' => $endStr,
                    'error' => $e->getMessage()
                ]);
                $end = Carbon::now()->endOfYear();
            }

            Log::info('Parsed date range:', [
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString()
            ]);

            if (!$this->checkAndRefreshToken()) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            $this->service = new Google_Service_Calendar($this->client);

            // TAHAP 1: Ambil event dari database lokal terlebih dahulu
            $localEvents = JadwalBimbingan::where('nip', Auth::user()->nip)
                ->where(function ($query) use ($start, $end) {
                    // Filter berdasarkan waktu
                    $query->where('waktu_mulai', '>=', $start)
                        ->where('waktu_mulai', '<=', $end);
                })
                ->get();

            // Simpan event_id dari database lokal untuk filter duplicates nanti
            $localEventIds = $localEvents->pluck('event_id')->filter()->toArray();

            // Format event lokal
            $formattedLocalEvents = $localEvents->map(function ($jadwal) {
                return [
                    'id' => $jadwal->event_id, // Gunakan event_id untuk konsistensi
                    'title' => 'Bimbingan - ' . ($jadwal->jenis_bimbingan ? ucfirst($jadwal->jenis_bimbingan) : 'Umum'),
                    'start' => Carbon::parse($jadwal->waktu_mulai)->toIso8601String(),
                    'end' => Carbon::parse($jadwal->waktu_selesai)->toIso8601String(),
                    'editable' => true,
                    'color' => '#4285f4',
                    'extendedProps' => [
                        'id' => $jadwal->id, // Local ID
                        'jenis_bimbingan' => $jadwal->jenis_bimbingan,
                        'has_kuota_limit' => $jadwal->has_kuota_limit,
                        'kuota' => $jadwal->kapasitas,
                        'jumlah_pendaftar' => $jadwal->jumlah_pendaftar,
                        'catatan' => $jadwal->catatan,
                        'lokasi' => $jadwal->lokasi,
                        'status' => $jadwal->status,
                        'description' => $this->generateDescription($jadwal),
                        'source' => 'local'
                    ]
                ];
            })->toArray();

            // Log untuk debugging
            Log::info('Local events loaded:', [
                'count' => count($formattedLocalEvents)
            ]);

            // TAHAP 2: Ambil event dari Google Calendar
            $calendarId = 'primary';
            $optParams = [
                'maxResults' => 250,
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => Carbon::now()->startOfMonth()->toRfc3339String(),
                'timeMax' => Carbon::now()->endOfYear()->toRfc3339String(), // Sampai akhir tahun berjalan
            ];

            try {
                $results = $this->service->events->listEvents($calendarId, $optParams);
            } catch (\Google_Service_Exception $e) {
                Log::error('Google Calendar API Error:', [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ]);

                // Coba refresh token jika unauthorized
                if ($e->getCode() == 401) {
                    $user = $this->getAuthUser();
                    $user->update([
                        'google_token_created_at' => Carbon::now()->subHours(2)
                    ]);

                    if (!$this->checkAndRefreshToken()) {
                        return response()->json([
                            'error' => 'Sesi Google Calendar Anda telah berakhir'
                        ], 401);
                    }

                    // Coba lagi setelah refresh token
                    $results = $this->service->events->listEvents($calendarId, $optParams);
                } else {
                    throw $e;
                }
            }

            // Format Google Calendar events
            $formattedGoogleEvents = [];
            foreach ($results->getItems() as $event) {
                // Skip all-day events atau event yang tidak punya waktu spesifik
                if (!isset($event->start->dateTime) || !isset($event->end->dateTime)) {
                    continue;
                }

                // Filtering duplikasi event
                if ($filterDuplicates && in_array($event->id, $localEventIds)) {
                    // Skip event yang sudah ada di database lokal
                    continue;
                }

                // Parse tanggal dengan penanganan error
                try {
                    $startDateTime = Carbon::parse($event->start->dateTime)->toIso8601String();
                    $endDateTime = Carbon::parse($event->end->dateTime)->toIso8601String();
                } catch (\Exception $e) {
                    Log::warning('Error parsing event dates, skipping:', [
                        'event_id' => $event->id,
                        'start' => $event->start->dateTime ?? 'null',
                        'end' => $event->end->dateTime ?? 'null',
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }

                $formattedGoogleEvents[] = [
                    'id' => $event->id,
                    'title' => $event->getSummary() ?: 'No Title',
                    'start' => $startDateTime,
                    'end' => $endDateTime,
                    'editable' => false,
                    'color' => '#9e9e9e',
                    'className' => 'external-event',
                    'extendedProps' => [
                        'isExternal' => true,
                        'source' => 'google',
                        'description' => $event->getDescription()
                    ]
                ];
            }

            // Log untuk debugging
            Log::info('Google events loaded:', [
                'total' => count($results->getItems()),
                'filtered' => count($formattedGoogleEvents)
            ]);

            // TAHAP 3: Gabungkan kedua sumber data
            $allEvents = array_merge($formattedLocalEvents, $formattedGoogleEvents);

            return response()->json($allEvents);
        } catch (\Exception $e) {
            Log::error('Error getting events: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Helper function untuk menghasilkan deskripsi
    private function generateDescription($jadwal)
    {
        // Tentukan status yang benar untuk ditampilkan
        $description = "Jenis Bimbingan: " . ($jadwal->jenis_bimbingan ? ucfirst($jadwal->jenis_bimbingan) : 'Umum') . "\n";

        if ($jadwal->has_kuota_limit) {
            $description .= "Kapasitas: {$jadwal->jumlah_pendaftar}/{$jadwal->kapasitas}\n";
        } else {
            $description .= "Kapasitas: Tidak Terbatas\n";
        }

        if ($jadwal->lokasi) {
            $description .= "Lokasi: {$jadwal->lokasi}\n";
        }

        if ($jadwal->catatan) {
            $description .= "Catatan: {$jadwal->catatan}\n";
        }

        return $description;
    }

    /**
     * Format events untuk response
     */
    protected function formatEvents($events)
    {
        $formattedEvents = [];

        foreach ($events as $event) {
            if (!$event->start->dateTime || !$event->end->dateTime) {
                continue;
            }

            $eventData = [
                'id' => $event->id,
                'title' => $event->getSummary() ?: 'No Title',
                'start' => Carbon::parse($event->start->dateTime)->toIso8601String(),
                'end' => Carbon::parse($event->end->dateTime)->toIso8601String(),
                'description' => $event->getDescription(),
                'editable' => false,
            ];

            // Custom formatting untuk event bimbingan
            if (strpos(strtolower($event->getSummary()), 'bimbingan') !== false) {
                $eventData['color'] = '#4285f4';
                $eventData['editable'] = true;
            } else {
                $eventData['color'] = '#9e9e9e';
                $eventData['className'] = 'external-event';
            }

            $formattedEvents[] = $eventData;
        }

        return response()->json($formattedEvents);
    }

    public function validateAndRefreshToken()
    {
        return $this->checkAndRefreshToken();
    }

    /**
     * Delete event from Google Calendar
     */
    public function deleteEvent($eventId)
    {
        try {
            if (!$this->checkAndRefreshToken()) {
                throw new \Exception('Not authenticated');
            }

            $this->service = new Google_Service_Calendar($this->client);
            $this->service->events->delete('primary', $eventId);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting Google Calendar event: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Membuat event baru di Google Calendar
     */
    public function createEvent($eventData)
    {
        try {
            if (!$this->checkAndRefreshToken()) {
                throw new \Exception('Belum terautentikasi dengan Google Calendar');
            }

            $this->initializeService();

            $start = $eventData['start'];
            $end = $eventData['end'];

            // Validasi waktu
            $this->validateEventTime($start, $end);

            // Membuat objek event Google Calendar
            $event = new Google_Service_Calendar_Event([
                'summary' => $eventData['summary'],
                'description' => $eventData['description'],
                'start' => [
                    'dateTime' => $start->toRfc3339String(),
                    'timeZone' => 'Asia/Jakarta',
                ],
                'end' => [
                    'dateTime' => $end->toRfc3339String(),
                    'timeZone' => 'Asia/Jakarta',
                ]
            ]);

            if (isset($eventData['reminders'])) {
                $reminders = $this->createEventReminders($eventData['reminders']);
                $event->setReminders($reminders);
            }

            // Memasukkan event ke kalender utama
            $createdEvent = $this->service->events->insert('primary', $event);

            if (!$createdEvent) {
                throw new \Exception('Gagal membuat event di Google Calendar');
            }

            $this->logEventCreation($createdEvent);

            return $createdEvent;
        } catch (\Exception $e) {
            $this->logEventError($e, $eventData);
            throw $e;
        }
    }

    public function updateEventAttendees($eventId, $attendees, $options = [])
    {
        try {
            if (!$this->checkAndRefreshToken()) {
                throw new \Exception('Tidak terautentikasi dengan Google Calendar');
            }

            $this->service = new Google_Service_Calendar($this->client);

            // Ambil event yang akan diupdate
            $event = $this->service->events->get('primary', $eventId);

            // Update attendees
            $event->setAttendees($attendees);

            // Update deskripsi jika disediakan
            if (isset($options['description'])) {
                $event->setDescription($options['description']);
            }

            // Update reminders jika disediakan
            if (isset($options['reminders'])) {
                $reminders = new Google_Service_Calendar_EventReminders();
                $reminders->setUseDefault($options['reminders']['useDefault'] ?? false);

                if (isset($options['reminders']['overrides'])) {
                    $overrides = [];
                    foreach ($options['reminders']['overrides'] as $override) {
                        $reminder = new Google_Service_Calendar_EventReminder();
                        $reminder->setMethod($override['method']);
                        $reminder->setMinutes($override['minutes']);
                        $overrides[] = $reminder;
                    }
                    $reminders->setOverrides($overrides);
                }

                $event->setReminders($reminders);
            }

            // Log untuk debugging
            Log::info('Updating event with attendees and options:', [
                'event_id' => $eventId,
                'attendees_count' => count($attendees),
                'options' => $options
            ]);

            // Update event dengan notifikasi
            return $this->service->events->update('primary', $eventId, $event, [
                'sendUpdates' => $options['sendUpdates'] ?? 'all' // Kirim notifikasi ke semua peserta
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating event attendees: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Helper Methods
     */
    protected function getCurrentGuard()
    {
        if (Auth::guard('dosen')->check()) return 'dosen';
        if (Auth::guard('mahasiswa')->check()) return 'mahasiswa';
        throw new \Exception('No valid authentication guard found');
    }

    protected function getAuthUser()
    {
        $guard = $this->getCurrentGuard();
        return Auth::guard($guard)->user();
    }

    protected function validateState($state)
    {
        if (!$state) return false;

        $user = $this->getAuthUser();
        return $state['id'] === $user->getAuthIdentifier()
            && $state['guard'] === $this->getCurrentGuard()
            && (time() - $state['timestamp']) <= 3600;
    }

    protected function redirectToIndex($message, $type)
    {
        $guard = $this->getCurrentGuard();
        if ($guard === 'mahasiswa') {
            return redirect()->route('pilihjadwal.index')
                ->with($type, $message);
        } else if ($guard === 'dosen') {
            return redirect()->route('dosen.jadwal.index')
                ->with($type, $message);
        }
        throw new \Exception('No valid authentication guard found');
    }

    protected function checkAndRefreshToken()
    {
        $user = $this->getAuthUser();

        if (!$user->hasGoogleCalendarConnected()) {
            return false;
        }

        if ($user->isGoogleTokenExpired()) {
            try {
                $currentToken = [
                    'access_token' => $user->google_access_token,
                    'refresh_token' => $user->google_refresh_token,
                    'expires_in' => $user->google_token_expires_in,
                    'created' => Carbon::parse($user->google_token_created_at)->timestamp,
                ];

                $this->client->setAccessToken($currentToken);

                if ($this->client->isAccessTokenExpired() && $user->google_refresh_token) {
                    try {
                        $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

                        // Validasi newToken
                        if (!isset($newToken['access_token'])) {
                            Log::error('New token received but no access_token present');
                            // Reset token tanpa method disconnectGoogleCalendar
                            $user->update([
                                'google_access_token' => null,
                                'google_refresh_token' => null,
                                'google_token_expires_in' => null,
                                'google_token_created_at' => null
                            ]);
                            return false;
                        }

                        // Update token
                        $user->updateGoogleToken(
                            $newToken['access_token'],
                            $newToken['refresh_token'] ?? $user->google_refresh_token,
                            $newToken['expires_in'] ?? 3600
                        );

                        $this->client->setAccessToken($newToken);
                    } catch (\Exception $e) {
                        Log::error('Error during token refresh: ' . $e->getMessage());
                        // Reset token tanpa method disconnectGoogleCalendar
                        $user->update([
                            'google_access_token' => null,
                            'google_refresh_token' => null,
                            'google_token_expires_in' => null,
                            'google_token_created_at' => null
                        ]);
                        return false;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error in checkAndRefreshToken: ' . $e->getMessage());
                return false;
            }
        } else {
            $this->client->setAccessToken([
                'access_token' => $user->google_access_token,
                'refresh_token' => $user->google_refresh_token,
                'expires_in' => $user->google_token_expires_in,
                'created' => Carbon::parse($user->google_token_created_at)->timestamp,
            ]);
        }

        return true;
    }

    /**
     * Initialize Google Calendar service
     */
    protected function initializeService()
    {
        if (!$this->service) {
            $this->service = new Google_Service_Calendar($this->client);
        }
    }

    /**
     * Validasi waktu event
     */
    protected function validateEventTime($start, $end)
    {
        Log::info('Validating event time:', [
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
        ]);

        // Validasi jam kerja (08:00 - 18:00)
        $startHour = $start->format('H');
        if ($startHour < 8 || $startHour >= 18) {
            throw new \Exception('Jadwal harus dalam jam kerja (08:00 - 18:00)');
        }

        // Validasi durasi minimum
        $durasi = $end->diffInMinutes($start, false);
        Log::info('Duration in minutes: ' . $durasi);

        if (abs($durasi) < 30) {
            throw new \Exception('Durasi minimum bimbingan adalah 30 menit');
        }
    }

    /**
     * Membuat pengaturan reminder untuk event
     */
    protected function createEventReminders($reminderSettings)
    {
        $reminders = new Google_Service_Calendar_EventReminders();
        $reminders->setUseDefault(false);

        $reminderOverrides = [];
        foreach ($reminderSettings['overrides'] as $override) {
            $reminder = new Google_Service_Calendar_EventReminder();
            $reminder->setMethod($override['method']);
            $reminder->setMinutes($override['minutes']);
            $reminderOverrides[] = $reminder;
        }

        $reminders->setOverrides($reminderOverrides);
        return $reminders;
    }

    /**
     * Log informasi pembuatan event
     */
    protected function logEventCreation($event)
    {
        Log::info('Successfully created Google Calendar event', [
            'event_id' => $event->id,
            'summary' => $event->summary,
            'start' => $event->start->dateTime,
            'end' => $event->end->dateTime,
            'description' => $event->description
        ]);
    }

    /**
     * Log error pembuatan event
     */
    protected function logEventError($exception, $eventData)
    {
        Log::error('Error creating Google Calendar event: ' . $exception->getMessage(), [
            'event_data' => $eventData,
            'trace' => $exception->getTraceAsString()
        ]);
    }

    protected function getErrorMessage($message)
    {
        // Cek beberapa kondisi umum
        if (str_contains($message, 'Email tidak sesuai')) {
            return 'Gunakan email yang terdaftar di sistem untuk menghubungkan Google Calendar.';
        }

        if (str_contains($message, 'invalid_grant') || str_contains($message, 'expired')) {
            return 'Silakan hubungkan ulang dengan Google Calendar.';
        }

        // Pesan default untuk error lainnya
        return 'Gagal terhubung ke Google Calendar. Silakan coba beberapa saat lagi.';
    }

    public function initWithUserToken($user)
    {
        if (!$user || !$user->hasGoogleCalendarConnected()) {
            Log::error('User tidak memiliki token Google Calendar yang valid', [
                'user_id' => $user ? $user->id : 'null'
            ]);
            return false;
        }

        try {
            // Reset client jika sudah digunakan sebelumnya
            $this->client = new Google_Client();

            // Atur konfigurasi awal client
            $credentialsPath = storage_path('app/google-calendar/credentials.json');
            if (!file_exists($credentialsPath)) {
                throw new \Exception('Google Calendar credentials file not found');
            }

            $this->client->setAuthConfig($credentialsPath);
            $this->client->setApplicationName('Sistem Bimbingan');
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');
            $this->client->setIncludeGrantedScopes(true);
            $this->client->addScope(Google_Service_Calendar::CALENDAR);
            $this->client->addScope('email');
            $this->client->addScope('profile');

            // Set token user
            $token = [
                'access_token' => $user->google_access_token,
                'refresh_token' => $user->google_refresh_token,
                'expires_in' => $user->google_token_expires_in,
                'created' => Carbon::parse($user->google_token_created_at)->timestamp,
            ];

            $this->client->setAccessToken($token);

            // Refresh token jika expired
            if ($this->client->isAccessTokenExpired() && $user->google_refresh_token) {
                Log::info('Refreshing expired token for user', [
                    'user_id' => $user->id,
                    'old_token_created' => $user->google_token_created_at
                ]);

                try {
                    $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

                    if (!isset($newToken['access_token'])) {
                        Log::error('Token refresh failed - no access_token in response', [
                            'response' => $newToken
                        ]);
                        return false;
                    }

                    // Update token baru
                    $user->google_access_token = $newToken['access_token'];
                    if (isset($newToken['refresh_token'])) {
                        $user->google_refresh_token = $newToken['refresh_token'];
                    }
                    $user->google_token_expires_in = $newToken['expires_in'] ?? 3600;
                    $user->google_token_created_at = now();
                    $user->save();

                    $this->client->setAccessToken($newToken);

                    Log::info('Token refreshed successfully for user', [
                        'user_id' => $user->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error refreshing token: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    return false;
                }
            }

            // Inisialisasi service
            $this->service = new Google_Service_Calendar($this->client);
            return true;
        } catch (\Exception $e) {
            Log::error('Error initializing client with user token: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    /**
     * Mencari event berdasarkan rentang waktu dan judul/deskripsi yang mirip
     * 
     * @param string $date Tanggal dalam format Y-m-d
     * @param string $startTime Waktu mulai dalam format H:i
     * @param string $endTime Waktu selesai dalam format H:i
     * @param string $keyword Kata kunci untuk dicari di judul/deskripsi event
     * @return bool Mengembalikan true jika event ditemukan
     */
    public function findEventByTimeAndKeyword($date, $startTime, $endTime, $keyword)
    {
        if (!$this->checkAndRefreshToken()) {
            return false;
        }

        $this->service = new Google_Service_Calendar($this->client);

        // Hitung rentang waktu pencarian (1 jam sebelum dan sesudah)
        $startDateTime = Carbon::parse($date . ' ' . $startTime)->subHour();
        $endDateTime = Carbon::parse($date . ' ' . $endTime)->addHour();

        $optParams = [
            'timeMin' => $startDateTime->toRfc3339String(),
            'timeMax' => $endDateTime->toRfc3339String(),
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        try {
            $events = $this->service->events->listEvents('primary', $optParams);

            // Cek setiap event yang ditemukan
            foreach ($events->getItems() as $event) {
                $eventTitle = $event->getSummary();
                $eventDesc = $event->getDescription();

                // Jika keyword ditemukan di judul atau deskripsi
                if (
                    stripos($eventTitle, $keyword) !== false ||
                    stripos($eventDesc, $keyword) !== false
                ) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error searching for events: ' . $e->getMessage());
        }

        return false;
    }
    /**
     * Menandai event sebagai dibatalkan di Google Calendar 
     */
    public function markEventAsCancelled($eventId, $reason)
    {
        try {
            if (!$this->checkAndRefreshToken()) {
                throw new \Exception('Tidak terautentikasi dengan Google Calendar');
            }

            $this->service = new Google_Service_Calendar($this->client);

            // Ambil event yang akan ditandai
            $event = $this->service->events->get('primary', $eventId);

            // Ubah judul untuk menunjukkan pembatalan
            $originalSummary = $event->getSummary();
            if (strpos($originalSummary, '[DIBATALKAN]') === false) {
                $event->setSummary('[DIBATALKAN] ' . $originalSummary);
            }

            // Tambahkan alasan pembatalan ke deskripsi
            $originalDesc = $event->getDescription() ?: '';
            $cancelNote = "\n\n--- DIBATALKAN ---\n" .
                "Alasan: {$reason}\n" .
                "Waktu pembatalan: " . now()->format('d-m-Y H:i');

            $event->setDescription($originalDesc . $cancelNote);

            // Ubah warna event menjadi abu-abu (indikasi dibatalkan)
            $event->setColorId('8'); // 8 = abu-abu di Google Calendar

            // Update status event menjadi dibatalkan - ini penting!
            $event->setStatus('cancelled');

            // Simpan perubahan
            return $this->service->events->update('primary', $eventId, $event);
        } catch (\Exception $e) {
            Log::error('Error marking Google Calendar event as cancelled: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Mendapatkan instance Google Client
     */
    public function getClient()
    {
        if (!$this->client) {
            throw new \Exception('Google Client belum diinisialisasi');
        }

        return $this->client;
    }
}
