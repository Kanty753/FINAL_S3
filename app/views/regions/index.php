<?php
ob_start();
?>

<div class="card-header">
    <h1>🌍 Liste des régions</h1>
    <a href="/regions/create" class="btn btn-primary">+ Ajouter une région</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($regions)): ?>
                <tr><td colspan="2" class="text-center" style="color:#999; padding:2rem;">Aucune région enregistrée.</td></tr>
            <?php else: ?>
                <?php foreach ($regions as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><strong><?= htmlspecialchars($r['nom']) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
$title = 'Régions';
include __DIR__ . '/../layout.php';
?>
