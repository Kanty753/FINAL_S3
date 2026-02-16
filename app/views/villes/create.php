<?php
ob_start();
?>

<h1>🏘️ Ajouter une ville</h1>

<div class="card" style="max-width: 500px;">
    <form action="/villes" method="POST">
        <div class="form-group">
            <label for="nom">Nom de la ville</label>
            <input type="text" id="nom" name="nom" class="form-control" required placeholder="Ex: Antananarivo">
        </div>
        <div class="form-group">
            <label for="region_id">Région</label>
            <select id="region_id" name="region_id" class="form-control" required>
                <option value="">-- Sélectionner une région --</option>
                <?php foreach ($regions as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex gap-1">
            <button type="submit" class="btn btn-success">✅ Enregistrer</button>
            <a href="/villes" class="btn btn-danger">Annuler</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = 'Ajouter une ville';
include __DIR__ . '/../layout.php';
?>
