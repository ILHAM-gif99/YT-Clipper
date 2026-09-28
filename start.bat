@echo off
title YT Clipper

cd /d "%~dp0"

echo ========================================
echo          YT CLIPPER
echo ========================================
echo.
echo Menjalankan Laravel...

start "YT Clipper Server" cmd /k ".\php\php.exe artisan serve --host=127.0.0.1 --port=8000"

timeout /t 3 /nobreak >nul

echo Menjalankan Worker...

start "YT Clipper Worker" cmd /k ".\php\php.exe artisan ytclip:worker"

timeout /t 2 /nobreak >nul

echo Membuka browser...

start "" "http://127.0.0.1:8000"

exit
