<?php
session_start();
include "db.php";

// Redirect to login if not logged in
if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['User_ID'];
$message = "";
$feedback_msg = "";

// ----------- Handle Order Submission -----------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'], $_POST['item_id'], $_POST['quantity'])) {
    $item_id = $_POST['item_id'];
    $quantity = intval($_POST['quantity']);

    // Check if menu item exists and is available
    $stmt = $conn->prepare("SELECT * FROM Menu_Item WHERE Item_ID=? AND Availability=1");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result_item = $stmt->get_result();

    if ($result_item->num_rows == 0) {
        $message = "Invalid menu item.";
    } else {
        $item = $result_item->fetch_assoc();
        $total_price = $item['Price'] * $quantity;

        // Insert order
        $stmt_order = $conn->prepare("INSERT INTO Orders (User_ID) VALUES (?)");
        $stmt_order->bind_param("i", $user_id);
        if ($stmt_order->execute()) {
            $order_id = $conn->insert_id;

            // Insert order details
            $stmt_detail = $conn->prepare("INSERT INTO Order_Details (Order_ID, Item_ID, Quantity) VALUES (?, ?, ?)");
            $stmt_detail->bind_param("iii", $order_id, $item_id, $quantity);
            $stmt_detail->execute();

            // Update admin balance
            $stmt_balance = $conn->prepare("UPDATE admin_balance SET balance = balance + ? WHERE id = 1");
            $stmt_balance->bind_param("d", $total_price);
            $stmt_balance->execute();

            $message = "Order placed successfully!";
        } else {
            $message = "Failed to place order: " . $conn->error;
        }
    }
}

// ----------- Handle Feedback Submission -----------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'], $_POST['feedback_message'])) {
    $feedback_message = trim($_POST['feedback_message']);
    if (!empty($feedback_message)) {
        $stmt_feedback = $conn->prepare("INSERT INTO feedback (User_ID, Message) VALUES (?, ?)");
        $stmt_feedback->bind_param("is", $user_id, $feedback_message);
        if ($stmt_feedback->execute()) {
            $feedback_msg = "Thank you for your feedback!";
        } else {
            $feedback_msg = "Failed to submit feedback.";
        }
    } else {
        $feedback_msg = "Please enter your feedback.";
    }
}

// ----------- Handle Search & Status Filter -----------
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';

// Fetch menu items
if (!empty($search)) {
    $stmt_items = $conn->prepare("SELECT * FROM Menu_Item WHERE Availability=1 AND Item_Name LIKE ?");
    $like_search = "%$search%";
    $stmt_items->bind_param("s", $like_search);
} else {
    $stmt_items = $conn->prepare("SELECT * FROM Menu_Item WHERE Availability=1");
}
$stmt_items->execute();
$result_items = $stmt_items->get_result();

// Fetch user orders
$sql_orders = "SELECT o.Order_ID, o.Order_Date, o.Status, mi.Item_Name, od.Quantity
               FROM Orders o
               JOIN Order_Details od ON o.Order_ID = od.Order_ID
               JOIN Menu_Item mi ON od.Item_ID = mi.Item_ID
               WHERE o.User_ID = ?";

if (!empty($status_filter) && in_array($status_filter, ['Pending', 'Preparing', 'Completed', 'Cancelled'])) {
    $sql_orders .= " AND o.Status=?";
}

$sql_orders .= " ORDER BY o.Order_Date DESC";

if (!empty($status_filter)) {
    $stmt_orders = $conn->prepare($sql_orders);
    $stmt_orders->bind_param("is", $user_id, $status_filter);
} else {
    $stmt_orders = $conn->prepare($sql_orders);
    $stmt_orders->bind_param("i", $user_id);
}
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order - East West Cafe</title>
<style>
body { font-family:'Segoe UI', sans-serif; margin:0; background: linear-gradient(135deg, #0bd637, #ff914d); color:#fff; }
header { background: rgba(10,7,7,0.4); padding:20px; text-align:center; backdrop-filter: blur(10px); position: relative; }
header h1 { margin:0; font-size:2rem; }
.logout { position:absolute; top:20px; right:20px; background:linear-gradient(135deg, #00ccff, #099eeeff); padding:10px 20px; border:none; border-radius:20px; color:#fff; cursor:pointer; text-decoration:none; }
.container { padding:40px; }
.search-form, .status-filter { text-align:center; margin-bottom:20px; }
.search-form input[type="text"], .status-filter select { padding:10px; border-radius:20px; border:none; width:200px; }
.search-form button, .status-filter button { padding:10px 20px; border-radius:20px; border:none; background: linear-gradient(135deg, #00ccff, #099eeeff); color:#fff; cursor:pointer; }
.menu-container { display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-bottom:40px; }
.menu-item { background: rgba(255,255,255,0.1); border-radius:20px; padding:20px; text-align:center; backdrop-filter: blur(12px); box-shadow:0 6px 18px rgba(0,0,0,0.3); transition: transform 0.3s; }
.menu-item:hover { transform: scale(1.05); }
.menu-item img { width:150px; height:150px; border-radius:15px; object-fit:cover; margin-bottom:15px; }
.menu-item h3 { margin:0; margin-bottom:10px; }
.menu-item p { margin:0; color:#ffd369; font-weight:bold; }
.order-btn { margin-top:10px; padding:8px 16px; border:none; border-radius:20px; background: linear-gradient(135deg, #00ccff, #099eeeff); color:#fff; cursor:pointer; }
.order-btn:hover { transform: scale(1.05); }
.orders-table { width:100%; border-collapse: collapse; margin-bottom:40px; }
.orders-table th, .orders-table td { padding:10px; border-bottom:1px solid #fff; text-align:center; }
.orders-table th { background: rgba(0,0,0,0.4); }
.message { text-align:center; margin-bottom:20px; color:#ffd369; font-weight:bold; }
textarea { width:80%; max-width:600px; height:100px; padding:10px; border-radius:12px; border:1px solid #ccc; font-size:14px; }
.feedback-btn { padding:10px 25px; border:none; border-radius:20px; background: linear-gradient(135deg, #00ccff, #099eeeff); color:#fff; cursor:pointer; }
</style>
</head>
<body>

<header>
<h1>Place Your Order</h1>
<a href="menu.php" class="logout">Back to Menu</a>
</header>

<div class="container">

<?php if(!empty($message)) echo "<div class='message'>$message</div>"; ?>
<?php if(!empty($feedback_msg)) echo "<div class='message'>$feedback_msg</div>"; ?>

<!-- Search Form -->
<form class="search-form" method="GET" action="">
    <input type="text" name="search" placeholder="Search menu..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<!-- Status Filter -->
<form class="status-filter" method="GET" action="">
    <select name="status_filter">
        <option value="">-- Filter by Status --</option>
        <option value="Pending" <?php if($status_filter=='Pending') echo 'selected'; ?>>Pending</option>
        <option value="Preparing" <?php if($status_filter=='Preparing') echo 'selected'; ?>>Preparing</option>
        <option value="Completed" <?php if($status_filter=='Completed') echo 'selected'; ?>>Completed</option>
        <option value="Cancelled" <?php if($status_filter=='Cancelled') echo 'selected'; ?>>Cancelled</option>
    </select>
    <button type="submit">Filter</button>
</form>

<!-- Menu Items -->
<div class="menu-container">
<?php if($result_items->num_rows > 0): ?>
    <?php while($item = $result_items->fetch_assoc()): ?>
    <div class="menu-item">
        <img src="<?php echo !empty($item['Image']) ? $item['Image'] : 'images/default.jpg'; ?>" alt="<?php echo $item['Item_Name']; ?>">
        <h3><?php echo $item['Item_Name']; ?></h3>
        <p>TK <?php echo $item['Price']; ?></p>
        <form method="POST" action="">
            <input type="hidden" name="item_id" value="<?php echo $item['Item_ID']; ?>">
            <input type="hidden" name="place_order" value="1">
            <input type="number" name="quantity" value="1" min="1"
                style="width:70px; padding:8px; margin-top:5px; border-radius:8px; border:1px solid #444; background:#222; color:#fff; font-size:14px; text-align:center;">
            <br>
            <button type="submit" class="order-btn">Order</button>
        </form>
    </div>
    <?php endwhile; ?>
<?php else: ?>
<p style="text-align:center;">No menu items found.</p>
<?php endif; ?>
</div>

<!-- User Orders -->
<h2 style="text-align:center; margin-bottom:20px;">Your Orders</h2>
<?php if($result_orders->num_rows > 0): ?>
<table class="orders-table">
<tr>
    <th>Order ID</th>
    <th>Item</th>
    <th>Quantity</th>
    <th>Date</th>
    <th>Status</th>
</tr>
<?php while($order = $result_orders->fetch_assoc()): ?>
<tr>
    <td><?php echo $order['Order_ID']; ?></td>
    <td><?php echo $order['Item_Name']; ?></td>
    <td><?php echo $order['Quantity']; ?></td>
    <td><?php echo $order['Order_Date']; ?></td>
    <td><?php echo $order['Status']; ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;">You have not placed any orders yet.</p>
<?php endif; ?>

<!-- Feedback Section -->
<h2 style="text-align:center; margin:40px 0 20px;">Give Us Feedback</h2>
<form method="POST" action="" style="text-align:center; margin-bottom:40px;">
    <textarea name="feedback_message" placeholder="Write your feedback here..." required></textarea>
    <br><br>
    <input type="hidden" name="submit_feedback" value="1">
    <button type="submit" class="feedback-btn">Submit Feedback</button>
</form>

</div>
</body>
</html>
