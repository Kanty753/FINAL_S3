<?php
/** @var array $villes */
/** @var array $articles */
<<<<<<< HEAD
=======
$base_url = Flight::baseUrl();
>>>>>>> d3692f7 (commit v1)
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-hand-holding-heart"></i> Nouveau besoin</h2>
        <p>Saisir un besoin pour une ville (article + quantité)</p>
    </div>
<<<<<<< HEAD
    <a href="/besoins" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="/besoins">
=======
    <a href="<?= $base_url ?>/besoins" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="<?= $base_url ?>/besoins">
>>>>>>> d3692f7 (commit v1)
        <div class="form-group">
            <label for="ville_id"><i class="fas fa-city"></i> Ville</label>
            <select name="ville_id" id="ville_id" class="form-control" required>
                <option value="">— Sélectionner une ville —</option>
                <?php foreach ($villes as $v): ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="article_id"><i class="fas fa-box-open"></i> Article</label>
                <select name="article_id" id="article_id" class="form-control" required>
                    <option value="">— Sélectionner un article —</option>
                    <?php foreach ($articles as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nom']) ?> (<?= number_format((float)$a['prix_unitaire'], 0, ',', ' ') ?> Ar)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantite"><i class="fas fa-sort-numeric-up"></i> Quantité</label>
                <input type="number" name="quantite" id="quantite" class="form-control" placeholder="Ex: 100" min="1" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer</button>
    </form>
</div>
