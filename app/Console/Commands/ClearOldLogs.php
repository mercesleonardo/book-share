<?php

namespace App\Console\Commands;

use App\Services\Log\LogCleanerService;
use Illuminate\Console\Command;

class ClearOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear-old {--days=30 : Número de dias para manter os logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove arquivos de log antigos da pasta storage/logs';

    /**
     * Execute the console command.
     */
    public function handle(LogCleanerService $logCleaner)
    {
        $days = (int) $this->option('days');

        $deletedCount = $logCleaner($days);

        if ($deletedCount > 0) {
            $this->info("✅ {$deletedCount} arquivos de log antigos foram removidos.");
        } else {
            $this->info('Nenhum arquivo de log antigo para remover.');
        }

        return self::SUCCESS;
    }
}
