<?php
ob_start();
?>

<h1>📋 Saisir un besoin</h1>

<div class="card" style="max-width: 500px;">
    <form action="/besoins" method="POST">
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
            <label for="article_id">Article</label>
            <select id="article_id" name="article_id" class="form-control" required>
                <option value="">-- Sélectionner un article --</option>
                <?php foreach ($articles as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nom']) ?> (<?= htmlspecialchars($a['type_besoin']) ?>) - <?= number_format($a['prix_unitaire'], 0, ',', ' ') ?> Ar</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantite">Quantité</label>
            <input type="number" id="quantite" name="quantite" class="form-control" required min="1" placeholder="Ex: 100">
        </div>
        <div class="flex gap-1">
            <button type="submit" class="btn btn-success">✅ Enregistrer</button>
            <a href="/besoins" class="btn btn-danger">Annuler</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = 'Saisir un besoin';
include __DIR__ . '/../layout.php';
?>
