<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "admin")
{
    header("Location: login.php");
    exit();
}

include("database.php");

// delete buyer
if(isset($_POST["delete_buyer"]))
{
    $del_id = $_POST["delete_buyer_id"];
    mysqli_query($conn, "DELETE FROM user_table WHERE User_Id=$del_id");
    header("Location: adminBuyers.php");
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


// get all buyers with their info
$sql       = "SELECT user_table.User_Id, user_table.First_Name, user_table.Last_Name,
                     user_table.Email, user_table.Mobile,
                     buyer.Buyer_Id, buyer.Cart_Id
              FROM user_table
              JOIN buyer ON user_table.Buyer_Id = buyer.Buyer_Id";
$result    = mysqli_query($conn, $sql);
$allBuyers = mysqli_fetch_all($result, MYSQLI_ASSOC);






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
        <div class="divider-label">All Buyers Info</div>
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


        <!-- tab er content — CSS show/hide kore -->
        <div class="tab-content-area">
          <!-- All Users tab — default e dekhabe -->
          <div class="tab-all-users" style="overflow-x:auto;">
            <table class="users-table">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Email</th>
                  <th>Mobile</th>
                  <th>Buyer ID</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>

                <?php foreach($allBuyers as $buyer): ?>
                <tr>
                  <td>
                    <div class="user-info-cell">
                      <div class="user-initial-circle" style="background:#3498DB;"><?php echo strtoupper(substr($buyer['First_Name'], 0, 1)); ?></div>
                      <div>
                        <div class="user-fullname"><?php echo $buyer['First_Name'] . " " . $buyer['Last_Name']; ?></div>
                      </div>
                    </div>
                  </td>
                  <td><span style="font-size:.76rem;color:var(--text-3)"><?php echo $buyer['Email']; ?></span></td>
                  <td><span class="mono-text"><?php echo $buyer['Mobile']; ?></span></td>
                  <td><span class="mono-text" style="font-size:.7rem"><?php echo $buyer['Buyer_Id']; ?></span></td>
                  <td>
                    <form method="POST">
                        <input type="hidden" name="delete_buyer_id" value="<?php echo $buyer['User_Id']; ?>">
                        <button type="submit" name="delete_buyer" class="btn-deleteuser">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>

              </tbody>
            </table>
          </div><!-- tab-all-users end -->
        </div><!-- tab-content-area end -->
      </div><!-- user panel end -->


    </div><!-- page-content-scroll end -->
  </div><!-- right-side end -->
</div><!-- my-dashboard-wrapper end -->

</body>
</html>