<?php
session_start();
include "db.php";

// Redirect to login if not Admin
if (!isset($_SESSION['User_ID']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Add Menu Item
if (isset($_POST['add_item'])) {
    $name = $_POST['item_name'];
    $price = $_POST['price'];
    $availability = isset($_POST['availability']) ? 1 : 0;
    $image = !empty($_POST['image']) ? $_POST['image'] : 'images/default.jpg';

    $stmt = $conn->prepare("INSERT INTO Menu_Item (Item_Name, Price, Availability, Added_By, Image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdiis", $name, $price, $availability, $_SESSION['User_ID'], $image);

    if ($stmt->execute()) {
        $message = "Menu item added successfully!";
    } else {
        $message = "Error adding menu item: " . $conn->error;
    }
}
// Handle Delete Menu Item
if (isset($_GET['delete'])) {
    $item_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM Menu_Item WHERE Item_ID=?");
    $stmt->bind_param("i", $item_id);
    if ($stmt->execute()) {
        $message = "Menu item deleted successfully!";
    } else {
        $message = "Error deleting menu item: " . $conn->error;
    }
}

// Handle Update Menu Item
if (isset($_POST['update_item'])) {
    $item_id = $_POST['item_id'];
    $name = $_POST['item_name'];
    $price = $_POST['price'];
    $availability = isset($_POST['availability']) ? 1 : 0;
    $image = !empty($_POST['image']) ? $_POST['image'] : 'images/default.jpg';

    $stmt = $conn->prepare("UPDATE Menu_Item SET Item_Name=?, Price=?, Availability=?, Image=? WHERE Item_ID=?");
    $stmt->bind_param("sdisi", $name, $price, $availability, $image, $item_id);
    if ($stmt->execute()) {
        $message = "Menu item updated successfully!";
    } else {
        $message = "Error updating menu item: " . $conn->error;
    }
}// Fetch all menu items
$result_items = $conn->query("SELECT * FROM Menu_Item ORDER BY Item_ID DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Menu - East West Cafe</title>
<style>
body { font-family: 'Segoe UI', sans-serif; margin:0; background: linear-gradient(135deg, #42a54cdc, #8c6eef); color:#fff; }
header { background: rgba(0,0,0,0.4); padding:20px; text-align:center; backdrop-filter: blur(10px); position: relative; }
header h1 { margin:0; font-size:2rem; }
.logout { position: absolute; top:20px; right:20px; background:linear-gradient(135deg, #00ccff, #099eeeff); padding:10px 20px; border:none; border-radius:20px; color:#fff; cursor:pointer; text-decoration:none; }
.container { padding:40px; }
.message { text-align:center; margin-bottom:20px; color:#ffd369; font-weight:bold; }
.menu-container { display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px; }
.menu-item { background: rgba(255,255,255,0.1); border-radius:20px; padding:20px; text-align:center; backdrop-filter: blur(12px); box-shadow:0 6px 18px rgba(0,0,0,0.3); transition: transform 0.3s; }
.menu-item:hover { transform: scale(1.05); }
.menu-item img { width:150px; height:150px; border-radius:15px; object-fit:cover; margin-bottom:15px; }
.menu-item h3 { margin:0; margin-bottom:10px; }
.menu-item p { margin:0; color:#ffd369; font-weight:bold; }
.btn { margin-top:10px; padding:8px 16px; border:none; border-radius:20px; background: linear-gradient(135deg,#ff6a00,#ee0979); color:#fff; cursor:pointer; font-weight:bold; text-decoration:none; display:inline-block; }
form.add-form, form.update-form { margin-bottom:40px; text-align:center; }
input[type="text"], input[type="number"] { padding:10px; border-radius:10px; border:none; margin:5px; }
</style>
</head>
<body>
<header>
<h1>Admin Menu - <?php echo $_SESSION['Name']; ?></h1>
<a href="admin_dashboard.php" class="logout">Back to Admin Dashboard</a>
</header>

<div class="container">

<?php if(!empty($message)) echo "<div class='message'>$message</div>"; ?>

<!-- Add Menu Item Form -->
<form class="add-form" method="POST" action="">
    <h2>Add New Menu Item</h2>
    <input type="text" name="item_name" placeholder="Item Name" required>
    <input type="number" name="price" placeholder="Price" step="0.01" required>
    <input type="text" name="image" placeholder="Image URL (optional)">
    <label><input type="checkbox" name="availability" checked> Available</label>
    <br>
    <button type="submit" name="add_item" class="btn">Add Item</button>
</form>
<!-- Menu Items -->
<div class="menu-container">
<?php if($result_items->num_rows > 0): ?>
    <?php while($item = $result_items->fetch_assoc()): ?>
        <div class="menu-item">
            <img src="<?php echo !empty($item['Image']) ? $item['Image'] : 'images/default.jpg'; ?>" alt="<?php echo $item['Item_Name']; ?>">
            <h3><?php echo $item['Item_Name']; ?></h3>
            <p>TK <?php echo $item['Price']; ?></p>
            <p>Status: <?php echo $item['Availability'] ? 'Available' : 'Unavailable'; ?></p>

            <!-- Update Form -->
            <form class="update-form" method="POST" action="">
                <input type="hidden" name="item_id" value="<?php echo $item['Item_ID']; ?>">
                <input type="text" name="item_name" value="<?php echo $item['Item_Name']; ?>" required>
                <input type="number" name="price" value="<?php echo $item['Price']; ?>" step="0.01" required>
                <input type="text" name="image" value="<?php echo $item['Image']; ?>" placeholder="Image URL">
                <label><input type="checkbox" name="availability" <?php echo $item['Availability'] ? 'checked' : ''; ?>> Available</label>
                <br>
                <button type="submit" name="update_item" class="btn">Update</button>
                <a href="?delete=<?php echo $item['Item_ID']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
            </form>
        </div>
         <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center;">No menu items found.</p>
<?php endif; ?>
</div>

</div>
</body>
</html>