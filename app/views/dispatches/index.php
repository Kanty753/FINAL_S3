<?php
ob_start();
?>

<div class="card-header">
    <h1>📦 Liste des dispatches</h1>
    <div class="flex gap-1">
        <a href="/dispatches/create" class="btn btn-primary">+ Dispatch manuel</a>
        <form action="/dispatches/simuler" method="POST" style="display:inline">
            <button type="submit" class="btn btn-warning" onclick="return confirm('Relancer la simulation ?')">🔄 Simuler</button>
        </form>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Ville</th>
                <th>Article</th>
                <th>Quantité attribuée</th>
                <th>Montant (Ar)</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dispatches)): ?>
                <tr><td colspan="6" class="text-center" style="color:#999; padding:2rem;">Aucun dispatch effectué.</td></tr>
            <?php else: ?>
                <?php foreach ($dispatches as $d): ?>
                    <tr>
                        <td><?= $d['id'] ?></td>
                        <td><strong><?= htmlspecialchars($d['ville_nom']) ?></strong></td>
                        <td><?= htmlspecialchars($d['article_nom']) ?></td>
                        <td class="text-right"><?= number_format($d['quantite_attribuee'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format($d['montant'], 0, ',', ' ') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($d['date_dispatch'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
$title = 'Dispatches';
include __DIR__ . '/../layout.php';
?>
