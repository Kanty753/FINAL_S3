<?php
ob_start();
?>

<div class="card-header">
    <h1>🎁 Liste des dons</h1>
    <a href="/dons/create" class="btn btn-primary">+ Enregistrer un don</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Article</th>
                <th>Type</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Montant (Ar)</th>
                <th>Dispatché</th>
                <th>Reste</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dons)): ?>
                <tr><td colspan="10" class="text-center" style="color:#999; padding:2rem;">Aucun don enregistré.</td></tr>
            <?php else: ?>
                <?php foreach ($dons as $d): ?>
                    <?php
                        $type_lower = strtolower($d['type_besoin']);
                        $badge = 'badge-nature';
                        if (strpos($type_lower, 'mat') !== false) $badge = 'badge-materiaux';
                        if (strpos($type_lower, 'arg') !== false) $badge = 'badge-argent';
                        $reste = $d['quantite'] - $d['quantite_dispatche'];
                    ?>
                    <tr>
                        <td><?= $d['id'] ?></td>
                        <td><strong><?= htmlspecialchars($d['article_nom']) ?></strong></td>
                        <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($d['type_besoin']) ?></span></td>
                        <td class="text-right"><?= number_format($d['quantite'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format($d['prix_unitaire'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format($d['montant_total'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format($d['quantite_dispatche'], 0, ',', ' ') ?></td>
                        <td class="text-right" style="font-weight:600; color: <?= $reste > 0 ? '#f39c12' : '#27ae60' ?>;">
                            <?= number_format($reste, 0, ',', ' ') ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($d['date_don'])) ?></td>
                        <td>
                            <form action="/dons/delete/<?= $d['id'] ?>" method="POST" style="display:inline" onsubmit="return confirm('Supprimer ce don et ses dispatches ?')">
                                <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
$title = 'Dons';
include __DIR__ . '/../layout.php';
?>
