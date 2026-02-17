<?php
/** @var array $dispatches */
<<<<<<< HEAD
function formatMontantDi($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
=======
/** @var array|null $simulation */
/** @var bool $is_simulation */
function formatMontantDi($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
$base_url = Flight::baseUrl();
$is_simulation = !empty($simulation);
>>>>>>> d3692f7 (commit v1)
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-truck"></i> Dispatches</h2>
        <p>Distribution des dons attribués aux villes sinistrées</p>
    </div>
    <div class="d-flex gap-2">
<<<<<<< HEAD
        <form method="POST" action="/dispatches/simuler" style="display:inline" onsubmit="return confirm('Cela va recalculer tous les dispatches. Continuer ?')">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-sync-alt"></i> Simuler le dispatch
            </button>
        </form>
        <a href="/dispatches/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau dispatch</a>
    </div>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-list"></i> Liste des dispatches</div>
=======
        <form method="POST" action="<?= $base_url ?>/dispatches/simuler" style="display:inline">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-eye"></i> Simuler le dispatch
            </button>
        </form>
    </div>
</div>

<?php if ($is_simulation): ?>
<!-- Résultat de la simulation (non sauvegardé) -->
<div class="alert alert-success" style="background:#FEF9E7; color:#F39C12; border-left-color:#F39C12">
    <i class="fas fa-info-circle"></i> <strong>Mode simulation :</strong> Les résultats ci-dessous sont un aperçu de la distribution. Rien n'est enregistré en base de données. La priorité est donnée aux besoins saisis en premier (date de saisie).
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
                    <th class="text-right">Qté attribuée</th>
                    <th class="text-right">P.U.</th>
                    <th class="text-right">Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($simulation)): ?>
                    <?php foreach ($simulation as $s): ?>
                    <tr>
                        <td class="text-muted">#<?= $s['don_id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($s['ville_nom']) ?></td>
                        <td><?= htmlspecialchars($s['article_nom']) ?></td>
                        <td class="text-right"><?= number_format((int)$s['quantite_attribuee'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= formatMontantDi($s['prix_unitaire']) ?></td>
                        <td class="text-right money-success"><?= formatMontantDi($s['montant']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">
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
>>>>>>> d3692f7 (commit v1)
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
<<<<<<< HEAD
                        <div class="empty-state"><i class="fas fa-truck"></i><p>Aucun dispatch enregistré — cliquez sur "Simuler" pour lancer la distribution</p></div>
=======
                        <div class="empty-state"><i class="fas fa-truck"></i><p>Aucun dispatch enregistré — cliquez sur "Simuler" pour voir la distribution</p></div>
>>>>>>> d3692f7 (commit v1)
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
