<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        if (Student::exists()) {
            return;
        }

        $rows = [
            [
                'student_id' => '20230883',
                'name' => 'John Casagan',
                'email' => 'johncasagan@gmail.com',
                'program' => 'BS Information Technology',
                'year_level' => '3rd Year',
                'status' => 'active',
                'last_visit' => '2026-04-05',
            ],
            [
                'student_id' => '20230881',
                'name' => 'Luke Hijara',
                'email' => 'lukehijara@gmail.com',
                'program' => 'BS Information Technology',
                'year_level' => '3rd Year',
                'status' => 'active',
                'last_visit' => '2026-04-03',
            ],
            [
                'student_id' => '20230884',
                'name' => 'Romel Balungag',
                'email' => 'romelbalungag@gmail.com',
                'program' => 'BS Information Technology',
                'year_level' => '3rd Year',
                'status' => 'active',
                'last_visit' => '2026-04-08',
            ],
        ];

        foreach ($rows as $row) {
            Student::create($row);
        }
    }
}
