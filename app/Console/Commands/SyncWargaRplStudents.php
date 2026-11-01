<?php

namespace App\Console\Commands;

use App\Services\WargaRplService;
use Illuminate\Console\Command;

class SyncWargaRplStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wargarpl:sync-students
                            {--token= : Override WargaRPL API Bearer Token}
                            {--force : Force sync without asking confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan data siswa dan kelas dari WargaRPL API ke database KREASI RPL';

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

        $totalApi = $test['total_students'] ?? 0;
        $this->info("Koneksi berhasil! Terdeteksi {$totalApi} siswa di WargaRPL API.");

        if (! $this->option('force') && ! $this->confirm('Apakah Anda yakin ingin memulai proses sinkronisasi data siswa?', true)) {
            $this->comment('Proses sinkronisasi dibatalkan.');

            return self::SUCCESS;
        }

        $this->info('Memulai sinkronisasi data siswa...');
        $startTime = microtime(true);

        $result = $service->syncStudents($token);

        if (! $result['success']) {
            $this->error('Sinkronisasi gagal: '.($result['message'] ?? 'Terjadi kesalahan'));

            return self::FAILURE;
        }

        $duration = round(microtime(true) - $startTime, 2);

        $this->newLine();
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Siswa dari API', $result['total_fetched'] ?? 0],
                ['Siswa Baru Ditambahkan', $result['created_count'] ?? 0],
                ['Siswa Diperbarui', $result['updated_count'] ?? 0],
                ['Kelas Disinkronkan', $result['classes_created'] ?? 0],
                ['Durasi Waktu', $duration.' detik'],
            ]
        );

        $this->info('✓ '.$result['message']);

        return self::SUCCESS;
    }
}
