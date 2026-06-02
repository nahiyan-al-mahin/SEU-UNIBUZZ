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
$sql      = "SELECT seller.Seller_Id, seller.Rating, seller.Department, seller.Semester,seller.University_Email,seller.Semester,seller.Student_Id,
                    user_table.First_Name, user_table.Last_Name,user_table.Mobile
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

mysqli_close($conn);
?>








<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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

    /* Show the hamburger label on mobile */
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
    <span class="current">Profile</span>
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
        <a href="sellerProfile.php" class="nav-item active">
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

            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="18" x2="15" y2="18"/><line x1="10" y1="22" x2="14" y2="22"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/></svg>
          </span> Ideas Hub
        </a>
      </div>

    </div>
  </aside>


  <main class="main-content">


    <div class="sec-head">
      <div>
        <div class="sec-title">Seller <em style="font-style:italic; color:var(--maroon);">Profile</em></div>
        <div class="sec-sub">Your account details at Southeast University</div>
      </div>
    </div>


    <div class="search-sep"></div>
    <div class="search-sep"></div>


    <div class="profile-card">


      <div class="profile-top">


        <div class="profile-avatar">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="8" r="4" fill="rgba(255,255,255,0.9)"/>
            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="rgba(255,255,255,0.9)" stroke-width="1.8" stroke-linecap="round" fill="none"/>
          </svg>
        </div>

        <div class="profile-top-info">

          <div class="profile-name"><?php echo $seller['First_Name'] . " " . $seller['Last_Name']; ?></div>

          <div class="profile-verified">&#9733; <?php echo $seller['Rating']; ?>  Verified Seller</div>
        </div>

      </div>


      <div class="search-sep" style="margin: 1.2rem 0;"></div>




      <div class="profile-row">

        <div class="profile-label">Seller ID</div>
        <div class="profile-value profile-mono">UB-2026-<?php echo $seller['Seller_Id']; ?></div>
      </div>


      <div class="profile-row">
        <div class="profile-label">University Email</div>
        <div class="profile-value profile-mono"><?php echo $seller['University_Email']; ?></div>
      </div>

      <!-- Rating row -->
      <div class="profile-row">
        <div class="profile-label">Seller Rating</div>
        <div class="profile-value profile-mono">
        <?php echo $seller['Rating']; ?> / 5.0
        </div>
      </div>

      <!-- Department row -->
      <div class="profile-row">
        <div class="profile-label">Department</div>
        <div class="profile-value"><?php echo $seller['Department']; ?>  Southeast University</div>
      </div>

      <!-- Semester row -->
      <div class="profile-row">
        <div class="profile-label">Semester</div>
        <div class="profile-value profile-mono"><?php echo $seller['Semester']; ?></div>
      </div>

      <!-- Student ID row -->
      <div class="profile-row">
        <div class="profile-label">Student ID</div>
        <div class="profile-value profile-mono"><?php echo $seller['Student_Id']; ?></div>
      </div>

      <!-- Mobile Phone row -->
      <div class="profile-row">
        <div class="profile-label">Mobile Phone</div>
        <div class="profile-value profile-mono"><?php echo $seller['Mobile']; ?></div>
      </div>

    </div><!-- end profile-card -->

  </main><!-- end main content -->

</div><!-- end page-shell -->

<label class="sidebar-overlay" for="sidebar-toggle"></label>
    
</body>
</html>