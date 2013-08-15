<?php
// require_once "retrieveText.php";
require_once 'zebra/Zebra_Database.php';
require_once 'login.php';
// echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
// header('Content-Type: text/html; charset=utf-8');
if(isset($_GET["method"]))
$method = $_GET["method"];
if(isset($method)&&!empty($method)){
	switch ($method) {
		case 'retrieveText':
			$doc_id = $_GET["doc_id"];
			$result = retrieveText($doc_id);
			break;
		
		case 'totalDocs':
			$result = getTotalDocs();
			break;

		case 'submitRating':
			$result = submitRating();
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
				
	}

	exit(json_encode($result));
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




function getTotalDocs(){
	$db = connectToDatabase();
	$db->query("SELECT * FROM Document");
	$rows = $db->returned_rows;
	return array("status_code"=>200,"total_docs"=>$rows);
}

function submitRating($doc_id,$user_id){


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


// print_r(login(1, "password"));
// $db = connectToDatabase();
// print_r(retrieveText(1));

?>