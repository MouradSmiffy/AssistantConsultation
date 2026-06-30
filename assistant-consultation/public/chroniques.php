<?php require_once 'includes_header.php';
$patient_id = (int)($_GET['patient_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_maladie'])) {
    $stmt = $pdo->prepare("INSERT INTO maladies_chroniques (patient_id, type_maladie, date_diagnostic, traitement_actuel, notes) VALUES (?,?,?,?,?)");
    $stmt->execute([$_POST['patient_id'], $_POST['type_maladie'], $_POST['date_diagnostic'] ?: null, $_POST['traitement_actuel'], $_POST['notes']]);
    header("Location: chroniques.php?patient_id=" . $_POST['patient_id']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_mesure'])) {
    $stmt = $pdo->prepare("INSERT INTO suivi_parametres (maladie_id, date_mesure, parametre, valeur, unite, notes) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$_POST['maladie_id'], $_POST['date_mesure'] ?: date('Y-m-d'), $_POST['parametre'], $_POST['valeur'], $_POST['unite'], $_POST['notes_mesure']]);
    header("Location: chroniques.php?patient_id=" . $patient_id);
    exit;
}
?>

<div class="topbar"><h2>Suivi des maladies chroniques</h2></div>

<?php if ($patient_id):
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id=?");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch();
?>

<div class="card">
    <h3>Ajouter une maladie chronique à suivre — <?= htmlspecialchars($patient['prenom'].' '.$patient['nom']) ?></h3>
    <form method="POST">
        <input type="hidden" name="patient_id" value="<?= $patient_id ?>">
        <div class="grid grid-2">
            <div class="form-group"><label>Type</label>
                <select name="type_maladie"><option value="HTA">Hypertension (HTA)</option><option value="Diabete">Diabète</option><option value="Asthme">Asthme</option><option value="Autre">Autre</option></select>
            </div>
            <div class="form-group"><label>Date de diagnostic</label><input type="date" name="date_diagnostic"></div>
        </div>
        <div class="form-group"><label>Traitement actuel</label><input type="text" name="traitement_actuel"></div>
        <div class="form-group"><label>Notes</label><input type="text" name="notes"></div>
        <button type="submit" name="ajouter_maladie" value="1" class="btn">Ajouter au suivi</button>
        <a href="patient_detail.php?id=<?= $patient_id ?>" class="btn btn-secondary">← Retour au dossier</a>
    </form>
</div>

<?php
$stmt = $pdo->prepare("SELECT * FROM maladies_chroniques WHERE patient_id=? AND actif=1");
$stmt->execute([$patient_id]);
$maladies = $stmt->fetchAll();
?>

<?php foreach ($maladies as $m): ?>
<div class="card">
    <h3><?= htmlspecialchars($m['type_maladie']) ?> — suivi depuis le <?= $m['date_diagnostic'] ? date('d/m/Y', strtotime($m['date_diagnostic'])) : '—' ?></h3>
    <p><strong>Traitement actuel :</strong> <?= htmlspecialchars($m['traitement_actuel'] ?: '—') ?></p>

    <form method="POST" style="margin:14px 0;padding:14px;background:var(--nude-50);border-radius:10px;">
        <input type="hidden" name="maladie_id" value="<?= $m['id'] ?>">
        <div class="grid grid-4">
            <div class="form-group"><label>Date</label><input type="date" name="date_mesure" value="<?= date('Y-m-d') ?>"></div>
            <div class="form-group"><label>Paramètre</label>
                <input type="text" name="parametre" placeholder="ex: Tension, Glycémie, DEP" list="params-<?= $m['id'] ?>">
                <datalist id="params-<?= $m['id'] ?>">
                    <?php if ($m['type_maladie']=='HTA'): ?><option value="Tension artérielle"><?php endif; ?>
                    <?php if ($m['type_maladie']=='Diabete'): ?><option value="Glycémie à jeun"><option value="HbA1c"><?php endif; ?>
                    <?php if ($m['type_maladie']=='Asthme'): ?><option value="Débit expiratoire de pointe"><?php endif; ?>
                </datalist>
            </div>
            <div class="form-group"><label>Valeur</label><input type="text" name="valeur" placeholder="ex: 135/85"></div>
            <div class="form-group"><label>Unité</label><input type="text" name="unite" placeholder="ex: mmHg, g/L, L/min"></div>
        </div>
        <div class="form-group"><label>Notes</label><input type="text" name="notes_mesure"></div>
        <button type="submit" name="ajouter_mesure" value="1" class="btn btn-sm">Ajouter une mesure</button>
    </form>

    <?php
    $stmt2 = $pdo->prepare("SELECT * FROM suivi_parametres WHERE maladie_id=? ORDER BY date_mesure DESC LIMIT 10");
    $stmt2->execute([$m['id']]);
    $mesures = $stmt2->fetchAll();
    ?>
    <?php if (!empty($mesures)): ?>
    <table>
        <tr><th>Date</th><th>Paramètre</th><th>Valeur</th><th>Notes</th></tr>
        <?php foreach ($mesures as $mes): ?>
        <tr>
            <td><?= date('d/m/Y', strtotime($mes['date_mesure'])) ?></td>
            <td><?= htmlspecialchars($mes['parametre']) ?></td>
            <td><?= htmlspecialchars($mes['valeur']) ?> <?= htmlspecialchars($mes['unite']) ?></td>
            <td><?= htmlspecialchars($mes['notes'] ?: '—') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p style="color:var(--text-muted);">Aucune mesure enregistrée pour le moment.</p>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<?php else: ?>
<div class="card">
    <h3>Tous les patients suivis pour maladies chroniques</h3>
    <?php
    $list = $pdo->query("SELECT m.*, p.nom, p.prenom, p.id as pid FROM maladies_chroniques m JOIN patients p ON m.patient_id=p.id WHERE m.actif=1 ORDER BY p.nom")->fetchAll();
    ?>
    <?php if (empty($list)): ?>
        <div class="empty-state">Aucun suivi en cours. Ouvrez un dossier patient pour démarrer un suivi.</div>
    <?php else: ?>
    <table>
        <tr><th>Patient</th><th>Maladie</th><th>Traitement</th><th></th></tr>
        <?php foreach ($list as $m): ?>
        <tr>
            <td><a href="patient_detail.php?id=<?= $m['pid'] ?>"><?= htmlspecialchars($m['prenom'].' '.$m['nom']) ?></a></td>
            <td><span class="badge badge-warning"><?= htmlspecialchars($m['type_maladie']) ?></span></td>
            <td><?= htmlspecialchars($m['traitement_actuel'] ?: '—') ?></td>
            <td><a href="chroniques.php?patient_id=<?= $m['pid'] ?>" class="btn btn-sm btn-secondary">Voir le suivi</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once 'includes_footer.php'; ?>
