# Guide : créer le fichier .exe d'installation

Je ne peux pas générer un .exe Windows directement depuis mon environnement (je travaille sur Linux et n'ai pas accès au site d'XAMPP). Mais voici la marche à suivre, simple, pour le faire toi-même en 15-20 minutes sur ton PC Windows. Une fois fait, tu auras un vrai `AssistantConsultation_Setup.exe` à donner à ta femme.

## Étape 1 — Télécharger les deux outils gratuits

1. **XAMPP Portable** : https://www.apachefriends.org/download.html (choisis la version Windows)
   - Installe-le normalement sur TON PC (pas besoin du sien) dans un dossier temporaire, par ex. `C:\xampp_temp`
2. **Inno Setup** (pour fabriquer le .exe) : https://jrsoftware.org/isdl.php — installe-le normalement.

## Étape 2 — Préparer le dossier du projet

1. Décompresse le fichier `assistant-consultation.zip` que je t'ai fourni quelque part sur ton PC, par ex. `C:\projet-assistant\`
2. Dans `C:\projet-assistant\installer\`, crée un dossier nommé `xampp_portable`
3. Copie TOUT le contenu de `C:\xampp_temp` (apache, mysql, php, htdocs, etc.) dans `C:\projet-assistant\installer\xampp_portable\`

## Étape 3 — Compiler l'installeur

1. Ouvre **Inno Setup Compiler**
2. Fichier → Ouvrir → sélectionne `C:\projet-assistant\installer\setup.iss`
3. Clique sur **Compiler** (ou Build → Compile)
4. Le fichier `AssistantConsultation_Setup.exe` apparaît dans `C:\projet-assistant\installer\Output\`

C'est ce fichier `.exe` que tu installes sur le poste de ta femme. Double-clic, suivant, suivant, et une icône "Assistant de Consultation" apparaît sur son Bureau.

## Étape 4 — Premier lancement chez elle

Lors de l'installation, deux actions s'enchaînent automatiquement :
1. **Initialiser la base de données** (une seule fois, à l'installation) — crée toutes les tables.
2. **Lancer l'Assistant de Consultation** — démarre le serveur et ouvre l'application dans le navigateur.

Ensuite, au quotidien, elle clique juste sur l'icône du Bureau pour tout démarrer.

## Accès phpMyAdmin (gestion avancée des données)

Une fois l'application démarrée (XAMPP actif), phpMyAdmin est accessible à :
`http://localhost/phpmyadmin`
(identifiant `root`, pas de mot de passe par défaut — vous pouvez en ajouter un dans XAMPP si vous le souhaitez pour plus de sécurité).

## Important — sauvegardes

Les données sont stockées localement dans le dossier MySQL de l'installation. Je te recommande de mettre en place une sauvegarde régulière (copier le dossier `xampp/mysql/data`, ou exporter via phpMyAdmin → Exporter) pour éviter toute perte de données.

## Alternative plus simple si tu préfères

Si fabriquer le .exe te semble too much, on peut aussi simplement :
- Installer XAMPP directement sur le PC de ta femme (interface graphique simple)
- Copier le dossier de l'application dans `htdocs`
- Lui créer un raccourci sur le Bureau (le fichier `demarrer_assistant.bat` suffit)

Pas besoin d'installeur compilé dans ce cas — dis-moi si tu préfères cette option, c'est plus rapide à mettre en place.
