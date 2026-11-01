<?php

namespace App\Console\Commands;

use App\Services\WargaRplService;
use Illuminate\Console\Command;

class SyncWargaRplTeachers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wargarpl:sync-teachers
                            {--token= : Override WargaRPL API Bearer Token}
                            {--reset-passwords : Reset password for existing teachers to DOB (DDMMYYYY)}
                            {--force : Force sync without asking confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan data guru dan tendik dari WargaRPL API ke database KREASI RPL';

    /**
     * Execute the console command.
     */
    public function handle(WargaRplService $service): int
    {
        $token = $this->option('token') ?: config('services.wargarpl.token');

        if (empty($token)) {
            $this->error('Token API WargaRPL belum dikonfigurasi. Gunakan opsi --token atau isi WARGARPL_API_TOKEN di file .env.');

            return self::FAILURE;
        }

        $this->info('Menghubungkan ke WargaRPL API ('.$service->getBaseUrl().')...');
        $this->comment('Token: '.$service->getMaskedToken($token));

        $test = $service->testConnection($token);
        if (! $test['success']) {
            $this->error('Koneksi gagal: '.($test['error'] ?? 'Unknown error'));

            return self::FAILURE;
        }

        $totalTeachers = $test['total_teachers'] ?? 0;
        $this->info("Koneksi berhasil! Terdeteksi {$totalTeachers} guru & tendik di WargaRPL API.");

        if (! $this->option('force') && ! $this->confirm('Apakah Anda yakin ingin memulai proses sinkronisasi data guru & tendik?', true)) {
            $this->comment('Proses sinkronisasi dibatalkan.');

            return self::SUCCESS;
        }

        $this->info('Memulai sinkronisasi data guru...');
        $startTime = microtime(true);

        $result = $service->syncTeachers($token, [
            'reset_passwords' => $this->option('reset-passwords'),
        ]);

        if (! $result['success']) {
            $this->error('Sinkronisasi gagal: '.($result['message'] ?? 'Terjadi kesalahan'));

            return self::FAILURE;
        }

        $duration = round(microtime(true) - $startTime, 2);

        $this->newLine();
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Guru dari API', $result['total_fetched'] ?? 0],
                ['Guru Baru Ditambahkan', $result['created_count'] ?? 0],
                ['Guru Diperbarui', $result['updated_count'] ?? 0],
                ['Durasi Waktu', $duration.' detik'],
            ]
        );

        $this->info('✓ '.$result['message']);
        $this->comment('Catatan: Akun guru dapat login menggunakan NIP dengan kata sandi Tanggal Lahir (DDMMYYYY).');

        return self::SUCCESS;
    }
}
