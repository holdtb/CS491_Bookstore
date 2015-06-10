<?php
/*
	buying_search.php

	This page checks to see if any books matching the criteria are found.
*/

	function purge_searchtrm($connection, $search){
		$search = mysqli_real_escape_string($connection, $search);
		$search = str_replace('-', '', $search);
		return $search;
	}

	session_start();
	$result_isbn;
	$concat_results;

	$servername = "localhost";
	$username = "root";
	$password = "s34h4wks";
	$dbname = "bookstore";

	$conn = mysqli_connect($servername, $username, $password, $dbname);

	if(mysqli_connect_errno()){
		die("Connection failed: " . mysqli_connect_error());
	}

	$final_isbns;

	$input_isbn = $_POST['srchisbn'];
	$input_isbn = purge_searchtrm($conn, $input_isbn);
	$input_title = $_POST['srchtitle'];
	$input_title = purge_searchtrm($conn, $input_title);
	$input_author = purge_searchtrm($conn, $_POST['srchauth']);

	$concat_results = $concat_results . $input_isbn . "&";

	$sql = "SELECT * FROM  BOOKS WHERE title='$input_title'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$concat_results = $concat_results . $row['isbn'] . "&";    
		}
	}

	if($input_author !== ''){
		$sql = "SELECT * FROM AUTHORS WHERE author LIKE " . "'%" . $input_author . "%'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$concat_results = $concat_results . $row['isbn'] . "&";
			}
		}
	}
	
	echo $concat_results;	

	$srch = explode('&', $concat_results);
	foreach($srch as $val){
		$sql = "SELECT * FROM ORDERS WHERE isbn='$val'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$final_isbns = $final_isbns . $row['isbn'] . "&";
				$order_numbers = $order_numbers . $row['orderID'] . "&";
			}
		}
	}



	$final_isbns = rtrim($final_isbns, "&");
	$order_numbers = rtrim($order_numbers, "&");

	$conn->close();
	$_SESSION['isbn_arr'] = explode('&', $final_isbns);
	$_SESSION['order_arr'] = explode('&', $order_numbers);

	//Redirect to search_results.php with GET var
	header( "Location: http://192.241.201.209/BookstoreTest/search_results.php");

	?>