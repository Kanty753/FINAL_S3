<?php
ob_start();
?>

<h1>📦 Dispatch manuel</h1>

<div class="card" style="max-width: 500px;">
    <form action="/dispatches" method="POST">
        <div class="form-group">
            <label for="don_id">Don (article - reste disponible)</label>
            <select id="don_id" name="don_id" class="form-control" required>
                <option value="">-- Sélectionner un don --</option>
                <?php foreach ($dons as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['article_nom']) ?> - Reste: <?= number_format($d['reste'], 0, ',', ' ') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="ville_id">Ville</label>
            <select id="ville_id" name="ville_id" class="form-control" required>
                <option value="">-- Sélectionner une ville --</option>
                <?php foreach ($villes as $v): ?>
                    <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantite_attribuee">Quantité à attribuer</label>
            <input type="number" id="quantite_attribuee" name="quantite_attribuee" class="form-control" required min="1" placeholder="Ex: 20">
        </div>
        <div class="flex gap-1">
            <button type="submit" class="btn btn-success">✅ Dispatcher</button>
            <a href="/dispatches" class="btn btn-danger">Annuler</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = 'Dispatch manuel';
include __DIR__ . '/../layout.php';
?>
