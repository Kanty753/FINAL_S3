<?php
ob_start();
?>

<div class="card-header">
    <h1>📋 Liste des besoins</h1>
    <a href="/besoins/create" class="btn btn-primary">+ Saisir un besoin</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Ville</th>
                <th>Article</th>
                <th>Type</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Montant (Ar)</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($besoins)): ?>
                <tr><td colspan="9" class="text-center" style="color:#999; padding:2rem;">Aucun besoin enregistré.</td></tr>
            <?php else: ?>
                <?php foreach ($besoins as $b): ?>
                    <?php
                        $type_lower = strtolower($b['type_besoin']);
                        $badge = 'badge-nature';
                        if (strpos($type_lower, 'mat') !== false) $badge = 'badge-materiaux';
                        if (strpos($type_lower, 'arg') !== false) $badge = 'badge-argent';
                    ?>
                    <tr>
                        <td><?= $b['id'] ?></td>
                        <td><strong><?= htmlspecialchars($b['ville_nom']) ?></strong></td>
                        <td><?= htmlspecialchars($b['article_nom']) ?></td>
                        <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($b['type_besoin']) ?></span></td>
                        <td class="text-right"><?= number_format($b['quantite'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format($b['prix_unitaire'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format($b['montant_total'], 0, ',', ' ') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($b['date_saisie'])) ?></td>
                        <td>
                            <form action="/besoins/delete/<?= $b['id'] ?>" method="POST" style="display:inline" onsubmit="return confirm('Supprimer ce besoin ?')">
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
$title = 'Besoins';
include __DIR__ . '/../layout.php';
?>
