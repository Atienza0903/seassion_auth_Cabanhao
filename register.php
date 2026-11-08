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
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($name === '' || strlen($name) > 100) {
            $error = 'Please enter a name with no more than 100 characters.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $error = 'Your password must be at least 8 characters.';
        } elseif ($password !== $confirmPassword) {
            $error = 'The passwords do not match.';
        } else {
            $statement = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $statement->execute(['email' => $email]);

            if ($statement->fetch()) {
                $error = 'An account with that email already exists.';
            } else {
                $statement = $pdo->prepare(
                    'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)'
                );
                $statement->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                ]);

                $_SESSION['flash']['success'] = 'Your account is ready. Sign in to continue.';
                redirect('index.php');
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account | Session Auth</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-intro">
            <a class="brand" href="index.php"><span class="brand-mark">S</span> SessionAuth</a>
            <div class="intro-copy">
                <p class="eyebrow">Make it yours</p>
                <h1>A good place<br>to get started.</h1>
                <p>Create an account and your personal dashboard will be ready in a moment.</p>
            </div>
            <div class="intro-foot"><span class="status-dot"></span> Your details stay protected</div>
        </section>

        <section class="auth-panel">
            <div class="form-wrap">
                <p class="eyebrow">Join SessionAuth</p>
                <h2>Create account</h2>
                <p class="form-lead">A few details and you are all set.</p>

                <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>

                <form method="post" action="register.php">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <label for="name">Your name</label>
                    <input id="name" name="name" type="text" autocomplete="name" placeholder="Maria Santos" value="<?= e((string) ($_POST['name'] ?? '')) ?>" maxlength="100" required>

                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" placeholder="you@example.com" value="<?= e((string) ($_POST['email'] ?? '')) ?>" required>

                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" placeholder="At least 8 characters" minlength="8" required>

                    <label for="confirm_password">Confirm password</label>
                    <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" placeholder="Enter it one more time" minlength="8" required>

                    <button class="button button-primary" type="submit">Create account <span aria-hidden="true">&#8594;</span></button>
                </form>

                <p class="form-switch">Already registered? <a href="index.php">Sign in</a></p>
            </div>
            <footer class="panel-foot">Passwords are never stored as plain text</footer>
        </section>
    </main>
</body>
</html>