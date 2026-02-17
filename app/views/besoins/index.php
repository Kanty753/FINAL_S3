<?php
/** @var array $besoins */
function formatMontantB($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
$base_url = Flight::baseUrl();
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-hand-holding-heart"></i> Besoins</h2>
        <p>Besoins des sinistrés par ville — saisis par article et quantité</p>
    </div>
    <a href="<?= $base_url ?>/besoins/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau besoin</a>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-list"></i> Liste des besoins</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ville</th>
                    <th>Article</th>
                    <th>Type</th>
                    <th class="text-right">Quantité</th>
                    <th class="text-right">P.U.</th>
                    <th class="text-right">Montant total</th>
                    <th>Date saisie</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($besoins)): ?>
                    <?php foreach ($besoins as $b): ?>
                    <tr>
                        <td class="text-muted">#<?= $b['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($b['ville_nom']) ?></td>
                        <td><?= htmlspecialchars($b['article_nom']) ?></td>
                        <td>
                            <?php
                                $cls = 'badge-nature';
                                if ($b['type_besoin'] === 'Matériaux') $cls = 'badge-materiaux';
                                elseif ($b['type_besoin'] === 'Argent') $cls = 'badge-argent';
                            ?>
                            <span class="badge <?= $cls ?>"><?= htmlspecialchars($b['type_besoin']) ?></span>
                        </td>
                        <td class="text-right"><?= number_format((int)$b['quantite'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= formatMontantB($b['prix_unitaire']) ?></td>
                        <td class="text-right money"><?= formatMontantB($b['montant_total']) ?></td>
                        <td class="text-muted"><?= date('d/m/Y H:i', strtotime($b['date_saisie'])) ?></td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('/api/besoins/<?= $b['id'] ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9">
                        <div class="empty-state"><i class="fas fa-hand-holding-heart"></i><p>Aucun besoin enregistré</p></div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
