<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "buyer")
{
    header("Location: login.php");
    exit();
}

include("database.php");

$userId = $_SESSION["user_id"];

// BUYER INFO 
$sql    = "SELECT user_table.First_Name, user_table.Last_Name,
                  buyer.Buyer_Id, buyer.Cart_Id
           FROM user_table
           JOIN buyer ON user_table.Buyer_Id = buyer.Buyer_Id
           WHERE user_table.User_Id = $userId";
$result = mysqli_query($conn, $sql);
$buyer  = mysqli_fetch_assoc($result);
$buyerId = $buyer['Buyer_Id'];
$cartId  = $buyer['Cart_Id'];

// INCREASE 
if(isset($_GET["increase"]))
{
    $productId = $_GET["increase"];
    mysqli_query($conn, "UPDATE add_to_cart SET Quantity = Quantity + 1 WHERE Cart_Id=$cartId AND Product_Id=$productId");
    header("Location: buyerCart.php");
    exit();
}

// DECREASE 
if(isset($_GET["decrease"]))
{
    $productId = $_GET["decrease"];

    $checkResult = mysqli_query($conn, "SELECT Quantity FROM add_to_cart WHERE Cart_Id=$cartId AND Product_Id=$productId");
    $check       = mysqli_fetch_assoc($checkResult);

    if($check && $check['Quantity'] <= 1)
    {
        // remove completely
        mysqli_query($conn, "DELETE FROM add_to_cart WHERE Cart_Id=$cartId AND Product_Id=$productId");
        mysqli_query($conn, "UPDATE cart SET Quantity = Quantity - 1 WHERE Cart_Id=$cartId AND Quantity > 0");
    }
    elseif($check)
    {
        mysqli_query($conn, "UPDATE add_to_cart SET Quantity = Quantity - 1 WHERE Cart_Id=$cartId AND Product_Id=$productId");
    }

    header("Location: buyerCart.php");
    exit();
}

// REMOVE FROM CART 
if(isset($_GET["remove"]))
{
    $productId = $_GET["remove"];
    mysqli_query($conn, "DELETE FROM add_to_cart WHERE Cart_Id=$cartId AND Product_Id=$productId");
    mysqli_query($conn, "UPDATE cart SET Quantity = Quantity - 1 WHERE Cart_Id=$cartId AND Quantity > 0");
    header("Location: buyerCart.php");
    exit();
}

// CHECKOUT
if(isset($_POST["checkout"]))
{
    $payMethod   = $_POST["pay_method"];
    $totalAmount = $_POST["total_amount"];

    $sql = "INSERT INTO `order`(Buyer_Id, Order_Date, Status, Total_amount)
            VALUES ($buyerId, NOW(), 'Pending', '$totalAmount')";
    mysqli_query($conn, $sql);
    $newOrderId = mysqli_insert_id($conn);

    $sql = "INSERT INTO payment(Order_Id, Payment_Method, Payment_Status, Amount)
            VALUES ($newOrderId, '$payMethod', 'Pending', '$totalAmount')";
    mysqli_query($conn, $sql);
    $newPaymentId = mysqli_insert_id($conn);

    mysqli_query($conn, "UPDATE `order` SET Payment_id=$newPaymentId WHERE Order_Id=$newOrderId");

    $cartItemsForOrder = mysqli_fetch_all(mysqli_query($conn, "SELECT Product_Id, Quantity FROM add_to_cart WHERE Cart_Id=$cartId"), MYSQLI_ASSOC);
    foreach($cartItemsForOrder as $item)
    {
        $pid = $item['Product_Id'];
        $qty = $item['Quantity'];
        mysqli_query($conn, "INSERT INTO product_orders(Product_Id, Order_Id, Quantity) VALUES ($pid, $newOrderId, $qty)");
    }

    mysqli_query($conn, "DELETE FROM add_to_cart WHERE Cart_Id=$cartId");
    mysqli_query($conn, "UPDATE cart SET Quantity=0 WHERE Cart_Id=$cartId");

    header("Location: buyerOrder.php");
    exit();
}

// GET CART ITEMS 
$sql       = "SELECT add_to_cart.Product_add_id, add_to_cart.Quantity,
                     product.Product_Id, product.Product_Name,
                     product.Price, product.Product_Image,
                     seller.Department,
                     user_table.First_Name, user_table.Last_Name
              FROM add_to_cart
              JOIN product ON add_to_cart.Product_Id = product.Product_Id
              JOIN seller ON product.Seller_Id = seller.Seller_Id
              JOIN user_table ON seller.User_Id = user_table.User_Id
              WHERE add_to_cart.Cart_Id = $cartId";
$result    = mysqli_query($conn, $sql);
$cartItems = mysqli_fetch_all($result, MYSQLI_ASSOC);

// calculate subtotal
$subtotal = 0;
foreach($cartItems as $item)
{
    $subtotal += $item['Price'] * $item['Quantity'];
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniBuzz — Cart · Southeast University</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="buyerDash.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-logo">Uni<em>Buzz</em></div>
        <div class="nav-sep"></div>
        <div class="nav-tabs">
            <a href="buyerHomepage.php" class="nav-tab">Marketplace</a>
            <a href="buyerIdea.php" class="nav-tab">Ideas Hub</a>
            <a href="buyerOrder.php" class="nav-tab">My Orders</a>
        </div>
        <div class="nav-right">
            <form action="logout.php" method="POST" style="margin:0;">
                <button class="btn-logout" type="submit" name="logout">Logout</button>
            </form>
            <a href="buyerCart.php" class="cart-trigger active">
                🛒 Cart <span class="cart-count"><?php echo count($cartItems); ?></span>
            </a>
            <div class="nav-avatar"><?php echo strtoupper(substr($buyer['First_Name'], 0, 1)); ?></div>
        </div>
    </nav>

    <div class="page-shell">
        <div class="app-content" style="max-width:900px;">

            <div class="sec-head">
                <div>
                    <div class="sec-title">Your <em>Cart</em></div>
                    <div class="sec-sub">Review your items before checkout</div>
                </div>
            </div>

            <?php if(count($cartItems) === 0): ?>

                <!-- EMPTY CART -->
                <div style="text-align:center;padding:3rem 1rem;color:var(--muted);">
                    <div style="font-size:2.5rem;margin-bottom:.75rem;">🛒</div>
                    <div style="font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:700;margin-bottom:.4rem;">Your cart is empty</div>
                    <div style="font-family:'Outfit',sans-serif;font-size:.88rem;">Browse the marketplace and add items to get started.</div>
                    <a href="buyerHomepage.php" style="display:inline-block;margin-top:1.2rem;font-family:'Outfit',sans-serif;font-size:.88rem;font-weight:600;color:var(--accent,#c9a84c);text-decoration:none;">← Back to Marketplace</a>
                </div>

            <?php else: ?>

                <!-- CART ITEMS LOOP -->
                <?php foreach($cartItems as $item): ?>
                <div class="cart-item">
                    <div class="ci-img">
                        <img src="<?php echo $item['Product_Image']; ?>" alt="">
                    </div>
                    <div class="ci-info">
                        <div class="ci-name"><?php echo $item['Product_Name']; ?></div>
                        <div class="ci-seller">
                            by <?php echo $item['First_Name'] . " " . $item['Last_Name']; ?>
                            · <?php echo $item['Department']; ?>
                        </div>
                        <div class="ci-qty-row">
                            <a href="buyerCart.php?decrease=<?php echo $item['Product_Id']; ?>" class="qty-btn">−</a>
                            <div class="qty-num"><?php echo $item['Quantity']; ?></div>
                            <a href="buyerCart.php?increase=<?php echo $item['Product_Id']; ?>" class="qty-btn">+</a>
                        </div>
                    </div>
                    <div class="ci-price">
                        &#2547;<?php echo number_format($item['Price'] * $item['Quantity'], 2); ?>
                    </div>
                    <a href="buyerCart.php?remove=<?php echo $item['Product_Id']; ?>"
                    class="btn-remove" title="Remove">✕</a>
                </div>
                <?php endforeach; ?>

                <!-- CART FOOTER -->
                <div class="cart-footer" style="border-radius:var(--r);border:1px solid var(--border);">
                    <div class="cart-summary-row">
                        <span>Subtotal</span>
                        <span>&#2547;<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="cart-summary-row">
                        <span>Campus delivery</span>
                        <span>Free</span>
                    </div>
                    <div class="cart-total-row">
                        <span class="cart-total-lbl">Total</span>
                        <span class="cart-total-val">&#2547;<?php echo number_format($subtotal, 2); ?></span>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="total_amount" value="<?php echo $subtotal; ?>">

                        <div class="fund-pay-label" style="margin-bottom:.45rem;">Pay via</div>
                        <div class="pay-methods-row">
                            <input class="pay-radio" type="radio" name="pay_method" id="pay-cod" value="Cash on Delivery" checked>
                            <label class="pay-opt" for="pay-cod">Cash on Delivery</label>
                        </div>

                        <button type="submit" name="checkout" class="btn-checkout" style="margin-top:25px;">
                            Checkout · &#2547;<?php echo number_format($subtotal, 2); ?> →
                        </button>
                    </form>
                </div>

            <?php endif; ?>

        </div>

        <div class="page-footer">
            <div class="footer-logo">UniBuzz</div>
            <div class="footer-info">Southeast University · Tejgaon, Dhaka · UniBuzz v2.4.1</div>
        </div>
    </div>

</body>
</html>