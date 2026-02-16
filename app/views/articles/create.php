<?php
ob_start();
?>

<h1>📦 Ajouter un article</h1>

<div class="card" style="max-width: 500px;">
    <form action="/articles" method="POST">
        <div class="form-group">
            <label for="nom">Nom de l'article</label>
            <input type="text" id="nom" name="nom" class="form-control" required placeholder="Ex: Riz">
        </div>
        <div class="form-group">
            <label for="type_besoin_id">Type de besoin</label>
            <select id="type_besoin_id" name="type_besoin_id" class="form-control" required>
                <option value="">-- Sélectionner un type --</option>
                <?php foreach ($types as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="prix_unitaire">Prix unitaire (Ar)</label>
            <input type="number" id="prix_unitaire" name="prix_unitaire" class="form-control" required min="0" step="0.01" placeholder="Ex: 2500">
        </div>
        <div class="flex gap-1">
            <button type="submit" class="btn btn-success">✅ Enregistrer</button>
            <a href="/articles" class="btn btn-danger">Annuler</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = 'Ajouter un article';
include __DIR__ . '/../layout.php';
?>
