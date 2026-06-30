<?php require_once 'includes_header.php';
$id = (int)($_GET['id'] ?? 0);
$patient = $pdo->prepare("SELECT * FROM patients WHERE id=?");
$patient->execute([$id]);
$patient = $patient->fetch();
if (!$patient) { echo "<div class='alert alert-warning'>Patient introuvable.</div>"; require 'includes_footer.php'; exit; }
$age = calculer_age($patient['date_naissance']);
?>

<div class="topbar">
    <h2><?= htmlspecialchars($patient['prenom'].' '.$patient['nom']) ?> <span style="color:var(--text-muted);font-size:15px;">(<?= $age ?> ans, <?= $patient['sexe'] ?>)</span></h2>
    <a href="nouvelle_consultation.php?patient_id=<?= $id ?>" class="btn">🩺 Nouvelle consultation</a>
</div>

<?php if (isset($_GET['nouveau'])): ?>
<div class="alert alert-info">Dossier créé avec succès.</div>
<?php endif; ?>

<div class="grid grid-2">
    <div class="card">
        <h3>Informations</h3>
        <p><strong>Téléphone :</strong> <?= htmlspecialchars($patient['telephone'] ?: '—') ?></p>
        <p><strong>Adresse :</strong> <?= htmlspecialchars($patient['adresse'] ?: '—') ?></p>
        <p><strong>Groupe sanguin :</strong> <?= htmlspecialchars($patient['groupe_sanguin'] ?: '—') ?></p>
        <p><strong>Allergies :</strong> <?= htmlspecialchars($patient['allergies'] ?: 'Aucune connue') ?></p>
    </div>
    <div class="card">
        <h3>Antécédents</h3>
        <p><strong>Médicaux :</strong> <?= htmlspecialchars($patient['antecedents_medicaux'] ?: '—') ?></p>
        <p><strong>Chirurgicaux :</strong> <?= htmlspecialchars($patient['antecedents_chirurgicaux'] ?: '—') ?></p>
        <p><strong>Familiaux :</strong> <?= htmlspecialchars($patient['antecedents_familiaux'] ?: '—') ?></p>
    </div>
</div>

<div class="card">
    <h3>Maladies chroniques suivies</h3>
    <?php
    $mc = $pdo->prepare("SELECT * FROM maladies_chroniques WHERE patient_id=? AND actif=1");
    $mc->execute([$id]);
    $mc = $mc->fetchAll();
    ?>
    <?php if (empty($mc)): ?>
        <p style="color:var(--text-muted);">Aucune maladie chronique suivie. <a href="chroniques.php?patient_id=<?= $id ?>">Ajouter un suivi</a></p>
    <?php else: ?>
        <?php foreach ($mc as $m): ?>
        <span class="badge badge-warning" style="margin-right:6px;"><?= htmlspecialchars($m['type_maladie']) ?></span>
        <?php endforeach; ?>
        <p><a href="chroniques.php?patient_id=<?= $id ?>">Voir le suivi détaillé →</a></p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Historique des consultations</h3>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM consultations WHERE patient_id=? ORDER BY date_consultation DESC");
    $stmt->execute([$id]);
    $consults = $stmt->fetchAll();
    ?>
    <?php if (empty($consults)): ?>
        <div class="empty-state">Aucune consultation enregistrée.</div>
    <?php else: ?>
    <table>
        <tr><th>Date</th><th>Motif</th><th>IMC</th><th>Tension</th><th>Diagnostic</th><th></th></tr>
        <?php foreach ($consults as $c): ?>
        <tr>
            <td><?= date('d/m/Y H:i', strtotime($c['date_consultation'])) ?></td>
            <td><?= htmlspecialchars(mb_strimwidth($c['motif'] ?? '', 0, 30, '...')) ?></td>
            <td><?= $c['imc'] ?: '—' ?></td>
            <td><?= $c['tension_systolique'] ? $c['tension_systolique'].'/'.$c['tension_diastolique'] : '—' ?></td>
            <td><?= htmlspecialchars(mb_strimwidth($c['diagnostic'] ?? '', 0, 30, '...')) ?></td>
            <td><a href="consultation_detail.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-secondary">Voir</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>

<?php require_once 'includes_footer.php'; ?>
