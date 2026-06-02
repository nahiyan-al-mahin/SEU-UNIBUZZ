<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "seller")
{
    header("Location: login.php");
    exit();
}

include("database.php");

$userId   = $_SESSION["user_id"];

// get seller info
$sql      = "SELECT seller.Seller_Id, seller.Rating, seller.Department, seller.Semester,
                    user_table.First_Name, user_table.Last_Name
             FROM seller
             JOIN user_table ON seller.User_Id = user_table.User_Id
             WHERE seller.User_Id = $userId";
$result   = mysqli_query($conn, $sql);
$seller   = mysqli_fetch_assoc($result);
$sellerId = $seller['Seller_Id'];


// total orders
$sql         = "SELECT COUNT(DISTINCT `order`.Order_Id) AS total
                FROM `order`
                JOIN product_orders ON `order`.Order_Id = product_orders.Order_Id
                JOIN product ON product_orders.Product_Id = product.Product_Id
                WHERE product.Seller_Id = $sellerId";
$result      = mysqli_query($conn, $sql);
$row         = mysqli_fetch_assoc($result);
$totalOrders = $row['total'];



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


mysqli_close($conn);
?>








<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SEU UniBuzz Marketplace</title>
  <link href="sellerDash.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&display=swap">
  <style>

  #sidebar-toggle { display: none; }
  .hamburger-label {
    display: none;              /* hidden on desktop */
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    padding: 6px;
    margin-right: 0.25rem;
    z-index: 200;               /* stays above all other content */
  }

  .hamburger-label span {
    display: block;
    width: 22px; height: 2px;
    background: var(--maroon);  /* matches your maroon design token */
    border-radius: 2px;
    transition: all 0.3s ease;  /* smooth animation when toggling */
  }

  #sidebar-toggle:checked ~ .page-shell .sidebar {
    transform: translateX(0) !important;
  }

   #sidebar-toggle:checked ~ .sidebar-overlay {
    display: block !important;
    z-index: 88 !important;   /* must be BELOW sidebar z-index of 90 */
  }
  @media (max-width: 860px) {

    .hamburger-label { display: flex; }

    .sidebar-overlay {
      display: none;
      position: fixed; inset: 0;       /* covers the entire screen */
      background: rgba(0,0,0,0.45);    /* semi-transparent dark backdrop */
      z-index: 88;                      /* below sidebar (z-index 90) but above page content */
      cursor: pointer;                  /* pointer cursor hints it's clickable */
    }
  }

</style>
</head>
<body>
<input type="checkbox" id="sidebar-toggle">
<nav class="navbar">
<label class="hamburger-label" for="sidebar-toggle">
  <span></span> <!-- top bar -->
  <span></span> <!-- middle bar -->
  <span></span> <!-- bottom bar -->
</label>
  <div class="nav-logo">UniBuzz</div>

  <div class="nav-sep"></div>
  <div class="nav-breadcrumb">
    <a href="#">Home</a>
    <span class="arrow">›</span>
    <span class="current">Marketplace</span>
  </div>

  <div class="nav-right">
              <form action="logout.php" method="POST" style="margin:0;">
                <button class="btn-logout" type="submit" name="logout">Logout</button>
              </form>
    <div class="nav-badge">
      Seller Account
    </div>
    <div class="nav-avatar"><?php echo strtoupper(substr($seller['First_Name'], 0, 1)); ?></div>
  </div>
</nav>


<div class="page-shell">


  <aside class="sidebar">
    <div class="sidebar-content">

      <div class="seller-profile">
        <div class="seller-avatar">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="8" r="4" fill="rgba(255,255,255,0.9)"/>
            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="rgba(255,255,255,0.9)" stroke-width="1.8" stroke-linecap="round" fill="none"/>
          </svg>
        </div>
        <div class="seller-name"><?php echo $seller['First_Name'] . " " . $seller['Last_Name']; ?></div>
        <div class="seller-dept"><?php echo $seller['Department']; ?>  Southeast University</div>
        <div class="seller-rating">&#9733; <?php echo $seller['Rating']; ?>  Verified Seller</div>
      </div>

      <div class="sidebar-nav">

        <div class="nav-group-label">Seller</div>
        <a href="sellerHomepage.php" class="nav-item ">
          <span class="ni">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </span> My Dashboard
        </a>
        
        <a href="sellerOrder.php" class="nav-item ">
          <span class="ni">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          </span> Orders Received
          <span class="ni-badge"><?php echo $totalOrders; ?></span>
        </a>

        <div class="nav-group-label">Account</div>
        <a href="globalChatRoom.php" class="nav-item">
          <span class="ni">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span> Messages
        </a>
        <a href="sellerProfile.php" class="nav-item">
          <span class="ni">
            <!-- person/user icon -->
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span> Profile
        </a>


        <div class="nav-group-label">Explore</div>
        <a href="sellerMarketplace.php" class="nav-item active">
          <span class="ni">

            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          </span> Marketplace
        </a>
        <a href="sellerIdeahub.php" class="nav-item">
          <span class="ni">

            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="18" x2="15" y2="18"/><line x1="10" y1="22" x2="14" y2="22"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/></svg>
          </span> Ideas Hub
        </a>
      </div>

    </div>
  </aside>




  <main class="main-content">
    <div class="panel-market">


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
        </form>

        
      </div>
      <div class="search-sep"></div>

      <div class="search-sep"></div>


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




      </div><!-- end products-grid -->

    </div><!-- end panel-market -->

  </main>

</div><!-- end page-shell -->


     
    <label class="sidebar-overlay" for="sidebar-toggle"></label>




</body>
</html>