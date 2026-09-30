@echo off
chcp 65001 >nul
cd /d "%~dp0"
set "PATH=%USERPROFILE%\scoop\shims;%PATH%"

echo Arret du serveur Symfony...
call symfony server:stop
echo.
echo (MySQL n'est pas arrete : il peut servir a d'autres projets. Arrete-le depuis XAMPP si besoin.)
timeout /t 3 >nul
