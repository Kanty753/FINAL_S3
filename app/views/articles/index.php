<?php
/** @var array $articles */
function formatPrix($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
$base_url = Flight::baseUrl();
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-box-open"></i> Articles</h2>
        <p>Catalogue des articles avec prix unitaire et type de besoin</p>
    </div>
    <a href="<?= $base_url ?>/articles/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvel article</a>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-list"></i> Liste des articles</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Type de besoin</th>
                    <th class="text-right">Prix unitaire</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($articles)): ?>
                    <?php foreach ($articles as $a): ?>
                    <tr>
                        <td class="text-muted">#<?= $a['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($a['nom']) ?></td>
                        <td>
                            <?php
                                $cls = 'badge-nature';
                                if ($a['type_besoin'] === 'Matériaux') $cls = 'badge-materiaux';
                                elseif ($a['type_besoin'] === 'Argent') $cls = 'badge-argent';
                            ?>
                            <span class="badge <?= $cls ?>"><?= htmlspecialchars($a['type_besoin']) ?></span>
                        </td>
                        <td class="text-right money"><?= formatPrix($a['prix_unitaire']) ?></td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('/api/articles/<?= $a['id'] ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">
                        <div class="empty-state"><i class="fas fa-box-open"></i><p>Aucun article enregistré</p></div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
