@echo off
setlocal EnableDelayedExpansion

:: Cek izin Administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    powershell -NoProfile -ExecutionPolicy Bypass -Command "Start-Process cmd.exe -ArgumentList '/k \"\"%~f0\"\" admin' -Verb RunAs"
    exit /b
)

title Optimasi Wi-Fi Intel AX201 - Antigravity
color 0A
cls

echo ========================================================
echo        MEMULAI PERBAIKAN & OPTIMASI WI-FI LAPTOP
echo ========================================================
echo.

echo [1/3] Mengubah Preferred Band menjadi 5 GHz...
powershell -NoProfile -Command "Set-NetAdapterAdvancedProperty -Name 'Wi-Fi' -DisplayName 'Preferred Band' -DisplayValue '3. Prefer 5GHz band'"
if %errorlevel% equ 0 (
    echo       [BERHASIL] Wi-Fi diset ke 5 GHz!
) else (
    echo       [GAGAL] Tidak dapat mengubah properti.
)
echo.

echo [2/3] Mengaktifkan Throughput Booster...
powershell -NoProfile -Command "Set-NetAdapterAdvancedProperty -Name 'Wi-Fi' -DisplayName 'Throughput Booster' -DisplayValue 'Enabled'"
if %errorlevel% equ 0 (
    echo       [BERHASIL] Throughput Booster Aktif!
)
echo.

echo [3/3] Membersihkan DNS Cache...
powershell -NoProfile -Command "Clear-DnsClientCache"
echo       [BERHASIL] Cache dibersihkan!
echo.

echo ========================================================
echo HASIL PENGATURAN TERBARU:
powershell -NoProfile -Command "Get-NetAdapterAdvancedProperty -Name 'Wi-Fi' | Where-Object { $_.DisplayName -match 'Preferred Band|Throughput Booster' } | Select-Object DisplayName, DisplayValue | Format-Table -AutoSize"
echo ========================================================
echo.
echo Selesai! Silakan disconnect lalu reconnect Wi-Fi Anda.
echo.
pause
