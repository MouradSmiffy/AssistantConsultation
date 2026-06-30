<?php require_once 'includes_header.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT c.*, p.nom, p.prenom, p.id as patient_id FROM consultations c JOIN patients p ON c.patient_id=p.id WHERE c.id=?");
$stmt->execute([$id]);
$c = $stmt->fetch();
if (!$c) { echo "<div class='alert alert-warning'>Consultation introuvable.</div>"; require 'includes_footer.php'; exit; }
?>
<div class="topbar">
    <h2>Consultation du <?= date('d/m/Y', strtotime($c['date_consultation'])) ?> — <?= htmlspecialchars($c['prenom'].' '.$c['nom']) ?></h2>
    <div>
        <a href="ordonnance.php?consultation_id=<?= $id ?>" class="btn btn-secondary">📋 Ordonnance</a>
        <a href="certificat.php?consultation_id=<?= $id ?>" class="btn btn-secondary">📄 Certificat</a>
        <a href="patient_detail.php?id=<?= $c['patient_id'] ?>" class="btn">← Retour au dossier</a>
    </div>
</div>

<div class="card">
    <h3>Résumé</h3>
    <p><strong>Motif :</strong> <?= nl2br(htmlspecialchars($c['motif'] ?: '—')) ?></p>
    <div class="grid grid-4">
        <div class="stat-box"><div class="value"><?= $c['imc'] ?: '—' ?></div><div class="label">IMC</div></div>
        <div class="stat-box"><div class="value"><?= $c['tension_systolique'] ? $c['tension_systolique'].'/'.$c['tension_diastolique'] : '—' ?></div><div class="label">Tension</div></div>
        <div class="stat-box"><div class="value"><?= $c['frequence_cardiaque'] ?: '—' ?></div><div class="label">Fréq. cardiaque</div></div>
        <div class="stat-box"><div class="value"><?= $c['score_cardiovasculaire'] ?: '—' ?></div><div class="label">Risque CV</div></div>
    </div>
</div>

<div class="card">
    <h3>Symptômes & diagnostic</h3>
    <p><strong>Symptômes :</strong> <?= nl2br(htmlspecialchars($c['symptomes'] ?: '—')) ?></p>
    <p><strong>Pistes diagnostiques :</strong> <?= nl2br(htmlspecialchars($c['pistes_diagnostiques'] ?: '—')) ?></p>
    <p><strong>Examen clinique :</strong> <?= nl2br(htmlspecialchars($c['examen_clinique'] ?: '—')) ?></p>
    <p><strong>Diagnostic retenu :</strong> <?= nl2br(htmlspecialchars($c['diagnostic'] ?: '—')) ?></p>
    <p><strong>Traitement :</strong> <?= nl2br(htmlspecialchars($c['traitement'] ?: '—')) ?></p>
    <p><strong>Notes :</strong> <?= nl2br(htmlspecialchars($c['notes'] ?: '—')) ?></p>
</div>

<?php require_once 'includes_footer.php'; ?>
