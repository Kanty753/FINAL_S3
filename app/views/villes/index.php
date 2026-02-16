<?php
ob_start();
?>

<div class="card-header">
    <h1>🏘️ Liste des villes</h1>
    <a href="/villes/create" class="btn btn-primary">+ Ajouter une ville</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Région</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($villes)): ?>
                <tr><td colspan="3" class="text-center" style="color:#999; padding:2rem;">Aucune ville enregistrée.</td></tr>
            <?php else: ?>
                <?php foreach ($villes as $v): ?>
                    <tr>
                        <td><?= $v['id'] ?></td>
                        <td><strong><?= htmlspecialchars($v['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($v['region_nom']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
$title = 'Villes';
include __DIR__ . '/../layout.php';
?>
