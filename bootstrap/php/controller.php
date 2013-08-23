<?php
// require_once "retrieveText.php";
require_once 'zebra/Zebra_Database.php';
require_once 'login.php';
// echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
// header('Content-Type: text/html; charset=utf-8');
if(isset($_REQUEST["method"]))
$method = $_REQUEST["method"];
if(isset($method)&&!empty($method)){
	switch ($method) {
		case 'resubmitRating':
			$result = resubmitRating();
			break;
		case 'documentsRated':
			$result = documentsRated();
			break;
		case 'retrieveText':
			$doc_id = $_GET["doc_id"];
			$result = retrieveText($doc_id);
			break;
		
		case 'totalDocs':
			$result = getTotalDocs();
			break;


		case 'login':
			$uid = $_GET['uid'];
			$password = $_GET['password'];
			$result = login($uid, $password);
			// echo "yeeeee";
			// $result = array("list"=>"sdsd");	
			break;
		case 'isLoggedIn':
			$result = isLoggedIn();
			break;
		case 'logout':
			@session_start();
			session_destroy();
			$result=array("status_code"=>200);
			break;
		case 'submitRating':
			// echo "string";
			@session_start();
			$result = submitRating();	
			break;	
		case 'alreadyRated':
			$result = alreadyRated();	
			break;
				
	}

	exit(json_encode($result));
}
function resubmitRating(){
	@session_start();
	if(isset($_SESSION['user'])&&!empty($_SESSION['user']))
	{
		$db = connectToDatabase();
		@session_start();
		$user = $_SESSION['user'];
		$doc_id = $_POST['doc_id'];
		$rating = $_POST['rating'];
		$text = $_POST['text'];
		$query = $db->query("DELETE FROM Document_Ratings WHERE user_id=$user AND doc_id=$doc_id");
		$query = $db->query("INSERT INTO Document_Ratings VALUES($doc_id,$rating,$user,'$text')");
		if($query)
		return array("status_code"=>200);
 		
 		

	}
	
	return array("status_code"=>400);

}

function documentsRated(){
	@session_start();
	if(isset($_SESSION['user'])&&!empty($_SESSION['user']))
	{
		$db = connectToDatabase();
		@session_start();
		$user = $_SESSION['user'];

		

		$query = $db->query("SELECT * FROM Document_Ratings WHERE user_id=$user");
		// if(!$query)
		// 	return array("status_code"=>400);
		// var_dump($query);
		$toRet = array("status_code"=>200);
		$arr = $db->fetch_assoc_all();
		$doc_ids = array();
		foreach($arr as $v){
    	$doc_id = $v['doc_id'];
    	array_push($doc_ids,$doc_id);
 		}
 		// array_push($toRet, "doc_ids"=>$doc_ids);
 		$toRet["doc_ids"]=$doc_ids;
 		return $toRet;

	}
	else{
		return array("status_code"=>400);
	}

}


function isLoggedIn(){
	@session_start();
	if(isset($_SESSION['user'])&&!empty($_SESSION['user']))
	{
		return array("status_code"=>200,"value"=>"yes");
	}
	else{
		return array('status_code'=>200,"value"=>"no" );
	}
}

function alreadyRated(){
	@session_start();
	if(!(isset($_SESSION['user'])&&!empty($_SESSION['user'])))
		return array("status_code"=>400);
	$user = $_SESSION['user'];
	$db = connectToDatabase();
	$query = $db->query();

}


function getTotalDocs(){
	$db = connectToDatabase();
	$db->query("SELECT * FROM Document");
	$rows = $db->returned_rows;
	return array("status_code"=>200,"total_docs"=>$rows);
}

function submitRating(){
	$doc_id = $_POST['doc_id'];
	@session_start();
	if(!isset($_SESSION['user'])||empty($_SESSION['user'])){
		return array('status_code' => 400);
	}
	else{
		$user = $_SESSION['user'];
		$db = connectToDatabase();
		$doc_id = $_POST['doc_id'];
		$rating = $_POST['rating'];
		$text = $_POST['text'];


		//check IF USER HAVE ALREADY rated
		$query = $db->query("SELECT * FROM Document_Ratings WHERE user_id=$user AND doc_id=$doc_id");
		if(!$query)
			return array("status_code"=>400);
		$num_rows = $db->returned_rows;
		if($num_rows){
			return array("status_code"=>302); //user already rated it. 
		}
		// echo $doc_id;
		// echo $rating;
		// echo $text;
		// echo $user;
		$query = $db->query("INSERT INTO Document_Ratings VALUES($doc_id,$rating,$user,'$text')");
		if($query)
		return array("status_code"=>200);
	}



}

function retrieveText($doc_id){

$db = connectToDatabase();
$query = $db->query("SELECT * FROM Document WHERE doc_id=$doc_id");
$text = $db->fetch_assoc_all();
// echo "string";
// print_r($text);
$rows = $db->returned_rows;
if($rows!=1) 
{
return array("status_code"=>404);
}
$text = $text[0]["doc_text"];
return array("status_code"=>200,"text"=>$text);


}

function connectToDatabase(){
$db = new Zebra_Database();
$db->debug = false;
$db->connect('localhost', 'irlab', 'irlabdaiict', 'Readability');
$db->show_debug_console();
$db->set_charset('utf8', 'utf8_general_ci');
return $db;

}

function login($userId, $password){
	$login = new Login($userId,$password);
	return $login->validateAndLogin(connectToDatabase());
}



// function submitRating(doc_id, user_id)


// print_r(login(1, "password"));
// $db = connectToDatabase();
// print_r(retrieveText(1));

?>