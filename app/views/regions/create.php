<?php
ob_start();
?>

<h1>🌍 Ajouter une région</h1>

<div class="card" style="max-width: 500px;">
    <form action="/regions" method="POST">
        <div class="form-group">
            <label for="nom">Nom de la région</label>
            <input type="text" id="nom" name="nom" class="form-control" required placeholder="Ex: Analamanga">
        </div>
        <div class="flex gap-1">
            <button type="submit" class="btn btn-success">✅ Enregistrer</button>
            <a href="/regions" class="btn btn-danger">Annuler</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = 'Ajouter une région';
include __DIR__ . '/../layout.php';
?>
