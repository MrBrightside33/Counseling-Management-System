<?php

namespace Database\Seeders;

use App\Models\Counselor;
use Illuminate\Database\Seeder;

class CounselorSeeder extends Seeder
{
    public function run(): void
    {
        if (Counselor::exists()) {
            return;
        }

        $rows = [
            [
                'name' => 'Mrs. Eda Abellano, RGC',
                'email' => 'abellanoeda@gmail.com',
                'specialization' => 'Academic Counseling',
                'availability' => 'Mon-Fri, 9AM-5PM',
                'total_sessions' => 0,
            ],
            [
                'name' => 'Mrs. Charlene Kyme Parco, RPm',
                'email' => 'parcocharlene@gmail.com',
                'specialization' => 'Career Guidance',
                'availability' => 'Mon-Wed, 10AM-6PM',
                'total_sessions' => 0,
            ],
        ];

        foreach ($rows as $row) {
            Counselor::create($row);
        }
    }
}
