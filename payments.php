<?php
require 'config.php';
$conn = db();
$result = $conn->query(
    "SELECT id, customer_name, customer_email, customer_phone, amount, currency,
            razorpay_order_id, razorpay_payment_id, status, created_at, paid_at
     FROM payments ORDER BY id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Payment Records</title>
<style>
body{font-family:Arial;margin:30px;background:#f4f6f8}
.wrap{background:#fff;padding:20px;border-radius:12px;overflow:auto}
table{border-collapse:collapse;width:100%;min-width:900px}
th,td{border:1px solid #ddd;padding:9px;text-align:left}
th{background:#eee}
.paid{font-weight:bold}
</style>
</head>
<body>
<div class="wrap">
<h2>Payment Records</h2>
<p><a href="index.php">← New Bill</a></p>
<table>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Amount</th>
<th>Order ID</th><th>Payment ID</th><th>Status</th><th>Created</th><th>Paid At</th>
<th>Invoice</th>
</tr>
<?php while($r = $result->fetch_assoc()): ?>
<tr>
<td><?=htmlspecialchars($r['id'])?></td>
<td><?=htmlspecialchars($r['customer_name'])?></td>
<td><?=htmlspecialchars($r['customer_email'] ?? '')?></td>
<td><?=htmlspecialchars($r['customer_phone'] ?? '')?></td>
<td>₹<?=htmlspecialchars(number_format((float)$r['amount'],2))?></td>
<td><?=htmlspecialchars($r['razorpay_order_id'])?></td>
<td><?=htmlspecialchars($r['razorpay_payment_id'] ?? '')?></td>
<td class="paid"><?=htmlspecialchars($r['status'])?></td>
<td><?=htmlspecialchars($r['created_at'])?></td>
<td><?=htmlspecialchars($r['paid_at'] ?? '')?></td>
<td>
    <?php if ($r['status'] === 'paid'): ?>

        <a
            href="invoice.php?id=<?php echo $r['id']; ?>"
            target="_blank"
        >
            🧾 Print Invoice
        </a>

    <?php else: ?>

        -

    <?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html>
<?php $conn->close(); ?>