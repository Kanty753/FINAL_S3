<?php
/** @var array $villes */
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-city"></i> Villes</h2>
        <p>Gestion des villes et leur rattachement aux régions</p>
    </div>
    <a href="/villes/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle ville</a>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-list"></i> Liste des villes</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Région</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($villes)): ?>
                    <?php foreach ($villes as $v): ?>
                    <tr>
                        <td class="text-muted">#<?= $v['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($v['nom']) ?></td>
                        <td><span class="badge badge-purple"><?= htmlspecialchars($v['region_nom']) ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('/api/villes/<?= $v['id'] ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">
                        <div class="empty-state"><i class="fas fa-city"></i><p>Aucune ville enregistrée</p></div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
