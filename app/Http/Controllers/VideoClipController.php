<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class VideoClipController extends Controller
{
    public function index()
    {
        return view('clipper');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => ['required', 'url'],
            'start' => ['required', 'regex:/^\d+:[0-5]\d$/'],
            'end' => ['required', 'regex:/^\d+:[0-5]\d$/'],
        ]);

        $url = trim($validated['url']);

        $host = strtolower(
            (string) parse_url($url, PHP_URL_HOST)
        );

        $host = preg_replace('/^www\./', '', $host);

        $allowedHosts = [
            'youtube.com',
            'm.youtube.com',
            'music.youtube.com',
            'youtu.be',
            // TikTok
            'tiktok.com',
        ];

        if (! in_array($host, $allowedHosts, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Gunakan link video YouTube atau TikTok yang valid.',
            ], 422);
        }

        $start = $this->timeToSeconds(
            $validated['start']
        );

        $end = $this->timeToSeconds(
            $validated['end']
        );

        if ($end <= $start) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu selesai harus lebih besar dari waktu mulai.',
            ], 422);
        }

        $token = (string) Str::uuid();

        $workDir = storage_path(
            "app/private/ytclip/{$token}"
        );

        File::ensureDirectoryExists($workDir);

        File::put(
            $workDir . DIRECTORY_SEPARATOR . 'job.json',
            json_encode([
                'url' => $url,
                'start' => $start,
                'end' => $end,
                'duration' => $end - $start,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->writeStatus(
            $workDir,
            'queued',
            0,
            'Menunggu worker...'
        );

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }

    public function status(string $token)
    {
        if (! preg_match('/^[0-9a-f-]{36}$/i', $token)) {
            abort(404);
        }

        $workDir = storage_path(
            "app/private/ytclip/{$token}"
        );

        $statusPath =
            $workDir . DIRECTORY_SEPARATOR . 'status.json';

        abort_unless(is_file($statusPath), 404);

        $status = json_decode(
            File::get($statusPath),
            true
        );

        return response()->json(
            $status ?: [
                'status' => 'failed',
                'stage' => 'failed',
                'progress' => 0,
                'message' => 'Status tidak tersedia.',
                'download_url' => null,
            ]
        );
    }

    public function download(string $token)
    {
        if (! preg_match('/^[0-9a-f-]{36}$/i', $token)) {
            abort(404);
        }

        $path = storage_path(
            "app/private/ytclip/{$token}/clip.mp4"
        );

        abort_unless(is_file($path), 404);

        return response()->download(
            $path,
            "yt-clip-{$token}.mp4"
        );
    }

    private function timeToSeconds(string $time): int
    {
        [$minutes, $seconds] = array_map(
            'intval',
            explode(':', $time)
        );

        return ($minutes * 60) + $seconds;
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
                        : (
                            $stage === 'queued'
                                ? 'queued'
                                : 'processing'
                        )
                );

        File::put(
            $workDir . DIRECTORY_SEPARATOR . 'status.json',
            json_encode([
                'status' => $status,
                'stage' => $stage,
                'progress' => round($progress, 1),
                'message' => $message,
                'download_url' => $downloadUrl,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
}
