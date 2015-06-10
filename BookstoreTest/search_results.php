<?php
  session_start();


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Search Results</title>
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
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse1">
          <i class="glyphicon glyphicon-search"></i>
          </button>
      
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
              <li><a href="your_orders.php"><i class="glyphicon glyphicon-inbox" style="color:#6f5499;"></i> View Offers</a></li>
              <li class="nav-divider"></li>
              <li><a href="about.php"><i class="glyphicon glyphicon-info-sign" style="color:#11dd11;"></i> About</a></li>
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
             <li><a href="#aboutModal" role="button" data-toggle="modal">About</a></li>
             <li><a href="logout_destroy.php">Logout</a></li>
           </ul>
        </div>  
     </div> 
</div>

<!--main-->
<div class="container" id="main">
    <div id="results_title">
      <h3>Search Results: </h3>
    </div>

  <?php
    session_start();
    $servername = "localhost";
    $username = "root";
    $password = "s34h4wks";
    $dbname = "bookstore";
    
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if(mysqli_connect_errno()){
      die("Connection failed: " . mysqli_connect_error());
    }


    $author;
    $title;
    

    /* display the results here */
   
    $isbns = $_SESSION['isbn_arr'];
    $isbns = array_unique($isbns);
    $order_numbers = $_SESSION['order_arr'];
    $order_numbers = array_unique($order_numbers);
  
    foreach ($isbns as $val) {
      $author_query = "SELECT * FROM AUTHORS WHERE isbn LIKE '%" . $val . "%'";
      $result = mysqli_query($conn, $author_query);
      $row = mysqli_fetch_array($result);
      $author = $row['author'];
      $title_query = "SELECT title,thumbnail FROM BOOKS WHERE isbn LIKE '%" . $val . "%'";
      $result = $conn->query($title_query);
      $row = mysqli_fetch_row($result);
      $title = $row[0];
      $thumbnail = $row[1];
      foreach ($order_numbers as $order) {
            $order_identification_query = "SELECT * FROM ORDERS WHERE isbn='$val' AND orderID='$order'";
            $result = $conn->query($order_identification_query);
            if($result->num_rows > 0){
               $row = mysqli_fetch_assoc($result);
                $order_id_num = $row["orderID"];
                $order_price = $row["price"];
                $cond = $row['condition'];

                $condition_sql = "SELECT * FROM CONDITIONS WHERE id='$cond'";
                $result = mysqli_query($conn, $condition_sql);
                $row = mysqli_fetch_array($result);
                $condition_str = $row['condition'];
             
               echo '<div class="col-md-4 col-sm-6">
                   <div class="panel panel-default">
                         <div class="panel-heading"><h4>' . $title . '</h4></div>
                      <div class="panel-body">
                            <p><img src="' . $thumbnail . '" class="img-thumbnail pull-right"><b>ISBN:</b> ' . $val . '</br>
                            <b>Author:</b> ' . $author . '</br>
                            <b>Condition:</b> ' . $condition_str . '</br></br>
                            <b>Price: </b>$'. $order_price . '</br></p>
                            <div class="clearfix"></div>
                            <hr>
                           <a href="' . 'offer.php?order='. $order_id_num . '">Buy Book</a>
                          </div>
                       </div>
                </div>';
            }
      }     
    }
  ?>
 
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