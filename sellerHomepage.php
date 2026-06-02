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


//UPLOAD PRODUCT 

if(isset($_POST["upload_product"]))
{
    $productName     = $_POST["product_name"];
    $productCategory = $_POST["category"];
    $quantity        = $_POST["quantity"];
    $price           = $_POST["price"];
    $delivery        = $_POST["delivery"];
    $contact         = $_POST["contact"];

    // handle image upload
    $imagePath = NULL;
    if(isset($_FILES["product_image"]) && $_FILES["product_image"]["error"] == 0)
    {
        $uploadDir  = "productImages/";
        $fileName   = time() . "_" . basename($_FILES["product_image"]["name"]);
        $targetPath = $uploadDir . $fileName;
        if(move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetPath))
        {
            $imagePath = $targetPath;
        }
    }

    $sql = "INSERT INTO product(Seller_Id, Product_Name, Product_Type, Price, Stock_Quantity, Product_Image, Product_Category_Id)
            VALUES ($sellerId, '$productName', '$delivery', '$price', '$quantity', '$imagePath', '$productCategory')";
    mysqli_query($conn, $sql);
    header("Location: sellerHomepage.php");
    exit();
}


// UPLOAD IDEA

if(isset($_POST["upload_idea"]))
{
    $ideaName = $_POST["idea_name"];
    $ideaDesc = $_POST["idea_desc"];
    $ideaFund = $_POST["fund_needed"];

    $sql = "INSERT INTO business_idea(Seller_Id, Idea, Description, Funding_Goal, Status)
            VALUES ($sellerId, '$ideaName', '$ideaDesc', '$ideaFund', 'Pending')";
    mysqli_query($conn, $sql);
    header("Location: sellerHomepage.php");
    exit();
}


//DELETE PRODUCT 

if(isset($_POST["delete_product"]))
{
    $productId = $_POST["product_id"];
    mysqli_query($conn, "DELETE FROM product WHERE Product_Id=$productId AND Seller_Id=$sellerId");
    header("Location: sellerHomepage.php");
    exit();
}


// STATS

// total revenue from completed orders
$sql          = "SELECT SUM(`order`.Total_amount) AS total
                 FROM `order`
                 JOIN product_orders ON `order`.Order_Id = product_orders.Order_Id
                 JOIN product ON product_orders.Product_Id = product.Product_Id
                 WHERE product.Seller_Id = $sellerId AND `order`.Status = 'Completed'";
$result       = mysqli_query($conn, $sql);
$row          = mysqli_fetch_assoc($result);
$totalRevenue = $row['total'] ?? 0;

// total active listings
$sql           = "SELECT COUNT(*) AS total FROM product WHERE Seller_Id=$sellerId";
$result        = mysqli_query($conn, $sql);
$row           = mysqli_fetch_assoc($result);
$totalListings = $row['total'];

// total orders fulfilled
$sql             = "SELECT COUNT(DISTINCT `order`.Order_Id) AS total
                    FROM `order`
                    JOIN product_orders ON `order`.Order_Id = product_orders.Order_Id
                    JOIN product ON product_orders.Product_Id = product.Product_Id
                    WHERE product.Seller_Id = $sellerId AND `order`.Status = 'Completed'";
$result          = mysqli_query($conn, $sql);
$row             = mysqli_fetch_assoc($result);
$ordersFulfilled = $row['total'];

// seller rating
$sellerRating = $seller['Rating'];


// PRODUCT LISTINGS 

$sql         = "SELECT product.Product_Id, product.Product_Name, product.Price,
                       product_category.Category_Name
                FROM product
                LEFT JOIN product_category ON product.Product_Category_Id = product_category.Product_Category_Id
                WHERE product.Seller_Id = $sellerId";
$result      = mysqli_query($conn, $sql);
$myProducts  = mysqli_fetch_all($result, MYSQLI_ASSOC);


// CATEGORIES FOR DROPDOWN 

$sql        = "SELECT * FROM product_category";
$result     = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);


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
<title>UniBuzz — Seller Dashboard · Southeast University</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="sellerDash.css">
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
    z-index: 88 !important;
  }

  @media (max-width: 860px) {
    .hamburger-label { display: flex; }
    .sidebar-overlay {
      display: none;
      position: fixed; inset: 0;       /* covers the entire screen */
      background: rgba(0,0,0,0.45);    /* semi-transparent dark backdrop */
      z-index: 89;                      /* below sidebar (z-index 90) but above page content */
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
    <span class="current">Seller Dashboard</span>
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
        <a href="sellerHomepage.php" class="nav-item active">
          <span class="ni">
            <!-- shop/store icon -->
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </span> My Dashboard
        </a>
        
        
        <!-- ni-badge shows pending orders -->
        <a href="sellerOrder.php" class="nav-item">
          <span class="ni">
            <!-- shopping cart icon -->
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



  <main class="main">

    <div class="page-header">
      <div>
        <!-- "Seller Dashboard" — "Dashboard" is italic and maroon -->
        <div class="page-title">Seller Dashboard</div>
        <div class="page-sub">Manage your listings, track orders, and grow your campus shop.</div>
      </div>
      <div class="hdr-actions">
        <a href="#list-product-form" class="btn-primary">+ List New Product</a>
      </div>
    </div>

    <div class="stats-strip">
      <div class="stat-card sc-maroon">
          <div class="stat-label">Total Revenue</div>
          <div class="stat-value">&#2547;<?php echo number_format($totalRevenue, 2); ?></div>
      </div>
      <div class="stat-card sc-gold">
          <div class="stat-label">Active Listings</div>
          <div class="stat-value"><?php echo $totalListings; ?></div>
      </div>
      <div class="stat-card sc-green">
          <div class="stat-label">Orders Fulfilled</div>
          <div class="stat-value"><?php echo $ordersFulfilled; ?></div>
      </div>
      <div class="stat-card sc-ink">
          <div class="stat-label">Seller Rating</div>
          <div class="stat-value"><?php echo $sellerRating; ?></div>
      </div>
    </div>



    <div class="content-grid">

      <div class="card" style="flex:0 0 420px; min-width:0;">
        <div class="card-header">
          <div>
            <div class="card-title">List a New Product</div>
            <div class="card-sub">Fill in the details and your item goes live instantly.</div>
          </div>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" id="list-product-form">

          <div class="section-label">Product Photos</div>
          <div class="upload-zone">
            <input type="file" name="product_image" accept="image/*">
            <div class="upload-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#8C6B72" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </div>
            <div class="upload-title">Click or drag photos here</div>
            <div class="upload-sub">JPG, PNG · Max 5MB each </div>
            </div>
            
            
          <div class="section-label">Basic Information</div>

          <div class="fld">
            <label>Product Name</label>
            <div class="fld-inner">
              <input type="text" name="product_name" placeholder="e.g. Handmade Leather Notebook" required>
            </div>
          </div>
          <div class="fld-row">
          <div class="fld">
              <label>Category</label>
              <select name="category" required>
                  <option value="" disabled selected>Select category</option>
                  <?php foreach($categories as $cat): ?>
                  <option value="<?php echo $cat['Product_Category_Id']; ?>">
                      <?php echo $cat['Category_Name']; ?>
                  </option>
                  <?php endforeach; ?>
              </select>
          </div>
            <div class="fld">
              <label>Quantity Available</label>
              <div class="fld-inner">
                <!-- hash icon to represent number/quantity -->
                <span class="fld-icon">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>
                </span>
                <!-- type="number" + min="1" stops 0 or negative values -->
                <input type="number" name="quantity" placeholder="1" min="1">
              </div>
            </div>
          </div>
          <div class="section-label">Pricing &amp; Delivery</div>

          <div class="fld-row">
            <div class="fld">
              <label>Price (BDT)</label>
              <div class="fld-inner price-wrap">
                <span class="price-prefix">&#2547;</span>
                <input type="number" name="price" placeholder="250" min="0" required>
              </div>
            </div>
          </div>

          <div class="fld">
            <label>Delivery Method</label>
            <div class="fld-inner">
              <select class="no-icon" name="delivery" style="padding-left:1rem">
                <option>Campus pickup only</option>
              </select>
            </div>
          </div>

          <div class="fld">
            <label>Contact Note</label>
            <div class="fld-inner">
              <input type="text" name="contact" placeholder="e.g. WhatsApp: 01XXXXXXXXX">
            </div>
          </div>

          <div style="display:flex; gap:0.75rem; margin-top:1.3rem;">
            <button type="submit" name="upload_product" class="btn-primary">List Product →</button>
          </div>

        </form>
      </div>






      <div class="right-col">

        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">My Listings</div>
              <div class="card-sub">5 products</div>
            </div>
          </div>

          <div class="card-sub"><?php echo $totalListings; ?> products</div>

          <?php foreach($myProducts as $product): ?>
          <div class="listing-item">
              <div class="listing-info">
                  <div class="listing-name"><?php echo $product['Product_Name']; ?></div>
                  <div class="listing-meta">
                      <span class="listing-cat-tag"><?php echo $product['Category_Name'] ?? '—'; ?></span>
                  </div>
              </div>
              <div class="listing-price">&#2547;<?php echo number_format($product['Price'], 2); ?></div>
              <div class="listing-actions">
                  <form method="POST">
                      <input type="hidden" name="product_id" value="<?php echo $product['Product_Id']; ?>">
                      <button type="submit" name="delete_product" class="la-btn del" title="Delete">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                      </button>
                  </form>
              </div>
          </div>
          <?php endforeach; ?>

        </div>

        <div class="idea-form-card">

          <div class="card-header">
            <div>
              <div class="card-title">Submit Your Idea</div>
              <div class="card-sub">Share a startup idea with SEU students</div>
            </div>
          </div>


            <form action="" method="POST">
                <div class="fld">
                    <label class="fld-label">Idea Name</label>
                    <input type="text" name="idea_name" class="fld-input" placeholder="e.g. Campus Ride Share" required>
                </div>
                <div class="fld">
                    <label class="fld-label">Idea Description</label>
                    <textarea name="idea_desc" class="fld-input fld-textarea" placeholder="Describe your idea..."></textarea>
                </div>
                <div class="fld">
                    <label class="fld-label">Fund Needed</label>
                    <input type="number" name="fund_needed" placeholder="e.g. 20000">
                </div>
                <button type="submit" name="upload_idea" class="btn-primary">Submit Idea →</button>
            </form>

        </div>

        <div class="card">
          <div class="card-header">
            <div class="card-title">Seller Tips</div>
          </div>
          <div class="tips-list">
            <div class="tip-item">
              <div class="tip-num">1</div> 
              <div>
                <div class="tip-item-title">Use real, clear photos</div>
                <div class="tip-item-text">Listings with 3+ original photos sell 2x faster than those with none.</div>
              </div>
            </div>
            <div class="tip-item">
              <div class="tip-num">2</div>
              <div>
                <div class="tip-item-title">Price fairly — check similar listings</div>
                <div class="tip-item-text">Browse the marketplace first to see what others charge for similar items.</div>
              </div>
            </div>
            <div class="tip-item">
              <div class="tip-num">3</div>
              <div>
                <div class="tip-item-title">Respond fast to buyers</div>
                <div class="tip-item-text">Fast replies boost your seller rating and build campus trust quickly.</div>
              </div>
            </div>
          </div>
        </div>

      </div><!-- end right column -->

    </div><!-- end content-grid -->

  </main><!-- end main -->

</div><!-- end page-shell -->
<label class="sidebar-overlay" for="sidebar-toggle"></label>      



</body>
</html>