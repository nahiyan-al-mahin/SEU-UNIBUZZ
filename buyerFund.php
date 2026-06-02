<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "buyer")
{
    header("Location: login.php");
    exit();
}
 
include("database.php");
 
$userId = $_SESSION["user_id"];
 
// VALIDATE IDEA ID 
 
if(!isset($_GET['idea']) || !is_numeric($_GET['idea']))
{
    header("Location: buyerIdea.php");
    exit();
}
 
$ideaId = (int)$_GET['idea'];
 
 
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
 
 
// CART COUNT FOR NAVBAR 
 
$sql         = "SELECT COUNT(*) AS total FROM add_to_cart WHERE Cart_Id = $cartId";
$result      = mysqli_query($conn, $sql);
$row         = mysqli_fetch_assoc($result);
$totalInCart = $row['total'];
 
 
// GET IDEA DETAILS 
 
$sql    = "SELECT business_idea.Idea_Id, business_idea.Idea, business_idea.Description,
                  business_idea.Funding_Goal, business_idea.Status,
                  user_table.First_Name, user_table.Last_Name,
                  seller.Department,
                  COALESCE(SUM(idea_fund.Amount), 0) AS total_raised
           FROM business_idea
           JOIN seller ON business_idea.Seller_Id = seller.Seller_Id
           JOIN user_table ON seller.User_Id = user_table.User_Id
           LEFT JOIN idea_fund ON idea_fund.Idea_Id = business_idea.Idea_Id
           WHERE business_idea.Idea_Id = $ideaId
           GROUP BY business_idea.Idea_Id";
$result = mysqli_query($conn, $sql);
 
if(mysqli_num_rows($result) == 0)
{
    header("Location: buyerIdea.php");
    exit();
}
 
$idea = mysqli_fetch_assoc($result);
 
 
// GET FUNDERS LIST 
 
$sql      = "SELECT user_table.First_Name, user_table.Last_Name,
                    idea_fund.Amount
             FROM idea_fund
             JOIN buyer ON idea_fund.Buyer_Id = buyer.Buyer_Id
             JOIN user_table ON buyer.User_Id = user_table.User_Id
             WHERE idea_fund.Idea_Id = $ideaId
             ORDER BY idea_fund.Fund_id DESC
             LIMIT 5";
$result   = mysqli_query($conn, $sql);
$funders  = mysqli_fetch_all($result, MYSQLI_ASSOC);
 
 
// HANDLE FUND SUBMISSION 
 
$successMsg = "";
$errorMsg   = "";
 
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['fund_amount']))
{
    $amount = floatval($_POST['fund_amount']);
 
    if($amount <= 0)
    {
        $errorMsg = "Please enter a valid amount greater than 0.";
    }
    else
    {
        $sql    = "INSERT INTO idea_fund (Idea_Id, Buyer_Id, Amount, Payment_Method, Payment_Status)
                   VALUES ($ideaId, $buyerId, $amount, 'Online', 'Completed')";
        $result = mysqli_query($conn, $sql);
 
        if($result)
        {
            $newFundId = mysqli_insert_id($conn);
 
            // Insert into transaction table
            $sql = "INSERT INTO transaction (Fund_id, Idea_Id) VALUES ($newFundId, $ideaId)";
            mysqli_query($conn, $sql);
 
            // Check if total raised has reached the funding goal
            $sql         = "SELECT COALESCE(SUM(Amount), 0) AS total_raised FROM idea_fund WHERE Idea_Id = $ideaId";
            $result      = mysqli_query($conn, $sql);
            $row         = mysqli_fetch_assoc($result);
            $totalRaised = $row['total_raised'];
 
            $sql    = "SELECT Funding_Goal FROM business_idea WHERE Idea_Id = $ideaId";
            $result = mysqli_query($conn, $sql);
            $row    = mysqli_fetch_assoc($result);
            $goal   = $row['Funding_Goal'];
 
            if($totalRaised >= $goal)
            {
                $sql = "UPDATE business_idea SET Status = 'Funded' WHERE Idea_Id = $ideaId";
                mysqli_query($conn, $sql);
            }
 
            $successMsg = "Thank you! Your funding of ৳" . number_format($amount, 2) . " has been recorded.";
 
            // Refresh idea data after funding
            $sql    = "SELECT business_idea.Idea_Id, business_idea.Idea, business_idea.Description,
                              business_idea.Funding_Goal, business_idea.Status,
                              user_table.First_Name, user_table.Last_Name,
                              seller.Department,
                              COALESCE(SUM(idea_fund.Amount), 0) AS total_raised
                       FROM business_idea
                       JOIN seller ON business_idea.Seller_Id = seller.Seller_Id
                       JOIN user_table ON seller.User_Id = user_table.User_Id
                       LEFT JOIN idea_fund ON idea_fund.Idea_Id = business_idea.Idea_Id
                       WHERE business_idea.Idea_Id = $ideaId
                       GROUP BY business_idea.Idea_Id";
            $result = mysqli_query($conn, $sql);
            $idea   = mysqli_fetch_assoc($result);
        }
        else
        {
            $errorMsg = "Something went wrong. Please try again.";
        }
    }
}
 
mysqli_close($conn);
 
$goal       = $idea['Funding_Goal'];
$raised     = $idea['total_raised'];
$progress   = ($goal > 0) ? min(round(($raised / $goal) * 100), 100) : 0;
$remaining  = max($goal - $raised, 0);
$firstLetter = strtoupper(substr($idea['First_Name'], 0, 1));
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniBuzz — Fund an Idea · Southeast University</title>
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
            <a href="buyerHomepage.php" class="nav-tab">Marketplace</a>
            <a href="buyerIdea.php" class="nav-tab active">Ideas Hub</a>
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
        <div class="fund-page-shell">
 
            <!-- Back Link -->
            <a href="buyerIdea.php" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back to Ideas Hub
            </a>
 
            <!-- Alert Messages -->
            <?php if($successMsg): ?>
                <div class="alert alert-success">
                     <?php echo $successMsg; ?>
                </div>
            <?php endif; ?>
            <?php if($errorMsg): ?>
                <div class="alert alert-error">
                     <?php echo $errorMsg; ?>
                </div>
            <?php endif; ?>
 
            <!-- Idea Hero Card -->
            <div class="idea-hero">
                <div class="idea-hero-title"><?php echo $idea['Idea']; ?></div>
                <div class="idea-hero-desc"><?php echo $idea['Description']; ?></div>
                <div class="idea-founder-row">
                    <div class="founder-avatar-lg"><?php echo $firstLetter; ?></div>
                    <div>
                        <div class="founder-info-name"><?php echo $idea['First_Name'] . " " . $idea['Last_Name']; ?></div>
                        <div class="founder-info-dept"><?php echo $idea['Department']; ?></div>
                    </div>
                    <span class="status-badge <?php echo strtolower($idea['Status']) !== 'active' ? 'inactive' : ''; ?>">
                        <?php echo $idea['Status']; ?>
                    </span>
                </div>
            </div>
 
 
            <!-- Funding Stats -->
            <div class="fund-stats-row">
                <div class="fund-stat-card">
                    <div class="fund-stat-label">Total Raised</div>
                    <div class="fund-stat-val raised-color">৳<?php echo number_format($raised, 0); ?></div>
                </div>
                <div class="fund-stat-card">
                    <div class="fund-stat-label">Funding Goal</div>
                    <div class="fund-stat-val">৳<?php echo number_format($goal, 0); ?></div>
                </div>
                <div class="fund-stat-card">
                    <div class="fund-stat-label">Still Needed</div>
                    <div class="fund-stat-val remain-color">৳<?php echo number_format($remaining, 0); ?></div>
                </div>
            </div>
 
 
            <!-- Progress Bar -->
            <div class="progress-wrap">
                <div class="progress-label-row">
                    <span class="progress-label">Funding Progress</span>
                    <span class="progress-pct"><?php echo $progress; ?>% funded</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width:<?php echo $progress; ?>%"></div>
                </div>
            </div>
 
 
            <!-- Fund Form -->
            <div class="fund-form-card">
                <div class="fund-form-title">Support This Idea</div>
                <div class="fund-form-sub">Choose a quick amount or enter your own. Every taka counts!</div>
 
                <!-- Quick Amount Chips — each is its own mini form so no JS needed -->
                <?php
                $quickAmounts   = [100, 250, 500, 1000, 2500, 5000];
                $selectedQuick  = isset($_GET['quick']) ? (int)$_GET['quick'] : 0;
                ?>
                <div class="quick-amounts">
                    <?php foreach($quickAmounts as $qa): ?>
                    <form method="GET" action="" style="margin:0;display:inline;">
                        <input type="hidden" name="idea"  value="<?php echo $ideaId; ?>">
                        <input type="hidden" name="quick" value="<?php echo $qa; ?>">
                        <button type="submit" class="qa-chip <?php echo ($selectedQuick === $qa) ? 'selected' : ''; ?>">
                            ৳<?php echo number_format($qa); ?>
                        </button>
                    </form>
                    <?php endforeach; ?>
                </div>
 
                <form method="POST" action="">
                    <div class="custom-amount-wrap">
                        <span class="currency-sym">৳</span>
                        <input
                            type="number"
                            name="fund_amount"
                            class="fund-input"
                            placeholder="Enter custom amount"
                            min="1"
                            step="1"
                            required
                            value="<?php echo ($selectedQuick > 0) ? $selectedQuick : ''; ?>"
                        >
                    </div>
                    <button type="submit" class="btn-fund-submit">💡 Fund This Idea</button>
                </form>
            </div>
 
 
            <!-- Recent Funders -->
            <div class="funders-card">
                <div class="funders-title">Recent Supporters</div>
 
                <?php if(count($funders) == 0): ?>
                    <div class="no-funders">No funders yet — be the first to support this idea!</div>
                <?php else: ?>
                    <?php foreach($funders as $funder): ?>
                    <div class="funder-row">
                        <div class="funder-avatar-sm"><?php echo strtoupper(substr($funder['First_Name'], 0, 1)); ?></div>
                        <div>
                            <div class="funder-name"><?php echo $funder['First_Name'] . " " . $funder['Last_Name']; ?></div>
                        </div>
                        <div class="funder-amount">৳<?php echo number_format($funder['Amount'], 2); ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
 
        </div>
    </div>





</body>
</html>