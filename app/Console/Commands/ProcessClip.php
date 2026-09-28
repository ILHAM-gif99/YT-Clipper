<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ProcessClip extends Command
{
    protected $signature = 'ytclip:process {token}';

    protected $description = 'Process one YouTube/TikTok clipping job';

    public function handle(): int
    {
        $token = $this->argument('token');

        $workDir = storage_path(
            "app/private/ytclip/{$token}"
        );

        $jobPath =
            $workDir . DIRECTORY_SEPARATOR . 'job.json';

        if (! is_file($jobPath)) {
            $this->error('Job tidak ditemukan.');
            return 1;
        }

        $job = json_decode(
            File::get($jobPath),
            true
        );

        if (! is_array($job)) {
            $this->error('Job tidak valid.');
            return 1;
        }

        $url = $job['url'];
        $start = (float) $job['start'];
        $duration = (float) $job['duration'];

        $this->writeStatus(
            $workDir,
            'downloading',
            0,
            'Mengunduh video ...'
        );

        $this->line('');
        $this->info('YT Clipper');
        $this->line("Token: {$token}");
        $this->line('Memulai download...');
        $this->line('');

        /*
        |--------------------------------------------------------------------------
        | Environment
        |--------------------------------------------------------------------------
        */

        $tempDir = config(
            'ytclip.temp_dir',
            storage_path('app/private/ytclip-tmp')
        );

        File::ensureDirectoryExists($tempDir);

        $systemRoot =
            getenv('SystemRoot')
            ?: getenv('SYSTEMROOT')
            ?: 'C:\\WINDOWS';

        $windir =
            getenv('WINDIR')
            ?: $systemRoot;

        $processEnv = [
            'SystemRoot' => $systemRoot,
            'SYSTEMROOT' => $systemRoot,
            'WINDIR' => $windir,
            'TEMP' => $tempDir,
            'TMP' => $tempDir,
            'ComSpec' => getenv('ComSpec')
                ?: 'C:\\Windows\\System32\\cmd.exe',
            'PATH' => getenv('PATH') ?: '',
        ];

        $downloadTemplate =
            $workDir
            . DIRECTORY_SEPARATOR
            . 'source.%(ext)s';

        /*
        |--------------------------------------------------------------------------
        | yt-dlp
        |--------------------------------------------------------------------------
        */

        $download = new Process([
            config(
                'ytclip.yt_dlp',
                'yt-dlp'
            ),

            '--js-runtimes',
            'deno:' . config(
                'ytclip.deno'
            ),

            '--no-playlist',

            '--restrict-filenames',

            '--newline',

            '-f',
            'bv*+ba/b',

            '--merge-output-format',
            'mp4',

            '-o',
            $downloadTemplate,

            '--print',
            'after_move:filepath',

            $url,

        ], base_path(), $processEnv);

        $download->setTimeout(
            (int) config(
                'ytclip.timeout',
                3600
            )
        );

        $downloadOutput = '';

        $download->run(
            function ($type, $buffer) use (
                $workDir,
                &$downloadOutput
            ) {
                $downloadOutput .= $buffer;

                if (
                    preg_match(
                        '/\[download\]\s+(\d+(?:\.\d+)?)%/',
                        $buffer,
                        $matches
                    )
                ) {
                    $percent = (float) $matches[1];

                    $overall =
                        $percent * 0.70;

                    $message =
                        "Mengunduh video... {$percent}%";

                    $this->writeStatus(
                        $workDir,
                        'downloading',
                        $overall,
                        $message
                    );

                    $this->output->write(
                        "\r{$message}   "
                    );
                }
            }
        );

        $this->line('');

        if ($download->getExitCode() !== 0) {
            $error =
                trim(
                    $download->getErrorOutput()
                );

            if ($error === '') {
                $error =
                    'yt-dlp gagal mengambil video.';
            }

            $this->writeStatus(
                $workDir,
                'failed',
                0,
                $this->friendlyError($error)
            );

            $this->error($error);

            return 1;
        }

        /*
        |--------------------------------------------------------------------------
        | Cari file source
        |--------------------------------------------------------------------------
        */

        $sourcePath = collect(
            preg_split(
                '/\R/',
                trim($downloadOutput)
            )
        )
            ->map(
                fn ($line) => trim($line)
            )
            ->filter(
                fn ($line) => $line !== ''
            )
            ->first(
                fn ($line) => is_file($line)
            );

        if (! $sourcePath) {
            $sourceFile = collect(
                File::files($workDir)
            )->first(
                fn ($file) => in_array(
                    strtolower(
                        $file->getExtension()
                    ),
                    [
                        'mp4',
                        'mkv',
                        'webm',
                        'mov',
                        'm4v',
                    ],
                    true
                )
            );

            $sourcePath =
                $sourceFile?->getPathname();
        }

        if (
            ! $sourcePath
            || ! is_file($sourcePath)
        ) {
            $message =
                'File video hasil download tidak ditemukan.';

            $this->writeStatus(
                $workDir,
                'failed',
                0,
                $message
            );

            $this->error($message);

            return 1;
        }

        /*
        |--------------------------------------------------------------------------
        | FFmpeg
        |--------------------------------------------------------------------------
        */

        $outputPath =
            $workDir
            . DIRECTORY_SEPARATOR
            . 'clip.mp4';

        $this->writeStatus(
            $workDir,
            'cutting',
            70,
            'Memotong video...'
        );

        $this->line('');
        $this->info('Download selesai.');
        $this->info('Memulai FFmpeg...');
        $this->line('');

        $ffmpeg = new Process([
            config(
                'ytclip.ffmpeg',
                'ffmpeg'
            ),

            '-y',

            '-hide_banner',

            '-ss',
            $this->ffmpegTime($start),

            '-i',
            $sourcePath,

            '-t',
            $this->ffmpegTime($duration),

            '-map',
            '0:v:0',

            '-map',
            '0:a:0?',

            '-c:v',
            'libx264',

            '-preset',
            'veryfast',

            '-crf',
            '18',

            '-c:a',
            'aac',

            '-b:a',
            '192k',

            '-movflags',
            '+faststart',

            '-progress',
            'pipe:1',

            '-nostats',

            $outputPath,

        ], base_path(), $processEnv);

        $ffmpeg->setTimeout(
            (int) config(
                'ytclip.timeout',
                3600
            )
        );

        $buffer = '';

        $ffmpeg->run(
            function ($type, $output) use (
                $workDir,
                $duration,
                &$buffer
            ) {
                $buffer .= $output;

                $lines = preg_split(
                    "/\r\n|\n|\r/",
                    $buffer
                );

                $buffer =
                    array_pop($lines);

                foreach ($lines as $line) {
                    $line = trim($line);

                    if (
                        str_starts_with(
                            $line,
                            'out_time_ms='
                        )
                    ) {
                        $microseconds =
                            (float) str_replace(
                                'out_time_ms=',
                                '',
                                $line
                            );

                        if ($microseconds < 0) {
                            continue;
                        }

                        $currentSeconds =
                            $microseconds / 1000000;

                        if ($duration > 0) {
                            $cutPercent =
                                (
                                    $currentSeconds
                                    / $duration
                                ) * 100;

                            $cutPercent =
                                min(
                                    100,
                                    max(
                                        0,
                                        $cutPercent
                                    )
                                );

                            $overall =
                                70
                                + ($cutPercent * 0.30);

                            $overall =
                                round(
                                    $overall,
                                    1
                                );

                            $message =
                                "Memotong video... " .
                                round($cutPercent) .
                                "%";

                            $this->writeStatus(
                                $workDir,
                                'cutting',
                                $overall,
                                $message
                            );

                            $this->output->write(
                                "\r{$message}   "
                            );
                        }
                    }
                }
            }
        );

        $this->line('');

        if (
            $ffmpeg->getExitCode() !== 0
            || ! is_file($outputPath)
        ) {
            $error =
                trim(
                    $ffmpeg->getErrorOutput()
                );

            if ($error === '') {
                $error =
                    'FFmpeg gagal memotong video.';
            }

            $this->writeStatus(
                $workDir,
                'failed',
                0,
                $error
            );

            $this->error($error);

            return 1;
        }

        if (is_file($sourcePath)) {
            File::delete($sourcePath);
        }

        $downloadUrl =
            route(
                'clipper.download',
                $token
            );

        $this->writeStatus(
            $workDir,
            'completed',
            100,
            'Video berhasil dipotong.',
            $downloadUrl
        );

        $this->info('');
        $this->info('✓ Video berhasil dipotong!');
        $this->info($downloadUrl);

        return 0;
    }

    private function writeStatus(
        string $workDir,
        string $stage,
        float $progress,
        string $message,
        ?string $downloadUrl = null
    ): void {
        $status =
            $stage === 'failed'
                ? 'failed'
                : (
                    $stage === 'completed'
                        ? 'completed'
                        : 'processing'
                );

        File::put(
            $workDir
            . DIRECTORY_SEPARATOR
            . 'status.json',
            json_encode([
                'status' => $status,
                'stage' => $stage,
                'progress' => round($progress, 1),
                'message' => $message,
                'download_url' => $downloadUrl,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    private function ffmpegTime(
        float $seconds
    ): string {
        return number_format(
            $seconds,
            3,
            '.',
            ''
        );
    }

    private function friendlyError(
        string $message
    ): string {
        return trim($message)
            ?: 'Proses gagal.';
    }
}
