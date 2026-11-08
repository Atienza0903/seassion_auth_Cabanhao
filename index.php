<?php
declare(strict_types=1);
require_once __DIR__ . '/ab.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_is_valid()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');

        $statement = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            redirect('dashboard.php');
        }

        $error = 'Email or password is incorrect.';
    }
}

$notice = take_flash('success');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | Session Auth</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-intro">
            <a class="brand" href="index.php"><span class="brand-mark">S</span> SessionAuth</a>
            <div class="intro-copy">
                <p class="eyebrow">A simpler, safer sign in</p>
                <h1>Your space,<br>ready when you are.</h1>
                <p>Sign in to pick up where you left off and see your personal dashboard.</p>
            </div>
            <div class="intro-foot"><span class="status-dot"></span> Secure session enabled</div>
        </section>

        <section class="auth-panel">
            <div class="form-wrap">
                <p class="eyebrow">Welcome back</p>
                <h2>Sign in</h2>
                <p class="form-lead">Enter your account details below.</p>

                <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
                <?php if ($notice): ?><div class="alert alert-success" role="status"><?= e($notice) ?></div><?php endif; ?>

                <form method="post" action="index.php">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" placeholder="you@example.com" value="<?= e((string) ($_POST['email'] ?? '')) ?>" required>

                    <div class="label-row"><label for="password">Password</label></div>
                    <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required>

                    <button class="button button-primary" type="submit">Sign in <span aria-hidden="true">&#8594;</span></button>
                </form>

                <p class="form-switch">New here? <a href="register.php">Create an account</a></p>
            </div>
            <footer class="panel-foot">Protected with encrypted password storage</footer>
        </section>
    </main>
</body>
</html>