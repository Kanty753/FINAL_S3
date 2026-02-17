<?php
/** @var array $recap */
function formatMontantR($v) { return number_format((float)$v, 0, ',', ' ') . ' Ar'; }
$base_url = Flight::baseUrl();
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-chart-line"></i> Récapitulation</h2>
        <p>Vue globale des besoins totaux, satisfaits et restants en montant</p>
    </div>
    <button class="btn btn-primary" onclick="actualiserRecap()" id="btnActualiser">
        <i class="fas fa-sync-alt"></i> Actualiser
    </button>
</div>

<!-- Cartes statistiques -->
<div class="stats-grid">
    <div class="stat-card pink">
        <div class="stat-icon"><i class="fas fa-hand-holding-heart"></i></div>
        <div class="stat-value" id="recap-besoins-totaux"><?= formatMontantR($recap['besoins_totaux']) ?></div>
        <div class="stat-label">Besoins totaux</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value" id="recap-besoins-satisfaits"><?= formatMontantR($recap['besoins_satisfaits']) ?></div>
        <div class="stat-label">Besoins satisfaits</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-value" id="recap-besoins-restants"><?= formatMontantR($recap['besoins_restants']) ?></div>
        <div class="stat-label">Besoins restants</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="fas fa-percentage"></i></div>
        <div class="stat-value" id="recap-pourcentage"><?= $recap['pourcentage_satisfait'] ?>%</div>
        <div class="stat-label">Taux de satisfaction</div>
    </div>
</div>

<!-- Détails -->
<div class="grid-2">
    <div class="card">
        <div class="card-title"><i class="fas fa-info-circle"></i> Détail de la satisfaction</div>
        <div style="display:grid; gap:16px;">
            <div class="d-flex justify-between align-center" style="padding:12px 16px; background:var(--lighter); border-radius:var(--radius-sm);">
                <div class="d-flex align-center gap-2">
                    <i class="fas fa-truck" style="color:var(--success)"></i>
                    <span style="font-weight:600">Satisfaits par dispatch</span>
                </div>
                <span class="money-success fw-bold" id="recap-satisfaits-dispatch"><?= formatMontantR($recap['besoins_satisfaits_dispatch']) ?></span>
            </div>
            <div class="d-flex justify-between align-center" style="padding:12px 16px; background:var(--lighter); border-radius:var(--radius-sm);">
                <div class="d-flex align-center gap-2">
                    <i class="fas fa-shopping-cart" style="color:var(--primary)"></i>
                    <span style="font-weight:600">Satisfaits par achat</span>
                </div>
                <span class="money fw-bold" id="recap-satisfaits-achat"><?= formatMontantR($recap['besoins_satisfaits_achat']) ?></span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title"><i class="fas fa-chart-pie"></i> Progression globale</div>
        <div style="text-align:center; padding:20px 0;">
            <div style="font-size:3rem; font-weight:800; color:var(--primary); line-height:1;" id="recap-pourcentage-big">
                <?= $recap['pourcentage_satisfait'] ?>%
            </div>
            <p style="color:var(--gray); margin-top:8px; font-size:0.9rem">des besoins sont satisfaits</p>
            <div class="progress-bar-container" style="margin-top:16px; height:16px;">
                <div class="progress-bar-fill" id="recap-progress-bar" style="width:<?= $recap['pourcentage_satisfait'] ?>%"></div>
            </div>
            <div class="d-flex justify-between" style="margin-top:8px; font-size:0.78rem; color:var(--gray)">
                <span>0%</span>
                <span>100%</span>
            </div>
        </div>
    </div>
</div>

<script nonce="<?= Flight::app()->get('csp_nonce') ?>">
    function actualiserRecap() {
        var btn = document.getElementById('btnActualiser');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';

        fetch(BASE_URL + '/api/recapitulation')
            .then(function(response) { return response.json(); })
            .then(function(data) {
                // Mettre à jour les valeurs
                document.getElementById('recap-besoins-totaux').textContent = formatMontantAr(data.besoins_totaux);
                document.getElementById('recap-besoins-satisfaits').textContent = formatMontantAr(data.besoins_satisfaits);
                document.getElementById('recap-besoins-restants').textContent = formatMontantAr(data.besoins_restants);
                document.getElementById('recap-pourcentage').textContent = data.pourcentage_satisfait + '%';
                document.getElementById('recap-satisfaits-dispatch').textContent = formatMontantAr(data.besoins_satisfaits_dispatch);
                document.getElementById('recap-satisfaits-achat').textContent = formatMontantAr(data.besoins_satisfaits_achat);
                document.getElementById('recap-pourcentage-big').textContent = data.pourcentage_satisfait + '%';
                document.getElementById('recap-progress-bar').style.width = data.pourcentage_satisfait + '%';

                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Actualiser';
            })
            .catch(function(err) {
                alert('Erreur lors de l\'actualisation : ' + err.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Actualiser';
            });
    }

    function formatMontantAr(value) {
        return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' Ar';
    }
</script>
