<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "admin")
{
    header("Location: login.php");
    exit();
}

include("database.php");


if(isset($_POST["delete_user"]))
{
    $del_id = $_POST["delete_user_id"];
    mysqli_query($conn, "DELETE FROM user_table WHERE User_Id=$del_id");
    header("Location: adminHomepage.php");
    exit();
}


//LOGGED IN ADMIN INFO 

$loggedInUserId = $_SESSION["user_id"];

$sql        = "SELECT user_table.First_Name, user_table.Last_Name
               FROM user_table
               WHERE user_table.User_Id = $loggedInUserId";
$result     = mysqli_query($conn, $sql);
$adminInfo  = mysqli_fetch_assoc($result);

$adminFullName   = $adminInfo['First_Name'] . " " . $adminInfo['Last_Name'];
$adminFirstLetter = strtoupper(substr($adminInfo['First_Name'], 0, 1));



// Total Users
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_table"))['total'];

// Total Listings (products)
$totalListings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM product"))['total'];

// Total Revenue (completed orders only)
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(Total_amount) AS total FROM `order` WHERE Status='Completed'"))['total'] ?? 0;

// Total Ideas Funded
$totalIdeas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM business_idea WHERE Status='Approved'"))['total'];

// Total Buyers
$totalBuyers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM buyer"))['total'];

// Total Sellers
$totalSellers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM seller"))['total'];

// Total Admins
$sql         = "SELECT COUNT(*) AS total FROM admin";
$result      = mysqli_query($conn, $sql);
$row         = mysqli_fetch_assoc($result);
$totalAdmins = $row['total'];

//ROLE PERCENTAGES 

if($totalUsers > 0)
{
    $buyerPercent  = round(($totalBuyers / $totalUsers) * 100);
    $sellerPercent = round(($totalSellers / $totalUsers) * 100);
    $adminPercent  = round(($totalAdmins / $totalUsers) * 100);
}
else
{
    $buyerPercent  = 0;
    $sellerPercent = 0;
    $adminPercent  = 0;
}


// DEPARTMENT PERCENTAGES 

$sql       = "SELECT COUNT(*) AS total FROM seller WHERE Department='CSE'";
$result    = mysqli_query($conn, $sql);
$row       = mysqli_fetch_assoc($result);
$totalCSE  = $row['total'];

$sql       = "SELECT COUNT(*) AS total FROM seller WHERE Department='EEE'";
$result    = mysqli_query($conn, $sql);
$row       = mysqli_fetch_assoc($result);
$totalEEE  = $row['total'];

$sql       = "SELECT COUNT(*) AS total FROM seller WHERE Department='BBA' OR Department='MBA'";
$result    = mysqli_query($conn, $sql);
$row       = mysqli_fetch_assoc($result);
$totalBBA  = $row['total'];

$totalOtherDept = $totalSellers - $totalCSE - $totalEEE - $totalBBA;

if($totalSellers > 0)
{
    $csePercent   = round(($totalCSE / $totalSellers) * 100);
    $eeePercent   = round(($totalEEE / $totalSellers) * 100);
    $bbaPercent   = round(($totalBBA / $totalSellers) * 100);
    $otherPercent = round(($totalOtherDept / $totalSellers) * 100);
}
else
{
    $csePercent   = 0;
    $eeePercent   = 0;
    $bbaPercent   = 0;
    $otherPercent = 0;
}


// ALL USERS TABLE
$sql        = "SELECT user_table.User_Id, user_table.First_Name, user_table.Last_Name,
                      user_table.Email, user_table.Mobile,
                      seller.Department, seller.Student_Id, seller.Semester,
                      user_table.Admin_Id, user_table.Buyer_Id, user_table.Seller_Id
               FROM user_table
               LEFT JOIN seller ON user_table.User_Id = seller.User_Id";
$result     = mysqli_query($conn, $sql);
$allUsers   = mysqli_fetch_all($result, MYSQLI_ASSOC);

// IDEAS FUND STATUS

$sql         = "SELECT business_idea.Idea_Id, business_idea.Idea, business_idea.Funding_Goal,
                       business_idea.Status,
                       seller.Department,
                       user_table.First_Name, user_table.Last_Name,
                       COALESCE(SUM(idea_fund.Amount), 0) AS raised
                FROM business_idea
                JOIN seller ON business_idea.Seller_Id = seller.Seller_Id
                JOIN user_table ON seller.User_Id = user_table.User_Id
                LEFT JOIN idea_fund ON idea_fund.Fund_id = business_idea.Idea_Id
                GROUP BY business_idea.Idea_Id";
$result      = mysqli_query($conn, $sql);
$allIdeas    = mysqli_fetch_all($result, MYSQLI_ASSOC);

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


      <div class="stats-row">

        <div class="stat-box" style="animation-delay:.04s">
          <div class="stat-name">Total Users</div>
          <div class="stat-number"><?php echo $totalUsers;?></div>
        </div>

        <div class="stat-box" style="animation-delay:.08s">
          <div class="stat-name">Active Listings</div>
          <div class="stat-number"><?php echo $totalListings; ?></div>
        </div>

        <div class="stat-box" style="animation-delay:.12s">
          <div class="stat-name">Total Revenue (৳)</div>
          <div class="stat-number"><?php echo $totalRevenue; ?></div>
        </div>

        <div class="stat-box" style="animation-delay:.16s">
          <div class="stat-name">Ideas Funded</div>
          <div class="stat-number"><?php echo $totalIdeas; ?></div>
        </div>

        <div class="stat-box" style="animation-delay:.2s">
          <div class="stat-name">Total Buyers</div>
          <div class="stat-number"><?php echo $totalBuyers; ?></div>
        </div>

        <div class="stat-box" style="animation-delay:.24s">
          <div class="stat-name">Total Sellers</div>
          <div class="stat-number"><?php echo $totalSellers; ?></div>
        </div>

      </div>

      <div class="divider-row">
        <div class="divider-label">Platform Analytics</div>
        <div class="divider-line"></div>
      </div>



      <div class="section-grid" style="grid-template-columns:1fr;margin-bottom:1rem;">

        <div class="content-card" style="animation-delay:.16s">
          <div class="card-top">
            <div>
              <div class="card-title">User Mix</div>
              <div class="card-subtitle">Role breakdown</div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-area">

              <div class="chart-legend">
                <!-- Buyers — nil color -->
                <div class="legend-row">
                  <div class="legend-color-box" style="background:var(--blue)"></div>
                  <div class="legend-label">Buyers</div>
                  <div class="legend-percent"><?php echo $buyerPercent; ?>% </div>
                </div>
                <!-- Sellers — maroon color -->
                <div class="legend-row">
                  <div class="legend-color-box" style="background:var(--maroon)"></div>
                  <div class="legend-label">Sellers</div>
                  <div class="legend-percent"><?php echo $sellerPercent; ?>% </div>
                </div>
                <!-- Admin — golden color -->
                <div class="legend-row">
                  <div class="legend-color-box" style="background:var(--gold)"></div>
                  <div class="legend-label">Admin/Mod</div>
                  <div class="legend-percent"><?php echo $adminPercent; ?>% </div>
                </div>
              </div>
            </div>


            <div style="margin-top:1.2rem;padding-top:1rem;border-top:1px solid var(--border);">
              <div class="card-subtitle" style="margin-bottom:.6rem;">Department breakdown</div>

              <div class="dept-bar-list">

                <div class="dept-bar-row">
                  <div class="dept-name">CSE</div>
                  <div class="bar-track"><div class="bar-fill fill-green" style="width:<?php echo $csePercent; ?>% "></div></div>
                  <div class="bar-percent"><?php echo $csePercent; ?>% </div>
                </div>

                <div class="dept-bar-row">
                  <div class="dept-name">BBA/MBA</div>
                  <div class="bar-track"><div class="bar-fill fill-green" style="width:<?php echo $bbaPercent; ?>% "></div></div>
                  <div class="bar-percent"><?php echo $bbaPercent; ?>% </div>
                </div>

                <div class="dept-bar-row">
                  <div class="dept-name">EEE</div>
                  <div class="bar-track"><div class="bar-fill fill-yellow" style="width:<?php echo $eeePercent; ?>% "></div></div>
                  <div class="bar-percent"><?php echo $eeePercent; ?>% </div>
                </div>

                <div class="dept-bar-row">
                  <div class="dept-name">Others</div>
                  <div class="bar-track"><div class="bar-fill fill-yellow" style="width:<?php echo $otherPercent; ?>% "></div></div>
                  <div class="bar-percent"><?php echo $otherPercent; ?>% </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div><!-- row 1 end -->

      <!-- arekta section divider -->
      <div class="divider-row">
        <div class="divider-label">User Management</div>
        <div class="divider-line"></div>
      </div>



      <div class="content-card" style="margin-bottom:1rem auto;animation-delay:.2s">
        <div class="card-top">
          <div>
            <!-- "Users" italic golden hobe -->
            <div class="card-title">All Users</div>
            <div class="card-subtitle">All registrations and activity</div>
          </div>
        </div>

        <!-- tab buttons — label click korle corresponding radio checked hoy -->
        <div class="tab-bar" style="padding:0 1.3rem;">
          <label class="tab-btn" for="ptab-users">All Users</label>
        </div>

        <!-- tab er content — CSS show/hide kore -->
        <div class="tab-content-area">
          <!-- All Users tab — default e dekhabe -->
          <div class="tab-all-users" style="overflow-x:auto;">
            <table class="users-table">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Role</th>
                  <th>Department</th>
                  <th>Student ID</th>
                  <th>Email</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>



                <?php foreach($allUsers as $user): ?>
                <tr>
                  <td>
                    <div class="user-info-cell">
                      <div class="user-initial-circle" style="background:#3498DB;"><?php echo strtoupper(substr($user['First_Name'], 0, 1)); ?></div>
                      <div>
                        <div class="user-fullname"><?php echo $user['First_Name'] . " " . $user['Last_Name']; ?></div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="status-tag tag-buyer"><?php
                                                          if(!is_null($user['Admin_Id']))  echo "Admin";
                                                          elseif(!is_null($user['Seller_Id'])) echo "Seller";
                                                          elseif(!is_null($user['Buyer_Id']))  echo "Buyer";
                                                          ?></span></td>
                  <td><span style="font-size:.76rem;color:var(--text-3)"><?php echo $user['Department'] ?? '—'; ?></span></td>
                  <td><span class="mono-text"><?php echo $user['Student_Id'] ?? '—'; ?></span></td>
                  <td><span class="mono-text" style="font-size:.7rem"><?php echo $user['Email']; ?></span></td>
                  <td>
                      <form method="POST">
                          <input type="hidden" name="delete_user_id" value="<?php echo $user['User_Id']; ?>">
                          <button type="submit" name="delete_user" class="btn-deleteuser">Delete</button>
                      </form>
                  </td>
                </tr>
                <?php endforeach; ?>

              </tbody>
            </table>
          </div><!-- tab-all-users end -->
        </div><!-- tab-content-area end -->
      </div><!-- user panel end -->

      <!-- section divider — marketplace section er age -->
      <div class="divider-row">
        <div class="divider-label">Marketplace &amp; Ideas Status</div>
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
                  <th>Idea</th>
                  <th>Raised</th>
                  <th>Progress</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>

                <?php foreach($allIdeas as $idea): ?>
                <?php
                    $goal     = $idea['Funding_Goal'];
                    $raised   = $idea['raised'];
                    $progress = ($goal > 0) ? round(($raised / $goal) * 100) : 0;
                ?>
                <tr>
                  <td>
                    <div class="user-fullname" style="font-size:.76rem"><?php echo $idea['Idea']; ?></div>
                  </td>
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