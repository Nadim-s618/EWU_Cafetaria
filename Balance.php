<?php
session_start();
include "db.php";

// Admin only
if (!isset($_SESSION['User_ID']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

/* ---------------- FETCH TOTAL BALANCE ---------------- */
$balance_sql = "SELECT balance FROM admin_balance WHERE id = 1";
$balance_result = mysqli_query($conn, $balance_sql);
$balance_row = mysqli_fetch_assoc($balance_result);
$totalBalance = $balance_row['balance'] ?? 0;

/* ---------------- FETCH ORDER DETAILS ---------------- */
$sql = "
    SELECT 
        u.Name AS customer_name,
        mi.Item_Name,
        mi.Price,
        od.Quantity,
        (mi.Price * od.Quantity) AS item_total,
        o.Order_Date
    FROM Orders o
    JOIN Users u ON o.User_ID = u.User_ID
    JOIN Order_Details od ON o.Order_ID = od.Order_ID
    JOIN Menu_Item mi ON od.Item_ID = mi.Item_ID
    WHERE o.Status = 'Completed'
    ORDER BY o.Order_Date DESC
";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Balance</title>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    margin:0;
    background: linear-gradient(135deg, #42a54cdc, #8c6eef);
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}
.container {
    background: rgba(255,255,255,0.1);
    padding:40px;
    border-radius:20px;
    width:90%;
    max-width:1100px;
    box-shadow:0 6px 20px rgba(0,0,0,0.3);
}
h1 {
    text-align:center;
    margin-bottom:25px;
}
table {
    width:100%;
    border-collapse:collapse;
    margin-bottom:25px;
}
th, td {
    padding:12px;
    text-align:center;
}
th {
    background: rgba(0,0,0,0.3);
}
tr:nth-child(even) {
    background: rgba(255,255,255,0.08);
}
.total {
    font-size:1.8rem;
    font-weight:bold;
    color:#ffd369;
    text-align:right;
}
.back-btn {
    display:inline-block;
    margin-top:20px;
    padding:10px 25px;
    border-radius:20px;
    background: linear-gradient(135deg, #00ccff, #099eeeff);
    color:#fff;
    text-decoration:none;
}
.back-btn:hover {
    transform:scale(1.05);
}
</style>
</head>

<body>

<div class="container">
    <h1>Cafeteria Balance (Admin)</h1>

    <table>
        <tr>
            <th>Customer</th>
            <th>Item</th>
            <th>Price (TK)</th>
            <th>Quantity</th>
            <th>Total (TK)</th>
            <th>Date</th>
        </tr>

        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['customer_name']); ?></td>
                <td><?= htmlspecialchars($row['Item_Name']); ?></td>
                <td><?= number_format($row['Price'], 2); ?></td>
                <td><?= $row['Quantity']; ?></td>
                <td><strong><?= number_format($row['item_total'], 2); ?></strong></td>
                <td><?= $row['Order_Date']; ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No completed orders yet.</td>
            </tr>
        <?php endif; ?>
    </table>

    <div class="total">
        💰 Total Cafeteria Balance: TK <?= number_format($totalBalance, 2); ?>
    </div>

    <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
</div>

</body>
</html>
