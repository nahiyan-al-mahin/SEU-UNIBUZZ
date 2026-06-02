<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "buyer")
{
    header("Location: login.php");
    exit();
}

include("database.php");

$userId = $_SESSION["user_id"];

// VALIDATE PRODUCT ID 

if(!isset($_GET['product']) || !is_numeric($_GET['product']))
{
    header("Location: buyerHomepage.php");
    exit();
}

$productId = (int)$_GET['product'];


// BUYER INFO 

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


// GET PRODUCT DETAILS 

$sql    = "SELECT product.Product_Id, product.Product_Name, product.Product_Type,
                  product.Price, product.Stock_Quantity, product.Product_Image,
                  product_category.Category_Name,
                  seller.Seller_Id, seller.Department, seller.Rating AS Seller_Rating,
                  user_table.First_Name, user_table.Last_Name
           FROM product
           LEFT JOIN product_category ON product.Product_Category_Id = product_category.Product_Category_Id
           JOIN seller ON product.Seller_Id = seller.Seller_Id
           JOIN user_table ON seller.User_Id = user_table.User_Id
           WHERE product.Product_Id = $productId";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0)
{
    header("Location: buyerHomepage.php");
    exit();
}

$product = mysqli_fetch_assoc($result);


// GET RATINGS 

$sql         = "SELECT product_rating.Rating_Id, product_rating.Rating,
                       product_rating.Review, product_rating.Buyer_Id,
                       user_table.First_Name, user_table.Last_Name
                FROM product_rating
                JOIN buyer ON product_rating.Buyer_Id = buyer.Buyer_Id
                JOIN user_table ON buyer.User_Id = user_table.User_Id
                WHERE product_rating.Product_Id = $productId
                ORDER BY product_rating.Rating_Id DESC";
$result      = mysqli_query($conn, $sql);
$allRatings  = mysqli_fetch_all($result, MYSQLI_ASSOC);

$totalRatings = count($allRatings);
$avgRating    = 0;
if($totalRatings > 0)
{
    $sum = 0;
    foreach($allRatings as $r) { $sum += $r['Rating']; }
    $avgRating = round($sum / $totalRatings, 1);
}


// CHECK IF BUYER ALREADY RATED 

$sql           = "SELECT Rating_Id, Rating, Review FROM product_rating
                  WHERE Product_Id = $productId AND Buyer_Id = $buyerId";
$result        = mysqli_query($conn, $sql);
$existingRating = mysqli_fetch_assoc($result);


// HANDLE RATING SUBMISSION 

$successMsg = "";
$errorMsg   = "";

if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_rating']))
{
    $rating = (int)$_POST['rating'];
    $review = mysqli_real_escape_string($conn, trim($_POST['review']));

    if($rating < 1 || $rating > 5)
    {
        $errorMsg = "Please select a rating between 1 and 5.";
    }
    else
    {
        if($existingRating)
        {
            // Update existing rating
            $ratingId = $existingRating['Rating_Id'];
            $sql      = "UPDATE product_rating SET Rating = $rating, Review = '$review'
                         WHERE Rating_Id = $ratingId";
            mysqli_query($conn, $sql);
            $successMsg = "Your rating has been updated.";
        }
        else
        {
            // Insert new rating
            $sql = "INSERT INTO product_rating (Product_Id, Buyer_Id, Rating, Review)
                    VALUES ($productId, $buyerId, $rating, '$review')";
            mysqli_query($conn, $sql);
            $successMsg = "Thank you for your rating!";
        }

        // Refresh ratings after submit
        $sql         = "SELECT product_rating.Rating_Id, product_rating.Rating,
                               product_rating.Review, product_rating.Buyer_Id,
                               user_table.First_Name, user_table.Last_Name
                        FROM product_rating
                        JOIN buyer ON product_rating.Buyer_Id = buyer.Buyer_Id
                        JOIN user_table ON buyer.User_Id = user_table.User_Id
                        WHERE product_rating.Product_Id = $productId
                        ORDER BY product_rating.Rating_Id DESC";
        $result      = mysqli_query($conn, $sql);
        $allRatings  = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $totalRatings = count($allRatings);
        $avgRating    = 0;
        if($totalRatings > 0)
        {
            $sum = 0;
            foreach($allRatings as $r) { $sum += $r['Rating']; }
            $avgRating = round($sum / $totalRatings, 1);
        }

        $sql            = "SELECT Rating_Id, Rating, Review FROM product_rating
                           WHERE Product_Id = $productId AND Buyer_Id = $buyerId";
        $result         = mysqli_query($conn, $sql);
        $existingRating = mysqli_fetch_assoc($result);
    }
}

mysqli_close($conn);

// STAR HELPER 

function renderStars($rating, $max = 5)
{
    $out = "";
    for($i = 1; $i <= $max; $i++)
    {
        $out .= $i <= $rating ? "★" : "☆";
    }
    return $out;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniBuzz — <?php echo $product['Product_Name']; ?> · Southeast University</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="buyerDash.css">
    <style>

    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-logo">UniBuzz</div>
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
        <div class="product-page-shell">

            <!-- Back Link -->
            <a href="buyerHomepage.php" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back to Marketplace
            </a>

            <!-- Alert Messages -->
            <?php if($successMsg): ?>
                <div class="alert alert-success"> <?php echo $successMsg; ?></div>
            <?php endif; ?>
            <?php if($errorMsg): ?>
                <div class="alert alert-error"> <?php echo $errorMsg; ?></div>
            <?php endif; ?>

            <!-- Product Hero -->
            <div class="product-hero">
                <div class="ph-image-wrap">
                    <?php if($product['Product_Image']): ?>
                        <img src="<?php echo $product['Product_Image']; ?>" alt="<?php echo $product['Product_Name']; ?>">
                    <?php else: ?>
                        <div class="ph-image-placeholder">📦</div>
                    <?php endif; ?>
                </div>

                <div class="ph-details">
                    <div class="ph-category"><?php echo $product['Category_Name'] ?? 'Uncategorized'; ?></div>
                    <div class="ph-name"><?php echo $product['Product_Name']; ?></div>
                    <div class="ph-price">৳<?php echo number_format($product['Price'], 2); ?></div>

                    <div class="ph-avg-row">
                        <span class="ph-stars"><?php echo renderStars(round($avgRating)); ?></span>
                        <span class="ph-avg-num"><?php echo $avgRating > 0 ? $avgRating : '—'; ?></span>
                        <span class="ph-total-ratings">(<?php echo $totalRatings; ?> <?php echo $totalRatings === 1 ? 'review' : 'reviews'; ?>)</span>
                    </div>

                    <div class="ph-meta-row">
                        <span class="ph-badge"><?php echo $product['Product_Type']; ?></span>
                        <?php if($product['Stock_Quantity'] > 0): ?>
                            <span class="ph-badge instock">In Stock · <?php echo $product['Stock_Quantity']; ?> left</span>
                        <?php else: ?>
                            <span class="ph-badge outstock">Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <div class="ph-divider"></div>

                    <div class="ph-seller-row">
                        <div class="seller-avatar"><?php echo strtoupper(substr($product['First_Name'], 0, 1)); ?></div>
                        <div>
                            <div class="seller-name"><?php echo $product['First_Name'] . " " . $product['Last_Name']; ?></div>
                            <div class="seller-dept"><?php echo $product['Department']; ?></div>
                        </div>
                    </div>

                    <div class="ph-divider"></div>

                    <?php if($product['Stock_Quantity'] > 0): ?>
                        <a href="buyerHomepage.php?add=<?php echo $product['Product_Id']; ?>" class="btn-add-car">
                            🛒 Add to Cart
                        </a>
                    <?php else: ?>
                        <span class="btn-add-cart outstock">Out of Stock</span>
                    <?php endif; ?>
                </div>
            </div>


            <!-- Rating Form -->
            <div class="rating-form-card">
                <div class="rating-form-title">
                    <?php echo $existingRating ? 'Update Your Rating' : 'Rate This Product'; ?>
                </div>
                <div class="rating-form-sub">
                    <?php echo $existingRating ? 'You already rated this product. You can update your rating below.' : 'Share your experience with other buyers.'; ?>
                </div>

                <form method="POST" action="">
                    <div class="star-selector">
                        <?php
                        $currentRating = $existingRating ? $existingRating['Rating'] : 0;
                        for($i = 5; $i >= 1; $i--):
                        ?>
                        <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>"
                            <?php echo ($currentRating == $i) ? 'checked' : ''; ?>>
                        <label for="star<?php echo $i; ?>">★</label>
                        <?php endfor; ?>
                    </div>

                    <textarea
                        name="review"
                        class="review-textarea"
                        placeholder="Write a review (optional)..."
                    ><?php echo $existingRating ? htmlspecialchars($existingRating['Review']) : ''; ?></textarea>

                    <button type="submit" name="submit_rating" class="btn-rate-submit">
                        <?php echo $existingRating ? ' Update Rating' : ' Submit Rating'; ?>
                    </button>
                </form>
            </div>


            <!-- Reviews List -->
            <div class="reviews-card">
                <div class="reviews-title">Reviews (<?php echo $totalRatings; ?>)</div>

                <?php if($totalRatings === 0): ?>
                    <div class="no-reviews">No reviews yet — be the first to rate this product!</div>
                <?php else: ?>
                    <?php foreach($allRatings as $r): ?>
                    <div class="review-row">
                        <div class="review-top">
                            <div class="review-avatar"><?php echo strtoupper(substr($r['First_Name'], 0, 1)); ?></div>
                            <div class="review-name">
                                <?php echo $r['First_Name'] . " " . $r['Last_Name']; ?>
                                <?php if($r['Buyer_Id'] == $buyerId): ?>
                                    <span class="your-rating-badge">You</span>
                                <?php endif; ?>
                            </div>
                            <div class="review-stars"><?php echo renderStars($r['Rating']); ?></div>
                        </div>
                        <?php if($r['Review'] !== '' && $r['Review'] !== null): ?>
                            <div class="review-text"><?php echo htmlspecialchars($r['Review']); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>
</html>