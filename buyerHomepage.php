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


// ADD TO CART 

if(isset($_GET["add"]))
{
    $productId = $_GET["add"];

    // check if product already in cart
    $check = mysqli_query($conn, "SELECT * FROM add_to_cart WHERE Cart_Id=$cartId AND Product_Id=$productId");

    if(mysqli_num_rows($check) == 0)
    {
        // not in cart yet — add it
        mysqli_query($conn, "INSERT INTO add_to_cart(Cart_Id, Product_Id) VALUES ($cartId, $productId)");

        // update cart quantity count
        mysqli_query($conn, "UPDATE cart SET Quantity = Quantity + 1 WHERE Cart_Id=$cartId");
    }

    header("Location: buyerHomepage.php");
    exit();
}


// STATS 

// total orders
$sql         = "SELECT COUNT(*) AS total FROM `order` WHERE Buyer_Id = $buyerId";
$result      = mysqli_query($conn, $sql);
$row         = mysqli_fetch_assoc($result);
$totalOrders = $row['total'];

// total items in cart
$sql         = "SELECT COUNT(*) AS total FROM add_to_cart WHERE Cart_Id = $cartId";
$result      = mysqli_query($conn, $sql);
$row         = mysqli_fetch_assoc($result);
$totalInCart = $row['total'];

// total spent on completed orders
$sql        = "SELECT SUM(Total_amount) AS total FROM `order`
               WHERE Buyer_Id = $buyerId AND Status = 'Completed'";
$result     = mysqli_query($conn, $sql);
$row        = mysqli_fetch_assoc($result);
$totalSpent = $row['total'] ?? 0;


// PRODUCTS 

$search = isset($_GET["productname"]) ? $_GET["productname"] : '';
$sort   = isset($_GET["sort"]) ? $_GET["sort"] : 'newest';

if($sort == "price_high")    $orderBy = "product.Price DESC";
elseif($sort == "price_low") $orderBy = "product.Price ASC";
elseif($sort == "top_rated") $orderBy = "seller.Rating DESC";
else                         $orderBy = "product.Product_Id DESC";

if(!empty($search))
{
    $sql = "SELECT product.*, seller.Rating, seller.Department,
                   user_table.First_Name, user_table.Last_Name,
                   product_category.Category_Name
            FROM product
            JOIN seller ON product.Seller_Id = seller.Seller_Id
            JOIN user_table ON seller.User_Id = user_table.User_Id
            LEFT JOIN product_category ON product.Product_Category_Id = product_category.Product_Category_Id
            WHERE product.Product_Name LIKE '%$search%'
            ORDER BY $orderBy";
}
else
{
    $sql = "SELECT product.*, seller.Rating, seller.Department,
                   user_table.First_Name, user_table.Last_Name,
                   product_category.Category_Name
            FROM product
            JOIN seller ON product.Seller_Id = seller.Seller_Id
            JOIN user_table ON seller.User_Id = user_table.User_Id
            LEFT JOIN product_category ON product.Product_Category_Id = product_category.Product_Category_Id
            ORDER BY $orderBy";
}

$result   = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// get all product IDs already in this buyer's cart
// so we can show "In Cart" instead of "Add to Cart" on those products
$sql            = "SELECT Product_Id FROM add_to_cart WHERE Cart_Id = $cartId";
$result         = mysqli_query($conn, $sql);
$cartRows       = mysqli_fetch_all($result, MYSQLI_ASSOC);
$inCartIds      = array_column($cartRows, 'Product_Id');

mysqli_close($conn);
?>








<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniBuzz — Buyer Dashboard · Southeast University</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="buyerDash.css">
</head>
<body>

      <nav class="navbar">
          <div class="nav-logo">Uni Buzz</div>
          <div class="nav-sep"></div>
          <div class="nav-tabs">
              <a href="buyerHomepage.php" class="nav-tab active">Marketplace</a>
              <a href="buyerIdea.php" class="nav-tab">Ideas Hub</a>
              <a href="buyerOrder.php" class="nav-tab">My Orders</a>
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






        
        <div class="app-content">



        <!-- WELCOME BANNER -->
        <div class="welcome-banner">
            <div class="wb-text">
                <div class="wb-name">Welcome back, <?php echo $buyer['First_Name']; ?></div>
                <div class="wb-sub">Southeast University · UniBuzz</div>
            </div>
            <div class="wb-stats">
                <div class="wb-stat">
                    <div class="wb-stat-val"><?php echo $totalOrders; ?></div>
                    <div class="wb-stat-lbl">Orders</div>
                </div>
                <div class="wb-stat">
                    <div class="wb-stat-val"><?php echo $totalInCart; ?></div>
                    <div class="wb-stat-lbl">In Cart</div>
                </div>
                <div class="wb-stat">
                    <div class="wb-stat-val">&#2547;<?php echo number_format($totalSpent, 2); ?></div>
                    <div class="wb-stat-lbl">Spent</div>
                </div>
            </div>
        </div>


        
            <div class="sec-head">
                <div>
                    <div class="sec-title">The <em>Marketplace</em></div>
                    <div class="sec-sub">Discover products from your fellow SEU students</div>
                </div>
            </div>

            <!-- search + filter bar -->
            <form method="GET">
            <div class="controls-bar">
                <div class="search-wrap">
                    <input type="text" name="productname"
                          value="<?php echo htmlspecialchars($search); ?>"
                          placeholder="Search products, sellers, categories…">
                </div>
                <select class="sort-select" name="sort" onchange="this.form.submit()">
                    <option value="newest"     <?php if($sort=='newest')     echo 'selected'; ?>>Sort: Newest</option>
                    <option value="price_high" <?php if($sort=='price_high') echo 'selected'; ?>>Sort: Price ↑</option>
                    <option value="price_low"  <?php if($sort=='price_low')  echo 'selected'; ?>>Sort: Price ↓</option>
                    <option value="top_rated"  <?php if($sort=='top_rated')  echo 'selected'; ?>>Sort: Top Rated</option>
                </select>
                <button type="submit" class="btn-deleteuser">Search</button>
            </div>
            </form>


            <!--product grid....each .pcard is one listing-->
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                <?php $alreadyInCart = in_array($product['Product_Id'], $inCartIds); ?>

                <div class="pcard" style="animation-delay:.04s">
                    <div class="pcard-img">
                        <img src="<?php echo $product['Product_Image']; ?>" alt="">
                    </div>
                    <div class="pcard-body">
                        <div class="pcard-cat"><?php echo $product['Category_Name'] ?? '—'; ?></div>
                        <div class="pcard-name"><a style="text-decoration: none; color:black" href="buyerRating.php?product=<?php echo $product['Product_Id']; ?>"><?php echo $product['Product_Name']; ?></div>
                        <div class="pcard-seller">
                            by <strong><?php echo $product['First_Name'] . " " . $product['Last_Name']; ?></strong>
                            · <?php echo $product['Department']; ?> Dept
                        </div>
                        <div class="pcard-stars">
                            <div class="stars-display">★★★★★</div>
                            <div class="stars-count">(<?php echo $product['Rating']; ?>)</div>
                        </div>
                        <div class="pcard-footer">
                            <div class="pcard-price">&#2547;<?php echo number_format($product['Price'], 2); ?></div>
                            <div class="pcard-actions">
                                <?php if($alreadyInCart): ?>
                                    <a href="buyerCart.php" class="btn-add-cart">✓ In Cart</a>
                                <?php else: ?>
                                    <a href="buyerHomepage.php?add=<?php echo $product['Product_Id']; ?>" class="btn-add-cart">Add to cart</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

</body>
</html>
