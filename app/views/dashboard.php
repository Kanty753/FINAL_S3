<?php
ob_start();

// Calcul des stats
$total_besoins_montant = 0;
$total_attribue_montant = 0;
foreach ($besoins as $b) {
    $total_besoins_montant += $b['montant_besoin'];
}
foreach ($dispatches as $d) {
    $total_attribue_montant += $d['montant_attribue'];
}
$couverture = $total_besoins_montant > 0 ? round(($total_attribue_montant / $total_besoins_montant) * 100, 1) : 0;
?>

<h1>📊 Tableau de bord</h1>

<!-- Stats globales -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($resume_villes) ?></div>
        <div class="stat-label">Villes sinistrées</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-value"><?= number_format($total_besoins_montant, 0, ',', ' ') ?> Ar</div>
        <div class="stat-label">Total des besoins</div>
    </div>
    <div class="stat-card success">
        <div class="stat-value"><?= number_format($total_attribue_montant, 0, ',', ' ') ?> Ar</div>
        <div class="stat-label">Total distribué</div>
    </div>
    <div class="stat-card <?= $couverture >= 80 ? 'success' : ($couverture >= 50 ? 'warning' : 'danger') ?>">
        <div class="stat-value"><?= $couverture ?>%</div>
        <div class="stat-label">Taux de couverture</div>
    </div>
</div>

<!-- Bouton simulation -->
<div class="card">
    <div class="card-header">
        <h2>⚡ Simulation du dispatch</h2>
        <form action="/dispatches/simuler" method="POST" style="display:inline;">
            <button type="submit" class="btn btn-warning" onclick="return confirm('Relancer la simulation du dispatch ? Cela va recalculer toutes les attributions.')">
                🔄 Simuler le dispatch
            </button>
        </form>
    </div>
    <p style="color:#777; font-size:0.9rem;">
        La simulation distribue les dons aux villes par ordre de date de saisie des besoins.
    </p>
</div>

<!-- Résumé par ville -->
<div class="card">
    <div class="card-header">
        <h2>🏘️ Résumé par ville</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Ville</th>
                <th>Région</th>
                <th>Total besoins (Ar)</th>
                <th>Total attribué (Ar)</th>
                <th>Couverture</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resume_villes as $rv): ?>
                <?php
                    $attribue_ville = 0;
                    foreach ($dispatches as $d) {
                        if ($d['ville_id'] == $rv['id']) {
                            $attribue_ville += $d['montant_attribue'];
                        }
                    }
                    $pct = $rv['total_besoin'] > 0 ? round(($attribue_ville / $rv['total_besoin']) * 100, 1) : 0;
                    $bar_class = $pct >= 100 ? '' : ($pct > 0 ? 'partial' : 'empty');
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($rv['ville']) ?></strong></td>
                    <td><?= htmlspecialchars($rv['region']) ?></td>
                    <td class="text-right"><?= number_format($rv['total_besoin'], 0, ',', ' ') ?></td>
                    <td class="text-right"><?= number_format($attribue_ville, 0, ',', ' ') ?></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <div class="progress" style="flex:1;">
                                <div class="progress-bar <?= $bar_class ?>" style="width: <?= min($pct, 100) ?>%"></div>
                            </div>
                            <span style="font-size:0.8rem; font-weight:600; color:<?= $pct >= 100 ? '#27ae60' : ($pct > 0 ? '#f39c12' : '#e74c3c') ?>">
                                <?= $pct ?>%
                            </span>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Détail besoins par ville -->
<div class="card">
    <div class="card-header">
        <h2>📋 Détail des besoins par ville</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Ville</th>
                <th>Région</th>
                <th>Article</th>
                <th>Type</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Montant (Ar)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($besoins as $b): ?>
                <?php
                    $type_lower = strtolower($b['type_besoin']);
                    $badge = 'badge-nature';
                    if (strpos($type_lower, 'mat') !== false) $badge = 'badge-materiaux';
                    if (strpos($type_lower, 'arg') !== false) $badge = 'badge-argent';
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($b['ville']) ?></strong></td>
                    <td><?= htmlspecialchars($b['region']) ?></td>
                    <td><?= htmlspecialchars($b['article']) ?></td>
                    <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($b['type_besoin']) ?></span></td>
                    <td class="text-right"><?= number_format($b['besoin_quantite'], 0, ',', ' ') ?></td>
                    <td class="text-right"><?= number_format($b['prix_unitaire'], 0, ',', ' ') ?></td>
                    <td class="text-right"><?= number_format($b['montant_besoin'], 0, ',', ' ') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Dons attribués par ville -->
<div class="card">
    <div class="card-header">
        <h2>🎁 Dons attribués par ville</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Ville</th>
                <th>Article</th>
                <th>Quantité attribuée</th>
                <th>Montant (Ar)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dispatches)): ?>
                <tr><td colspan="4" class="text-center" style="color:#999; padding:2rem;">Aucun dispatch effectué. Cliquez sur "Simuler le dispatch" pour distribuer les dons.</td></tr>
            <?php else: ?>
                <?php foreach ($dispatches as $d): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($d['ville']) ?></strong></td>
                        <td><?= htmlspecialchars($d['article']) ?></td>
                        <td class="text-right"><?= number_format($d['total_attribue'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format($d['montant_attribue'], 0, ',', ' ') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- État des dons -->
<div class="card">
    <div class="card-header">
        <h2>📦 État des dons</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Article</th>
                <th>Quantité totale</th>
                <th>Quantité dispatchée</th>
                <th>Reste</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($total_dons as $td): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($td['article']) ?></strong></td>
                    <td class="text-right"><?= number_format($td['quantite_don'], 0, ',', ' ') ?></td>
                    <td class="text-right"><?= number_format($td['quantite_dispatche'], 0, ',', ' ') ?></td>
                    <td class="text-right" style="color: <?= $td['reste'] > 0 ? '#f39c12' : '#27ae60' ?>; font-weight:600;">
                        <?= number_format($td['reste'], 0, ',', ' ') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
$title = 'Tableau de bord';
include __DIR__ . '/layout.php';
?>
