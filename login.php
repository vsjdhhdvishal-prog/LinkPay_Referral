<?php
require __DIR__ . '/config.php';
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf();
 $email=strtolower(trim($_POST['email']??'')); $pass=$_POST['password']??'';
 $s=$pdo->prepare('SELECT id,name,email,password_hash FROM users WHERE email=? LIMIT 1'); $s->execute([$email]); $u=$s->fetch();
 if($u && password_verify($pass,$u['password_hash'])) {
   session_regenerate_id(true); $_SESSION['user_id']=(int)$u['id']; $_SESSION['user_name']=$u['name'];
   header('Location: dashboard.php'); exit;
 } else $error='Email ya password sahi nahi hai.';
}
require __DIR__ . '/header.php';
?>
<div class="formbox"><h1>Login</h1><?php if($error): ?><div class="notice"><?= e($error) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Email</label><input type="email" name="email" required><label>Password</label><input type="password" name="password" required><button>Login</button></form><p class="muted">Account nahi hai? <a href="register.php">Register</a></p></div>
<?php require __DIR__ . '/footer.php'; ?>
