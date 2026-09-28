<?php

return [

    'yt_dlp' => env(
        'YTDLP_BINARY'
    ) ?: base_path('tools/yt-dlp.exe'),

    'deno' => env(
        'DENO_BINARY'
    ) ?: base_path('tools/deno/deno.exe'),

    'ffmpeg' => env(
        'FFMPEG_BINARY'
    ) ?: base_path('tools/ffmpeg/ffmpeg.exe'),

    'timeout' => (int) env(
        'VIDEO_PROCESS_TIMEOUT',
        3600
    ),

    'temp_dir' => env(
        'YTCLIP_TEMP_DIR',
        storage_path('app/private/ytclip-tmp')
    ),

];
