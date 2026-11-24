<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InitDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:init {--seed : Ejecutar también los seeders con los datos maestros}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializa la base de datos PostgreSQL desde cero ejecutando el esquema DDL oficial y migraciones de Laravel.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('===========================================================');
        $this->info(' INICIALIZADOR DE BASE DE DATOS - SISTEMA DE FIRMA DIGITAL');
        $this->info('===========================================================');

        $schemaFile = database_path('schema.sql');
        if (!File::exists($schemaFile)) {
            $this->error("No se encontró el archivo DDL en: {$schemaFile}");
            return Command::FAILURE;
        }

        $this->line('1. Ejecutando estructura DDL (Tablas, Constraints, Índices de BD.md)...');
        $sql = File::get($schemaFile);

        try {
            DB::unprepared($sql);
            $this->info('✓ Estructura de tablas PostgreSQL creada con éxito.');
        } catch (\Throwable $e) {
            $this->error('Error al ejecutar schema.sql: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->line('2. Ejecutando migraciones complementarias de Laravel (pago_tramite, jobs, sessions)...');
        $this->call('migrate', ['--force' => true]);

        if ($this->option('seed')) {
            $this->line('3. Sembrando datos maestros y usuarios oficiales...');
            $this->call('db:seed', ['--force' => true]);
        }

        $this->info('===========================================================');
        $this->info('✓ BASE DE DATOS LISTA Y TOTALMENTE OPERATIVA');
        $this->info('===========================================================');

        return Command::SUCCESS;
    }
}
