BY NAME BILLING - PHP + MYSQL + RAZORPAY
============================================

Requirements
------------
1. XAMPP (Apache + MySQL)
2. PHP with cURL enabled
3. A Razorpay account with TEST API keys

INSTALLATION
------------
1. Extract this folder into:
   C:\xampp\htdocs\razorpay_billing_app

2. Start Apache and MySQL in XAMPP.

3. Open phpMyAdmin:
   http://localhost/phpmyadmin

4. Import db.sql.

5. Open config.php and replace:
   rzp_test_YOUR_KEY_ID
   YOUR_KEY_SECRET
   with your Razorpay TEST API credentials.

6. Open:
   http://localhost/razorpay_billing_app/

HOW IT WORKS
------------
- Enter customer name and amount.
- PHP creates a Razorpay Order on the server.
- Razorpay Checkout opens.
- After payment, PHP verifies the Razorpay signature.
- The payment is marked "paid" in MySQL.
- View saved records at payments.php.

IMPORTANT SECURITY
------------------
- Never put RAZORPAY_KEY_SECRET in JavaScript or HTML.
- Use TEST keys while developing.
- Use HTTPS before production.
- For production, add Razorpay webhooks and an admin login.
- Do not store card numbers, CVV, UPI PIN, or other payment credentials.
- The app stores Razorpay IDs and payment status, not card details.

If PHP reports that curl_init() is undefined:
1. Open C:\xampp\php\php.ini
2. Find: ;extension=curl
3. Remove the semicolon: extension=curl
4. Restart Apache.

Razorpay documentation:
https://razorpay.com/integrations/
