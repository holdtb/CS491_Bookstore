<?php
/*
	buying_search.php

	This page checks to see if any books matching the criteria are found.
*/

	function purge_searchtrm($search){
		$search = mysqli_escape_string($search);
		$search = str_replace('-', '', $search);
		return $search;
	}

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


	//$search_critera = purge_searchtrm($_POST["srchterm"]);
	$search_critera = "Klein";
	echo $search_critera;
	$search_array = explode(',', $search_critera);
	echo count($search_array); 

	/* Search through database for search criteria */
	foreach ($search_array as $srch) {
		$sql = "SELECT * FROM ORDERS WHERE isbn='$srch'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
	  	  // output data of each row
	    	while($row = $result->fetch_assoc()) {
	    		$concat_results = $concat_results . $row['isbn'] . "#";
	    		echo "Found isbn in ORDER";
	        
	    	}
	    }
	    $sql = "SELECT * FROM AUTHORS WHERE author LIKE " . "'%" . $srch . "%'";
	    echo $sql;
	    $result = $conn->query($sql);

		if ($result->num_rows > 0) {
	  	  // output data of each row
	    	while($row = $result->fetch_assoc()) {
	    		$concat_results = $concat_results . $row['isbn'] . "#";
	        	echo "Found author in AUTHORS";
	    	}
	    }
	}

	$final_isbns;

	$srch2 = explode('#', $concat_results);
	foreach($srch2 as $val){
		$sql = "SELECT * FROM ORDERS WHERE isbn='$val'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
	  	  // output data of each row
	    	while($row = $result->fetch_assoc()) {
	    		$finalisbns = $finalisbns . $row['isbn'] . "#";
	        	echo "FOUND ORDER";
	    	}
	    }
	}

echo "</br>";
echo $finalisbns;

	$conn->close();

?>