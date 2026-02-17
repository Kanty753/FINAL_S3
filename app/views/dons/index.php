<?php
/** @var array $dons */
function formatMontantD($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
$base_url = Flight::baseUrl();
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-gift"></i> Dons</h2>
        <p>Dons reçus avec article, quantité et état du dispatch</p>
    </div>
    <a href="<?= $base_url ?>/dons/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau don</a>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-list"></i> Liste des dons</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Article</th>
                    <th>Type</th>
                    <th class="text-right">Quantité</th>
                    <th class="text-right">P.U.</th>
                    <th class="text-right">Montant total</th>
                    <th class="text-right">Qté dispatchée</th>
                    <th class="text-right">Reste</th>
                    <th>Date don</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dons)): ?>
                    <?php foreach ($dons as $d): ?>
                    <?php $reste = (int)$d['quantite'] - (int)$d['quantite_dispatche']; ?>
                    <tr>
                        <td class="text-muted">#<?= $d['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($d['article_nom']) ?></td>
                        <td>
                            <?php
                                $cls = 'badge-nature';
                                if ($d['type_besoin'] === 'Matériaux') $cls = 'badge-materiaux';
                                elseif ($d['type_besoin'] === 'Argent') $cls = 'badge-argent';
                            ?>
                            <span class="badge <?= $cls ?>"><?= htmlspecialchars($d['type_besoin']) ?></span>
                        </td>
                        <td class="text-right"><?= number_format((int)$d['quantite'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= formatMontantD($d['prix_unitaire']) ?></td>
                        <td class="text-right money"><?= formatMontantD($d['montant_total']) ?></td>
                        <td class="text-right money-success"><?= number_format((int)$d['quantite_dispatche'], 0, ',', ' ') ?></td>
                        <td class="text-right <?= $reste > 0 ? 'money-danger' : 'money-success' ?>"><?= number_format($reste, 0, ',', ' ') ?></td>
                        <td class="text-muted"><?= date('d/m/Y H:i', strtotime($d['date_don'])) ?></td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('/api/dons/<?= $d['id'] ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="10">
                        <div class="empty-state"><i class="fas fa-gift"></i><p>Aucun don enregistré</p></div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
