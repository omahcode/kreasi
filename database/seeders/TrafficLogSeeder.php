<?php

namespace Database\Seeders;

use App\Models\TrafficLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrafficLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (TrafficLog::count() > 30) {
            return;
        }

        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $paths = [
            '/login',
            '/admin/dashboard',
            '/guru/dashboard',
            '/siswa/dashboard',
            '/admin/users',
            '/admin/classes',
            '/admin/students/sync',
            '/guru/projects',
            '/siswa/projects',
            '/siswa/pkl',
            '/siswa/logbook',
            '/admin/companies',
            '/admin/pkl-groups',
        ];

        $ips = ['127.0.0.1', '192.168.1.15', '192.168.1.28', '192.168.1.42', '192.168.1.55', '114.124.200.12', '180.252.170.8'];

        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:129.0) Gecko/20100101 Firefox/129.0',
            'Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.6613.88 Mobile Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.15',
        ];

        for ($day = 6; $day >= 0; $day--) {
            $count = rand(45, 95);
            for ($i = 0; $i < $count; $i++) {
                $user = $users->random();
                $dt = now()->subDays($day)->setHour(rand(7, 21))->setMinute(rand(0, 59))->setSecond(rand(0, 59));

                TrafficLog::create([
                    'user_id' => $user->id,
                    'ip_address' => $ips[array_rand($ips)],
                    'user_agent' => $agents[array_rand($agents)],
                    'method' => rand(0, 6) === 0 ? 'POST' : 'GET',
                    'path' => $paths[array_rand($paths)],
                    'route_name' => 'web.activity',
                    'status_code' => 200,
                    'created_at' => $dt,
                    'updated_at' => $dt,
                ]);
            }
        }
    }
}
