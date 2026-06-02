<!--#################### From here PHP starts #################### -->


<?php
session_start();
if(isset($_POST["signup"]))
{
    $firstname    = $_POST["FirstName"];
    $lastname    = $_POST["LastName"];
    $pass        = $_POST["userpassword"];
    $confirmpass = $_POST["confirmpassword"];
    $email     =  $_POST["seumail"];
    $phone    =  $_POST["userPhone"];
    $studentid    =  $_POST["studentID"];
    $studentsem  =  $_POST["semester"];
    $studentdept    =  $_POST["dept"];

    // Check passwords match FIRST
    if($pass !== $confirmpass)
    {
        $error = "Passwords do not match.";
    }
    // Check username not empty
    elseif(empty($firstname)|| empty($lastname))
    {
        $error = "Username cannot be empty.";
    }
    else
    {
        $password = password_hash($pass, PASSWORD_DEFAULT);

        include("database.php");

        // Check if username already exists
        $check = mysqli_query($conn, "SELECT * FROM user_table WHERE Email='$email'");
        if(mysqli_num_rows($check) > 0)
        {
            $error = "Email/Student ID is already in use.";
        }
        else
        {

            // Insert user first with Buyer_Id as NULL
            $sql = "INSERT INTO user_table(First_Name, Last_Name, Email, Mobile, Password, Admin_Id, Seller_Id, Buyer_Id)
                    VALUES ('$firstname', '$lastname', '$email', '$phone', '$password', NULL, NULL, NULL)";
            mysqli_query($conn, $sql);
            $newUserId = mysqli_insert_id($conn); // get new User_Id

            // Step 2 — Insert into seller table
            $sellerSql = "INSERT INTO seller(User_Id, University_Email, Rating, Department, Semester, Student_Id)
                          VALUES ($newUserId, '$email', 0.00, '$studentdept', '$studentsem', '$studentid')";
            mysqli_query($conn, $sellerSql);
            $newSellerId = mysqli_insert_id($conn);

            // Step 3 — Update user_table with Seller_Id
            mysqli_query($conn, "UPDATE user_table SET Seller_Id=$newSellerId WHERE User_Id=$newUserId");

            mysqli_close($conn);
            header("Location: login.php?registered=1");
            exit();
        }


    }

}
?>




<!--#################### From here HTML starts #################### -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEU UniBuzz - Registration</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="registration.css">
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
             <h2 class="card-heading">Create Account</h2>
             <p class="card-subtext">Use your SEU credentials to be a seller</p>

            <!-- Login form -->
             <form method="post">
                <div class="input-row">
                    <!-- First name input-->
                    <div class="input-block">
                        <label>First Name</label>
                        <input type="text" name="FirstName" placeholder="Mahin" required>
                    </div>


                    <!-- Last name input-->
                    <div class="input-block">
                        <label>Last Name</label>
                        <input type="text" name="LastName" placeholder="Uddin" required >

                    </div>
                </div>



                <!-- Phone Number input-->
                 <div class="input-block">
                    <label>Phone Number</label>
                    <input type="text" name="userPhone" placeholder="01000000000" required>
                 </div>


                <!-- seu id input-->
                 <div class="input-block">
                    <label>SEU Student ID</label>
                    <input type="text" name="studentID" placeholder="2020000000000" required>
                 </div>


                <!-- seu mail input-->
                 <div class="input-block">
                    <label>SEU Institutional Email</label>
                    <input type="text" name="seumail" placeholder="2020000000000@seu.edu.bd" required>
                 </div>


                 <!-- Password input-->
                  <div class="input-block">
                    <label>Password</label>
                    <div class="password-in">
                        <input type="password" name="userpassword" placeholder="Enter your password" required>
                    </div>
                  </div>
                 <!-- again Password input-->
                  <div class="input-block">
                    <label>Confirm Password</label>
                    <div class="password-in">
                        <input type="password" name="confirmpassword" placeholder="Enter your password" required>
                    </div>
                  </div>
                

                          <!-- Two-column row for Department and Semester dropdowns -->
                    <div class="input-row">
                    <div class="input-block">
                        <label>Department</label>
                        <div class="input-block">
                        <!-- no-icon class removes icon left-padding since there's no emoji icon here -->
                        <!-- disabled+selected first option acts as a placeholder -->
                        <select name="dept" class="no-icon" style="padding-left:1rem;" required>
                            <option value="" disabled selected>Select department</option>
                            <option>CSE</option>
                            <option>EEE</option>
                            <option>BBA / MBA</option>
                            <option>English</option>
                            <option>Law</option>
                            <option>Pharmacy</option>
                            <option>Civil Engineering</option>
                            <option>Architecture</option>
                            <option>Other</option>
                        </select>
                        </div>
                    </div>
                    <div class="input-block">
                        <label>Semester</label>
                        <div class="input-block">
                        <select name="semester" class="no-icon" style="padding-left:1rem;">
                            <option value="" disabled selected>Select semester</option>
                            <option>1st</option>
                            <option>2nd</option>
                            <option>3rd</option>
                            <option>4th</option>
                            <option>5th</option>
                            <option>6th</option>
                            <option>7th</option>
                            <option>8th</option>
                            <option>Graduate</option>
                        </select>
                        </div>
                    </div>
                    </div>


                   <!-- Sign in button-->
                    <button type="submit" class="signin-btn" name="signup">Create My Account</button>


          <?php if(isset($error)): ?>
              <div class="note-banner note-error"><?php echo $error; ?></div>
          <?php endif; ?>
             </form>

             <!-- New account-->
              <p class="new-account">Already have account? <a href="login.php">Sign In</a></p>

              <!-- footer for info-->
               <p class="card-footer">
                <strong>SOUTHEAST UNIVERSTY</strong> Tejgaon Industrial Area, Dhaka-1208 Bangladesh
               </p>
         </div>

         <!-- Card shesh-->
    </div>
</body>
</html>