@echo off
title Initialisation de la base de donnees
echo Demarrage de MySQL...
start "" "%~dp0xampp\xampp_start.exe"
timeout /t 6 /nobreak >nul

echo Creation de la base de donnees...
"%~dp0xampp\mysql\bin\mysql.exe" -u root < "%~dp0sql\schema.sql"
"%~dp0xampp\mysql\bin\mysql.exe" -u root < "%~dp0sql\seed.sql"

echo.
echo Base de donnees initialisee avec succes !
echo Vous pouvez maintenant utiliser l'Assistant de Consultation.
pause
