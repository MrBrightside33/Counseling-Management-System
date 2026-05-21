<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Student;
use Illuminate\Support\Collection;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\JcTable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportDocxExporter
{
    /**
     * @param  array<string, mixed>  $analytics
     * @param  Collection<int, Appointment>  $sessions
     */
    public function analyticsDocument(array $analytics, Collection $sessions): BinaryFileResponse
    {
        $phpWord = $this->newDocument();
        $section = $phpWord->addSection();

        $this->addDocumentHeader($section, 'Analytics Report');
        $section->addText('Period: '.$analytics['period_label']);
        $section->addTextBreak();

        $section->addTitle('System overview', 2);
        $this->addKeyValueTable($section, [
            'Total students' => (string) $analytics['students']['total'],
            'Active students' => (string) $analytics['students']['active'],
            'Inactive students' => (string) $analytics['students']['inactive'],
            'Total counselors' => (string) $analytics['counselors']['total'],
            'Total sessions' => (string) $analytics['appointments']['total'],
            'Completed sessions' => (string) $analytics['appointments']['completed'],
            'Scheduled sessions' => (string) $analytics['appointments']['scheduled'],
            'Cancelled sessions' => (string) $analytics['appointments']['cancelled'],
            'No-show sessions' => (string) $analytics['appointments']['no_show'],
            'Completion rate' => $analytics['appointments']['completion_rate'].'%',
            'Sessions with notes' => (string) $analytics['appointments']['with_notes'],
            'Notes coverage' => $analytics['appointments']['notes_rate'].'%',
        ]);
        $section->addTextBreak();

        $section->addTitle('Sessions by status', 2);
        $this->addDataTable($section, ['Status', 'Count', 'Share %'], array_map(
            fn ($row) => [$row['label'], (string) $row['count'], $row['percent'].'%'],
            $analytics['by_status']
        ));
        $section->addTextBreak();

        $section->addTitle('Sessions by counseling type', 2);
        $this->addDataTable($section, ['Type', 'Count', 'Share %'], array_map(
            fn ($row) => [$row['label'], (string) $row['count'], $row['percent'].'%'],
            $analytics['by_type']
        ));
        $section->addTextBreak();

        $section->addTitle('Sessions by program', 2);
        $this->addDataTable($section, ['Program', 'Sessions'], array_map(
            fn ($row) => [$row['label'], (string) $row['count']],
            $analytics['by_program']
        ));
        $section->addTextBreak();

        $section->addTitle('Counselor workload', 2);
        $this->addDataTable($section, ['Counselor', 'Sessions', 'Completed'], array_map(
            fn ($row) => [$row['label'], (string) $row['count'], (string) $row['completed']],
            $analytics['by_counselor']
        ));
        $section->addTextBreak();

        $section->addTitle('Monthly sessions (last 6 months)', 2);
        $this->addDataTable($section, ['Month', 'Sessions'], array_map(
            fn ($row) => [$row['label'], (string) $row['count']],
            $analytics['by_month']
        ));
        $section->addTextBreak();

        $section->addTitle('Session records', 2);
        $this->addSessionsTable($section, $sessions);

        return $this->download($phpWord, 'analytics-report-'.now()->format('Y-m-d').'.docx');
    }

    /**
     * @param  Collection<int, Appointment>  $sessions
     */
    public function studentDocument(Student $student, Collection $sessions): BinaryFileResponse
    {
        $phpWord = $this->newDocument();
        $section = $phpWord->addSection();

        $this->addDocumentHeader($section, 'Student Session Report');
        $this->addKeyValueTable($section, [
            'Student ID' => $student->student_id,
            'Name' => $student->name,
            'Email' => $student->email,
            'Program' => $student->program,
            'Year level' => $student->year_level,
            'Status' => ucfirst($student->status),
            'Total sessions' => (string) $sessions->count(),
        ]);
        $section->addTextBreak();

        $section->addTitle('Session history', 2);
        $this->addSessionsTable($section, $sessions);

        return $this->download(
            $phpWord,
            'student-'.$student->student_id.'-sessions-'.now()->format('Y-m-d').'.docx'
        );
    }

    private function newDocument(): PhpWord
    {
        $phpWord = new PhpWord;
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 18, 'color' => '140DED']);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14, 'color' => '333333']);

        return $phpWord;
    }

    /**
     * @param  \PhpOffice\PhpWord\Element\Section  $section
     */
    private function addDocumentHeader($section, string $title): void
    {
        $section->addTitle('CPC Counseling Management System', 1);
        $section->addTitle($title, 2);
        $section->addText('Generated: '.now()->format('M d, Y h:i A'));
        $section->addTextBreak();
    }

    /**
     * @param  \PhpOffice\PhpWord\Element\Section  $section
     * @param  array<string, string>  $pairs
     */
    private function addKeyValueTable($section, array $pairs): void
    {
        $table = $section->addTable($this->tableStyle());
        foreach ($pairs as $label => $value) {
            $table->addRow();
            $table->addCell(3500)->addText($label, ['bold' => true]);
            $table->addCell(5500)->addText($value);
        }
    }

    /**
     * @param  \PhpOffice\PhpWord\Element\Section  $section
     * @param  list<string>  $headers
     * @param  list<list<string>>  $rows
     */
    private function addDataTable($section, array $headers, array $rows): void
    {
        if ($rows === []) {
            $section->addText('No data available.');

            return;
        }

        $table = $section->addTable($this->tableStyle());
        $table->addRow();
        foreach ($headers as $header) {
            $table->addCell(2500)->addText($header, ['bold' => true]);
        }
        foreach ($rows as $row) {
            $table->addRow();
            foreach ($row as $cell) {
                $table->addCell(2500)->addText($cell);
            }
        }
    }

    /**
     * @param  \PhpOffice\PhpWord\Element\Section  $section
     * @param  Collection<int, Appointment>  $sessions
     */
    private function addSessionsTable($section, Collection $sessions): void
    {
        if ($sessions->isEmpty()) {
            $section->addText('No session records.');

            return;
        }

        $headers = [
            'Date',
            'Time',
            'Student ID',
            'Student',
            'Program',
            'Counselor',
            'Type',
            'Status',
            'Notes',
        ];

        $rows = $sessions->map(fn (Appointment $a) => [
            $a->date->format('Y-m-d'),
            $a->time,
            $a->student->student_id,
            $a->student->name,
            $a->student->program,
            $a->counselor->name,
            $a->type,
            ucfirst(str_replace('-', ' ', $a->status)),
            $a->notes ?? '',
        ])->all();

        $this->addDataTable($section, $headers, $rows);
    }

    /**
     * @return array<string, mixed>
     */
    private function tableStyle(): array
    {
        return [
            'borderSize' => 6,
            'borderColor' => 'CCCCCC',
            'cellMargin' => 80,
            'alignment' => JcTable::CENTER,
        ];
    }

    private function download(PhpWord $phpWord, string $filename): BinaryFileResponse
    {
        $directory = storage_path('app/temp');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory.'/'.uniqid('report_', true).'.docx';

        $sharedDir = base_path('vendor/phpoffice/phpword/src/PhpWord/Shared');
        $previousDir = getcwd();
        chdir($sharedDir);

        try {
            IOFactory::createWriter($phpWord, 'Word2007')->save($path);
        } finally {
            if ($previousDir !== false) {
                chdir($previousDir);
            }
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}
