<?php
require __DIR__ . '/config.php'; require_login();
$s=$pdo->prepare('SELECT id,name,email,referral_code FROM users WHERE id=?'); $s->execute([$_SESSION['user_id']]); $u=$s->fetch();
if(!$u){session_destroy();header('Location: login.php');exit;}
$s=$pdo->prepare("SELECT COUNT(*) c FROM referrals WHERE referrer_id=? AND status='approved'");$s->execute([$u['id']]);$approved=(int)$s->fetch()['c'];
$s=$pdo->prepare("SELECT COALESCE(SUM(referrer_reward),0) total FROM referrals WHERE referrer_id=? AND status='approved'");$s->execute([$u['id']]);$earned=(float)$s->fetch()['total'];
$s=$pdo->prepare("SELECT COUNT(*) c FROM referrals WHERE referrer_id=? AND status='pending'");$s->execute([$u['id']]);$pending=(int)$s->fetch()['c'];
$refLink=site_url('register.php?ref='.$u['referral_code']);
require __DIR__ . '/header.php';
?>
<main class="wrap"><h1>Welcome, <?= e($u['name']) ?> 👋</h1><p class="muted">Aapka referral dashboard</p><div class="statgrid"><div class="card"><h3>Approved Earnings</h3><div class="num">₹<?= number_format($earned,2) ?></div></div><div class="card"><h3>Approved Referrals</h3><div class="num"><?= $approved ?></div></div><div class="card"><h3>Pending Referrals</h3><div class="num"><?= $pending ?></div></div></div><div class="card"><h2>Your Referral Link</h2><div class="linkrow"><input id="refLink" readonly value="<?= e($refLink) ?>"><button type="button" onclick="navigator.clipboard.writeText(document.getElementById('refLink').value).then(()=>alert('Link copied'))">Copy Link</button></div><p class="muted">Link share karein. Signup hone par reward pending rahega; approval guaranteed nahi hai.</p></div><div class="card" style="margin-top:18px"><h2>Recent Referrals</h2><table class="table"><tr><th>Date</th><th>Reward</th><th>Status</th></tr><?php $s=$pdo->prepare('SELECT created_at,referrer_reward,status FROM referrals WHERE referrer_id=? ORDER BY id DESC LIMIT 20');$s->execute([$u['id']]);foreach($s as $r): ?><tr><td><?= e($r['created_at']) ?></td><td>₹<?= number_format((float)$r['referrer_reward'],2) ?></td><td><?= e(ucfirst($r['status'])) ?></td></tr><?php endforeach; ?></table></div><div class="notice" style="margin-top:18px">Starter project mein actual withdrawal/payment nahi hai. Dashboard ke amounts demo ledger hain, cash balance nahi.</div></main>
<?php require __DIR__ . '/footer.php'; ?>
