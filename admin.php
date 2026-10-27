<?php
require __DIR__ . '/config.php'; require_login();
$s=$pdo->prepare('SELECT email,is_admin FROM users WHERE id=?');$s->execute([$_SESSION['user_id']]);$me=$s->fetch();
if(!$me || !(int)$me['is_admin'] || strtolower($me['email'])!==strtolower(ADMIN_EMAIL)){http_response_code(403);exit('Admin access denied. Set is_admin=1 for your account and update ADMIN_EMAIL in config.php.');}
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf(); $id=(int)($_POST['id']??0);$action=$_POST['action']??'';
 if(in_array($action,['approved','rejected'],true)){
  $s=$pdo->prepare("UPDATE referrals SET status=?,reviewed_at=NOW() WHERE id=? AND status='pending'");$s->execute([$action,$id]);$msg='Review saved.';
 }
}
require __DIR__ . '/header.php';
?>
<main class="wrap"><h1>Admin — Referral Review</h1><p class="muted">Approval se pehle signup ko manually verify karein. Fake/self referrals approve na karein.</p><?php if($msg): ?><div class="pill"><?= e($msg) ?></div><?php endif; ?><div class="card"><table class="table"><tr><th>ID</th><th>Referrer</th><th>New User</th><th>Reward</th><th>Created</th><th>Action</th></tr><?php $q=$pdo->query("SELECT r.id,r.referrer_reward,r.new_user_reward,r.created_at,u.name ref_name,n.name new_name FROM referrals r JOIN users u ON u.id=r.referrer_id JOIN users n ON n.id=r.referred_user_id WHERE r.status='pending' ORDER BY r.id ASC");foreach($q as $r): ?><tr><td><?= (int)$r['id'] ?></td><td><?= e($r['ref_name']) ?></td><td><?= e($r['new_name']) ?></td><td>₹<?= number_format((float)$r['referrer_reward'],2) ?> / ₹<?= number_format((float)$r['new_user_reward'],2) ?></td><td><?= e($r['created_at']) ?></td><td><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button name="action" value="approved">Approve</button><button name="action" value="rejected" style="background:#b83249">Reject</button></form></td></tr><?php endforeach; ?></table></div></main>
<?php require __DIR__ . '/footer.php'; ?>
