<?php /** Formulaire de création d'une région */ ?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-map"></i> Nouvelle région</h2>
        <p>Ajouter une nouvelle région géographique</p>
    </div>
    <a href="/regions" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="/regions">
        <div class="form-group">
            <label for="nom"><i class="fas fa-tag"></i> Nom de la région</label>
            <input type="text" name="nom" id="nom" class="form-control" placeholder="Ex: Région Nord" required>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer</button>
    </form>
</div>
