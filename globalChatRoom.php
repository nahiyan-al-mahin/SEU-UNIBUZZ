<?php
session_start();
if(!isset($_SESSION["logged_in"]))
{
    header("Location: login.php");
    exit();
}


include("database.php");

// send message
if(isset($_POST["send_message"]))
{
    $message = $_POST["message"];
    $userId  = $_SESSION["user_id"];

    if(!empty(trim($message)))
    {
        $sql = "INSERT INTO global_chat(User_Id, Message, Message_Date)
                VALUES ($userId, '$message', NOW())";
        mysqli_query($conn, $sql);
        header("Location: globalChatRoom.php");
        exit();
    }
}

// get logged in user info
$userId  = $_SESSION["user_id"];
$sql     = "SELECT user_table.First_Name, user_table.Last_Name,
                   user_table.Admin_Id, user_table.Seller_Id, user_table.Buyer_Id,
                   seller.Department
            FROM user_table
            LEFT JOIN seller ON user_table.User_Id = seller.User_Id
            WHERE user_table.User_Id = $userId";
$result  = mysqli_query($conn, $sql);
$me      = mysqli_fetch_assoc($result);

// figure out logged in user role
if(!is_null($me['Admin_Id']))       $myRole = "Admin";
elseif(!is_null($me['Seller_Id']))  $myRole = "Seller";
else                                $myRole = "Buyer";

$myName = $me['First_Name'] . " " . $me['Last_Name'];
$myDept = $me['Department'] ?? '';

// get all messages with sender info
$sql = "SELECT global_chat.Message_id, global_chat.Message, global_chat.User_Id, global_chat.Message_Date,
               user_table.First_Name, user_table.Last_Name,
               user_table.Admin_Id, user_table.Seller_Id, user_table.Buyer_Id,
               seller.Department
        FROM global_chat
        JOIN user_table ON global_chat.User_Id = user_table.User_Id
        LEFT JOIN seller ON user_table.User_Id = seller.User_Id
        ORDER BY global_chat.Message_Date ASC";
$result   = mysqli_query($conn, $sql);
$messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_close($conn);
?>


<?php
if($_SESSION["role"] == "admin")        $backLink = "adminHomepage.php";
elseif($_SESSION["role"] == "seller")   $backLink = "sellerHomepage.php";
else                                    $backLink = "buyerHomepage.php";
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>UniBuzz — Community Chat · Southeast University</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="globalChatRoom.css">
</head>
<body>


<nav class="navbar">
  <div class="nav-logo">UniBuzz</div>

  <div class="nav-sep"></div>

  <!-- Room name + live member count -->
  <div class="nav-room">
    Community Chat
  </div>

  <!-- Right side: navigation buttons + avatar -->
  <div class="nav-right">
    <a href="<?php echo $backLink; ?>"  class="nav-btn nav-btn-ghost">Back</a>
    <div class="nav-avatar"><?php echo strtoupper(substr($myName, 0, 1)); ?></div>
  </div>
</nav>


<div class="page-wrapper">
<div class="chat-root">

  <aside class="sidebar-left">

    <div class="sl-content">

      <!-- Sidebar title and subtitle -->
      <div class="sl-header">
        <div class="sl-title">UniBuzz Chat</div>
        <div class="sl-sub">Southeast University</div>
      </div>

      <!-- Gold separator line — matches the <hr> in login/signup pages -->
      <div class="sl-divider"></div>

      <!-- "CHANNELS" section label -->
      <div class="channel-group-label">Channels</div>

      <a href="globalChatRoom.php" class="channel-item active">
        Global Lounge
      </a>

      <!-- Current user's info at the very bottom of the sidebar -->
      <div class="sl-footer">
        <!-- Small gold avatar -->
        <div class="sl-footer-avatar"><?php echo strtoupper(substr($myName, 0, 1)); ?></div>
        <div>
          <div class="sl-footer-name"><?php echo $myName; ?></div>
          <div class="sl-footer-role"><?php echo $myRole; ?> · <?php echo $myDept; ?></div>
        </div>
      </div>
    </div>
  </aside>

  <!--  MAIN CHAT -->
  <div class="chat-main">

    <!-- Channel header -->
    <div class="chat-header">
      <div class="chat-header-left">
        <div>
          <!-- Channel name -->
          <div class="ch-name">Global Lounge</div>
          <div class="ch-desc">Open to all SEU students — share products, ideas, experiences &amp; connect</div>
        </div>
      </div>

    </div>

    <!-- Pinned message bar -->
    <div class="pinned-bar">
      <span class="pin-icon">📌</span>
      <div class="pin-text"><strong>Admin Fahim:</strong> Welcome to the UniBuzz Global Lounge! Keep it respectful, promote your products freely, and support your fellow SEU students. 🎓</div>
    </div>

    <!-- MESSAGES AREA -->
    <div class="messages-wrap" id="messages">
        <?php foreach($messages as $msg): ?>
        <?php
            if(!is_null($msg['Admin_Id']))       $role = "Admin";
            elseif(!is_null($msg['Seller_Id']))  $role = "Seller";
            else                                 $role = "Buyer";

            if($role == "Admin")        $avatarClass = "av-admin";
            elseif($role == "Seller")   $avatarClass = "av-seller";
            else                        $avatarClass = "av-buyer";

            $senderName   = $msg['First_Name'] . " " . $msg['Last_Name'];
            $firstLetter  = strtoupper(substr($msg['First_Name'], 0, 1));
            $time         = date("h:i A", strtotime($msg['Message_Date']));
        ?>
        <div class="msg-row">
            <div class="msg-avatar  <?php echo $avatarClass; ?>">
                <?php echo $firstLetter; ?>
            </div>
            <div class="msg-body-wrap">
                <div class="msg-meta">
                    <span class="msg-name"><?php echo $senderName; ?></span>
                    <span class="msg-role-badge role-<?php echo strtolower($role); ?>">
                        <?php
                        if($role == "Admin")       echo "Admin";
                        elseif($role == "Seller")  echo "Seller";
                        else                       echo "Buyer";
                        ?>
                    </span>
                    <span class="msg-time"><?php echo $time; ?></span>
                </div>
                <div class="msg-text"><?php echo htmlspecialchars($msg['Message']); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

 
    
    <!-- INPUT AREA -->
    <div class="chat-input-area">
        <form method="POST">
            <div class="input-row">
                <div class="input-wrap">
                    <textarea class="chat-textarea" name="message"
                            placeholder="Message to global-lounge…" rows="1"></textarea>
                </div>
                <button class="send-btn" type="submit" name="send_message">➤</button>
            </div>
            <div class="input-hint">
                <span>Press <strong>Enter</strong> to send</span>
                <div class="input-hint-dot"></div>
                <span><strong>Shift+Enter</strong> for new line</span>
            </div>
        </form>
    </div>

  </div><!-- chat-main -->

</div><!-- chat-root -->
</div><!-- page-wrapper -->

</body>
</html>


