<!--#################### From here PHP starts #################### -->
<?php
include("database.php");

#This part is for search input
$search = isset($_GET["productname"]) ? $_GET["productname"] : '';
$sort   = isset($_GET["sort"]) ? $_GET["sort"] : 'newest';

// build ORDER BY based on sort selection
if($sort == "price_high")     $orderBy = "product.Price DESC";
elseif($sort == "price_low")  $orderBy = "product.Price ASC";
elseif($sort == "top_rated")  $orderBy = "seller.Rating DESC";
else                          $orderBy = "product.Product_Id DESC"; // newest = highest id first

// build WHERE based on search input
if(!empty($search))
{
    $sql = "SELECT product.*, seller.Rating, seller.Department,
                   user_table.First_Name, user_table.Last_Name
            FROM product
            JOIN seller ON product.Seller_Id = seller.Seller_Id
            JOIN user_table ON seller.User_Id = user_table.User_Id
            WHERE product.Product_Name LIKE '%$search%'
            ORDER BY $orderBy";
}
else
{
    $sql = "SELECT product.*, seller.Rating, seller.Department,
                   user_table.First_Name, user_table.Last_Name
            FROM product
            JOIN seller ON product.Seller_Id = seller.Seller_Id
            JOIN user_table ON seller.User_Id = user_table.User_Id
            ORDER BY $orderBy";
}

$result = mysqli_query($conn, $sql);


?>






<!--#################### From here HTML starts #################### -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SEU UniBuzz Marketplace</title>
  <link href="index.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&display=swap">
</head>
<body>
    <!-- This is the navbar part -->
    <nav class="navbar">
    <div class="nav-logo">UniBuzz</div>
    <div class="nav-sep"></div>
    <div class="nav-tabs">
      <a href="registration.php" class="nav-tab">BECOME A SELLER</a>
    </div>
    <div class="nav-right">
      <a href="helpAndSupport.html" class="nav-tab">HELP & SUPPORT</a>
      <a href="login.php" class="nav-tab">LOGIN</a>
      <a href="signup.php" class="nav-tab">SIGNUP</a>
    </div>
    </nav>



<!-- This is the navbar option part -->
<nav class="navbar-option">

<!-- This part is for right side options div-->
<div class="nav-middle">
  <a href="index.php" class="nav-option">Home</a>
  <a href="ideashubG.php" class="nav-option">Ideas Hub</a>
</div>

</nav>



<div class="page-shell">
  <div class="app-content">
  <div class="app-content">
    <div class="panel-market">

      <!-- section header title on left-->
      <div class="sec-head">
        <div>
          <div class="sec-title">The Marketplace</div>
          <div class="sec-sub">Discover products from fellow SEU students</div>
        </div>
      </div>

      <!-- Search bar -->
        <form method="get">
        <div class="controls-bar">
        <div class="search-warp">
          <input type="text" placeholder="Seach products" name="productname">
        </div>

        <!-- Sorting dropdown-->
        <select class="sort-select" name="sort">
            <option value="newest">Sort: Newest</option>
            <option value="price_high">Sort: Price High</option>
            <option value="price_low">Sort: Price Low</option>
            <option value="top_rated">Sort: Top Rated</option>
        </select>

        <!-- submit button -->
         <button class="button-submit" name="submit">Search</button>
        </div>
        </form>
      <!--This is a horizontal line div-->
      <div class="search-sep"></div>
      <!--This is a horizontal line div-->
      <div class="search-sep"></div>

      <!-- This is where the products go -->
      <div class="products-grid">


      <?php while($row = mysqli_fetch_assoc($result)): ?>
      <div class="pcard" style="animation-delay: .04s;">
          <div class="pcard-img">
              <img src="<?php echo $row['Product_Image']; ?>" alt="">
          </div>
          <div class="pcard-body">
              <div class="pcard-name"><?php echo $row['Product_Name']; ?></div>
              <div class="pcard-seller">
                  by <strong><?php echo $row['First_Name'] . " " . $row['Last_Name']; ?></strong>
                  <?php echo $row['Department']; ?> Dept.
              </div>
              <div class="pcard-stars">
                  <div class="stars-display">★★★★★</div>
                  <div class="stars-count">(<?php echo $row['Rating']; ?>)</div>
              </div>
              <div class="pcard-footer">
                  <div class="pcard-price">&#2547;<?php echo number_format($row['Price'], 2); ?></div>
              </div>
          </div>
      </div>
      <?php endwhile; ?>

      </div>


  </div>
  </div>


      <!--This is a horizontal line div-->
      <div class="search-sep2"></div>
      <!--This is a horizontal line div-->
      <div class="search-sep3"></div>





</body>
</html>