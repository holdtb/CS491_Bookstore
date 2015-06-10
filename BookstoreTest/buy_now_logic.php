<?php
/*
	buy_now_logic.php

	This file takes in a order number for a buy it now deal and does the following:

		1. Email seller
		2. Tell seller to pick a location and date for transaction
		3. Email buyer
		4. Buyer Confirms/Cancels
			4a. Remove
			4b. Keep
*/
	session_start();		


	$servername = "localhost";
	$username = "root";
	$password = "s34h4wks";
	$dbname = "bookstore";
	        
	$conn = mysqli_connect($servername, $username, $password, $dbname);

	if(mysqli_connect_errno()){
	  die("Connection failed: " . mysqli_connect_error());
	}


 	$offer_id = $_GET['order'];
    $sql = "SELECT * FROM ORDERS WHERE orderID='$offer_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $isbn = $row['isbn'];
    $posted = $row['timestamp'];
    $seller_id = $row['sellerUID'];
    $location = $row['location'];
    $condition = $row['condition'];
    
    $price = $row['price'];

    $title_sql = "SELECT * FROM BOOKS WHERE isbn='$isbn'";
    $result = mysqli_query($conn, $title_sql);
    $row = mysqli_fetch_array($result);
    $title = $row['title'];
    $thumbnail = $row['thumbnail'];

    $author_sql = "SELECT * FROM AUTHORS WHERE isbn='$isbn'";
    $result = mysqli_query($conn, $author_sql);
    $row = mysqli_fetch_array($result);
    $author = $row['author'];

    $condition_sql = "SELECT * FROM CONDITIONS WHERE id='$condition'";
    $result = mysqli_query($conn, $condition_sql);
    $row = mysqli_fetch_array($result);
    $condition_str = $row['condition'];


  echo "<h2>Attempting to make an offer on book...</h2></br></br>";


  /*
	Update OFFERS table with the new offer
  */
	$buyerUID = $_SESSION['uid'];
	$update_sql = "INSERT INTO OFFERS(orderID, buyerUID, price) VALUES ('offer_id', '$buyer', '$price')";
	$result = mysqli_query($conn, $update_sql);


  echo "Sending offer to " . $seller_id . "</br>";
	//error_reporting(E_ALL);
	error_reporting(E_STRICT);

	date_default_timezone_set('America/Los_Angeles');

	require_once('../../../usr/share/php/libphp-phpmailer/class.phpmailer.php');
	//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

	$mail             = new PHPMailer();

	$body             = 'You have received an offer for the following book on the Bookstore Website!</br></br>

 							<b>Title:</b> ' . $title . '</br>
							<b>Author:</b> ' . $author . '</br>
							<b>ISBN:</b> ' . $isbn . '</br>
                            <b>Condition:</b> ' . $condition_str . '</br>
                            <b>Price: </b>$'. $price . '</br></p>

                            </br></br>To complete the sale, please propose a date, location, and time to sell 
                            the book.</br> Please visit <a href="http://192.241.201.209/BookstoreTest/your_orders.php">Your Orders</a>.';


	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host       = "mail.yourdomain.com"; // SMTP server
	$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
	                                           // 1 = errors and messages
	                                           // 2 = messages only
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
	$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
	$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
	$mail->Username   = "books.western@gmail.com";  // GMAIL username
	$mail->Password   = "s34h4wks";            // GMAIL password

	$mail->SetFrom("books.western@gmail.com", "Bookstore Website");

	$mail->Subject    = "You have received an offer";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	$mail->MsgHTML($body);
	$address = $seller_id . "@students.wwu.edu";
	$mail->AddAddress($address, "Seller");

	if(!$mail->Send()) {
	  echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	  echo "Message sent!";

	}
	header( "refresh:3;url=http://192.241.201.209/BookstoreTest/buying.php");
	exit;
	

?>