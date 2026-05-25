<?php

namespace App\Console\Commands;

use App\Services\ClassDiagramDocxExporter;
use Illuminate\Console\Command;

class GenerateClassDiagramDocx extends Command
{
    protected $signature = 'docs:class-diagram';

    protected $description = 'Generate the system UML class diagram as a DOCX file';

    public function handle(ClassDiagramDocxExporter $exporter): int
    {
        $path = $exporter->generate();

        $this->info('Class diagram DOCX generated successfully.');
        $this->line($path);

        return self::SUCCESS;
    }
}
