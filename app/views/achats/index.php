<?php
/** @var array $achats */
/** @var array $villes */
/** @var string|null $ville_id_filtre */
/** @var float $frais_pourcent */
function formatMontantAc($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
$base_url = Flight::baseUrl();
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-shopping-cart"></i> Achats</h2>
        <p>Achats effectués via les dons en argent pour couvrir les besoins en nature et matériaux (frais : <?= $frais_pourcent ?>%)</p>
    </div>
    <a href="<?= $base_url ?>/achats/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvel achat</a>
</div>

<!-- Filtre par ville -->
<div class="card" style="padding:16px 24px">
    <form method="GET" action="<?= $base_url ?>/achats" class="d-flex align-center gap-2">
        <label for="ville_id" style="font-weight:600;white-space:nowrap"><i class="fas fa-filter"></i> Filtrer par ville :</label>
        <select name="ville_id" id="ville_id" class="form-control" style="max-width:250px">
            <option value="">— Toutes les villes —</option>
            <?php foreach ($villes as $v): ?>
            <option value="<?= $v['id'] ?>" <?= ($ville_id_filtre == $v['id']) ? 'selected' : '' ?>><?= htmlspecialchars($v['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Filtrer</button>
        <?php if ($ville_id_filtre): ?>
        <a href="<?= $base_url ?>/achats" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Réinitialiser</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-list"></i> Liste des achats</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ville</th>
                    <th>Article</th>
                    <th>Type</th>
                    <th class="text-right">Montant brut</th>
                    <th class="text-right">Frais (%)</th>
                    <th class="text-right">Montant total</th>
                    <th>Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($achats)): ?>
                    <?php foreach ($achats as $ac): ?>
                    <?php $montantTotal = (float)$ac['montant'] + ((float)$ac['montant'] * (float)$ac['frais'] / 100); ?>
                    <tr>
                        <td class="text-muted">#<?= $ac['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($ac['ville_nom']) ?></td>
                        <td><?= htmlspecialchars($ac['article_nom']) ?></td>
                        <td>
                            <?php
                                $cls = 'badge-nature';
                                if ($ac['type_besoin'] === 'Matériaux') $cls = 'badge-materiaux';
                                elseif ($ac['type_besoin'] === 'Argent') $cls = 'badge-argent';
                            ?>
                            <span class="badge <?= $cls ?>"><?= htmlspecialchars($ac['type_besoin']) ?></span>
                        </td>
                        <td class="text-right money"><?= formatMontantAc($ac['montant']) ?></td>
                        <td class="text-right"><?= number_format((float)$ac['frais'], 0) ?>%</td>
                        <td class="text-right money-success"><?= formatMontantAc($montantTotal) ?></td>
                        <td class="text-muted"><?= date('d/m/Y H:i', strtotime($ac['date_achat'])) ?></td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('/api/achats/<?= $ac['id'] ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9">
                        <div class="empty-state"><i class="fas fa-shopping-cart"></i><p>Aucun achat enregistré</p></div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
