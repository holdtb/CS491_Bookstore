<?php
/*
	scrape.php
	Created: May 4th 2015 by Bryan Holdt

	This script does a few things:

	1) Receive data from selling.php via POST

	2) Check local db for books meeting the details from step 2
		2a) If a match is found, skip to step 4

	3) IF nothing found in step 2, begin scraping

	4) Redirect the user to the appropriate page - confirmation or error
*/

	$isbn;
	$title;
	$author;

	/*
		Returns the number of rows in a result set or -1 if the resultset was 0
	*/
	echo "we started";
	function getNumberOfResultsInResultSet($resultSet){
		if(!is_null($resultSet)){
			return $resultSet->num_rows;
		}else{
			return -1;
		}
	}

	/* Get data about user from CAS*/
	//$username = phpCAS.getUser();



	/* Step 2 - Query local DB */
	echo "Step 2";
	$servername = "localhost";
	$username = "root";
	$password = "s34h4wks";
	$dbname = "bookstore";
	
	echo "attempting to connect";	
	$conn = mysqli_connect($servername, $username, $password, $dbname);

	if(mysqli_connect_errno()){
		die("Connection failed: " . mysqli_connect_error());
	}
	echo "we connected <br>";
	//Case 1 -- ISBN entered (this is enough info)
	if(isset($_POST['Isbn'])){
		echo "we found the ISBN
		\n";
		echo "the isbn is
		";
		$isbn = $_POST['Isbn'];
		var_dump($isbn);
		echo "set variable from post";
		$stmt = $conn->prepare("SELECT * FROM BOOKS WHERE isbn = ?");
		echo "binding parameter<br>";
		$stmt->bind_param('s', $isbn);
		echo "executing<br>";
		$stmt->execute();
		echo "setting it to result<br>";
		$stmt->bind_result($isbn1,$title,$publisher,$year,$edition,$tstamp); //Result stored in $result
		$stmt->fetch();
		printf("Test title: %s Year: %s \n", $title, $year);
		$stmt->close();
		echo "we found the ISBN and ran the SQL<br>";
	}else if(isset($_POST['Title']) && !isset($_POST['Author'])){
		//Case 2 -- Just Title of Book entered
		echo "just the title<br>";
		$title = $_POST['Title'];
		$stmt = $conn->prepare("SELECT * FROM BOOKS WHERE title = ?");
		$stmt->bind_param("s", $title);
		$stmt->execute();
		$stmt->bind_result($result); //Result stored in $result
		$stmt->fetch();
		$stmt->close();
	}else if(isset($_POST['Author']) && !isset($_POST['Title'])){
		//Case 3 -- Just author entered
		echo "just the author";
		$author = $_POST['Author'];
		$stmt = $conn->prepare("SELECT * FROM BOOKS WHERE author = ?");
		$stmt->bind_param("s", $author);
		$stmt->execute();
		$stmt->bind_result($result); //Result stored in $result
		$stmt->fetch();
		$stmt->close();
	}else if(isset($_POST['Author']) && isset($_POST['Title'])){
		//Case 4 -- Title + Author entered
		echo "title and author";
		$author = $_POST['Author'];
		$title = $_POST['Title'];
		$stmt = $conn->prepare("SELECT * FROM BOOKS WHERE title = ? AND author = ?");
		$stmt->bind_param("ss", $title, $author);
		$stmt->execute();
		$stmt->bind_result($result); //Result stored in $result
		$stmt->fetch();
		$stmt->close();
	}else{
		//Error?
		$result = NULL;
		echo "Not enough info given.\n";
	}

	/* Check if any results came back */  
	$numResults = getNumberOfResultsInResultSet($isbn);
	if($numResults > 0){
		//We have a local match. No need to scrape.
		$needScrape = FALSE;
		echo "no scraping";
	}else{
		//No results found. Must scrape the web
		$needScrape = TRUE;
		echo "we need to scrape";
	}




	if($needScrape){
		//case 1, ISBN is supplied
		echo "We must scrape";
		if(isset($_POST['Isbn'])){
			$suppliedIsbn = $_POST['Isbn'];
			$googleURL = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $suppliedIsbn;
			echo "$googleURL";
			$page = file_get_contents($googleURL);
			$data = json_decode($page, true);
			echo "attempting to parse ISBN\n";
			$title = $data['items'][0]['volumeInfo']['title'];
			$authors = @implode(",", $data['items'][0]['volumeInfo']['authors']);  
			$publisher = $data['items'][0]['volumeInfo']['publisher'];
			//echo "Pagecount = " . $data['items'][0]['volumeInfo']['pageCount'];
			$thumbnail = $data['items'][0]['volumeInfo']['imageLinks']['thumbnail'];
			$date = $data['items'][0]['volumeInfo']['publishedDate'];
			$year = substr($date, 0, 4);
			echo "Attempting to add to database\n";
			//Add to local database
			$stmt = $conn->prepare("INSERT INTO BOOKS (isbn, title, publisher, year, thumbnail) VALUES (?,?,?,?,?)");
			$stmt->bind_param('sssss', $suppliedIsbn, $title, $publisher, $year, $thumbnail);
			$stmt->execute();
			$stmt->close();
			echo "ADDED TO DATABASE";
		}

	}


?>