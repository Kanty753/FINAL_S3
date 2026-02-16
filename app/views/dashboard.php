<?php
/** @var array $stats */
/** @var array $etat_dons */
/** @var array $dashboard */

function formatMontant($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
?>

<div class="page-header">
    <div>
        <h2>📊 Tableau de bord</h2>
        <p>Vue d'ensemble des besoins et des dons attribués par ville</p>
    </div>
    <form method="POST" action="/dispatches/simuler" style="display:inline" onsubmit="return confirm('Cela va recalculer tous les dispatches. Continuer ?')">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sync-alt"></i> Simuler le dispatch
        </button>
    </form>
</div>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card purple">
        <div class="stat-icon"><i class="fas fa-map"></i></div>
        <div class="stat-value"><?= $stats['total_regions'] ?? 0 ?></div>
        <div class="stat-label">Régions</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-icon"><i class="fas fa-city"></i></div>
        <div class="stat-value"><?= $stats['total_villes'] ?? 0 ?></div>
        <div class="stat-label">Villes</div>
    </div>
    <div class="stat-card pink">
        <div class="stat-icon"><i class="fas fa-hand-holding-heart"></i></div>
        <div class="stat-value"><?= $stats['total_besoins'] ?? 0 ?></div>
        <div class="stat-label">Besoins</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon"><i class="fas fa-gift"></i></div>
        <div class="stat-value"><?= $stats['total_dons'] ?? 0 ?></div>
        <div class="stat-label">Dons</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-truck"></i></div>
        <div class="stat-value"><?= $stats['total_dispatches'] ?? 0 ?></div>
        <div class="stat-label">Dispatches</div>
    </div>
</div>

<!-- État global des dons -->
<div class="card">
    <div class="card-title"><i class="fas fa-chart-bar"></i> État global des dons</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Article</th>
                    <th class="text-right">Qté don</th>
                    <th class="text-right">Qté dispatchée</th>
                    <th class="text-right">Reste</th>
                    <th>Progression</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($etat_dons)): ?>
                    <?php foreach ($etat_dons as $don): ?>
                    <?php
                        $pct = (int)$don['quantite_don'] > 0 ? round((int)$don['quantite_dispatche'] / (int)$don['quantite_don'] * 100) : 0;
                    ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($don['article']) ?></td>
                        <td class="text-right"><?= number_format((int)$don['quantite_don'], 0, ',', ' ') ?></td>
                        <td class="text-right money-success"><?= number_format((int)$don['quantite_dispatche'], 0, ',', ' ') ?></td>
                        <td class="text-right <?= (int)$don['reste'] > 0 ? 'money-danger' : 'money-success' ?>">
                            <?= number_format((int)$don['reste'], 0, ',', ' ') ?>
                        </td>
                        <td style="min-width:140px">
                            <div class="d-flex align-center gap-2">
                                <div class="progress-bar-container" style="flex:1">
                                    <div class="progress-bar-fill" style="width:<?= $pct ?>%"></div>
                                </div>
                                <span style="font-size:0.78rem;font-weight:600;color:var(--primary);min-width:36px"><?= $pct ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted">Aucun don enregistré</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Tableau de bord par ville -->
<div class="card">
    <div class="card-title"><i class="fas fa-city"></i> Besoins & dons attribués par ville</div>

    <?php if (!empty($dashboard)): ?>
        <?php foreach ($dashboard as $ville): ?>
        <div class="card" style="background: var(--lighter); box-shadow: none; border: 2px solid var(--light);">
            <div class="d-flex justify-between align-center" style="margin-bottom:16px">
                <div>
                    <h3 style="font-size:1.1rem;font-weight:700;color:var(--primary)"><?= htmlspecialchars($ville['ville']) ?></h3>
                    <span class="badge badge-purple"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($ville['region']) ?></span>
                </div>
                <div style="text-align:right">
                    <div style="font-size:0.75rem;color:var(--gray);text-transform:uppercase;letter-spacing:0.5px">Total besoins</div>
                    <div class="money" style="font-size:1.3rem"><?= formatMontant($ville['total_besoin_montant']) ?></div>
                </div>
            </div>

            <div class="grid-2">
                <!-- Besoins -->
                <div>
                    <h4 style="font-size:0.85rem;font-weight:700;color:var(--danger);margin-bottom:10px">
                        <i class="fas fa-hand-holding-heart"></i> Besoins
                    </h4>
                    <?php if (!empty($ville['besoins'])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Type</th>
                                <th class="text-right">Qté</th>
                                <th class="text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ville['besoins'] as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['article']) ?></td>
                                <td>
                                    <?php
                                        $tbClass = 'badge-nature';
                                        if ($b['type_besoin'] === 'Matériaux') $tbClass = 'badge-materiaux';
                                        elseif ($b['type_besoin'] === 'Argent') $tbClass = 'badge-argent';
                                    ?>
                                    <span class="badge <?= $tbClass ?>"><?= htmlspecialchars($b['type_besoin']) ?></span>
                                </td>
                                <td class="text-right"><?= number_format((int)$b['besoin_quantite'], 0, ',', ' ') ?></td>
                                <td class="text-right money"><?= formatMontant($b['montant_besoin']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="text-muted" style="font-size:0.85rem">Aucun besoin enregistré</p>
                    <?php endif; ?>
                </div>

                <!-- Dons attribués -->
                <div>
                    <h4 style="font-size:0.85rem;font-weight:700;color:var(--success);margin-bottom:10px">
                        <i class="fas fa-gift"></i> Dons attribués
                    </h4>
                    <?php if (!empty($ville['dons_attribues'])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th class="text-right">Qté attribuée</th>
                                <th class="text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ville['dons_attribues'] as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['article']) ?></td>
                                <td class="text-right"><?= number_format((int)$d['total_attribue'], 0, ',', ' ') ?></td>
                                <td class="text-right money-success"><?= formatMontant($d['montant_attribue']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="text-muted" style="font-size:0.85rem">Aucun don attribué</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucune donnée disponible pour le tableau de bord</p>
        </div>
    <?php endif; ?>
</div>
