<!--#################### From here PHP starts #################### -->
<?php
$result=null;
$result2=null;
include("database.php");

#This part is for search input
if(isset($_GET["submit"]))
    {
        $bookname=$_GET["productname"];
        $sql= "SELECT * FROM product WHERE Product_Name LIKE '%$bookname%'";
    }
else{
    $sql= "SELECT * FROM product ORDER BY RAND() LIMIT 7";  #This is for all product
}



$sql2="SELECT * FROM business_idea ORDER BY RAND() LIMIT 5";  #This is for all idea
$sql3="SELECT First_Name as Seller_Name FROM user_table join
        product
        WHERE user_table.Seller_Id=product.Seller_Id";  #This is for user name on product
$sql4="SELECT Department FROM seller join
        product
        WHERE seller.Seller_Id=product.Seller_Id"; #This is for user dept on product


#These result is for products
$result=mysqli_query($conn,$sql);
$result2=mysqli_query($conn,$sql2); #This result is for idea
$result3=mysqli_query($conn,$sql3);
$result4=mysqli_query($conn,$sql4);


#These result is for ideas
$sql5="SELECT First_Name as Seller_Name FROM user_table join
        business_idea
        WHERE user_table.Seller_Id=business_idea.Seller_Id";  #This is for user name on idea
$sql6="SELECT Department FROM seller join
        business_idea
        WHERE seller.Seller_Id=business_idea.Seller_Id"; #This is for user dept on idea
$result5=mysqli_query($conn,$sql5);
$result6=mysqli_query($conn,$sql6);




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
    <!-- This part is for right side options div-->
    <div class="nav-middle">
      <a href="marketplaceG.php" class="nav-option">Markectplace</a>
      <a href="ideashubG.php" class="nav-option">Ideas Hub</a>
    </div>
    </nav>


<div class="page-shell">
  <!-- App content centers and constrains the max width of all contents -->
  <div class="app-content">
    <div class="panel-market">
      <!-- section header title on left-->
      <div class="sec-head">
        <div>
          <div class="sec-title">The Marketplace</div>
          <div class="sec-sub">Discover products from fellow SEU students</div>
        </div>
      </div>
      <!-- Search bar -->
        <form method="get">
        <div class="controls-bar">
        <div class="search-warp">
          <input type="text" placeholder="Seach products" name="productname">
        </div>

        <!-- Sorting dropdown-->
         <select class="sort-select">
          <option>Sort: Newest</option>
          <option>Sort: Price High</option>
          <option>Sort: Price Low</option>
          <option>Sort: Top Rated</option>
         </select>

        <!-- submit button -->
         <button class="button-submit" name="submit">Search</button>
        </form>
      

      </div>
      <!--This is horizontal line div-->
      <div class="search-sep"></div>
      <div class="search-sep"></div>

      <!-- This is where the products go -->
      <div class="products-grid">


<!--This is the backend php part -->
        <?php
        while($row=mysqli_fetch_assoc($result))
            { $row3=mysqli_fetch_assoc($result3);
                $row4=mysqli_fetch_assoc($result4)
        ?>
<!--This is the backend php part -->


        <!-- product 1 card style -->
        <div class="pcard" style="animation-delay: .04s;">

          <!-- This is for image in card-->
          <div class="pcard-img">
            <img src="<?php echo $row['Product_Image']?>" alt="">
          </div>
          <!-- This is the card body -->
          <div class="pcard-body">

            <!-- product name -->
            <div class="pcard-name"><?php echo $row['Product_Name']?></div>

            <!-- seller name -->
            <div class="pcard-seller">by <strong><?php echo $row3['Seller_Name']?></strong> <?php echo $row4['Department']?> Dept.</div>

            <!-- This is product ratings -->
            <div class="pcard-stars">
              <div class="stars-display">★★★★★</div>
              <div class="stars-count">(24 reviews)</div>
            </div>

            <!-- card footer -->
            <div class="pcard-footer">
              <div class="pcard-price"><?php echo $row['Price']?></div>
            </div>
          </div>

        </div>


<!--This is the backend php part -->
        <?php 
            }
        ?>
<!--This is the backend php part -->

      </div>


  </div>


      <!--This is a horizontal line div-->
      <div class="search-sep2"></div>
      <!--This is a horizontal line div-->
      <div class="search-sep3"></div>






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



<!--This is the backend php part -->
        <?php
        while($row2=mysqli_fetch_assoc($result2))
            { $row5=mysqli_fetch_assoc($result5);
                $row6=mysqli_fetch_assoc($result6)
        ?>
<!--This is the backend php part -->


          <!-- idea no 1 -->
          <div class="icard" style="animation-delay: .04s;">
            
            <!-- ideas title and sub line-->
            <div class="icard-title"><?php echo $row2['Idea']?></div>
            <div class="icard-desc"><?php echo $row2['Description']?></div>

            <!-- idea funder info-->
            <div class="icard-founder">
              <div class="icard-founder-name"><?php echo $row5['Seller_Name']?></div>
              <div class="icard-founder-dept"><?php echo $row6['Department']?> Dept.</div>
            </div>


            <!-- funding progress -->
            <div class="fund-bar-wrap">
              <div class="fund-meta">
                <span>Raised: <strong>$14000</strong></span>
                <span>Goal: <?php echo $row2['Funding_Goal']?></span>
              </div>

            </div>

          </div>



<!--This is the backend php part -->
        <?php 
            }
        ?>
<!--This is the backend php part -->

        </div>

      </div>


      <!--This is a horizontal line div-->
      <div class="search-sep2"></div>
      <!--This is a horizontal line div-->
      <div class="search-sep3"></div>


      
    </div>  
</div>


<!-- This is the footer part -->
<div class="footer">

  <!-- This is the website name -->
  <div class="web-name">&copy Southeast University UniBuzz 2026</div>

</div>




</body>
</html>