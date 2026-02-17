<?php
/** @var array $regions */
<<<<<<< HEAD
=======
$base_url = Flight::baseUrl();
>>>>>>> d3692f7 (commit v1)
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-city"></i> Nouvelle ville</h2>
        <p>Ajouter une ville rattachée à une région</p>
    </div>
<<<<<<< HEAD
    <a href="/villes" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="/villes">
=======
    <a href="<?= $base_url ?>/villes" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="<?= $base_url ?>/villes">
>>>>>>> d3692f7 (commit v1)
        <div class="form-group">
            <label for="nom"><i class="fas fa-tag"></i> Nom de la ville</label>
            <input type="text" name="nom" id="nom" class="form-control" placeholder="Ex: Antananarivo" required>
        </div>
        <div class="form-group">
            <label for="region_id"><i class="fas fa-map"></i> Région</label>
            <select name="region_id" id="region_id" class="form-control" required>
                <option value="">— Sélectionner une région —</option>
                <?php foreach ($regions as $r): ?>
                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer</button>
    </form>
</div>
