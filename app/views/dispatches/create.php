<?php
/** @var array $dons */
/** @var array $villes */
$base_url = Flight::baseUrl();
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-truck"></i> Nouveau dispatch</h2>
        <p>Attribuer manuellement un don à une ville</p>
    </div>
    <a href="<?= $base_url ?>/dispatches" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="<?= $base_url ?>/dispatches">
        <div class="form-group">
            <label for="don_id"><i class="fas fa-gift"></i> Don</label>
            <select name="don_id" id="don_id" class="form-control" required>
                <option value="">— Sélectionner un don —</option>
                <?php foreach ($dons as $d): ?>
                <option value="<?= $d['id'] ?>">Don #<?= $d['id'] ?> — <?= htmlspecialchars($d['article_nom']) ?> (reste: <?= $d['reste'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="ville_id"><i class="fas fa-city"></i> Ville</label>
                <select name="ville_id" id="ville_id" class="form-control" required>
                    <option value="">— Sélectionner une ville —</option>
                    <?php foreach ($villes as $v): ?>
                    <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantite_attribuee"><i class="fas fa-sort-numeric-up"></i> Quantité à attribuer</label>
                <input type="number" name="quantite_attribuee" id="quantite_attribuee" class="form-control" placeholder="Ex: 50" min="1" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer</button>
    </form>
</div>
