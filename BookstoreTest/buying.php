<?php

session_start();

  if(isset($_SESSION['uid'])){
    //Already have a session
  }else{
    
    $identity = $_GET["uid"];
    if(is_null($identity))
      if(isset($_SESSION["uid"]))
        $identity = $_SESSION["uid"];

    if(is_null($identity) || $identity == ""){
      //Not logged in. Redirect.
       header('Location: http://west.wwu.edu/stcsp/stc000/CAS/buying.asp');
    }else{
        $_SESSION['uid'] = $identity;
    }
  }

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <title>Bookstore Website</title>
  <meta name="generator" content="Bootply" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="icon" type="image/png" href="images/icon.ico" type="image/icon">
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet"> <!-- Needed for glyphicons to work -->
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->
      <link href="css/styles.css" rel="stylesheet">
    </head>
    <body>
      <nav class="navbar navbar-fixed-top header">
        <div class="col-md-12">
          <div class="navbar-header">
            <a href="#" class="navbar-brand">Bookstore Web Site</a>
          </div>
        </div>	
      </nav>
      <div class="navbar navbar-default" id="subnav">
        <div class="col-md-12">
          <div class="navbar-header">

            <a href="#" style="margin-left:15px;" class="navbar-btn btn btn-default btn-plus dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-shopping-cart" style="color:#1111dd;"></span> Buying <small><i class="glyphicon glyphicon-chevron-down"></i></small></a>
            <ul class="nav dropdown-menu">
              <li><a href="buying.php"><i class="glyphicon glyphicon-shopping-cart" style="color:#1111dd;"></i> Buying</a></li>
              <li class="nav-divider"></li>
              <li><a href="selling.php"><i class="glyphicon glyphicon-bullhorn" style="color:#FFD829;"></i> Selling</a></li>
              <li class="nav-divider"></li>
              <li><a href="your_orders.php"><i class="glyphicon glyphicon-user" style="color:#6f5499;"></i> Your Orders</a></li>
              <li class="nav-divider"></li>
              <li><a href="view_offers.php"><i class="glyphicon glyphicon-inbox" style="color:#6f5499;"></i> View Offers</a></li>
              <li class="nav-divider"></li>
              <li><a href="#"><i class="glyphicon glyphicon-exclamation-sign" style="color:#dd1111;"></i> Logout</a></li>
            </ul>
            <?php
              //Tests for CAS

              if(isset($_SESSION['uid'])){
                echo $_SESSION['uid'];
              }else{
                echo "Guest";
              }
              
            ?>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse2">
             <span class="sr-only">Toggle navigation</span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
           </button>

         </div>
         <div class="collapse navbar-collapse" id="navbar-collapse2">
          <ul class="nav navbar-nav navbar-right">
           <!-- <li class="active"><a href="#">Posts</a></li> -->
           <!-- <li><a href="#loginModal" role="button" data-toggle="modal">Login</a></li> -->
           <li><a href="#aboutModal" role="button" data-toggle="modal">About</a></li>
           <li><a href="logout_destroy.php"> Logout</a></li>
         </ul>
       </div>	
     </div>	
   </div>

   <!--main-->
   <div class="container" id="main">

    <div class="row" align="center">
      <div class="col-md-6 col-sm-8 col col-sm-offset-3">
       <div class="well"> 
         <h4>Search for books </br><span style="font-size:65%">Enter as much information as you can</span></h4>
         <form class="" action="buying_search.php" method="post">
           <div class="input-group" style="max-width:470px;">
             <input type="text" class="form-control" placeholder="ISBN" name="srchisbn" id="srchisbn">
             <input type="text" class="form-control" placeholder="Title" name="srchtitle" id="srchtitle">
             <input type="text" class="form-control" placeholder="Author" name="srchauth" id="srchauth">
             <button class="btn btn-default btn-primary" style="margin-top:10px;" type="submit"><i class="glyphicon glyphicon-search"></i> Search</button>        
         </form>
       </div>
     </div>
      <h3>Recent Posts:</h3>
   </div>


   <div class="row">

    <?php
       

        /* Select the 3 most recently posted books */
        $servername = "localhost";
        $username = "root";
        $password = "s34h4wks";
        $dbname = "bookstore";
        
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        if(mysqli_connect_errno()){
          die("Connection failed: " . mysqli_connect_error());
        }

        
        $recent_query = "SELECT * FROM ORDERS ORDER BY timestamp DESC LIMIT 5";
        $result = $conn->query($recent_query);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

              /* Get the Book Title and price */
              $order_id_num = $row["orderID"];
              $isbn = $row["isbn"];
              $price = $row["price"];
              $condition = $row["condition"];

              $condition_sql = "SELECT * FROM CONDITIONS WHERE id='$condition'";
              $result2 = mysqli_query($conn, $condition_sql);
              $row2 = mysqli_fetch_array($result2);
              $condition_str = $row2['condition'];


              $author_sql = "SELECT * FROM AUTHORS WHERE isbn='$isbn'";
              $result3 = mysqli_query($conn, $author_sql);
              $row3 = mysqli_fetch_array($result3);
              $author = $row3['author'];

              $book_query = "SELECT * FROM BOOKS WHERE isbn = '$isbn'";
              $book_result = $conn->query($book_query);
              while($book_row = $book_result->fetch_assoc()){
                $title = $book_row["title"];
                $thumburl = $book_row["thumbnail"];
              }

              
                     

             echo '<div class="col-md-4 col-sm-6">
                       <div class="panel panel-default">
                         <div class="panel-heading"><h4>' . $title . '</h4></div>
                         <div class="panel-body">
                          <p><img src="'  . $thumburl . '" class="img-thumbnail pull-right">Condition: ' . $condition_str .'</br>Author: ' . $author . '</br>Price: $' . $price . '</br></p>
                          <div class="clearfix"></div>
                          <hr>
                           <a href="' . 'offer.php?order='. $order_id_num . '">Buy Book</a>
                        </div>
                      </div>
                    </div>';    
            }
        }    
    ?>

</div><!--/row-->

<hr>

</div><!--/main-->

<!--login modal-->
<div id="loginModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h2 class="text-center"><img src="https://lh5.googleusercontent.com/-b0-k99FZlyE/AAAAAAAAAAI/AAAAAAAAAAA/eu7opA4byxI/photo.jpg?sz=100" class="img-circle"><br>Login</h2>
      </div>
      <div class="modal-body">
        <form class="form col-md-12 center-block">
          <div class="form-group">
            <input type="text" class="form-control input-lg" placeholder="Email">
          </div>
          <div class="form-group">
            <input type="password" class="form-control input-lg" placeholder="Password">
          </div>
          <div class="form-group">
            <button class="btn btn-primary btn-lg btn-block">Sign In</button>
            <span class="pull-right"><a href="#">Register</a></span><span><a href="#">Need help?</a></span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <div class="col-md-12">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </div>	
      </div>
    </div>
  </div>
</div>


<!--about modal-->
<div id="aboutModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h2 class="text-center">About</h2>
      </div>
      <div class="modal-body">
        <div class="col-md-12 text-center">
          <a href="http://www.bootply.com/DwnjTNuvVt">This Bootstrap Template</a><br>was made with <i class="glyphicon glyphicon-heart"></i> by <a href="http://bootply.com/templates">Bootply</a>
          <br><br>
          <a href="https://github.com/iatek/bootstrap-google-plus">GitHub Fork</a>
          <p>Modified by Bryan Holdt, Matt Lam, and Vadim Belonenko</p>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">OK</button>
      </div>
    </div>
  </div>
</div>
<!-- script references -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>
