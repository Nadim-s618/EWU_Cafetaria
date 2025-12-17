<?php
// dashboard.php - Dashboard for EAST WEST CAFE
include "db.php";

// Fetch available menu items from the database
$menu_items = [];
$sql = "SELECT * FROM Menu_Item WHERE Availability = 1"; // only available items
$result = mysqli_query($conn, $sql);
// Fetch top selling product
$top_product = null;

$top_sql = "
    SELECT 
        mi.Item_Name,
        mi.Image,
        SUM(od.Quantity) AS total_sold
    FROM Order_Details od
    JOIN Orders o ON od.Order_ID = o.Order_ID
    JOIN Menu_Item mi ON od.Item_ID = mi.Item_ID
    GROUP BY od.Item_ID
    ORDER BY total_sold DESC
    LIMIT 1
";

$top_result = mysqli_query($conn, $top_sql);

if ($top_result && mysqli_num_rows($top_result) > 0) {
    $top_product = mysqli_fetch_assoc($top_result);
}


if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menu_items[] = [
            "Item_ID" => $row['Item_ID'], // add Item_ID for ordering
            "name" => $row['Item_Name'],
            "price" => $row['Price'],
            "image" => !empty($row['Image']) ? $row['Image'] : 'images/default.jpg'
        ];
    }
} else {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - East West Cafe</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: linear-gradient(135deg, #3988bd, #bfe4f5);
      color: #fff;
    }
    header {
      background: rgba(4, 211, 248, 0.613);
      padding: 20px;
      text-align: center;
      backdrop-filter: blur(10px);
      position: relative;
    }
    header h1 {
      margin: 0;
      font-size: 2rem;
    }
    .top-product-box {
background-image:url('data:image/png;base64,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'); 
border-radius: 22px;
  padding: 25px 30px;
  margin: 30px 40px 10px;
  box-shadow: 0 6px 22px rgba(0,0,0,0.3);
  backdrop-filter: blur(12px);
}

.top-product-title {
  font-size: 1.2rem;
  font-weight: bold;
  color: #ffd369;
  margin-bottom: 15px;
  text-transform: uppercase;
}

.top-product-content {
  display: flex;
  align-items: center;
  gap: 25px;
}

.top-product-img {
  width: 130px;
  height: 130px;
  border-radius: 18px;
  object-fit: cover;
  box-shadow: 0 5px 15px rgba(0,0,0,0.4);
}

.top-product-name {
  font-size: 1.7rem;
  font-weight: bold;
}

.top-product-sold {
  font-size: 1.1rem;
  margin-top: 8px;
}

    .welcome {
      margin-top: 5px;
      font-size: 1rem;
      color: #b7b4af;
    }
    .logout {
      position: absolute;
      top: 20px;
      right: 20px;
      background: linear-gradient(135deg, #00ccff, #099eeeff);
      padding: 10px 40px;
      border: none;
      border-radius: 20px;
      color: #fff;
      cursor: pointer;
      text-decoration: none;
    }
    .menu-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      padding: 40px;
    }
    .menu-item {
      background: rgba(104, 179, 204, 0.547);
      border-radius: 20px;
      padding: 20px;
      text-align: center;
      backdrop-filter: blur(12px);
      box-shadow:  6px 18px rgba(124, 198, 217, 0.3);
      transition: transform 0.3s;
    }
    .menu-item:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    }
    .menu-item img {
      width: 150px;
      height: 150px;
      border-radius: 15px;
      object-fit: cover;
      margin-bottom: 15px;
    }
    .menu-item h3 {
      margin: 0;
      margin-bottom: 10px;
      font-size: 1.2rem;
    }
    .menu-item p {
      margin: 0;
      font-size: 1rem;
      color: #ffd369;
      font-weight: bold;
    }
    .order-btn {
      margin-top: 10px;
      padding: 8px 16px;
      border: none;
      border-radius: 20px;
      background:linear-gradient(135deg, #00ccff, #099eeeff);
      color: #fff;
      cursor: pointer;
      font-weight: bold;
    }
    .order-btn:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body>

<header>
  <h1><i>East West Cafe</i></h1>
  <h1>Menu</h1>
  <a href="logout.php" class="logout">Logout</a>
  <?php if ($top_product): ?>
<div class="top-product-box">
  <div class="top-product-title">🔥 Top Selling Product</div>

  <div class="top-product-content">
    <img 
      src="<?= !empty($top_product['Image']) ? $top_product['Image'] : 'images/default.jpg'; ?>" 
      alt="<?= htmlspecialchars($top_product['Item_Name']); ?>" 
      class="top-product-img"
    >

    <div>
      <div class="top-product-name">
        <?= htmlspecialchars($top_product['Item_Name']); ?>
      </div>
      <div class="top-product-sold">
        Sold: <strong><?= $top_product['total_sold']; ?></strong> times
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

</header>

<div class="menu-container">
  <?php if(!empty($menu_items)): ?>
    <?php foreach($menu_items as $item): ?>
      <div class="menu-item">
        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
        <h3><?php echo $item['name']; ?></h3>
        <p>TK <?php echo $item['price']; ?></p>
        <form action="order.php" method="GET">
          <input type="hidden" name="item_id" value="<?php echo $item['Item_ID']; ?>">
          <button type="submit" class="order-btn">Order</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="text-align:center; font-size:1.2rem;">No menu items available.</p>
  <?php endif; ?>
</div>

</body>
</html>