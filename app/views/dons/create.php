<?php
/** @var array $articles */
<<<<<<< HEAD
=======
$base_url = Flight::baseUrl();
>>>>>>> d3692f7 (commit v1)
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-gift"></i> Nouveau don</h2>
        <p>Enregistrer un don reçu (article + quantité)</p>
    </div>
<<<<<<< HEAD
    <a href="/dons" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="/dons">
=======
    <a href="<?= $base_url ?>/dons" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="<?= $base_url ?>/dons">
>>>>>>> d3692f7 (commit v1)
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
                <input type="number" name="quantite" id="quantite" class="form-control" placeholder="Ex: 120" min="1" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer</button>
    </form>
</div>
