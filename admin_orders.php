<?php
session_start();
include "db.php";

// Only Admin can access
if (!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle delete
if (isset($_GET['delete'])) {
    $order_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Orders WHERE Order_ID=?");
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        $message = "Order deleted successfully.";
    } else {
        $message = "Failed to delete order.";
    }
    $stmt->close();
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'], $_POST['status'], $_POST['payment_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $payment_status = $_POST['payment_status'];

    $stmt = $conn->prepare("UPDATE Orders SET Status=?, Payment_Status=? WHERE Order_ID=?");
    $stmt->bind_param("ssi", $status, $payment_status, $order_id);
    if ($stmt->execute()) {
        $message = "Order updated successfully.";
    } else {
        $message = "Failed to update order.";
    }
    $stmt->close();
}

// Fetch all orders
$sql = "SELECT o.Order_ID, o.Order_Date, o.Status, o.Payment_Status, u.Name AS UserName, 
               mi.Item_Name, od.Quantity
        FROM Orders o
        JOIN Users u ON o.User_ID = u.User_ID
        JOIN Order_Details od ON o.Order_ID = od.Order_ID
        JOIN Menu_Item mi ON od.Item_ID = mi.Item_ID
        ORDER BY o.Order_Date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Orders - East West Cafe</title>
<style>
body { font-family:'Segoe UI',sans-serif; margin:0; background:linear-gradient(135deg, #42a54cdc, #8c6eef); color:#fff; }
header { background:rgba(0,0,0,0.4); padding:20px; text-align:center; position:relative; }
header h1 { margin:0; font-size:2rem; }
.back { position:absolute; top:20px; left:20px; background:linear-gradient(135deg, #00ccff, #099eeeff); padding:10px 20px; border-radius:20px; text-decoration:none; color:#fff; }
.container { padding:30px; }
.message { text-align:center; margin-bottom:20px; font-weight:bold; color:#ffd369; }
table { width:100%; border-collapse:collapse; margin-top:20px; }
th,td { padding:12px; text-align:center; border-bottom:1px solid #fff; }
th { background:rgba(0,0,0,0.5); }
form { display:flex; justify-content:center; gap:10px; align-items:center; }
select { padding:6px; border-radius:6px; border:none; }
button, .delete-btn { padding:8px 12px; border:none; border-radius:10px; cursor:pointer; color:#fff; }
button { background:linear-gradient(135deg,#28a745,#218838); }
.delete-btn { background:linear-gradient(135deg,#ff0000,#cc0000); text-decoration:none; }
</style>
</head>
<body>

<header>
    <h1>Manage Orders</h1>
    <a href="admin_dashboard.php" class="back">Back to Admin Dashboard</a>
</header>

<div class="container">
<?php if(!empty($message)) echo "<div class='message'>$message</div>"; ?>

<?php if ($result->num_rows > 0): ?>
<table>
    <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Item</th>
        <th>Quantity</th>
        <th>Date</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['Order_ID']; ?></td>
        <td><?php echo $row['UserName']; ?></td>
        <td><?php echo $row['Item_Name']; ?></td>
        <td><?php echo $row['Quantity']; ?></td>
        <td><?php echo $row['Order_Date']; ?></td>
        <td>
            <form method="POST" action="">
                <input type="hidden" name="order_id" value="<?php echo $row['Order_ID']; ?>">
                <select name="status">
                    <option value="Pending" <?php if($row['Status']=="Pending") echo "selected"; ?>>Pending</option>
                    <option value="Preparing" <?php if($row['Status']=="Preparing") echo "selected"; ?>>Preparing</option>
                    <option value="Completed" <?php if($row['Status']=="Completed") echo "selected"; ?>>Completed</option>
                    <option value="Cancelled" <?php if($row['Status']=="Cancelled") echo "selected"; ?>>Cancelled</option>
                </select>
        </td>
        <td>
                <select name="payment_status">
                    <option value="Pending" <?php if($row['Payment_Status']=="Pending") echo "selected"; ?>>Pending</option>
                    <option value="Paid" <?php if($row['Payment_Status']=="Paid") echo "selected"; ?>>Paid</option>
                    <option value="Failed" <?php if($row['Payment_Status']=="Failed") echo "selected"; ?>>Failed</option>
                    <option value="Refunded" <?php if($row['Payment_Status']=="Refunded") echo "selected"; ?>>Refunded</option>
                </select>
        </td>
        <td>
                <button type="submit">Update</button>
            </form>
            <a href="?delete=<?php echo $row['Order_ID']; ?>" class="delete-btn" onclick="return confirm('Delete this order?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;">No orders found.</p>
<?php endif; ?>
</div>
</body>
</html>