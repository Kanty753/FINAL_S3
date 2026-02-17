<?php
/** @var array $dispatches */
/** @var array|null $simulation */
/** @var bool $is_simulation */
function formatMontantDi($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
$base_url = Flight::baseUrl();
$is_simulation = !empty($simulation);
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-truck"></i> Dispatches</h2>
        <p>Distribution des dons attribués aux villes sinistrées</p>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="<?= $base_url ?>/dispatches/simuler" style="display:inline">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-eye"></i> Simuler le dispatch
            </button>
        </form>
        <?php if ($is_simulation): ?>
        <form method="POST" action="<?= $base_url ?>/dispatches/valider" style="display:inline" onsubmit="return confirm('Cela va valider et enregistrer le dispatch. Continuer ?')">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> Valider le dispatch
            </button>
        </form>
        <?php endif; ?>
        <form method="POST" action="<?= $base_url ?>/dispatches/reset" style="display:inline" onsubmit="return confirm('Supprimer tous les dispatches ?')">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Réinitialiser
            </button>
        </form>
        <a href="<?= $base_url ?>/dispatches/create" class="btn btn-secondary"><i class="fas fa-plus"></i> Nouveau dispatch</a>
    </div>
</div>

<?php if ($is_simulation): ?>
<!-- Résultat de la simulation (non sauvegardé) -->
<div class="alert alert-success" style="background:#FEF9E7; color:#F39C12; border-left-color:#F39C12">
    <i class="fas fa-info-circle"></i> <strong>Mode simulation :</strong> Les résultats ci-dessous ne sont PAS encore enregistrés. Cliquez sur "Valider le dispatch" pour les sauvegarder.
    <br><small><i class="fas fa-sort-amount-up"></i> <strong>Règle de priorité :</strong> Si plusieurs villes demandent le même article, la ville ayant fait la demande en premier est servie en priorité. Le reste est redistribué aux suivantes par ordre chronologique.</small>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-eye"></i> Résultat de la simulation</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Don ID</th>
                    <th>Ville</th>
                    <th>Article</th>
                    <th class="text-right">Qté demandée</th>
                    <th class="text-right">Qté attribuée</th>
                    <th class="text-right">Reste non couvert</th>
                    <th class="text-right">P.U.</th>
                    <th class="text-right">Montant</th>
                    <th>Date demande</th>
                    <th class="text-center">Priorité</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($simulation)): ?>
                    <?php foreach ($simulation as $s): ?>
                    <tr>
                        <td class="text-muted">#<?= $s['don_id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($s['ville_nom']) ?></td>
                        <td><?= htmlspecialchars($s['article_nom']) ?></td>
                        <td class="text-right"><?= number_format((int)$s['quantite_demandee'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format((int)$s['quantite_attribuee'], 0, ',', ' ') ?></td>
                        <td class="text-right <?= ((int)$s['quantite_restante'] > 0) ? 'text-danger' : 'text-success' ?>">
                            <?= number_format((int)$s['quantite_restante'], 0, ',', ' ') ?>
                        </td>
                        <td class="text-right"><?= formatMontantDi($s['prix_unitaire']) ?></td>
                        <td class="text-right money-success"><?= formatMontantDi($s['montant']) ?></td>
                        <td class="text-muted"><?= date('d/m/Y H:i', strtotime($s['date_demande'])) ?></td>
                        <td class="text-center">
                            <?php if ($s['priorite'] === 'Premier servi'): ?>
                                <span class="badge" style="background:#27ae60;color:#fff;padding:3px 8px;border-radius:4px;font-size:0.8em;">
                                    <i class="fas fa-trophy"></i> 1er servi
                                </span>
                            <?php else: ?>
                                <span class="badge" style="background:#3498db;color:#fff;padding:3px 8px;border-radius:4px;font-size:0.8em;">
                                    <i class="fas fa-arrow-right"></i> Redistribution
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="10">
                        <div class="empty-state"><i class="fas fa-info-circle"></i><p>La simulation ne produit aucun dispatch</p></div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Dispatches enregistrés -->
<div class="card">
    <div class="card-title"><i class="fas fa-list"></i> Dispatches enregistrés</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ville</th>
                    <th>Article</th>
                    <th class="text-right">Qté attribuée</th>
                    <th class="text-right">P.U.</th>
                    <th class="text-right">Montant</th>
                    <th>Date dispatch</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dispatches)): ?>
                    <?php foreach ($dispatches as $d): ?>
                    <tr>
                        <td class="text-muted">#<?= $d['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($d['ville_nom']) ?></td>
                        <td><?= htmlspecialchars($d['article_nom']) ?></td>
                        <td class="text-right"><?= number_format((int)$d['quantite_attribuee'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= formatMontantDi($d['prix_unitaire']) ?></td>
                        <td class="text-right money-success"><?= formatMontantDi($d['montant']) ?></td>
                        <td class="text-muted"><?= date('d/m/Y H:i', strtotime($d['date_dispatch'])) ?></td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('/api/dispatches/<?= $d['id'] ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">
                        <div class="empty-state"><i class="fas fa-truck"></i><p>Aucun dispatch enregistré — cliquez sur "Simuler" puis "Valider" pour lancer la distribution</p></div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
