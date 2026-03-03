@echo off
title ASCEND Simulation Server
cls
echo ========================================================
echo   ASCEND CLINICAL SIMULATION LAUNCHER
echo ========================================================
echo.
echo   1. Checking for PHP...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo.
    echo   [ERROR] PHP is not installed or not in your PATH.
    echo   Please install PHP for Windows to run this simulation.
    echo   Download: https://windows.php.net/download/
    echo.
    pause
    exit
)

echo   2. Starting Local Server...
echo   3. Opening Dashboard...
echo.
echo   [INSTRUCTIONS]
echo   - Do NOT close this black window.
echo   - The simulation is running at http://localhost:8000
echo   - To stop, close this window.
echo.

:: Open Browser after 2 seconds
timeout /t 2 >nul
start http://localhost:8000

:: Start PHP Server
php -S localhost:8000