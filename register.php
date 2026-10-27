<?php
require __DIR__ . '/config.php';
$error = '';
$refCode = preg_replace('/[^A-Za-z0-9]/', '', $_GET['ref'] ?? $_SESSION['ref_code'] ?? '');
if ($refCode !== '') $_SESSION['ref_code'] = $refCode;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf();
  $name = trim($_POST['name'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $pass = $_POST['password'] ?? '';
  $ref = preg_replace('/[^A-Za-z0-9]/', '', $_SESSION['ref_code'] ?? '');
  if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 8) {
    $error = 'Naam, valid email aur kam se kam 8-character password enter karein.';
  } else {
    try {
      $pdo->beginTransaction();
      $referrerId = null;
      if ($ref !== '') {
        $s = $pdo->prepare('SELECT id FROM users WHERE referral_code=? LIMIT 1');
        $s->execute([$ref]); $r = $s->fetch();
        if ($r) $referrerId = (int)$r['id'];
      }
      $code = bin2hex(random_bytes(5));
      $s = $pdo->prepare('INSERT INTO users(name,email,password_hash,referral_code,referred_by) VALUES(?,?,?,?,?)');
      $s->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),$code,$referrerId]);
      $newId = (int)$pdo->lastInsertId();
      if ($referrerId && $referrerId !== $newId) {
        $s = $pdo->prepare('INSERT INTO referrals(referrer_id,referred_user_id) VALUES(?,?)');
        // corrected below: explicit column/value count
        $s->execute([$referrerId,$newId]);
      }
      $pdo->commit();
      unset($_SESSION['ref_code']);
      $_SESSION['user_id'] = $newId;
      $_SESSION['user_name'] = $name;
      header('Location: dashboard.php'); exit;
    } catch (PDOException $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      $error = 'Ye email pehle se registered ho sakta hai. Dobara check karein.';
    }
  }
}
require __DIR__ . '/header.php';
?>
<div class="formbox"><h1>Create Account</h1><p class="muted">Referral ID: <?= e($refCode ?: 'Direct signup') ?></p><?php if($error): ?><div class="notice"><?= e($error) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Full name</label><input name="name" required maxlength="100"><label>Email</label><input type="email" name="email" required><label>Password (minimum 8 characters)</label><input type="password" name="password" minlength="8" required><button type="submit">Register</button></form><p class="muted">Already account hai? <a href="login.php">Login</a></p></div>
<?php require __DIR__ . '/footer.php'; ?>
