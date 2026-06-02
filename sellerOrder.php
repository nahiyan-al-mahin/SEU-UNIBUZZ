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


//  STATS 

// total orders
$sql         = "SELECT COUNT(DISTINCT `order`.Order_Id) AS total
                FROM `order`
                JOIN product_orders ON `order`.Order_Id = product_orders.Order_Id
                JOIN product ON product_orders.Product_Id = product.Product_Id
                WHERE product.Seller_Id = $sellerId";
$result      = mysqli_query($conn, $sql);
$row         = mysqli_fetch_assoc($result);
$totalOrders = $row['total'];

// pending orders
$sql           = "SELECT COUNT(DISTINCT `order`.Order_Id) AS total
                  FROM `order`
                  JOIN product_orders ON `order`.Order_Id = product_orders.Order_Id
                  JOIN product ON product_orders.Product_Id = product.Product_Id
                  WHERE product.Seller_Id = $sellerId AND `order`.Status = 'Pending'";
$result        = mysqli_query($conn, $sql);
$row           = mysqli_fetch_assoc($result);
$totalPending  = $row['total'];

// completed orders
$sql            = "SELECT COUNT(DISTINCT `order`.Order_Id) AS total
                   FROM `order`
                   JOIN product_orders ON `order`.Order_Id = product_orders.Order_Id
                   JOIN product ON product_orders.Product_Id = product.Product_Id
                   WHERE product.Seller_Id = $sellerId AND `order`.Status = 'Completed'";
$result         = mysqli_query($conn, $sql);
$row            = mysqli_fetch_assoc($result);
$totalCompleted = $row['total'];

// total earnings from completed orders
$sql          = "SELECT SUM(`order`.Total_amount) AS total
                 FROM `order`
                 JOIN product_orders ON `order`.Order_Id = product_orders.Order_Id
                 JOIN product ON product_orders.Product_Id = product.Product_Id
                 WHERE product.Seller_Id = $sellerId AND `order`.Status = 'Completed'";
$result       = mysqli_query($conn, $sql);
$row          = mysqli_fetch_assoc($result);
$totalEarned  = $row['total'] ?? 0;


// ORDER LIST 

$sql       = "SELECT `order`.Order_Id, `order`.Order_Date, `order`.Status, `order`.Total_amount,
                     product.Product_Name,
                     user_table.First_Name, user_table.Last_Name
              FROM `order`
              JOIN product_orders ON `order`.Order_Id = product_orders.Order_Id
              JOIN product ON product_orders.Product_Id = product.Product_Id
              JOIN buyer ON `order`.Buyer_Id = buyer.Buyer_Id
              JOIN user_table ON buyer.User_Id = user_table.User_Id
              WHERE product.Seller_Id = $sellerId
              ORDER BY `order`.Order_Date DESC";
$result    = mysqli_query($conn, $sql);
$allOrders = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_close($conn);
?>










<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Received</title>
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

<!--  NAVBAR -->
<nav class="navbar">

<label class="hamburger-label" for="sidebar-toggle">
  <span></span> <!-- top bar -->
  <span></span> <!-- middle bar -->
  <span></span> <!-- bottom bar -->
</label>
  <div class="nav-logo">Uni<em>Buzz</em></div>

  <div class="nav-sep"></div>

  <div class="nav-breadcrumb">
    <a href="#">Home</a>
    <span class="arrow">›</span>
    <span class="current">Order Received</span>
  </div>

  <!-- Right side  navbar-->
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
<!-- PAGE SHELL -->
<div class="page-shell">

  <!-- SIDEBAR-->
  <aside class="sidebar">
    <div class="sidebar-content">

      <!-- seller profile-->
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

        
        <a href="sellerOrder.php" class="nav-item active">
          <span class="ni">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          </span> Orders Received
          <span class="ni-badge"><?php echo $totalOrders; ?></span>
        </a>

        <!-- account section links -->
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
        <a href="sellerMarketplace.php" class="nav-item">
          <span class="ni">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          </span> Marketplace
        </a>
        <a href="sellerIdeahub.php" class="nav-item">
          <span class="ni">
            <!-- lightbulb icon -->
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="18" x2="15" y2="18"/><line x1="10" y1="22" x2="14" y2="22"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/></svg>
          </span> Ideas Hub
        </a>
      </div>


    </div>
  </aside>
  <!--  MAIN CONTENT -->
  <main class="main-content">

    <!-- page title row -->
    <div class="sec-head">
      <div>
        <!-- "Orders" plain, "Received" italic maroon — matches marketplace title style -->
        <div class="sec-title">Orders <em style="font-style:italic; color:var(--maroon);">Received</em></div>
        <div class="sec-sub">Review and manage orders placed with you</div>
      </div>
    </div>


    <div class="search-sep"></div>
    <div class="search-sep"></div>

    <!--STATS ROW -->
    <div class="orders-stats">
        <div class="ostat-card">
            <div class="ostat-label">Total Orders</div>
            <div class="ostat-value"><?php echo $totalOrders; ?></div>
        </div>
        <div class="ostat-card">
            <div class="ostat-label">Pending</div>
            <div class="ostat-value"><?php echo $totalPending; ?></div>
        </div>
        <div class="ostat-card">
            <div class="ostat-label">Completed</div>
            <div class="ostat-value"><?php echo $totalCompleted; ?></div>
        </div>
        <div class="ostat-card">
            <div class="ostat-label">Total Earned</div>
            <div class="ostat-value ostat-taka">&#2547;<?php echo number_format($totalEarned, 2); ?></div>
        </div>
    </div>


    <div class="search-sep"></div>
    <div class="search-sep"></div>

    <div class="orders-list">
        <?php if(count($allOrders) == 0): ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <div class="empty-title">No orders yet</div>
                <div class="empty-sub">Orders will appear here once buyers purchase your products.</div>
            </div>
        <?php else: ?>
            <?php foreach($allOrders as $order): ?>
            <?php
                $statusClass = ($order['Status'] == 'Completed') ? 'orow-completed' : 'orow-pending';
                $badgeClass  = ($order['Status'] == 'Completed') ? 'status-completed' : 'status-pending';
                $date        = date("M j, Y", strtotime($order['Order_Date']));
                $buyerName   = $order['First_Name'] . " " . $order['Last_Name'];
            ?>
            <div class="orow <?php echo $statusClass; ?>">
                <div class="orow-info">
                    <div class="orow-name"><?php echo $order['Product_Name']; ?></div>
                    <div class="orow-date"><?php echo $date; ?></div>
                </div>
                <div class="orow-buyer"><?php echo $buyerName; ?></div>
                <div class="orow-price">&#2547;<?php echo number_format($order['Total_amount'], 2); ?></div>
                <div class="orow-status <?php echo $badgeClass; ?>"><?php echo $order['Status']; ?></div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

  </main><!-- end main content -->

</div><!-- end page-shell -->

<!-- Sidebar overlay -->
<label class="sidebar-overlay" for="sidebar-toggle"></label>
    
</body>
</html>