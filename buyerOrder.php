<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "buyer")
{
    header("Location: login.php");
    exit();
}

include("database.php");

$userId = $_SESSION["user_id"];


//  BUYER INFO 

$sql     = "SELECT user_table.First_Name, user_table.Last_Name,
                   buyer.Buyer_Id, buyer.Cart_Id
            FROM user_table
            JOIN buyer ON user_table.Buyer_Id = buyer.Buyer_Id
            WHERE user_table.User_Id = $userId";
$result  = mysqli_query($conn, $sql);
$buyer   = mysqli_fetch_assoc($result);
$buyerId = $buyer['Buyer_Id'];
$cartId  = $buyer['Cart_Id'];


// CART COUNT FOR NAVBAR 

$sql         = "SELECT COUNT(*) AS total FROM add_to_cart WHERE Cart_Id = $cartId";
$result      = mysqli_query($conn, $sql);
$row         = mysqli_fetch_assoc($result);
$totalInCart = $row['total'];


// GET ALL ORDERS 

$sql       = "SELECT `order`.Order_Id, `order`.Order_Date,
                     `order`.Status, `order`.Total_amount,
                     payment.Payment_Method, payment.Payment_Status
              FROM `order`
              LEFT JOIN payment ON `order`.Payment_id = payment.Payment_id
              WHERE `order`.Buyer_Id = $buyerId
              ORDER BY `order`.Order_Date DESC";
$result    = mysqli_query($conn, $sql);
$allOrders = mysqli_fetch_all($result, MYSQLI_ASSOC);


// GET ITEMS FOR EACH ORDER 

$orderItems = [];
foreach($allOrders as $order)
{
    $oid  = $order['Order_Id'];
    $sql  = "SELECT product.Product_Name, product.Price, product.Product_Image,
                    user_table.First_Name, user_table.Last_Name,
                    seller.Department
             FROM product_orders
             JOIN product ON product_orders.Product_Id = product.Product_Id
             JOIN seller ON product.Seller_Id = seller.Seller_Id
             JOIN user_table ON seller.User_Id = user_table.User_Id
             WHERE product_orders.Order_Id = $oid";
    $res  = mysqli_query($conn, $sql);
    $orderItems[$oid] = mysqli_fetch_all($res, MYSQLI_ASSOC);
}

mysqli_close($conn);
?>









<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--header at the top of the website
    -->
    <title>UniBuzz — My Orders · Southeast University</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="buyerDash.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-logo">Uni Buzz</div>
        <div class="nav-sep"></div>
        <div class="nav-tabs">
            <a href="buyerHomepage.php" class="nav-tab">Marketplace</a>
            <a href="buyerIdea.php" class="nav-tab">Ideas Hub</a>
            <a href="buyerOrder.php" class="nav-tab active">My Orders</a>
        </div>
        <div class="nav-right">
            <form action="logout.php" method="POST" style="margin:0;">
                <button class="btn-logout" type="submit" name="logout">Logout</button>
            </form>
            <a href="buyerCart.php" class="cart-trigger">
                🛒 Cart <span class="cart-count"><?php echo $totalInCart; ?></span>
            </a>
            <div class="nav-avatar"><?php echo strtoupper(substr($buyer['First_Name'], 0, 1)); ?></div>
        </div>
    </nav>

    <div class="page-shell">
        <div class="app-content" style="max-width:920px;">

            <div class="sec-head">
                <div>
                    <div class="sec-title">My <em>Orders</em></div>
                    <div class="sec-sub">Track your purchases from SEU student sellers</div>
                </div>
            </div>

            <!-- order -->
            <?php if(count($allOrders) == 0): ?>
                <div style="text-align:center;padding:3rem;color:var(--muted);">
                    You have no orders yet. <a href="buyerHomepage.php">Start shopping →</a>
                </div>

            <?php else: ?>
                <?php foreach($allOrders as $order): ?>
                <?php
                    $oid   = $order['Order_Id'];
                    $date  = date("d M Y", strtotime($order['Order_Date']));
                    $items = $orderItems[$oid];

                    // status css class
                    if($order['Status'] == 'Completed')      $statusClass = "status-completed";
                    elseif($order['Status'] == 'Pending')    $statusClass = "status-pending";
                    else                                     $statusClass = "status-delivering";
                ?>
                <div class="order-card">
                    <div class="order-head">
                        <div>
                            <div class="order-id">Order #SEU-<?php echo $oid; ?></div>
                            <div class="order-date">Placed on <?php echo $date; ?></div>
                        </div>
                        <div class="order-status <?php echo $statusClass; ?>">
                            <?php echo $order['Status']; ?>
                        </div>
                    </div>

                    <div class="order-items">
                        <?php foreach($items as $item): ?>
                        <div class="order-item">
                            <div class="ci-img">
                                <img src="<?php echo $item['Product_Image']; ?>" alt="">
                            </div>
                            <div class="ci-info">
                                <div class="ci-name"><?php echo $item['Product_Name']; ?></div>
                                <div class="ci-seller">
                                    by <?php echo $item['First_Name'] . " " . $item['Last_Name']; ?>
                                    · <?php echo $item['Department']; ?>
                                </div>
                            </div>
                            <div class="order-item-price">
                                &#2547;<?php echo number_format($item['Price'], 2); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-foot">
                        <div class="order-total">
                            Total: <span>&#2547;<?php echo number_format($order['Total_amount'], 2); ?></span>
                        </div>
                        <div class="order-pay">
                            Paid via <?php echo $order['Payment_Method'] ?? '—'; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
