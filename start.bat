@echo off
chcp 65001 >nul
title Symfony - e-commerce
cd /d "%~dp0"
set "PATH=%USERPROFILE%\scoop\shims;%PATH%"

where symfony >nul 2>&1
if errorlevel 1 (
    echo [ERREUR] La commande "symfony" est introuvable. Installe Symfony CLI : scoop install symfony-cli
    pause
    exit /b 1
)

echo [1/4] MySQL...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if errorlevel 1 (
    if not exist "C:\xampp\mysql\bin\mysqld.exe" (
        echo [ERREUR] C:\xampp\mysql\bin\mysqld.exe introuvable. Demarre MySQL depuis XAMPP.
        pause
        exit /b 1
    )
    start "" /min "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
    timeout /t 5 /nobreak >nul
    tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
    if errorlevel 1 (
        echo [ERREUR] MySQL ne demarre pas. Ouvre XAMPP et regarde le message d'erreur.
        pause
        exit /b 1
    )
    echo      MySQL demarre.
) else (
    echo      MySQL deja en cours.
)

echo [2/4] Apache (pour phpMyAdmin)...
tasklist /FI "IMAGENAME eq httpd.exe" 2>nul | find /I "httpd.exe" >nul
if errorlevel 1 (
    if exist "C:\xampp\apache\bin\httpd.exe" (
        start "" /min "C:\xampp\apache\bin\httpd.exe"
        timeout /t 3 /nobreak >nul
        echo      Apache demarre.
    ) else (
        echo      [ATTENTION] Apache introuvable, phpMyAdmin ne s'ouvrira pas.
    )
) else (
    echo      Apache deja en cours.
)

echo [3/4] Serveur Symfony...
call symfony server:stop >nul 2>&1
call symfony serve -d --no-tls
if errorlevel 1 (
    echo [ERREUR] Le serveur Symfony n'a pas demarre.
    pause
    exit /b 1
)

echo [4/4] Ouverture du site et de la base de donnees...
call symfony open:local
start "" "http://localhost/phpmyadmin/index.php?route=/database/structure&db=my_shop_dev"
echo.
echo Serveur lance (voir l'adresse ci-dessus). stop.bat pour l'arreter.
timeout /t 4 >nul
