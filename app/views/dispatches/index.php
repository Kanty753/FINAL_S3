<?php
/** @var array $dispatches */
/** @var array|null $simulation */
/** @var bool $is_simulation */
/** @var string $strategie */
function formatMontantDi($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
$base_url = Flight::baseUrl();
$is_simulation = !empty($simulation);
$strategie = $strategie ?? 'fifo';

$strategieLabels = [
    'fifo' => 'Par ordre de saisie (FIFO)',
    'plus_petit' => 'Par infériorité des ressources nécessaires',
    'proportionnel' => 'Par proportionnalité',
];
$strategieLabel = $strategieLabels[$strategie] ?? $strategieLabels['fifo'];
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-truck"></i> Dispatches</h2>
        <p>Distribution des dons attribués aux villes sinistrées</p>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="<?= $base_url ?>/dispatches/reset" style="display:inline" onsubmit="return confirm('Supprimer tous les dispatches ?')">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Réinitialiser
            </button>
        </form>
        <a href="<?= $base_url ?>/dispatches/create" class="btn btn-secondary"><i class="fas fa-plus"></i> Nouveau dispatch</a>
    </div>
</div>

<!-- Sélection de la stratégie et simulation -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-title"><i class="fas fa-cogs"></i> Stratégie de dispatch</div>
    <form method="POST" action="<?= $base_url ?>/dispatches/simuler" id="formSimuler">
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
            <label style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border: 2px solid <?= $strategie === 'fifo' ? '#3498db' : '#e0e0e0' ?>; border-radius: 8px; cursor: pointer; background: <?= $strategie === 'fifo' ? '#eaf4fd' : '#fff' ?>; transition: all 0.2s;">
                <input type="radio" name="strategie" value="fifo" <?= $strategie === 'fifo' ? 'checked' : '' ?> style="accent-color: #3498db; width: 18px; height: 18px;">
                <div>
                    <strong style="color: #2c3e50;"><i class="fas fa-sort-amount-up"></i> Par ordre de saisie (FIFO)</strong>
                    <br><small style="color: #7f8c8d;">La priorité revient à la ville qui a soumis ses besoins en premier. Les besoins suivants sont redistribués par ordre chronologique.</small>
                </div>
            </label>
            <label style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border: 2px solid <?= $strategie === 'plus_petit' ? '#27ae60' : '#e0e0e0' ?>; border-radius: 8px; cursor: pointer; background: <?= $strategie === 'plus_petit' ? '#eafaf1' : '#fff' ?>; transition: all 0.2s;">
                <input type="radio" name="strategie" value="plus_petit" <?= $strategie === 'plus_petit' ? 'checked' : '' ?> style="accent-color: #27ae60; width: 18px; height: 18px;">
                <div>
                    <strong style="color: #2c3e50;"><i class="fas fa-sort-numeric-down"></i> Par infériorité des ressources nécessaires</strong>
                    <br><small style="color: #7f8c8d;">La priorité revient à la ville qui demande la plus petite quantité. Le reste est redistribué aux demandes suivantes par ordre croissant.</small>
                </div>
            </label>
            <label style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border: 2px solid <?= $strategie === 'proportionnel' ? '#e67e22' : '#e0e0e0' ?>; border-radius: 8px; cursor: pointer; background: <?= $strategie === 'proportionnel' ? '#fef5e7' : '#fff' ?>; transition: all 0.2s;">
                <input type="radio" name="strategie" value="proportionnel" <?= $strategie === 'proportionnel' ? 'checked' : '' ?> style="accent-color: #e67e22; width: 18px; height: 18px;">
                <div>
                    <strong style="color: #2c3e50;"><i class="fas fa-balance-scale"></i> Par proportionnalité</strong>
                    <br><small style="color: #7f8c8d;">Les dons sont répartis proportionnellement entre les villes selon leurs besoins. Seule la partie entière est conservée (arrondi à l'inférieur). Il peut y avoir des restes.</small>
                </div>
            </label>
        </div>
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-eye"></i> Simuler le dispatch
        </button>
    </form>
</div>

<?php if ($is_simulation): ?>
<!-- Résultat de la simulation (non sauvegardé) -->
<div class="alert alert-success" style="background:#FEF9E7; color:#F39C12; border-left-color:#F39C12">
    <i class="fas fa-info-circle"></i> <strong>Mode simulation :</strong> Les résultats ci-dessous ne sont PAS encore enregistrés.
    <br><small><i class="fas fa-cogs"></i> <strong>Stratégie utilisée :</strong> <?= htmlspecialchars($strategieLabel) ?></small>
    <?php if ($strategie === 'fifo'): ?>
        <br><small><i class="fas fa-sort-amount-up"></i> Si plusieurs villes demandent le même article, la ville ayant fait la demande en premier est servie en priorité. Le reste est redistribué aux suivantes par ordre chronologique.</small>
    <?php elseif ($strategie === 'plus_petit'): ?>
        <br><small><i class="fas fa-sort-numeric-down"></i> La ville ayant le plus petit besoin en quantité est servie en priorité. Le reste est redistribué par ordre croissant de besoin.</small>
    <?php else: ?>
        <br><small><i class="fas fa-balance-scale"></i> Les dons sont répartis proportionnellement entre toutes les villes demandeuses. Seule la partie entière est conservée (arrondi à l'inférieur). Des restes sont possibles.</small>
    <?php endif; ?>
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
                    <?php if ($strategie === 'proportionnel'): ?>
                    <th class="text-right">Part exacte</th>
                    <?php endif; ?>
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
                    <?php
                    $totalAttribueSimu = 0;
                    $totalResteSimu = 0;
                    $totalMontantSimu = 0;
                    ?>
                    <?php foreach ($simulation as $s): ?>
                    <?php
                    $totalAttribueSimu += (int)$s['quantite_attribuee'];
                    $totalResteSimu += (int)$s['quantite_restante'];
                    $totalMontantSimu += (float)$s['montant'];
                    ?>
                    <tr>
                        <td class="text-muted">#<?= $s['don_id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($s['ville_nom']) ?></td>
                        <td><?= htmlspecialchars($s['article_nom']) ?></td>
                        <td class="text-right"><?= number_format((int)$s['quantite_demandee'], 0, ',', ' ') ?></td>
                        <?php if ($strategie === 'proportionnel'): ?>
                        <td class="text-right text-muted"><?= number_format($s['part_exacte'] ?? 0, 2, ',', ' ') ?></td>
                        <?php endif; ?>
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
                            <?php elseif ($s['priorite'] === 'Plus petit besoin'): ?>
                                <span class="badge" style="background:#8e44ad;color:#fff;padding:3px 8px;border-radius:4px;font-size:0.8em;">
                                    <i class="fas fa-sort-numeric-down"></i> + petit
                                </span>
                            <?php elseif ($s['priorite'] === 'Proportionnel'): ?>
                                <span class="badge" style="background:#e67e22;color:#fff;padding:3px 8px;border-radius:4px;font-size:0.8em;">
                                    <i class="fas fa-balance-scale"></i> Proportionnel
                                </span>
                            <?php else: ?>
                                <span class="badge" style="background:#3498db;color:#fff;padding:3px 8px;border-radius:4px;font-size:0.8em;">
                                    <i class="fas fa-arrow-right"></i> Redistribution
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight: bold; background: #f8f9fa;">
                        <td colspan="<?= $strategie === 'proportionnel' ? 5 : 4 ?>"></td>
                        <td class="text-right"><?= number_format($totalAttribueSimu, 0, ',', ' ') ?></td>
                        <td class="text-right text-danger"><?= number_format($totalResteSimu, 0, ',', ' ') ?></td>
                        <td></td>
                        <td class="text-right money-success"><?= formatMontantDi($totalMontantSimu) ?></td>
                        <td colspan="2"></td>
                    </tr>
                <?php else: ?>
                    <tr><td colspan="<?= $strategie === 'proportionnel' ? 11 : 10 ?>">
                        <div class="empty-state"><i class="fas fa-info-circle"></i><p>La simulation ne produit aucun dispatch</p></div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Bouton Valider le dispatch -->
    <div style="margin-top: 16px; display: flex; gap: 10px; align-items: center;">
        <form method="POST" action="<?= $base_url ?>/dispatches/valider" style="display:inline" onsubmit="return confirm('Cela va valider et enregistrer le dispatch avec la stratégie « <?= htmlspecialchars($strategieLabel) ?> ». Continuer ?')">
            <input type="hidden" name="strategie" value="<?= htmlspecialchars($strategie) ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> Valider et enregistrer ce dispatch
            </button>
        </form>
        <span style="color: #7f8c8d; font-size: 0.9em;">
            <i class="fas fa-info-circle"></i> La validation supprimera les anciens dispatches et enregistrera cette simulation.
        </span>
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
