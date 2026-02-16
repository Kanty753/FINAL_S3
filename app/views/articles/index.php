<?php
ob_start();
?>

<div class="card-header">
    <h1>📦 Liste des articles</h1>
    <a href="/articles/create" class="btn btn-primary">+ Ajouter un article</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Type de besoin</th>
                <th>Prix unitaire (Ar)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($articles)): ?>
                <tr><td colspan="4" class="text-center" style="color:#999; padding:2rem;">Aucun article enregistré.</td></tr>
            <?php else: ?>
                <?php foreach ($articles as $a): ?>
                    <?php
                        $type_lower = strtolower($a['type_besoin']);
                        $badge = 'badge-nature';
                        if (strpos($type_lower, 'mat') !== false) $badge = 'badge-materiaux';
                        if (strpos($type_lower, 'arg') !== false) $badge = 'badge-argent';
                    ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><strong><?= htmlspecialchars($a['nom']) ?></strong></td>
                        <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($a['type_besoin']) ?></span></td>
                        <td class="text-right"><?= number_format($a['prix_unitaire'], 0, ',', ' ') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
$title = 'Articles';
include __DIR__ . '/../layout.php';
?>
