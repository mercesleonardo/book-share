<?php

namespace App\Services\Log;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class LogCleanerService
{
    /**
     * Deleta arquivos de log mais antigos que o número de dias especificado.
     *
     * @param int $days O número de dias para manter os logs.
     * @return int O número de arquivos deletados.
     */
    public function __invoke(int $days): int
    {
        $path      = storage_path('logs');
        $limitDate = Carbon::now()->subDays($days);

        if (!File::exists($path)) {
            return 0;
        }

        $files        = File::files($path);
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp(File::lastModified($file));

            if ($lastModified->lt($limitDate)) {
                File::delete($file);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}
