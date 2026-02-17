<?php
/** @var array $regions */
$base_url = Flight::baseUrl();
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-map"></i> Régions</h2>
        <p>Gestion des régions géographiques</p>
    </div>
    <a href="<?= $base_url ?>/regions/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle région</a>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-list"></i> Liste des régions</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($regions)): ?>
                    <?php foreach ($regions as $r): ?>
                    <tr>
                        <td class="text-muted">#<?= $r['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($r['nom']) ?></td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('/api/regions/<?= $r['id'] ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">
                        <div class="empty-state"><i class="fas fa-map"></i><p>Aucune région enregistrée</p></div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
