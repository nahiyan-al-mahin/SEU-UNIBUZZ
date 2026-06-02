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


// GET ALL IDEAS 

$sql      = "SELECT business_idea.Idea_Id, business_idea.Idea, business_idea.Description,
                    business_idea.Funding_Goal, business_idea.Status,
                    user_table.First_Name, user_table.Last_Name,
                    seller.Department,
                    COALESCE(SUM(idea_fund.Amount), 0) AS total_raised
             FROM business_idea
             JOIN seller ON business_idea.Seller_Id = seller.Seller_Id
             JOIN user_table ON seller.User_Id = user_table.User_Id
             LEFT JOIN idea_fund ON idea_fund.Idea_Id = business_idea.Idea_Id
             GROUP BY business_idea.Idea_Id
             ORDER BY business_idea.Idea_Id DESC";
$result   = mysqli_query($conn, $sql);
$allIdeas = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_close($conn);
?>









<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniBuzz — Ideas Hub · Southeast University</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="buyerDash.css">
    <style>
        .funded-badge {
            display: inline-block;
            width: 100%;
            text-align: center;
            padding: .6rem 1rem;
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
            border-radius: 99px;
            font-family: 'Outfit', sans-serif;
            font-size: .84rem;
            font-weight: 600;
            box-sizing: border-box;
        }
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
        <div class="app-content">


        <!-- WELCOME BANNER -->
        <div class="welcome-banner">
            <div class="wb-text">
                <div class="wb-name">Welcome To, Idea Funding Place </div>
                <div class="wb-sub">Southeast University · UniBuzz</div>
            </div>
            <div class="wb-stats">
                <div class="wb-stat">
                    <div class="wb-stat-val">Fund Ideas</div>
                </div>
                <div class="wb-stat">
                    <div class="wb-stat-val">Become business partner</div>
                </div>
                <div class="wb-stat">
                    <div class="wb-stat-val">Help SEU BusinessZone to Grow</div>
                </div>
            </div>
        </div>



            <!--section header at the top-->
            <div class="sec-head">
                <div>
                    <div class="sec-title">Ideas <em>Hub</em></div>
                    <div class="sec-sub">Back a campus startup or find co-founders for your own idea</div>
                </div>
            </div>




            <!--from here starts the ideas card-->
            <div class="ideas-grid">
                <?php if(count($allIdeas) == 0): ?>
                    <div style="text-align:center;padding:3rem;color:var(--muted);">
                        No ideas posted yet.
                    </div>

                <?php else: ?>
                    <?php foreach($allIdeas as $idea): ?>
                    <?php
                        $goal     = $idea['Funding_Goal'];
                        $raised   = $idea['total_raised'];
                        $progress = ($goal > 0) ? round(($raised / $goal) * 100) : 0;
                        $firstLetter = strtoupper(substr($idea['First_Name'], 0, 1));
                    ?>
                    <div class="icard" style="animation-delay:.04s">
                        <div class="icard-title"><?php echo $idea['Idea']; ?></div>
                        <div class="icard-desc"><?php echo $idea['Description']; ?></div>

                        <div class="icard-founder">
                            <div class="founder-avatar"><?php echo $firstLetter; ?></div>
                            <span class="icard-founder-name">
                                <?php echo $idea['First_Name'] . " " . $idea['Last_Name']; ?>
                            </span>
                            <span class="icard-founder-dept"><?php echo $idea['Department']; ?></span>
                        </div>

                        <div class="fund-bar-wrap">
                            <div class="fund-meta">
                                <span>Raised: <span class="raised-amt">&#2547;<?php echo number_format($raised, 2); ?></span></span>
                                <span class="goal-amt">Goal: &#2547;<?php echo number_format($goal, 2); ?></span>
                            </div>
                            <div class="fund-bar">
                                <div class="fund-fill" style="width:<?php echo min($progress, 100); ?>%"></div>
                            </div>
                        </div>

                        <div class="icard-footer">
                            <?php if($idea['Status'] === 'Funded'): ?>
                                <div class="funded-badge">Fully Funded — ৳<?php echo number_format($raised, 2); ?> raised</div>
                            <?php else: ?>
                                <a href="buyerFund.php?idea=<?php echo $idea['Idea_Id']; ?>" class="btn-fund">
                                    Fund This Idea
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>



</body>
</html>