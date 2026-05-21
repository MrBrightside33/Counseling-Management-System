<?php

return [
    'students' => [
        ['id' => '1', 'name' => 'John Casagan', 'studentId' => '20230883', 'email' => 'johncasagan@gmail.com', 'program' => 'BS Information Technology', 'yearLevel' => '3rd Year', 'status' => 'active', 'lastVisit' => '2026-04-05'],
        ['id' => '2', 'name' => 'Luke Hijara', 'studentId' => '20230881', 'email' => 'lukehijara@gmail.com', 'program' => 'BS Information Technology', 'yearLevel' => '3rd Year', 'status' => 'active', 'lastVisit' => '2026-04-03'],
        ['id' => '3', 'name' => 'Romel balungag', 'studentId' => '20230884', 'email' => 'romelbalungag@gmail.com', 'program' => 'BS Information Technology', 'yearLevel' => '3rh Year', 'status' => 'active', 'lastVisit' => '2026-04-08'],
    ],
    'counselors' => [
        ['id' => '1', 'name' => 'Mrs. Eda Abellano, RGC', 'email' => 'abellanoeda@gmail.com', 'specialization' => 'Academic Counseling', 'availability' => 'Mon-Fri, 9AM-5PM', 'totalSessions' => 0],
        ['id' => '2', 'name' => 'Mrs. Charlene Kyme Parco, RPm', 'email' => 'parcocharlene@gmail.com', 'specialization' => 'Career Guidance', 'availability' => 'Mon-Wed, 10AM-6PM', 'totalSessions' => 0],
    ],
    'appointments' => [
        ['id' => '1', 'studentId' => '1', 'studentName' => 'John Casagan', 'counselorId' => '1', 'counselorName' => 'Mrs. Eda Abellano, RGC', 'date' => '2026-04-10', 'time' => '10:00 AM', 'type' => 'Academic Planning', 'status' => 'scheduled'],
        ['id' => '2', 'studentId' => '3', 'studentName' => 'Luke Hijara', 'counselorId' => '2', 'counselorName' => 'Mrs. Charlene Kyme Parco, RPm', 'date' => '2026-04-10', 'time' => '2:00 PM', 'type' => 'Career Guidance', 'status' => 'scheduled'],
        ['id' => '3', 'studentId' => '2', 'studentName' => 'Romel Balungag', 'counselorId' => '3', 'counselorName' => 'Mrs. Eda Abellano, RGC', 'date' => '2026-04-11', 'time' => '11:00 AM', 'type' => 'Personal Issue', 'status' => 'scheduled'],
    ],
];
