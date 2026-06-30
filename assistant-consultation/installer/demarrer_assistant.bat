@echo off
title Assistant de Consultation
echo Demarrage du serveur...

REM Démarre Apache et MySQL via XAMPP (installé dans le même dossier que cette application)
start "" "%~dp0xampp\xampp_start.exe"

REM Attendre que les serveurs soient prêts
timeout /t 5 /nobreak >nul

REM Ouvrir l'application dans le navigateur par défaut
start "" "http://localhost/assistant-consultation/public/index.php"

echo L'application est lancee. Vous pouvez fermer cette fenetre.
exit
