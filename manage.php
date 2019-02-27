<?php
require_once('init.php');
require_once('functions.php');
	#print_r($_SESSION);
	//system("gcc ./questions/reverse/1/1.c");
//echo $_GET['id'];
#print_r($_GET);

#Header("Content-type: application/octet-stream"); 
#Header("Content-Disposition: attachment; filename=".$_GET['ques_id'].'.zip'); 
#readfile("./questions/reverse/Kood'sRe1.zip");

#[type] => 3 [typeid] => 1
if(!isset($_GET['check'])){
	#header('Location: ./404.html');
	include '404.html';
	die();
}
if(file_exists('./questions/'.$questionType[$_SESSION['type']].'/'.$_SESSION['typeid'])){
	#echo $_SESSION['type'].'--'.$questionType[$_SESSION['type']].'cz';
	header('Content-type:application/octet-stream');
	header('Content-Disposition:attachment;filename='.$_GET['check']);
	readfile('./questions/'.$questionType[$_SESSION['type']].'/'.$_SESSION['typeid']);
}
else{
	include '404.html';
	die();
}
?>
