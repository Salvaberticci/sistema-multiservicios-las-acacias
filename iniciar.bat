@echo off
title Multiservicios Las Acacias
cd /d "%~dp0"

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0iniciar.ps1" %*

if errorlevel 1 (
    echo.
    echo Ocurrio un error. Revisa el mensaje de arriba.
)

echo.
pause
