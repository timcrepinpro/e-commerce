@echo off
chcp 65001 >nul
cd /d "%~dp0"
set "PATH=%USERPROFILE%\scoop\shims;%PATH%"

echo Arret du serveur Symfony...
call symfony server:stop
echo.
echo (MySQL et Apache ne sont pas arretes : ils peuvent servir a d autres projets. Arrete-les depuis XAMPP si besoin.)
timeout /t 3 >nul
