<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Counselor;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        if (Appointment::exists()) {
            return;
        }

        $john = Student::where('student_id', '20230883')->first();
        $luke = Student::where('student_id', '20230881')->first();
        $romel = Student::where('student_id', '20230884')->first();
        $eda = Counselor::where('email', 'abellanoeda@gmail.com')->first();
        $charlene = Counselor::where('email', 'parcocharlene@gmail.com')->first();

        if (! $john || ! $luke || ! $romel || ! $eda || ! $charlene) {
            return;
        }

        $rows = [
            [
                'student_id' => $john->id,
                'counselor_id' => $eda->id,
                'date' => '2026-04-10',
                'time' => '10:00 AM',
                'type' => 'Academic Planning',
                'status' => 'scheduled',
            ],
            [
                'student_id' => $luke->id,
                'counselor_id' => $charlene->id,
                'date' => '2026-04-10',
                'time' => '2:00 PM',
                'type' => 'Career Guidance',
                'status' => 'scheduled',
            ],
            [
                'student_id' => $romel->id,
                'counselor_id' => $eda->id,
                'date' => '2026-04-11',
                'time' => '11:00 AM',
                'type' => 'Personal Issue',
                'status' => 'scheduled',
            ],
        ];

        foreach ($rows as $row) {
            Appointment::create($row);
        }
    }
}
