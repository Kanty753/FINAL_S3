<?php
/**
 * Layout principal de l'application BNGRC
 * Variables attendues: $content (string), $title (string optionnel)
 */
$pageTitle = isset($title) ? $title . ' - BNGRC' : 'BNGRC - Suivi des collectes et distributions';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            color: #333;
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #1a5276, #2e86c1);
            color: #fff;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 100;
            height: 60px;
        }
        .navbar .brand {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-decoration: none;
            color: #fff;
        }
        .navbar .brand span { color: #f39c12; }
        .navbar nav { display: flex; gap: 0; }
        .navbar nav a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .navbar nav a:hover, .navbar nav a.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        h1, h2, h3 { color: #1a5276; }
        h1 { font-size: 1.8rem; margin-bottom: 1.5rem; }
        h2 { font-size: 1.4rem; margin-bottom: 1rem; }

        /* Cards */
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #eee;
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            border-left: 4px solid #2e86c1;
            text-align: center;
        }
        .stat-card.warning { border-left-color: #f39c12; }
        .stat-card.success { border-left-color: #27ae60; }
        .stat-card.danger { border-left-color: #e74c3c; }
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1a5276;
        }
        .stat-card .stat-label {
            font-size: 0.85rem;
            color: #777;
            margin-top: 0.3rem;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        thead th {
            background: #1a5276;
            color: #fff;
            padding: 0.8rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        thead th:first-child { border-radius: 8px 0 0 0; }
        thead th:last-child { border-radius: 0 8px 0 0; }
        tbody td {
            padding: 0.7rem 1rem;
            border-bottom: 1px solid #eee;
        }
        tbody tr:hover { background: #f8f9fa; }
        tbody tr:last-child td { border-bottom: none; }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.5rem 1.2rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #2e86c1;
            color: #fff;
        }
        .btn-primary:hover { background: #1a5276; }
        .btn-success {
            background: #27ae60;
            color: #fff;
        }
        .btn-success:hover { background: #1e8449; }
        .btn-danger {
            background: #e74c3c;
            color: #fff;
        }
        .btn-danger:hover { background: #c0392b; }
        .btn-warning {
            background: #f39c12;
            color: #fff;
        }
        .btn-warning:hover { background: #d68910; }
        .btn-sm { padding: 0.3rem 0.8rem; font-size: 0.8rem; }

        /* Forms */
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 0.6rem 1rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.2s;
            background: #fafafa;
        }
        .form-control:focus {
            outline: none;
            border-color: #2e86c1;
            background: #fff;
        }
        select.form-control { cursor: pointer; }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-nature { background: #d5f5e3; color: #1e8449; }
        .badge-materiaux { background: #fdebd0; color: #d68910; }
        .badge-argent { background: #d6eaf8; color: #2e86c1; }

        /* Utilities */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mb-2 { margin-bottom: 1rem; }
        .flex { display: flex; }
        .gap-1 { gap: 0.5rem; }

        /* Progress bar */
        .progress {
            height: 8px;
            background: #eee;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: #27ae60;
            border-radius: 4px;
            transition: width 0.3s;
        }
        .progress-bar.partial { background: #f39c12; }
        .progress-bar.empty { background: #e74c3c; }

        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem;
            color: #999;
            font-size: 0.85rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar { flex-direction: column; height: auto; padding: 1rem; }
            .navbar nav { flex-wrap: wrap; justify-content: center; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            table { font-size: 0.8rem; }
            thead th, tbody td { padding: 0.5rem; }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="/dashboard" class="brand"><span>BNGRC</span> Sinistrés</a>
        <nav>
            <a href="/dashboard">Tableau de bord</a>
            <a href="/regions">Régions</a>
            <a href="/villes">Villes</a>
            <a href="/articles">Articles</a>
            <a href="/besoins">Besoins</a>
            <a href="/dons">Dons</a>
            <a href="/dispatches">Dispatches</a>
        </nav>
    </header>

    <main class="container">
        <?= $content ?>
    </main>

    <footer class="footer">
        &copy; 2026 BNGRC - Bureau National de Gestion des Risques et des Catastrophes
    </footer>
</body>
</html>
