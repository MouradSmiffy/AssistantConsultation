<?php require_once 'includes_header.php';
$consultation_id = (int)($_GET['consultation_id'] ?? 0);
$stmt = $pdo->prepare("SELECT c.*, p.* FROM consultations c JOIN patients p ON c.patient_id=p.id WHERE c.id=?");
$stmt->execute([$consultation_id]);
$data = $stmt->fetch();
if (!$data) { echo "<div class='alert alert-warning'>Consultation introuvable.</div>"; require 'includes_footer.php'; exit; }
$cabinet = $pdo->query("SELECT * FROM parametres_cabinet LIMIT 1")->fetch();

$modeles = [
    'repos' => "Je soussigné(e) {medecin}, certifie avoir examiné ce jour {patient}, né(e) le {naissance}, et certifie que son état de santé nécessite un repos du {debut} au {fin} inclus.",
    'aptitude' => "Je soussigné(e) {medecin}, certifie avoir examiné ce jour {patient}, né(e) le {naissance}, et certifie qu'il/elle ne présente, à l'examen clinique de ce jour, aucune contre-indication apparente à la pratique de l'activité concernée.",
    'sport' => "Je soussigné(e) {medecin}, certifie avoir examiné ce jour {patient}, né(e) le {naissance}, et certifie qu'il/elle ne présente aucune contre-indication apparente à la pratique du sport en compétition.",
    'scolaire' => "Je soussigné(e) {medecin}, certifie avoir examiné ce jour {patient}, né(e) le {naissance}, et certifie que son état de santé justifie une absence scolaire du {debut} au {fin} inclus.",
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type_certificat'];
    $contenu = strtr($_POST['contenu'], [
        '{medecin}' => $cabinet['nom_medecin'],
        '{patient}' => $data['prenom'].' '.$data['nom'],
        '{naissance}' => date('d/m/Y', strtotime($data['date_naissance'])),
        '{debut}' => $_POST['date_debut'] ? date('d/m/Y', strtotime($_POST['date_debut'])) : '',
        '{fin}' => $_POST['date_fin'] ? date('d/m/Y', strtotime($_POST['date_fin'])) : '',
    ]);
    $stmt = $pdo->prepare("INSERT INTO certificats (patient_id, consultation_id, type_certificat, contenu, date_debut, date_fin) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$data['patient_id'], $consultation_id, $type, $contenu, $_POST['date_debut'] ?: null, $_POST['date_fin'] ?: null]);
    $certificat_id = $pdo->lastInsertId();
    header("Location: certificat.php?consultation_id=$consultation_id&imprimer=$certificat_id");
    exit;
}

$imprimer = $_GET['imprimer'] ?? null;
if ($imprimer) {
    $stmt = $pdo->prepare("SELECT * FROM certificats WHERE id=?");
    $stmt->execute([$imprimer]);
    $certificat = $stmt->fetch();
}
?>

<?php if ($imprimer): ?>
<div class="topbar no-print">
    <h2>Certificat généré</h2>
    <div><button onclick="window.print()" class="btn">🖨️ Imprimer</button> <a href="consultation_detail.php?id=<?= $consultation_id ?>" class="btn btn-secondary">Retour</a></div>
</div>
<div class="card" style="max-width:700px;">
    <div style="text-align:center;margin-bottom:24px;">
        <h2 style="margin:0;color:var(--nude-800);"><?= htmlspecialchars($cabinet['nom_medecin']) ?></h2>
        <p style="margin:2px 0;color:var(--text-muted);"><?= htmlspecialchars($cabinet['specialite']) ?></p>
        <p style="margin:2px 0;font-size:13px;color:var(--text-muted);"><?= htmlspecialchars($cabinet['adresse_cabinet']) ?> — <?= htmlspecialchars($cabinet['telephone_cabinet']) ?></p>
    </div>
    <h3 style="text-align:center;text-transform:uppercase;letter-spacing:1px;">Certificat médical</h3>
    <p style="line-height:1.9;margin-top:30px;"><?= nl2br(htmlspecialchars($certificat['contenu'])) ?></p>
    <p style="margin-top:30px;">Certificat établi à la demande de l'intéressé(e) et remis en main propre, pour servir et valoir ce que de droit.</p>
    <div style="margin-top:60px;display:flex;justify-content:space-between;">
        <p>Fait le <?= date('d/m/Y') ?></p>
        <p>Signature et cachet</p>
    </div>
</div>

<?php else: ?>

<div class="topbar"><h2>Nouveau certificat — <?= htmlspecialchars($data['prenom'].' '.$data['nom']) ?></h2></div>

<form method="POST" class="card">
    <div class="form-group">
        <label>Type de certificat</label>
        <select name="type_certificat" id="type_certificat" onchange="majModele()">
            <option value="repos">Certificat de repos</option>
            <option value="aptitude">Certificat d'aptitude</option>
            <option value="sport">Certificat d'aptitude au sport</option>
            <option value="scolaire">Certificat d'absence scolaire</option>
        </select>
    </div>
    <div class="grid grid-2">
        <div class="form-group"><label>Date de début (si applicable)</label><input type="date" name="date_debut" id="date_debut"></div>
        <div class="form-group"><label>Date de fin (si applicable)</label><input type="date" name="date_fin" id="date_fin"></div>
    </div>
    <div class="form-group">
        <label>Texte du certificat (modifiable)</label>
        <textarea name="contenu" id="contenu" rows="6"></textarea>
    </div>
    <button type="submit" class="btn">Générer le certificat</button>
</form>

<script>
const modeles = <?= json_encode($modeles, JSON_UNESCAPED_UNICODE) ?>;
const medecin = <?= json_encode($cabinet['nom_medecin']) ?>;
const patient = <?= json_encode($data['prenom'].' '.$data['nom']) ?>;
const naissance = <?= json_encode(date('d/m/Y', strtotime($data['date_naissance']))) ?>;

function majModele() {
    const type = document.getElementById('type_certificat').value;
    let texte = modeles[type];
    texte = texte.replace('{medecin}', medecin).replace('{patient}', patient).replace('{naissance}', naissance);
    document.getElementById('contenu').value = texte;
}
majModele();
</script>
<?php endif; ?>

<?php require_once 'includes_footer.php'; ?>
