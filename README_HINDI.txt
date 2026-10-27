LINKPAY REFERRAL STARTER (PHP + MySQL)
======================================

IMPORTANT:
- Ye starter project hai; real money payment system nahi hai.
- Referral rewards PENDING rakhe jaate hain. Admin ko verify karke manually approve karna hoga.
- Sirf link open/click par automatic cash reward dena fraud-prone hai; fake clicks/self-referrals ko rokne ke liye validation zaroori hai.
- Koi guaranteed earning ya real withdrawal is code se nahi hota.

INSTALL (cPanel):
1. public_html mein is ZIP ko upload karke Extract karein.
2. cPanel > MySQL Databases mein database aur database user banayein; user ko ALL PRIVILEGES dein.
3. phpMyAdmin > apna database > Import > schema.sql import karein.
4. config.php mein DB_HOST, DB_NAME, DB_USER, DB_PASS set karein.
5. config.php mein ADMIN_EMAIL ko apne email se badlein.
6. Website kholen. Register karke test karein.
7. Admin page /admin.php par admin email se login karne par pending rewards dikhte hain. Admin role ko database mein manually set karein:
   UPDATE users SET is_admin=1 WHERE email='your-email@example.com';

Security before live launch:
- HTTPS enable karein.
- Strong database password.
- Email verification, rate limits, anti-bot/click validation, privacy policy, terms, tax/legal review aur proper payout provider add karein.
- Production site par click ko cash ke barabar na maanein. Rewards qualifying signup/approved action ke baad hi den.
