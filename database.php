<!-- This file if to connect database  -->


<?php
    $db_server="localhost";
    $db_user="root";
    $db_pass="";
    $db_name="seu_businesszone_database";
    $conn="";

    try
    {
        $conn= mysqli_connect($db_server,
                            $db_user,
                            $db_pass,
                            $db_name);
        mysqli_set_charset($conn, "utf8mb4");
    }
    
    catch(mysqli_sql_exception)
    {
       // echo"Not connected to the database";
    }

    if($conn)
        {
           // echo"Connected";
        }
?>