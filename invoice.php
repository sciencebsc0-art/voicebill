```php
<?php
require 'config.php';

/*
    invoice.php

    Usage:
    invoice.php?id=1

    Example:
    http://localhost/razorpay_billing_app/invoice.php?id=1
*/

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid invoice ID.");
}

$conn = db();

$stmt = $conn->prepare("
    SELECT
        id,
        customer_name,
        customer_email,
        customer_phone,
        amount,
        currency,
        razorpay_order_id,
        razorpay_payment_id,
        status,
        created_at,
        paid_at
    FROM payments
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$payment = $result->fetch_assoc();

$stmt->close();
$conn->close();

if (!$payment) {
    die("Invoice not found.");
}

/* Only show invoice for successful payments */
if ($payment['status'] !== 'paid') {
    die("Invoice is available only after successful payment.");
}

/* Invoice number */
$invoice_no = "INV-" . date("Ymd", strtotime($payment['created_at'])) . "-" . str_pad($payment['id'], 4, "0", STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
    Invoice <?php echo htmlspecialchars($invoice_no); ?>
</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f2f2f2;
    margin: 0;
    padding: 30px;
}

.invoice {
    width: 800px;
    max-width: 100%;
    margin: auto;
    background: white;
    padding: 40px;
    box-sizing: border-box;
    box-shadow: 0 0 10px rgba(0,0,0,0.15);
}

.header {
    display: flex;
    justify-content: space-between;
    border-bottom: 2px solid #222;
    padding-bottom: 20px;
}

.company h1 {
    margin: 0;
}

.company p {
    margin: 5px 0;
}

.invoice-title {
    text-align: right;
}

.invoice-title h2 {
    margin: 0;
}

.info {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
}

.customer,
.payment-info {
    width: 48%;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
}

table th,
table td {
    border: 1px solid #ccc;
    padding: 12px;
}

table th {
    background: #eee;
    text-align: left;
}

.amount {
    text-align: right;
    font-weight: bold;
    font-size: 18px;
}

.success {
    color: green;
    font-weight: bold;
}

.print-button {
    text-align: center;
    margin-top: 25px;
}

button {
    background: #111;
    color: white;
    border: none;
    padding: 12px 25px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #333;
}

.footer {
    text-align: center;
    margin-top: 40px;
    border-top: 1px solid #ccc;
    padding-top: 15px;
    color: #555;
}

@media print {

    body {
        background: white;
        padding: 0;
    }

    .invoice {
        width: 100%;
        box-shadow: none;
    }

    .print-button {
        display: none;
    }

}

</style>

</head>

<body>

<div class="invoice">

    <!-- COMPANY DETAILS -->

    <div class="header">

        <div class="company">

            <h1>YOUR COMPANY NAME</h1>

            <p>Your Address</p>

            <p>Phone: 9876543210</p>

            <p>Email: your@email.com</p>

        </div>

        <div class="invoice-title">

            <h2>INVOICE</h2>

            <p>
                Invoice No:
                <strong>
                    <?php echo htmlspecialchars($invoice_no); ?>
                </strong>
            </p>

            <p>
                Date:
                <?php
                echo date(
                    "d-m-Y",
                    strtotime($payment['created_at'])
                );
                ?>
            </p>

        </div>

    </div>


    <!-- CUSTOMER INFORMATION -->

    <div class="info">

        <div class="customer">

            <h3>Bill To</h3>

            <p>
                <strong>
                    <?php
                    echo htmlspecialchars(
                        $payment['customer_name']
                    );
                    ?>
                </strong>
            </p>

            <?php if (!empty($payment['customer_email'])): ?>

                <p>
                    Email:
                    <?php
                    echo htmlspecialchars(
                        $payment['customer_email']
                    );
                    ?>
                </p>

            <?php endif; ?>


            <?php if (!empty($payment['customer_phone'])): ?>

                <p>
                    Phone:
                    <?php
                    echo htmlspecialchars(
                        $payment['customer_phone']
                    );
                    ?>
                </p>

            <?php endif; ?>

        </div>


        <!-- PAYMENT INFORMATION -->

        <div class="payment-info">

            <h3>Payment Details</h3>

            <p>
                Payment ID:
                <?php
                echo htmlspecialchars(
                    $payment['razorpay_payment_id']
                );
                ?>
            </p>

            <p>
                Order ID:
                <?php
                echo htmlspecialchars(
                    $payment['razorpay_order_id']
                );
                ?>
            </p>

            <p>
                Status:
                <span class="success">
                    PAID
                </span>
            </p>

        </div>

    </div>


    <!-- BILL TABLE -->

    <table>

        <tr>

            <th>Description</th>

            <th>Amount</th>

        </tr>

        <tr>

            <td>
                Payment / Billing Amount
            </td>

            <td class="amount">

                ₹
                <?php
                echo number_format(
                    (float)$payment['amount'],
                    2
                );
                ?>

            </td>

        </tr>

        <tr>

            <td>
                <strong>Total Paid</strong>
            </td>

            <td class="amount">

                ₹
                <?php
                echo number_format(
                    (float)$payment['amount'],
                    2
                );
                ?>

            </td>

        </tr>

    </table>


    <!-- FOOTER -->

    <div class="footer">

        <p>
            <strong>Payment Successful</strong>
        </p>

        <p>
            Thank you for your payment.
        </p>

        <p>
            This is a computer-generated invoice.
        </p>

    </div>


    <!-- PRINT BUTTON -->

    <div class="print-button">

        <button onclick="window.print()">
            🖨 Print Invoice
        </button>

    </div>

</div>

</body>

</html>
```
