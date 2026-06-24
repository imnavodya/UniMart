<?php
require_once __DIR__ . '/../includes/header.php';

if (isLoggedIn()) {
    header("Location: /UniMart/index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    if (!isStudentEmail($email)) {
        $error = 'Registration is restricted to verified university emails only (e.g., @nsbm.ac.lk).';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
            if ($stmt->execute([$name, $email, $hashed])) {
                login($email, $password, $conn);
                header("Location: /UniMart/index.php");
                exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<section class="py-5 position-relative z-10" style="margin-top: 100px; min-height: 70vh;">
    <div class="container d-flex justify-content-center align-items-center h-100">
        <div class="glass-panel p-5 w-100" data-aos="zoom-in" style="max-width: 500px;">
            <div class="text-center mb-5">
                <h2 class="outfit fw-bold text-main mb-2">Create Account</h2>
                <p class="text-muted">Join the premium NSBM marketplace</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger border-0 rounded-4">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="mb-4">
                    <label class="form-label text-muted">Full Name</label>
                    <div class="position-relative">
                        <i class="fas fa-user position-absolute top-50 translate-middle-y text-muted ms-3"></i>
                        <input type="text" name="name" class="form-control bg-white text-main border-secondary ps-5 py-3 rounded-4 focus-glow" required autofocus>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-muted">Email Address</label>
                    <div class="position-relative">
                        <i class="fas fa-envelope position-absolute top-50 translate-middle-y text-muted ms-3"></i>
                        <input type="email" name="email" class="form-control bg-white text-main border-secondary ps-5 py-3 rounded-4 focus-glow" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-muted">Password</label>
                    <div class="position-relative">
                        <i class="fas fa-lock position-absolute top-50 translate-middle-y text-muted ms-3"></i>
                        <input type="password" name="password" class="form-control bg-white text-main border-secondary ps-5 py-3 rounded-4 focus-glow" required>
                    </div>
                </div>
                
                <div class="mb-5">
                    <label class="form-label text-muted">Confirm Password</label>
                    <div class="position-relative">
                        <i class="fas fa-lock position-absolute top-50 translate-middle-y text-muted ms-3"></i>
                        <input type="password" name="confirm_password" class="form-control bg-white text-main border-secondary ps-5 py-3 rounded-4 focus-glow" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-glow w-100 py-3 fs-5 mb-4">Register</button>
                
                <p class="text-center text-muted m-0">Already have an account? <a href="/UniMart/auth/login.php" class="text-primary text-decoration-none hover-white transition">Log in</a></p>
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
