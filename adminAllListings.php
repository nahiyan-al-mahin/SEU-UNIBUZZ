<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "admin")
{
    header("Location: login.php");
    exit();
}

include("database.php");


// delete product
if(isset($_POST["delete_product"]))
{
    $del_id = $_POST["delete_product_id"];
    mysqli_query($conn, "DELETE FROM product WHERE Product_Id=$del_id");
    header("Location: adminAllListings.php");
    exit();
}

//LOGGED IN ADMIN INFO 

$loggedInUserId = $_SESSION["user_id"];

$sql        = "SELECT user_table.First_Name, user_table.Last_Name
               FROM user_table
               WHERE user_table.User_Id = $loggedInUserId";
$result     = mysqli_query($conn, $sql);
$adminInfo  = mysqli_fetch_assoc($result);

$adminFullName   = $adminInfo['First_Name'] . " " . $adminInfo['Last_Name'];
$adminFirstLetter = strtoupper(substr($adminInfo['First_Name'], 0, 1));



// get all products with seller name and category
$sql         = "SELECT product.Product_Id, product.Product_Name, product.Product_Type,
                       product.Price, product.Stock_Quantity, product.Product_Image,
                       product_category.Category_Name,
                       user_table.First_Name, user_table.Last_Name
                FROM product
                JOIN seller ON product.Seller_Id = seller.Seller_Id
                JOIN user_table ON seller.User_Id = user_table.User_Id
                LEFT JOIN product_category ON product.Product_Category_Id = product_category.Product_Category_Id";
$result      = mysqli_query($conn, $sql);
$allProducts = mysqli_fetch_all($result, MYSQLI_ASSOC);




mysqli_close($conn);
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UniBuzz — Admin Dashboard · Southeast University</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,400;0,600;0,700;0,800;1,700&family=Inter:wght@300;400;500;600&family=Fira+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="adminDash.css">
</head>
<body>

<!--sidebar ar main -->
<div class="my-dashboard-wrapper">

  <!-- bam diker nav panel -->
  <aside class="left-panel" id="sidebar">
    <div class="left-panel-inner">
      <div class="logo-area">
        <div class="site-name">UniBuzz</div>
        <div class="site-subtitle">Admin Console</div>
      </div>
      <div class="nav-links">

        <!-- Overview section er header label -->
        <div class="nav-group-title">Overview</div>
        <a href="adminHomepage.php" class="nav-link">Dashboard</a>

        <!-- Users section -->
        <div class="nav-group-title">Users</div>
        <a href="adminBuyers.php" class="nav-link">Buyers</a>
        <a href="adminSellers.php" class="nav-link">Sellers</a>

        <!-- Marketplace section -->
        <div class="nav-group-title">Marketplace</div>
        <a href="adminAllListings.php" class="nav-link">All Listings</a>
        <a href="adminAllOrders.php" class="nav-link">All Orders</a>

        <!-- Ideas Fund section -->
        <div class="nav-group-title">Ideas Fund</div>
        <a href="adminIdeas.php" class="nav-link">All Ideas</a>

        <!-- Community section -->
        <div class="nav-group-title">Community</div>
        <a href="globalChatRoom.php" class="nav-link">Chat Rooms</a>

      </div>

      <div class="admin-info-bar">
        <div class="admin-pic"><?php echo $adminFirstLetter; ?></div>
        <div>
          <div class="admin-fullname"><?php echo $adminFullName; ?></div>
        </div>
      </div>

    </div>
  </aside>



  <div class="right-side">

    <div class="header-bar">
      <div class="page-path">
        <a href="#">UniBuzz</a>
        <span class="sep">›</span>
        <span class="current">Dashboard</span>
      </div>


      <div class="header-right-side">
          <form action="logout.php" method="POST" style="margin:0;">
            <button class="btn-logout" type="submit" name="logout" >Logout</button>
          </form>
        <div class="nav-avatar" style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--gold),#A8842A);display:flex;align-items:center;justify-content:center;font-family:'Nunito',sans-serif;font-size:.9rem;font-weight:700;color:var(--bg);cursor:pointer;"><?php echo $adminFirstLetter; ?></div>
      </div>
    </div>

    <div class="page-content-scroll">


      <div class="page-top-section">
        <div>
          <div class="big-page-title">Admin Dashboard</div>
          <div class="page-subtitle">Southeast University · Tejgaon, Dhaka · UniBuzz v2.4.1</div>
        </div>
      </div>


      <div class="divider-row">
        <div class="divider-label">All Products</div>
        <div class="divider-line"></div>
      </div>

      <!-- This is where the products go -->
      <div class="products-grid">


<!--This is the backend php part -->
        <?php foreach($allProducts as $product): ?>
<!--This is the backend php part -->


        <!-- product 1 card style -->
        <div class="pcard" style="animation-delay: .04s;">

          <!-- This is for image in card-->
          <div class="pcard-img">
            <img src="<?php echo $product['Product_Image']?>" alt="">
          </div>
          <!-- This is the card body -->
          <div class="pcard-body">

            <!-- product name -->
            <div class="pcard-name"><?php echo $product['Product_Name']; ?></div>

            <!-- seller name -->
            <div class="pcard-seller">by <strong><?php echo $product['First_Name'] . " " . $product['Last_Name']; ?></strong></div>

            <!-- This is product ratings -->
            <div class="pcard-stars">
              <div class="stars-display"><?php echo $product['Category_Name'] ?? '—'; ?></div>
              <div class="stars-count">(<?php echo $product['Product_Type']; ?>)</div>
              <div class="stars-count">Quantity: <?php echo $product['Stock_Quantity']; ?></div>
            </div>

            <!-- card footer -->
            <div class="pcard-footer">
              <div class="pcard-price">৳<?php echo number_format($product['Price'], 2); ?></div>
                      <form method="POST">
                            <input type="hidden" name="delete_product_id" value="<?php echo $product['Product_Id']; ?>">
                            <button type="submit" name="delete_product" class="btn-deleteuser">Delete</button>
                      </form>
            </div>
          </div>

        </div>
        <?php endforeach; ?>
      </div>    <!--grid end-->

    </div><!-- page-content-scroll end -->
  </div><!-- right-side end -->
</div><!-- my-dashboard-wrapper end -->

</body>
</html>