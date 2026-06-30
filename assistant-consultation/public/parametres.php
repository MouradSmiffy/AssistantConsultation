<?php require_once 'includes_header.php';
$cabinet = $pdo->query("SELECT * FROM parametres_cabinet LIMIT 1")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE parametres_cabinet SET nom_medecin=?, specialite=?, adresse_cabinet=?, telephone_cabinet=?, numero_ordre=? WHERE id=?");
    $stmt->execute([$_POST['nom_medecin'], $_POST['specialite'], $_POST['adresse_cabinet'], $_POST['telephone_cabinet'], $_POST['numero_ordre'], $cabinet['id']]);
    header("Location: parametres.php?ok=1");
    exit;
}
?>

<div class="topbar"><h2>Paramètres du cabinet</h2></div>

<?php if (isset($_GET['ok'])): ?><div class="alert alert-info">Paramètres enregistrés.</div><?php endif; ?>

<form method="POST" class="card" style="max-width:600px;">
    <p style="color:var(--text-muted);font-size:13px;">Ces informations apparaîtront sur les ordonnances et certificats générés.</p>
    <div class="form-group"><label>Nom du médecin</label><input type="text" name="nom_medecin" value="<?= htmlspecialchars($cabinet['nom_medecin']) ?>"></div>
    <div class="form-group"><label>Spécialité</label><input type="text" name="specialite" value="<?= htmlspecialchars($cabinet['specialite']) ?>"></div>
    <div class="form-group"><label>Adresse du cabinet</label><input type="text" name="adresse_cabinet" value="<?= htmlspecialchars($cabinet['adresse_cabinet']) ?>"></div>
    <div class="form-group"><label>Téléphone</label><input type="text" name="telephone_cabinet" value="<?= htmlspecialchars($cabinet['telephone_cabinet']) ?>"></div>
    <div class="form-group"><label>Numéro d'ordre</label><input type="text" name="numero_ordre" value="<?= htmlspecialchars($cabinet['numero_ordre']) ?>"></div>
    <button type="submit" class="btn">Enregistrer</button>
</form>

<?php require_once 'includes_footer.php'; ?>
