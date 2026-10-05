```php
<?php

require 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);

    // Validate
    if ($name === '') {
        $message = "Please enter customer name.";
    } elseif ($amount <= 0) {
        $message = "Please enter a valid amount.";
    } else {

        $conn = db();

        /*
         * Generate our own bill number.
         * No Razorpay is used.
         */
        $bill_no = 'BILL-' . date('YmdHis') . '-' . rand(100, 999);

        /*
         * Store billing information.
         *
         * The old Razorpay columns are left empty.
         */
        $stmt = $conn->prepare("
            INSERT INTO payments
            (
                customer_name,
                customer_email,
                customer_phone,
                amount,
                currency,
                razorpay_order_id,
                razorpay_payment_id,
                razorpay_signature,
                status,
                paid_at
            )
            VALUES
            (?, ?, ?, ?, 'INR', ?, NULL, NULL, 'paid', NOW())
        ");

        if (!$stmt) {
            die("Database error: " . $conn->error);
        }

        $stmt->bind_param(
            "sssds",
            $name,
            $email,
            $phone,
            $amount,
            $bill_no
        );

        if ($stmt->execute()) {

            $id = $conn->insert_id;

            $stmt->close();
            $conn->close();

            /*
             * Open the invoice after saving.
             */
            header("Location: invoice.php?id=" . $id);
            exit;

        } else {

            $message = "Failed to save bill: " . $stmt->error;

            $stmt->close();
            $conn->close();
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Billing System</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f2f4f7;
}

.container {
    width: 450px;
    max-width: 95%;
    margin: 50px auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.12);
}

h1 {
    text-align: center;
    margin-bottom: 25px;
}

label {
    display: block;
    margin-top: 15px;
    margin-bottom: 6px;
    font-weight: bold;
}

input {
    width: 100%;
    padding: 11px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
}

button {
    width: 100%;
    margin-top: 25px;
    padding: 12px;
    border: none;
    border-radius: 6px;
    background: #111;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background: #333;
}

.records {
    display: block;
    text-align: center;
    margin-top: 20px;
    text-decoration: none;
    color: #0066cc;
}

.error {
    background: #ffe5e5;
    color: #b00000;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
}

</style>

</head>

<body>

<div class="container">

<h1>Customer Billing</h1>

<?php if ($message !== ''): ?>

<div class="error">
    <?php echo htmlspecialchars($message); ?>
</div>

<?php endif; ?>


<form method="POST">

<label>
    Customer Name *
</label>

<input
    type="text"
    name="customer_name"
    placeholder="Enter customer name"
    required
>


<label>
    Email
</label>

<input
    type="email"
    name="customer_email"
    placeholder="customer@example.com"
>


<label>
    Phone
</label>

<input
    type="text"
    name="customer_phone"
    placeholder="Enter phone number"
>


<label>
    Amount (₹) *
</label>

<input
    type="number"
    name="amount"
    min="1"
    step="0.01"
    placeholder="Enter amount"
    required
>


<button type="submit">
    Generate Invoice
</button>

</form>


<a
    class="records"
    href="payments.php"
>
    View Billing Records
</a>

</div>

</body>

</html>
```
