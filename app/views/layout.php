<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC — <?= $page_title ?? 'Suivi des Dons' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style nonce="<?= Flight::app()->get('csp_nonce') ?>">
        :root {
            --primary: #6C5CE7;
            --primary-light: #A29BFE;
            --secondary: #00CEC9;
            --accent: #FD79A8;
            --warning: #FDCB6E;
            --danger: #E17055;
            --success: #00B894;
            --info: #0984E3;
            --dark: #2D3436;
            --gray: #636E72;
            --light: #DFE6E9;
            --lighter: #F0F3F5;
            --white: #FFFFFF;
            --bg: #F8F9FD;
            --shadow: 0 4px 24px rgba(108, 92, 231, 0.10);
            --shadow-lg: 0 8px 40px rgba(108, 92, 231, 0.15);
            --radius: 16px;
            --radius-sm: 10px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
            min-height: 100vh;
        }

        /* ====== SIDEBAR ====== */
        .sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: 270px;
            background: linear-gradient(180deg, #6C5CE7 0%, #4834D4 50%, #341F97 100%);
            padding: 24px 0;
            z-index: 100;
            overflow-y: auto;
            box-shadow: 4px 0 30px rgba(108, 92, 231, 0.3);
        }

        .sidebar-brand {
            padding: 0 24px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            margin-bottom: 20px;
        }

        .sidebar-brand h1 {
            color: #fff;
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .sidebar-brand span {
            color: var(--warning);
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: block;
            margin-top: 4px;
        }

        .nav-section {
            padding: 0 16px;
            margin-bottom: 8px;
        }

        .nav-section-title {
            color: rgba(255,255,255,0.4);
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 16px 12px 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 2px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.12);
            color: #fff;
            transform: translateX(4px);
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        /* ====== MAIN ====== */
        .main-content {
            margin-left: 270px;
            padding: 32px 40px;
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .page-header h2 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        .page-header p {
            color: var(--gray);
            font-size: 0.9rem;
            margin-top: 4px;
        }

        /* ====== CARDS ====== */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 28px;
            margin-bottom: 24px;
            transition: var(--transition);
            border: 1px solid rgba(108, 92, 231, 0.06);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--primary);
        }

        /* ====== STAT CARDS ====== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid rgba(108, 92, 231, 0.06);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
        }

        .stat-card.purple::before { background: linear-gradient(90deg, #6C5CE7, #A29BFE); }
        .stat-card.teal::before { background: linear-gradient(90deg, #00CEC9, #81ECEC); }
        .stat-card.pink::before { background: linear-gradient(90deg, #FD79A8, #E84393); }
        .stat-card.orange::before { background: linear-gradient(90deg, #E17055, #FDCB6E); }
        .stat-card.blue::before { background: linear-gradient(90deg, #0984E3, #74B9FF); }

        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
            margin-bottom: 16px;
        }

        .stat-card.purple .stat-icon { background: linear-gradient(135deg, #6C5CE7, #A29BFE); }
        .stat-card.teal .stat-icon { background: linear-gradient(135deg, #00CEC9, #81ECEC); }
        .stat-card.pink .stat-icon { background: linear-gradient(135deg, #FD79A8, #E84393); }
        .stat-card.orange .stat-icon { background: linear-gradient(135deg, #E17055, #FDCB6E); }
        .stat-card.blue .stat-icon { background: linear-gradient(135deg, #0984E3, #74B9FF); }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            color: var(--gray);
            font-weight: 500;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ====== TABLES ====== */
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius-sm);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        table thead th {
            background: linear-gradient(135deg, #6C5CE7, #4834D4);
            color: #fff;
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        table thead th:first-child { border-radius: var(--radius-sm) 0 0 0; }
        table thead th:last-child { border-radius: 0 var(--radius-sm) 0 0; }

        table tbody tr {
            transition: var(--transition);
            border-bottom: 1px solid var(--lighter);
        }

        table tbody tr:hover {
            background: rgba(108, 92, 231, 0.04);
        }

        table tbody td {
            padding: 13px 16px;
            color: var(--dark);
        }

        table tbody tr:last-child td:first-child { border-radius: 0 0 0 var(--radius-sm); }
        table tbody tr:last-child td:last-child { border-radius: 0 0 var(--radius-sm) 0; }

        /* ====== BADGES ====== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-nature { background: #E8F8F5; color: #00B894; }
        .badge-materiaux { background: #FEF5E7; color: #E17055; }
        .badge-argent { background: #EBF5FB; color: #0984E3; }
        .badge-purple { background: #F3F0FF; color: #6C5CE7; }
        .badge-teal { background: #E0FFFE; color: #00897B; }
        .badge-pink { background: #FDE8F0; color: #E84393; }

        /* ====== BUTTONS ====== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6C5CE7, #4834D4);
            color: #fff;
            box-shadow: 0 4px 16px rgba(108, 92, 231, 0.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(108, 92, 231, 0.45);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #00CEC9, #00B894);
            color: #fff;
            box-shadow: 0 4px 16px rgba(0, 206, 201, 0.35);
        }
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(0, 206, 201, 0.45);
        }

        .btn-danger {
            background: linear-gradient(135deg, #E17055, #D63031);
            color: #fff;
            box-shadow: 0 4px 16px rgba(225, 112, 85, 0.3);
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(225, 112, 85, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #FDCB6E, #F39C12);
            color: #fff;
            box-shadow: 0 4px 16px rgba(253, 203, 110, 0.35);
        }

        .btn-sm {
            padding: 7px 14px;
            font-size: 0.78rem;
            border-radius: 8px;
        }

        .btn-icon {
            width: 36px; height: 36px;
            padding: 0;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ====== FORMS ====== */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--light);
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-family: inherit;
            transition: var(--transition);
            background: var(--white);
            color: var(--dark);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.12);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23636E72' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        /* ====== ALERTS ====== */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #E8F8F5;
            color: #00B894;
            border-left: 4px solid #00B894;
        }

        .alert-error {
            background: #FDECEA;
            color: #E17055;
            border-left: 4px solid #E17055;
        }

        /* ====== PROGRESS BAR ====== */
        .progress-bar-container {
            background: var(--lighter);
            border-radius: 50px;
            height: 10px;
            overflow: hidden;
            width: 100%;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 50px;
            background: linear-gradient(90deg, #6C5CE7, #00CEC9);
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ====== GRID LAYOUTS ====== */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }

        /* ====== MONEY FORMAT ====== */
        .money { font-weight: 700; color: var(--primary); }
        .money-success { font-weight: 700; color: var(--success); }
        .money-danger { font-weight: 700; color: var(--danger); }

        /* ====== EMPTY STATE ====== */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--gray);
        }
        .empty-state i { font-size: 3rem; margin-bottom: 16px; color: var(--light); }
        .empty-state p { font-size: 0.95rem; }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 1024px) {
            .sidebar { width: 220px; }
            .main-content { margin-left: 220px; padding: 24px; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 270px;
            }
            .main-content { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* ====== NUMBER FORMATTING HELPER ====== */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-muted { color: var(--gray); }
        .fw-bold { font-weight: 700; }
        .mt-2 { margin-top: 16px; }
        .mb-2 { margin-bottom: 16px; }
        .gap-2 { gap: 12px; }
        .d-flex { display: flex; }
        .align-center { align-items: center; }
        .justify-between { justify-content: space-between; }

        /* ====== FOOTER ====== */
        .footer {
            margin-left: 270px;
            background: linear-gradient(135deg, #2D3436 0%, #1a1a2e 100%);
            color: rgba(255, 255, 255, 0.8);
            padding: 32px 40px;
            border-top: 3px solid var(--primary);
        }
        .footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
        }
        .footer-title {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--primary-light);
            margin-bottom: 8px;
        }
        .footer-participants {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }
        .footer-participant {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(108, 92, 231, 0.12);
            border: 1px solid rgba(108, 92, 231, 0.25);
            padding: 8px 16px;
            border-radius: 50px;
            transition: var(--transition);
        }
        .footer-participant:hover {
            background: rgba(108, 92, 231, 0.25);
            transform: translateY(-2px);
        }
        .footer-participant .etu {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 0.8rem;
            color: var(--secondary);
        }
        .footer-participant .name {
            font-weight: 500;
            font-size: 0.85rem;
            color: var(--white);
        }
        .footer-copy {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.4);
        }
        @media (max-width: 1024px) {
            .footer { margin-left: 220px; padding: 24px; }
        }
        @media (max-width: 768px) {
            .footer { margin-left: 0; }
            .footer-content { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h1>🛡️ BNGRC</h1>
            <span>Gestion des dons</span>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Principal</div>
            <a href="/dashboard" class="nav-link <?= ($active_page ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i> Tableau de bord
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Données géographiques</div>
            <a href="/regions" class="nav-link <?= ($active_page ?? '') === 'regions' ? 'active' : '' ?>">
                <i class="fas fa-map"></i> Régions
            </a>
            <a href="/villes" class="nav-link <?= ($active_page ?? '') === 'villes' ? 'active' : '' ?>">
                <i class="fas fa-city"></i> Villes
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Catalogue</div>
            <a href="/articles" class="nav-link <?= ($active_page ?? '') === 'articles' ? 'active' : '' ?>">
                <i class="fas fa-box-open"></i> Articles
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Opérations</div>
            <a href="/besoins" class="nav-link <?= ($active_page ?? '') === 'besoins' ? 'active' : '' ?>">
                <i class="fas fa-hand-holding-heart"></i> Besoins
            </a>
            <a href="/dons" class="nav-link <?= ($active_page ?? '') === 'dons' ? 'active' : '' ?>">
                <i class="fas fa-gift"></i> Dons
            </a>
            <a href="/dispatches" class="nav-link <?= ($active_page ?? '') === 'dispatches' ? 'active' : '' ?>">
                <i class="fas fa-truck"></i> Dispatches
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_message) ?>
        </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div>
                <div class="footer-title"><i class="fas fa-users"></i> Participants</div>
                <div class="footer-participants">
                    <div class="footer-participant">
                        <span class="etu">ETU004061</span>
                        <span class="name">Karl</span>
                    </div>
                    <div class="footer-participant">
                        <span class="etu">ETU004103</span>
                        <span class="name">Kanty</span>
                    </div>
                    <div class="footer-participant">
                        <span class="etu">ETU004126</span>
                        <span class="name">Jordie</span>
                    </div>
                </div>
            </div>
            <div class="footer-copy">
                © <?= date('Y') ?> BNGRC — Projet S3
            </div>
        </div>
    </footer>

    <script nonce="<?= Flight::app()->get('csp_nonce') ?>">
        function confirmDelete(url) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                fetch(url, { method: 'DELETE' })
                    .then(r => r.json())
                    .then(d => { if(d.success) location.reload(); else alert(d.error || 'Erreur'); })
                    .catch(() => alert('Erreur de connexion'));
            }
        }

        function simulerDispatch() {
            if (confirm('Cela va recalculer tous les dispatches. Continuer ?')) {
                fetch('/api/dispatches/simuler', { method: 'POST' })
                    .then(r => r.json())
                    .then(d => { if(d.success) location.reload(); else alert(d.error || 'Erreur'); })
                    .catch(() => alert('Erreur de connexion'));
            }
        }

        function formatNumber(n) {
            return new Intl.NumberFormat('fr-FR').format(n);
        }
    </script>
</body>
</html>
