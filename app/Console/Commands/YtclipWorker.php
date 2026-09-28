<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class YtclipWorker extends Command
{
    protected $signature = 'ytclip:worker';

    protected $description = 'Run YT Clipper worker';

    public function handle(): int
    {
        $this->info('YT Clipper Worker aktif.');
        $this->info('Menunggu job...');
        $this->line('');

        $baseDir =
            storage_path(
                'app/private/ytclip'
            );

        File::ensureDirectoryExists(
            $baseDir
        );

        while (true) {

            $directories =
                File::directories(
                    $baseDir
                );

            foreach ($directories as $workDir) {

                $statusPath =
                    $workDir
                    . DIRECTORY_SEPARATOR
                    . 'status.json';

                $jobPath =
                    $workDir
                    . DIRECTORY_SEPARATOR
                    . 'job.json';

                if (
                    ! is_file($statusPath)
                    || ! is_file($jobPath)
                ) {
                    continue;
                }

                $status = json_decode(
                    File::get($statusPath),
                    true
                );

                if (
                    ! is_array($status)
                    || ($status['status'] ?? null)
                        !== 'queued'
                ) {
                    continue;
                }

                $token =
                    basename($workDir);

                $this->info(
                    "Memproses job: {$token}"
                );

                Artisan::call(
                    'ytclip:process',
                    [
                        'token' => $token,
                    ]
                );

                $output =
                    Artisan::output();

                if ($output !== '') {
                    $this->line($output);
                }

                $this->line(
                    'Menunggu job berikutnya...'
                );
                $this->line('');
            }

            sleep(1);
        }
    }
}
