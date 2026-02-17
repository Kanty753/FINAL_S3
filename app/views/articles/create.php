<?php
/** @var array $types_besoins */
$base_url = Flight::baseUrl();
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-box-open"></i> Nouvel article</h2>
        <p>Ajouter un article au catalogue</p>
    </div>
    <a href="<?= $base_url ?>/articles" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="<?= $base_url ?>/articles">
        <div class="form-group">
            <label for="nom"><i class="fas fa-tag"></i> Nom de l'article</label>
            <input type="text" name="nom" id="nom" class="form-control" placeholder="Ex: Riz, Tôle, Huile..." required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="type_besoin_id"><i class="fas fa-layer-group"></i> Type de besoin</label>
                <select name="type_besoin_id" id="type_besoin_id" class="form-control" required>
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($types_besoins as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="prix_unitaire"><i class="fas fa-coins"></i> Prix unitaire (Ar)</label>
                <input type="number" name="prix_unitaire" id="prix_unitaire" class="form-control" placeholder="Ex: 2500" min="0" step="0.01" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer</button>
    </form>
</div>
