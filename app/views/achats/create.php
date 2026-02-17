<?php
/** @var array $besoins_restants */
/** @var array $dons_argent */
/** @var array $villes */
/** @var float $frais_pourcent */
/** @var string|null $error */
function formatMontantAcC($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
$base_url = Flight::baseUrl();
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-shopping-cart"></i> Nouvel achat</h2>
        <p>Acheter des besoins en nature/matériaux via les dons en argent (frais : <?= $frais_pourcent ?>%)</p>
    </div>
    <a href="<?= $base_url ?>/achats" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<div class="grid-2">
    <!-- Formulaire d'achat -->
    <div class="card">
        <div class="card-title"><i class="fas fa-file-invoice-dollar"></i> Formulaire d'achat</div>
        <form method="POST" action="<?= $base_url ?>/achats" id="achatForm">
            <div class="form-group">
                <label for="besoin_id"><i class="fas fa-hand-holding-heart"></i> Besoin à couvrir</label>
                <select name="besoin_id" id="besoin_id" class="form-control" required onchange="calculerMontant()">
                    <option value="">— Sélectionner un besoin —</option>
                    <?php foreach ($besoins_restants as $b): ?>
                    <option value="<?= $b['besoin_id'] ?>" 
                            data-prix="<?= $b['prix_unitaire'] ?>"
                            data-restant="<?= $b['quantite_restante'] ?>"
                            data-article="<?= htmlspecialchars($b['article_nom']) ?>"
                            data-ville="<?= htmlspecialchars($b['ville_nom']) ?>">
                        <?= htmlspecialchars($b['ville_nom']) ?> — <?= htmlspecialchars($b['article_nom']) ?> 
                        (reste: <?= (int)$b['quantite_restante'] ?> × <?= formatMontantAcC($b['prix_unitaire']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="don_id"><i class="fas fa-money-bill-wave"></i> Don en argent à utiliser</label>
                <select name="don_id" id="don_id" class="form-control" required>
                    <option value="">— Sélectionner un don en argent —</option>
                    <?php foreach ($dons_argent as $d): ?>
                    <option value="<?= $d['don_id'] ?>" data-restant="<?= $d['montant_restant'] ?>">
                        Don #<?= $d['don_id'] ?> — Disponible : <?= formatMontantAcC($d['montant_restant']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="quantite"><i class="fas fa-sort-numeric-up"></i> Quantité à acheter</label>
                <input type="number" name="quantite" id="quantite" class="form-control" placeholder="Ex: 10" min="1" required oninput="calculerMontant()">
            </div>

            <!-- Aperçu du calcul -->
            <div id="apercu" style="display:none; background:var(--lighter); border-radius:var(--radius-sm); padding:16px; margin-bottom:20px; border:2px solid var(--light);">
                <h4 style="font-size:0.9rem; font-weight:700; color:var(--primary); margin-bottom:12px">
                    <i class="fas fa-calculator"></i> Aperçu du calcul
                </h4>
                <div style="display:grid; gap:8px; font-size:0.88rem">
                    <div class="d-flex justify-between">
                        <span>Montant brut :</span>
                        <span class="fw-bold" id="montant_brut">0 Ar</span>
                    </div>
                    <div class="d-flex justify-between">
                        <span>Frais (<?= $frais_pourcent ?>%) :</span>
                        <span class="money-danger" id="montant_frais">0 Ar</span>
                    </div>
                    <hr style="border:1px solid var(--light)">
                    <div class="d-flex justify-between">
                        <span class="fw-bold">Total à débiter :</span>
                        <span class="money fw-bold" style="font-size:1.1rem" id="montant_total">0 Ar</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Confirmer l'achat</button>
        </form>
    </div>

    <!-- Tableau des besoins restants -->
    <div class="card">
        <div class="card-title"><i class="fas fa-clipboard-list"></i> Besoins restants achetables</div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Ville</th>
                        <th>Article</th>
                        <th>Type</th>
                        <th class="text-right">Besoin</th>
                        <th class="text-right">Dispatché</th>
                        <th class="text-right">Acheté</th>
                        <th class="text-right">Restant</th>
                        <th class="text-right">Coût restant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($besoins_restants)): ?>
                        <?php foreach ($besoins_restants as $b): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($b['ville_nom']) ?></td>
                            <td><?= htmlspecialchars($b['article_nom']) ?></td>
                            <td>
                                <?php
                                    $cls = 'badge-nature';
                                    if ($b['type_besoin'] === 'Matériaux') $cls = 'badge-materiaux';
                                ?>
                                <span class="badge <?= $cls ?>"><?= htmlspecialchars($b['type_besoin']) ?></span>
                            </td>
                            <td class="text-right"><?= (int)$b['besoin_quantite'] ?></td>
                            <td class="text-right money-success"><?= (int)$b['quantite_dispatche'] ?></td>
                            <td class="text-right money"><?= (int)$b['quantite_achetee'] ?></td>
                            <td class="text-right money-danger"><?= (int)$b['quantite_restante'] ?></td>
                            <td class="text-right money"><?= formatMontantAcC((int)$b['quantite_restante'] * (float)$b['prix_unitaire']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8">
                            <div class="empty-state"><i class="fas fa-check-circle"></i><p>Tous les besoins sont satisfaits !</p></div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script nonce="<?= Flight::app()->get('csp_nonce') ?>">
    var fraisPourcent = <?= $frais_pourcent ?>;
    
    function calculerMontant() {
        var select = document.getElementById('besoin_id');
        var quantiteInput = document.getElementById('quantite');
        var apercu = document.getElementById('apercu');
        
        var option = select.options[select.selectedIndex];
        var quantite = parseInt(quantiteInput.value) || 0;
        
        if (option.value && quantite > 0) {
            var prix = parseFloat(option.getAttribute('data-prix'));
            var maxRestant = parseInt(option.getAttribute('data-restant'));
            
            // Limiter la quantité au restant
            if (quantite > maxRestant) {
                quantiteInput.value = maxRestant;
                quantite = maxRestant;
            }
            
            var montantBrut = quantite * prix;
            var montantFrais = montantBrut * fraisPourcent / 100;
            var montantTotal = montantBrut + montantFrais;
            
            document.getElementById('montant_brut').textContent = formatNumber(montantBrut) + ' Ar';
            document.getElementById('montant_frais').textContent = formatNumber(Math.round(montantFrais)) + ' Ar';
            document.getElementById('montant_total').textContent = formatNumber(Math.round(montantTotal)) + ' Ar';
            apercu.style.display = 'block';
        } else {
            apercu.style.display = 'none';
        }
    }
</script>
