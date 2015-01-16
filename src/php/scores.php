<?php
include_once("minuit4/application/class.Singleton.php");
include_once("minuit4/db/class.MysqlHandler.php");
include_once("minuit4/db/class.Query.php");
include_once("minuit4/data/class.Encoding.php");
include_once("minuit4/data/class.SimpleJSON.php");

$actions = array("liste", "save");
if(!isset($_GET["action"]) || empty($_GET["action"]) || !in_array($_GET["action"], $actions))
	die("bouboup!");
$_GET["action"]();

function save()
{
	if(empty($_POST) || !check(array("user_snake", "score_snake")))
		die("informations manquantes");
	$query = Query::insert(array("score_snake"=>$_POST["score_snake"],"user_snake"=>$_POST["user_snake"],"date_snake"=>"now()","ip_snake"=>$_SERVER["REMOTE_ADDR"]))->into("snake")->get();
	MysqlHandler::getInstance()->fromQuery($query);
	liste();
}

function liste()
{
	$query = Query::select("user_snake, score_snake, date_snake", "snake")->order("score_snake", "DESC")->limit(0, 5)->get();
	$data = MysqlHandler::getInstance()->fromQuery($query);
	header("Content-type: application/json");
	echo SimpleJSON::encode($data);
}

function check($pArray)
{
	foreach($pArray as $key)
	{
		if(!isset($_POST[$key]) || empty($_POST[$key]))
			return false;
	}
	return true;
}