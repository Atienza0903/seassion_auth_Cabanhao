<?php
declare(strict_types=1);
require_once __DIR__ . '/ab.php';

if (!isset($_SESSION['user_id'])) {
    redirect('index.php');
}

$userName = (string) ($_SESSION['user_name'] ?? 'there');
$userEmail = (string) ($_SESSION['user_email'] ?? '');
$userId = (int) $_SESSION['user_id'];
$initial = strtoupper(substr($userName, 0, 1));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | SessionAuthSystem</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
    <header class="topbar">
        <div class="topbar-brand-group">
            <a class="brand" href="dashboard.php"><span class="brand-mark">S</span> SessionAuthSystem</a>
            <a class="dashboard-nav-link" href="#overview" aria-current="page">Dashboard</a>
        </div>
        <nav class="topbar-actions" aria-label="Account navigation">
            <span class="avatar" aria-label="<?= e($userName) ?>"><?= e($initial) ?></span>
            <span class="topbar-name"><?= e($userName) ?></span>
            <form method="post" action="logout.php">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <button class="button button-quiet" type="submit">Logout</button>
            </form>
        </nav>
    </header>

    <main class="dashboard-main">
        <section class="welcome-block">
            <div>
                <p class="eyebrow">Your dashboard</p>
                <h1>Welcome, <?= e($userName) ?>!</h1>
                <p>This page is protected and only visible to logged-in users.</p>
            </div>
            <a class="explore-button" href="#overview">Explore Dashboard <span aria-hidden="true">&#8594;</span></a>
        </section>

        <section class="dashboard-grid" id="overview" aria-label="Account overview">
            <article class="info-panel">
                <div class="card-heading"><span class="panel-icon icon-green" aria-hidden="true">&#10003;</span><h2>Account Status</h2></div>
                <p>Your session is active and protected. You can securely use your account.</p>
                <span class="panel-meta">STATUS <strong>Active</strong></span>
            </article>
            <article class="info-panel">
                <div class="card-heading"><span class="panel-icon icon-blue" aria-hidden="true">&#9678;</span><h2>User Details</h2></div>
                <p><strong>User ID:</strong> <?= $userId ?><br><strong>Username:</strong> <?= e($userName) ?><br><strong>Email:</strong> <?= e($userEmail) ?></p>
                <span class="panel-meta">ACCOUNT <strong><?= e($userName) ?></strong></span>
            </article>
            <article class="info-panel">
                <div class="card-heading"><span class="panel-icon icon-orange" aria-hidden="true">&#8635;</span><h2>Active Session ID</h2></div>
                <p>Your session is active. The session ID is hidden for security.</p>
                <span class="panel-meta">SESSION <strong>Active</strong></span>
            </article>
        </section>

        <div class="dashboard-note"><span class="status-dot"></span> Dashboard access is limited to authenticated users.</div>
    </main>
</body>
</html>