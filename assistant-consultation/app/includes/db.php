<?php
// Connexion à la base de données MySQL (XAMPP local)
$DB_HOST = '127.0.0.1';
$DB_NAME = 'assistant_consultation';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données. Vérifiez que XAMPP (Apache + MySQL) est bien démarré.<br>Détail technique : " . htmlspecialchars($e->getMessage()));
}
