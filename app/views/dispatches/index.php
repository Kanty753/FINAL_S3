<?php
/** @var array $dispatches */
function formatMontantDi($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-truck"></i> Dispatches</h2>
        <p>Distribution des dons attribués aux villes sinistrées</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-warning" onclick="simulerDispatch()">
            <i class="fas fa-sync-alt"></i> Simuler le dispatch
        </button>
        <a href="/dispatches/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau dispatch</a>
    </div>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-list"></i> Liste des dispatches</div>
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
                        <div class="empty-state"><i class="fas fa-truck"></i><p>Aucun dispatch enregistré — cliquez sur "Simuler" pour lancer la distribution</p></div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
