<!--#################### From here PHP starts #################### -->
<?php
$result=null;
$result2=null;
include("database.php");


$sql2="SELECT * FROM business_idea ORDER BY RAND()";  #This is for all idea


$result2=mysqli_query($conn,$sql2); #This result is for idea

// These result is for ideas
$sql5="SELECT First_Name as Seller_Name FROM user_table join
        business_idea
        WHERE user_table.Seller_Id=business_idea.Seller_Id";  #This is for user name on idea
$sql6="SELECT Department FROM seller join
        business_idea
        WHERE seller.Seller_Id=business_idea.Seller_Id"; #This is for user dept on idea
$result5=mysqli_query($conn,$sql5);
$result6=mysqli_query($conn,$sql6);

// get all ideas with seller name, total raised amount and progress
$sql      = "SELECT business_idea.Idea_Id, business_idea.Idea, business_idea.Description,
                    business_idea.Funding_Goal, business_idea.Status,
                    user_table.First_Name, user_table.Last_Name,
                    seller.Department,
                    COALESCE(SUM(idea_fund.Amount), 0) AS total_raised
             FROM business_idea
             JOIN seller ON business_idea.Seller_Id = seller.Seller_Id
             JOIN user_table ON seller.User_Id = user_table.User_Id
             LEFT JOIN idea_fund ON idea_fund.Fund_id = business_idea.Idea_Id
             GROUP BY business_idea.Idea_Id";
$result   = mysqli_query($conn, $sql);
$allIdeas = mysqli_fetch_all($result, MYSQLI_ASSOC);


?>


<!--#################### From here HTML starts #################### -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SEU UniBuzz Home</title>
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
<div class="nav-middle">
  <a href="index.php" class="nav-option">Home</a>
  <a href="marketplaceG.php" class="nav-option">Markectplace</a>
</div>

</nav>


<div class="page-shell">
  <div class="app-content">


      <!-- Ideas hub section-->
      <div class="panel-ideas">

        <!-- This is for the header section-->
        <div class="sec-head">

          <!-- This is for the title and subtitle -->
          <div>
            <div class="sec-title">Ideas Hub</div>
            <div class="sec-sub">Back a campus startup or find co-founders for SEU students</div>
          </div>        
        </div>

        <!-- dashed line -->
        <div class="dashed"></div>

        <!-- ideas grid -->
        <div class="ideas-grid">

        <?php foreach($allIdeas as $idea): ?>
        <?php
            $goal     = $idea['Funding_Goal'];
            $raised   = $idea['total_raised'];
            $progress = ($goal > 0) ? round(($raised / $goal) * 100) : 0;
        ?>
        <div class="icard" style="animation-delay: .04s;">
            <div class="icard-title"><?php echo $idea['Idea']; ?></div>
            <div class="icard-desc"><?php echo $idea['Description']; ?></div>
            <div class="icard-founder">
                <div class="icard-founder-name"><?php echo $idea['First_Name'] . " " . $idea['Last_Name']; ?></div>
                <div class="icard-founder-dept"><?php echo $idea['Department']; ?> Dept.</div>
            </div>
            <div class="fund-bar-wrap">
                <div class="fund-meta">
                    <span>Raised: <strong>&#2547;<?php echo number_format($raised, 2); ?></strong></span>
                    <span>Goal: &#2547;<?php echo number_format($goal, 2); ?></span>
                </div>
                <div class="fund-bar">
                    <div class="fund-fill" style="width:<?php echo $progress; ?>%"></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        </div>

      </div>


      <!--This is a horizontal line div-->
      <div class="search-sep2"></div>
      <!--This is a horizontal line div-->
      <div class="search-sep3"></div>


      
    </div>  
</div>






</body>
</html>