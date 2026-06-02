<!--#################### From here PHP starts #################### -->

<?php
session_start();
if(isset($_POST["login"]))
{
    $usermail = $_POST["usermail"];  # fetch user mail
    $pass     = $_POST["userpassword"]; #fetch user password

    include("database.php");

    $sql    = "SELECT * FROM user_table WHERE Email='$usermail'";
    $result = mysqli_query($conn, $sql);
    $user   = mysqli_fetch_assoc($result);

    if($user && password_verify($pass, $user['Password']))   #have user password check with sql
    {
        $_SESSION["logged_in"] = true;
        $_SESSION["username"]  = $user['First_Name'];
        $_SESSION["user_id"]   = $user['User_Id'];

        if(!is_null($user['Admin_Id']))
        {
            $_SESSION["role"] = "admin";
            header("Location: adminHomepage.php");
        }
        elseif(!is_null($user['Seller_Id']))
        {
            $_SESSION["role"] = "seller";
            header("Location: sellerHomepage.php");
        }
        elseif(!is_null($user['Buyer_Id']))
        {
            $_SESSION["role"] = "buyer";
            header("Location: buyerHomepage.php");
        }
        else
        {
            $error = "No role assigned to this account.";
        }
        exit();
    }
    else
    {
        $error = "Invalid email or password.";
    }

    mysqli_close($conn);
}
?>






<!--#################### From here HTML starts #################### -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEU UniBuzz - Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <!-- Full page er jonno div-->
    <div class="page-bg">
        <!-- kichu shape er jonno div-->
        <div class="bg-shape s1"></div>
        <div class="bg-shape s2"></div>
        <div class="bg-box s3"></div>
        <div class="bg-box s4"></div>
        <div class="bg-shape s5"></div>
        <div class="bg-shape s6"></div>
        <div class="bg-box s7"></div>
        <div class="bg-box s8"></div>
        <div class="bg-shape s9"></div>
        <div class="bg-shape s10"></div>
        <div class="bg-box s11"></div>
        <div class="bg-box s12"></div>
        <!-- Login card -->
         <div class="login-card">
            <!-- card er upore uni logo-->
            <div class="card-logo">
                <div class="logo-box">
                    <img src="images/SEU_LOGO.png" alt="">
                </div>
                <div>
                    <p class="brand-name">SEU UniBuzz</p>
                    <p class="brand-sub">Southeast University Dhaka</p>
                </div>
            </div>

            <!-- one horizontal line-->
             <div class="divider"></div>
            <!-- Welcome text -->
             <h2 class="card-heading">Welcome back</h2>
             <p class="card-subtext">Sign in with your SEU credentials to be a seller</p>



            <!-- Login form -->
             <form method="POST">
                <!-- Student id input-->
                 <div class="input-block">
                    <label>Email</label>
                    <input type="text" name="usermail" placeholder="yourname@mail.com" required>
                 </div>
                 <!-- Password input-->
                  <div class="input-block">
                    <label>Password</label>
                    <div class="password-in">
                        <input type="password" name="userpassword" placeholder="Enter your password" required>
                    </div>
                  </div>
                  <!-- forgot passowrd-->
                   <div class="forgot-pass">
                    <a href="">Forgot password?</a>
                   </div>

                   <!-- Sign in button-->
                    <button type="submit" class="signin-btn" name="login">Sign in to UniBuzz</button>


                    
            <?php if(isset($error)): ?>
                <div class="error-popup"><?php echo $error; ?></div>
            <?php endif; ?>
             </form>

             <!-- New account-->
              <p class="new-account">New To UniBuzz? <a href="signup.php">Create a free buyer account</a></p>

              <!-- footer for info-->
               <p class="card-footer">
                <strong>SOUTHEAST UNIVERSTY</strong> Tejgaon Industrial Area, Dhaka-1208 Bangladesh
               </p>
         </div>


         <!-- Card shesh-->
    </div>

</body>
</html>