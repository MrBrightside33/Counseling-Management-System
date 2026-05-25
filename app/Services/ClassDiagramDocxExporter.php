<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\JcTable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClassDiagramDocxExporter
{
    public function download(): BinaryFileResponse
    {
        $path = $this->generate();

        return response()->download($path, 'CPC-Counseling-System-Class-Diagram.docx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    public function generate(): string
    {
        $directory = storage_path('app/documentation');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory.'/CPC-Counseling-System-Class-Diagram.docx';

        $phpWord = new PhpWord;
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 20, 'color' => '140DED']);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14, 'color' => '333333']);
        $phpWord->addTitleStyle(3, ['bold' => true, 'size' => 12, 'color' => '140DED']);

        $section = $phpWord->addSection();
        $section->addTitle('CPC Counseling Management System', 1);
        $section->addTitle('UML Class Diagram', 2);
        $section->addText('Generated: '.now()->format('M d, Y h:i A'));
        $section->addText('Laravel application — domain models, controllers, and services.');
        $section->addTextBreak(2);

        $section->addTitle('Relationship overview', 2);
        $this->addRelationshipDiagram($section);
        $section->addTextBreak();

        $section->addTitle('Relationships (detail)', 2);
        $this->addDataTable($section, ['From', 'Multiplicity', 'To', 'Description'], [
            ['Student', '1', 'Appointment', 'hasMany — a student has many counseling sessions'],
            ['Counselor', '1', 'Appointment', 'hasMany — a counselor conducts many sessions'],
            ['Appointment', '*', 'Student', 'belongsTo — each session belongs to one student'],
            ['Appointment', '*', 'Counselor', 'belongsTo — each session belongs to one counselor'],
            ['User', '—', '—', 'Admin account (authentication); no Eloquent link to counseling entities'],
        ]);
        $section->addTextBreak();

        $section->addTitle('Domain models (Eloquent)', 2);
        foreach ($this->domainClasses() as $class) {
            $this->addClassBox($section, $class);
            $section->addTextBreak();
        }

        $section->addTitle('HTTP controllers', 2);
        $section->addText('All controllers extend App\\Http\\Controllers\\Controller and use Laravel routing with auth middleware (except AuthController guest routes).');
        $section->addTextBreak();
        foreach ($this->controllerClasses() as $class) {
            $this->addClassBox($section, $class);
            $section->addTextBreak();
        }

        $section->addTitle('Services', 2);
        foreach ($this->serviceClasses() as $class) {
            $this->addClassBox($section, $class);
            $section->addTextBreak();
        }

        $section->addTitle('Controller dependencies on models & services', 2);
        $this->addDataTable($section, ['Controller', 'Uses'], [
            ['AuthController', 'User'],
            ['DashboardController', 'Student, Counselor, Appointment'],
            ['StudentController', 'Student'],
            ['CounselorController', 'Counselor, Appointment'],
            ['AppointmentController', 'Appointment, Student, Counselor'],
            ['ReportController', 'Appointment, Student, Counselor, ReportDocxExporter'],
            ['ProfileController', 'User (Auth)'],
        ]);

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

        return $path;
    }

    /**
     * @param  \PhpOffice\PhpWord\Element\Section  $section
     */
    private function addRelationshipDiagram($section): void
    {
        $lines = [
            '┌──────────────┐         ┌─────────────────┐         ┌──────────────┐',
            '│   Student    │ 1     * │   Appointment   │ *     1 │   Counselor  │',
            '│──────────────│─────────│─────────────────│─────────│──────────────│',
            '│ student_id   │         │ date, time      │         │ specialization│',
            '│ name, email  │         │ type, status    │         │ availability  │',
            '│ program      │         │ notes           │         │ avatar        │',
            '└──────────────┘         └─────────────────┘         └──────────────┘',
            '',
            '┌──────────────┐',
            '│     User     │  (Admin — login, profile; separate from counseling records)',
            '│ name, email  │',
            '│ password     │',
            '└──────────────┘',
        ];

        foreach ($lines as $line) {
            $section->addText($line, ['name' => 'Courier New', 'size' => 9]);
        }
    }

    /**
     * @param  \PhpOffice\PhpWord\Element\Section  $section
     * @param  array{name: string, stereotype: string, attributes: list<string>, methods: list<string>}  $class
     */
    private function addClassBox($section, array $class): void
    {
        $section->addTitle($class['name'].' («'.$class['stereotype'].'»)', 3);

        $table = $section->addTable($this->tableStyle());
        $table->addRow();
        $table->addCell(9000)->addText('Attributes', ['bold' => true, 'color' => '140DED']);
        $table->addRow();
        foreach ($class['attributes'] as $attribute) {
            $table->addCell(9000)->addText($attribute, ['size' => 10]);
        }
        $table->addRow();
        $table->addCell(9000)->addText('Methods / operations', ['bold' => true, 'color' => '140DED']);
        $table->addRow();
        foreach ($class['methods'] as $method) {
            $table->addCell(9000)->addText($method, ['size' => 10]);
        }
    }

    /**
     * @param  \PhpOffice\PhpWord\Element\Section  $section
     * @param  list<string>  $headers
     * @param  list<list<string>>  $rows
     */
    private function addDataTable($section, array $headers, array $rows): void
    {
        $table = $section->addTable($this->tableStyle());
        $table->addRow();
        foreach ($headers as $header) {
            $table->addCell(2200)->addText($header, ['bold' => true]);
        }
        foreach ($rows as $row) {
            $table->addRow();
            foreach ($row as $cell) {
                $table->addCell(2200)->addText($cell);
            }
        }
    }

    /**
     * @return list<array{name: string, stereotype: string, attributes: list<string>, methods: list<string>}>
     */
    private function domainClasses(): array
    {
        return [
            [
                'name' => 'User',
                'stereotype' => 'Model / Authenticatable',
                'attributes' => [
                    '- id: bigint',
                    '- name: string',
                    '- email: string (unique)',
                    '- password: string (hashed)',
                    '- avatar: string|null',
                    '- remember_token: string|null',
                    '- email_verified_at: datetime|null',
                    '- created_at, updated_at: timestamp',
                ],
                'methods' => [
                    '+ avatarUrl(): ?string',
                    '+ initials(): string',
                ],
            ],
            [
                'name' => 'Student',
                'stereotype' => 'Model',
                'attributes' => [
                    '- id: bigint',
                    '- student_id: string (8 digits, unique)',
                    '- name: string',
                    '- email: string (Gmail, unique)',
                    '- program: string',
                    '- year_level: string',
                    '- status: enum (active, inactive)',
                    '- last_visit: date|null',
                    '- created_at, updated_at: timestamp',
                ],
                'methods' => [
                    '+ appointments(): HasMany → Appointment',
                    '+ getFormattedLastVisitAttribute(): string',
                ],
            ],
            [
                'name' => 'Counselor',
                'stereotype' => 'Model',
                'attributes' => [
                    '- id: bigint',
                    '- name: string',
                    '- email: string',
                    '- phone: string|null',
                    '- avatar: string|null',
                    '- specialization: string',
                    '- availability: string|null',
                    '- total_sessions: int',
                    '- created_at, updated_at: timestamp',
                ],
                'methods' => [
                    '+ appointments(): HasMany → Appointment',
                    '+ avatarUrl(): ?string',
                    '+ initials(): string',
                ],
            ],
            [
                'name' => 'Appointment',
                'stereotype' => 'Model',
                'attributes' => [
                    '- id: bigint',
                    '- student_id: bigint (FK)',
                    '- counselor_id: bigint (FK)',
                    '- date: date',
                    '- time: string',
                    '- type: string (counseling type)',
                    '- status: enum (scheduled, completed, cancelled, no-show)',
                    '- notes: text|null',
                    '- created_at, updated_at: timestamp',
                ],
                'methods' => [
                    '+ student(): BelongsTo → Student',
                    '+ counselor(): BelongsTo → Counselor',
                    '+ getFormattedDateAttribute(): string',
                ],
            ],
        ];
    }

    /**
     * @return list<array{name: string, stereotype: string, attributes: list<string>, methods: list<string>}>
     */
    private function controllerClasses(): array
    {
        return [
            [
                'name' => 'AuthController',
                'stereotype' => 'Controller',
                'attributes' => ['—'],
                'methods' => [
                    '+ showLogin(): View|RedirectResponse',
                    '+ login(Request): RedirectResponse',
                    '+ showRegister(): View|RedirectResponse',
                    '+ register(Request): RedirectResponse',
                    '+ logout(Request): RedirectResponse',
                ],
            ],
            [
                'name' => 'DashboardController',
                'stereotype' => 'Controller',
                'attributes' => ['—'],
                'methods' => ['+ index(): View'],
            ],
            [
                'name' => 'StudentController',
                'stereotype' => 'Controller',
                'attributes' => [
                    '+ PROGRAMS: array',
                    '+ YEAR_LEVELS: array',
                    '+ STATUSES: array',
                ],
                'methods' => [
                    '+ index(Request): View',
                    '+ create(): RedirectResponse',
                    '+ store(Request): RedirectResponse',
                    '+ update(Request, Student): RedirectResponse',
                    '+ destroy(Student): RedirectResponse',
                    '+ formData(): array',
                ],
            ],
            [
                'name' => 'CounselorController',
                'stereotype' => 'Controller',
                'attributes' => ['+ SPECIALIZATIONS: array'],
                'methods' => [
                    '+ index(Request): View',
                    '+ create(): RedirectResponse',
                    '+ store(Request): RedirectResponse',
                    '+ update(Request, Counselor): RedirectResponse',
                    '+ destroy(Counselor): RedirectResponse',
                    '+ formData(): array',
                ],
            ],
            [
                'name' => 'AppointmentController',
                'stereotype' => 'Controller',
                'attributes' => [
                    '+ COUNSELING_TYPES: array',
                    '+ STATUSES: array',
                    '+ TIME_SLOTS: array',
                ],
                'methods' => [
                    '+ index(Request): View',
                    '+ create(): RedirectResponse',
                    '+ store(Request): RedirectResponse',
                    '+ update(Request, Appointment): RedirectResponse',
                    '+ updateStatus(Request, Appointment): RedirectResponse',
                    '+ destroy(Appointment): RedirectResponse',
                    '+ formData(): array',
                ],
            ],
            [
                'name' => 'ReportController',
                'stereotype' => 'Controller',
                'attributes' => ['- docx: ReportDocxExporter'],
                'methods' => [
                    '+ index(Request): View',
                    '+ exportSummary(Request): BinaryFileResponse',
                    '+ exportStudent(Request, Student): BinaryFileResponse',
                    '+ updateSessionNotes(Request, Appointment): RedirectResponse',
                ],
            ],
            [
                'name' => 'ProfileController',
                'stereotype' => 'Controller',
                'attributes' => ['—'],
                'methods' => [
                    '+ edit(): View',
                    '+ update(Request): RedirectResponse',
                    '- deleteAvatar(User): void',
                ],
            ],
        ];
    }

    /**
     * @return list<array{name: string, stereotype: string, attributes: list<string>, methods: list<string>}>
     */
    private function serviceClasses(): array
    {
        return [
            [
                'name' => 'ReportDocxExporter',
                'stereotype' => 'Service',
                'attributes' => ['—'],
                'methods' => [
                    '+ analyticsDocument(analytics, sessions): BinaryFileResponse',
                    '+ studentDocument(Student, sessions): BinaryFileResponse',
                    '- download(PhpWord, filename): BinaryFileResponse',
                ],
            ],
            [
                'name' => 'ClassDiagramDocxExporter',
                'stereotype' => 'Service',
                'attributes' => ['—'],
                'methods' => [
                    '+ download(): BinaryFileResponse',
                    '+ generate(): string',
                    '- domainClasses(): array',
                    '- controllerClasses(): array',
                ],
            ],
        ];
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
}
