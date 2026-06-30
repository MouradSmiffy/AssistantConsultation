<?php require_once 'includes_header.php';
$consultation_id = (int)($_GET['consultation_id'] ?? 0);
$stmt = $pdo->prepare("SELECT c.*, p.* , c.id as cid FROM consultations c JOIN patients p ON c.patient_id=p.id WHERE c.id=?");
$stmt->execute([$consultation_id]);
$data = $stmt->fetch();
if (!$data) { echo "<div class='alert alert-warning'>Consultation introuvable.</div>"; require 'includes_footer.php'; exit; }
$cabinet = $pdo->query("SELECT * FROM parametres_cabinet LIMIT 1")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $meds = [];
    foreach ($_POST['med_nom'] as $i => $nom) {
        if (trim($nom) === '') continue;
        $meds[] = [
            'nom' => $nom,
            'dosage' => $_POST['med_dosage'][$i],
            'posologie' => $_POST['med_posologie'][$i],
            'duree' => $_POST['med_duree'][$i],
        ];
    }
    $stmt = $pdo->prepare("INSERT INTO ordonnances (consultation_id, patient_id, contenu) VALUES (?,?,?)");
    $stmt->execute([$consultation_id, $data['patient_id'], json_encode($meds, JSON_UNESCAPED_UNICODE)]);
    $ordonnance_id = $pdo->lastInsertId();
    header("Location: ordonnance.php?consultation_id=$consultation_id&imprimer=$ordonnance_id");
    exit;
}

$imprimer = $_GET['imprimer'] ?? null;
if ($imprimer) {
    $stmt = $pdo->prepare("SELECT * FROM ordonnances WHERE id=?");
    $stmt->execute([$imprimer]);
    $ordonnance = $stmt->fetch();
    $meds = json_decode($ordonnance['contenu'], true);
}
?>

<?php if ($imprimer): ?>
<div class="topbar no-print">
    <h2>Ordonnance générée</h2>
    <div><button onclick="window.print()" class="btn">🖨️ Imprimer</button> <a href="consultation_detail.php?id=<?= $consultation_id ?>" class="btn btn-secondary">Retour</a></div>
</div>
<div class="card" style="max-width:700px;">
    <div style="text-align:center;margin-bottom:20px;">
        <h2 style="margin:0;color:var(--nude-800);"><?= htmlspecialchars($cabinet['nom_medecin']) ?></h2>
        <p style="margin:2px 0;color:var(--text-muted);"><?= htmlspecialchars($cabinet['specialite']) ?></p>
        <p style="margin:2px 0;font-size:13px;color:var(--text-muted);"><?= htmlspecialchars($cabinet['adresse_cabinet']) ?> — <?= htmlspecialchars($cabinet['telephone_cabinet']) ?></p>
        <?php if ($cabinet['numero_ordre']): ?><p style="margin:2px 0;font-size:13px;color:var(--text-muted);">N° d'ordre : <?= htmlspecialchars($cabinet['numero_ordre']) ?></p><?php endif; ?>
    </div>
    <hr style="border-color:var(--nude-200);">
    <p><strong>Patient :</strong> <?= htmlspecialchars($data['prenom'].' '.$data['nom']) ?> — <?= calculer_age($data['date_naissance']) ?> ans</p>
    <p><strong>Date :</strong> <?= date('d/m/Y') ?></p>
    <h3 style="margin-top:24px;">Prescription</h3>
    <ol style="line-height:2;">
        <?php foreach ($meds as $m): ?>
        <li>
            <strong><?= htmlspecialchars($m['nom']) ?></strong> <?= htmlspecialchars($m['dosage']) ?><br>
            <?= htmlspecialchars($m['posologie']) ?> — durée : <?= htmlspecialchars($m['duree']) ?>
        </li>
        <?php endforeach; ?>
    </ol>
    <div style="margin-top:60px;text-align:right;">
        <p>Signature et cachet</p>
    </div>
</div>

<?php else: ?>

<div class="topbar"><h2>Nouvelle ordonnance — <?= htmlspecialchars($data['prenom'].' '.$data['nom']) ?></h2></div>

<?php if ($data['traitement']): ?>
<div class="alert alert-info">Traitement noté en consultation : « <?= htmlspecialchars($data['traitement']) ?> »</div>
<?php endif; ?>

<form method="POST" class="card">
    <h3>Médicaments</h3>
    <div id="listeMeds">
        <div class="grid grid-4 ligne-med" style="margin-bottom:8px;">
            <input type="text" name="med_nom[]" placeholder="Nom du médicament">
            <input type="text" name="med_dosage[]" placeholder="Dosage (ex: 500mg)">
            <input type="text" name="med_posologie[]" placeholder="Posologie (ex: 1cp x3/j)">
            <input type="text" name="med_duree[]" placeholder="Durée (ex: 7 jours)">
        </div>
    </div>
    <button type="button" class="btn btn-secondary btn-sm" onclick="ajouterLigne()">+ Ajouter un médicament</button>
    <div style="margin-top:20px;">
        <button type="submit" class="btn">Générer l'ordonnance</button>
    </div>
</form>

<script>
function ajouterLigne() {
    const div = document.createElement('div');
    div.className = 'grid grid-4 ligne-med';
    div.style.marginBottom = '8px';
    div.innerHTML = `
        <input type="text" name="med_nom[]" placeholder="Nom du médicament">
        <input type="text" name="med_dosage[]" placeholder="Dosage (ex: 500mg)">
        <input type="text" name="med_posologie[]" placeholder="Posologie (ex: 1cp x3/j)">
        <input type="text" name="med_duree[]" placeholder="Durée (ex: 7 jours)">`;
    document.getElementById('listeMeds').appendChild(div);
}
</script>
<?php endif; ?>

<?php require_once 'includes_footer.php'; ?>
