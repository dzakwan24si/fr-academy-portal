<?php

namespace App\Console\Commands;

use Database\Seeders\CompetencySeeder;
use Illuminate\Console\Command;
use Throwable;

class ImportCompetencyDictionaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competency:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Impor Kamus Kompetensi (Estate 2026 & Mill 2027) dari JSON. Tidak menimpa data yang sudah ada.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $seeder = new CompetencySeeder;
            $seeder->setContainer($this->laravel)->setCommand($this);
            $seeder->run();
        } catch (Throwable $e) {
            $this->error('Impor dibatalkan: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Selesai.');

        return self::SUCCESS;
    }
}
