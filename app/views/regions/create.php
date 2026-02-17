<<<<<<< HEAD
<?php /** Formulaire de création d'une région */ ?>
=======
<?php
/** Formulaire de création d'une région */
$base_url = Flight::baseUrl();
?>
>>>>>>> d3692f7 (commit v1)

<div class="page-header">
    <div>
        <h2><i class="fas fa-map"></i> Nouvelle région</h2>
        <p>Ajouter une nouvelle région géographique</p>
    </div>
<<<<<<< HEAD
    <a href="/regions" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="/regions">
=======
    <a href="<?= $base_url ?>/regions" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="<?= $base_url ?>/regions">
>>>>>>> d3692f7 (commit v1)
        <div class="form-group">
            <label for="nom"><i class="fas fa-tag"></i> Nom de la région</label>
            <input type="text" name="nom" id="nom" class="form-control" placeholder="Ex: Région Nord" required>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer</button>
    </form>
</div>
