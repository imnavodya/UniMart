<?php
require_once __DIR__ . '/../includes/header.php';

if (isLoggedIn()) {
    header("Location: /UniMart/index.php");
    exit;
}

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '/UniMart/index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $redirect = $_POST['redirect'];
    
    if (login($email, $password, $conn)) {
        if (isAdmin()) {
            header("Location: /UniMart/admin/index.php");
        } else {
            header("Location: " . $redirect);
        }
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>

<section class="py-5 position-relative z-10" style="margin-top: 100px; min-height: 70vh;">
    <div class="container d-flex justify-content-center align-items-center h-100">
        <div class="glass-panel p-5 w-100" data-aos="zoom-in" style="max-width: 500px;">
            <div class="text-center mb-5">
                <h2 class="outfit fw-bold text-main mb-2">Welcome Back</h2>
                <p class="text-muted">Login to your UniMart account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger border-0 rounded-4">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                
                <div class="mb-4">
                    <label class="form-label text-muted">Email Address</label>
                    <div class="position-relative">
                        <i class="fas fa-envelope position-absolute top-50 translate-middle-y text-muted ms-3"></i>
                        <input type="email" name="email" class="form-control bg-white text-main border-secondary ps-5 py-3 rounded-4 focus-glow" required autofocus>
                    </div>
                </div>
                
                <div class="mb-5">
                    <label class="form-label text-muted">Password</label>
                    <div class="position-relative">
                        <i class="fas fa-lock position-absolute top-50 translate-middle-y text-muted ms-3"></i>
                        <input type="password" name="password" class="form-control bg-white text-main border-secondary ps-5 py-3 rounded-4 focus-glow" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-glow w-100 py-3 fs-5 mb-4">Login</button>
                
                <p class="text-center text-muted m-0">Don't have an account? <a href="/UniMart/auth/register.php" class="text-primary text-decoration-none hover-white transition">Sign up</a></p>
            </form>
        </div>
    </div>
</section>

<style>
.focus-glow:focus {
    background-color: var(--bg-2);
    border-color: var(--primary);
    box-shadow: 0 0 15px var(--primary-glow);
    color: var(--text-main);
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
