<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "admin")
{
    header("Location: login.php");
    exit();
}

include("database.php");



//LOGGED IN ADMIN INFO 

$loggedInUserId = $_SESSION["user_id"];

$sql        = "SELECT user_table.First_Name, user_table.Last_Name
               FROM user_table
               WHERE user_table.User_Id = $loggedInUserId";
$result     = mysqli_query($conn, $sql);
$adminInfo  = mysqli_fetch_assoc($result);

$adminFullName   = $adminInfo['First_Name'] . " " . $adminInfo['Last_Name'];
$adminFirstLetter = strtoupper(substr($adminInfo['First_Name'], 0, 1));



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

// delete idea
if(isset($_POST["delete_idea"]))
{
    $del_id = $_POST["delete_idea_id"];
    mysqli_query($conn, "DELETE FROM business_idea WHERE Idea_Id=$del_id");
    header("Location: allIdeas.php");
    exit();
}



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
        <div class="divider-label">All Idea and Fundings</div>
        <div class="divider-line"></div>
      </div>

      <div class="section-grid" style="grid-template-columns:1fr;margin-bottom:1rem;">

        <!-- Ideas Fund Status panel — crowdfunding campaigns er progress -->
        <div class="content-card" style="animation-delay:.28s">
          <div class="card-top">
            <div>
              <!-- "Fund" italic golden hobe -->
              <div class="card-title">Ideas Fund Status</div>
              <div class="card-subtitle">Active campaigns</div>
            </div>
          </div>
          <div class="card-body" style="padding:.5rem .9rem;">
            <table class="users-table">
              <thead>
                <tr>
                  <th>Idea ID</th>
                  <th>Idea</th>
                  <th>Idea Creator</th>
                  <th>Department</th>
                  <th>Funding Goal</th>
                  <th>Raised</th>
                  <th>Progress</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>

                <?php foreach($allIdeas as $idea): ?>
                <?php
                    $goal     = $idea['Funding_Goal'];
                    $raised   = $idea['total_raised'];
                    $progress = ($goal > 0) ? round(($raised / $goal) * 100) : 0;
                ?>
                <tr>
                  <td><span class="mono-text" style="color:var(--text-3)"><?php echo $idea['Idea_Id']; ?></span></td>
                  <td><span class="mono-text" style="color:var(--text-3)"><?php echo $idea['Idea']; ?></span></td>  
                  <td>
                    <div class="user-fullname" style="font-size:.76rem"><?php echo $idea['First_Name'] . " " . $idea['Last_Name']; ?></div>
                  </td>
                  <td><span class="mono-text" style="color:var(--text-3)"><?php echo $idea['Department']; ?></span></td>
                  <td><span class="mono-text" style="color:var(--text-3)">৳<?php echo number_format($idea['Funding_Goal'], 2); ?></span></td>
                  <td><span class="mono-text" style="color:var(--text-3)">৳<?php echo number_format($raised, 2); ?></span></td>
                  <td>
                    <div class="funding-bar-wrap">
                      <div class="funding-bar-track">
                        <div class="funding-bar-fill" style="width:8%;background:var(--maroon)"></div>
                      </div>
                      <span class="funding-percent-text"><?php echo $progress; ?>%</span>
                    </div>
                  </td>
                  <td><span class="status-tag tag-pending"><?php echo $idea['Status']; ?></span></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div><!-- row 2 end -->

    </div><!-- page-content-scroll end -->
  </div><!-- right-side end -->
</div><!-- my-dashboard-wrapper end -->

</body>
</html>