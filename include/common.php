<?php

/**
* $Id: common.php,v 1.5 2005/03/23 18:08:30 fx2024 Exp $
* Module: SmartSection
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

if( !defined("FNMA_DIRNAME") ){
	define("FNMA_DIRNAME", 'fnMangosAdmin');
}

if( !defined("FNMA_URL") ){
	define("FNMA_URL", XOOPS_URL.'/modules/'.FNMA_DIRNAME.'/');
}
if( !defined("FNMA_ROOT_PATH") ){
	define("FNMA_ROOT_PATH", XOOPS_ROOT_PATH.'/modules/'.FNMA_DIRNAME.'/');
}

if( !defined("FNMA_IMAGES_URL") ){
	define("FNMA_IMAGES_URL", FNMA_URL.'/images/');
}

include_once(FNMA_ROOT_PATH . "class" . DS . "kernel.php");
include_once(FNMA_ROOT_PATH . "include" . DS . "functions.php");
include_once(FNMA_ROOT_PATH . "class" . DS . "keyhighlighter.php");
include_once(FNMA_ROOT_PATH . "include" . DS . "functions.mangos.php");

// creating the very basic kernel 
$fnmaCore = new mainCore;

// Creating the fnMangosAdmin object
$fnmaModule =& fnGetModuleInfo();
$myts = MyTextSanitizer::getInstance();
$sfnmaModuleName = $myts->displayTarea($fnmaModule->getVar('name'));

// Creating the fnMangosAdmin config Object
$fnmaConfig =& fnGetModuleConfig();

// include additional files for the handler 
include_once(FNMA_ROOT_PATH . "class" . DS . "shops.php");
include_once(FNMA_ROOT_PATH . "class" . DS . "votes.php");
include_once(FNMA_ROOT_PATH . "class" . DS . "realms.php");
include_once(FNMA_ROOT_PATH . "class" . DS . "stories.php");
include_once(FNMA_ROOT_PATH . "class" . DS . "account" . DS . "account.php");

// Creating the shops handler object
$fnma_shop_handler =& xoops_getmodulehandler('shops', FNMA_DIRNAME);

// TODO:
// in progress
// Creating the realms handler object
//$fnma_realms_handler =& xoops_getmodulehandler('realms', FNMA_DIRNAME);

// Creating the votes handler object
//$fnma_votes_handler =& xoops_getmodulehandler('votes', FNMA_DIRNAME);

// Creating the stories handler object
//$fnma_stories_handler =& xoops_getmodulehandler('stories', FNMA_DIRNAME);
//dev($fnma_stories_handler);

// settings for the error functions
ini_set('log_errors', TRUE);
ini_set('html_errors', FALSE);
ini_set('error_log', 'docs/logs/error_log.log');
ini_set('display_errors', TRUE);


// settings for the debug function
$fnmaConfig['DO_DEBUG'] = TRUE;
if(!$fnmaConfig['DO_DEBUG'] == 'true') {
	include_once(FNMA_ROOT_PATH . "include" . DS . "debugger.php");
}
// include DB  class
include_once(FNMA_ROOT_PATH . 'class' . DS . 'database'. DS .'mysql.php');

// build the global database
$fnmaDB = array();
$fnmaDB["logon"] = new MangosDatabase;
$fnmaDB["logon"]->connect($fnmaConfig['mysql_host'], $fnmaConfig['mysql_user'], $fnmaConfig['mysql_passwd'], $fnmaConfig['realm_db_name'], XOOPS_DB_CHARSET);

// Check the database status. 0 = cannot connect, 1 = success, 2 = DB doesnt exist
if($fnmaDB["logon"]->status() != 1)
{
	echo "Cannot connect to the Realm database. Please make sure you have run the installer to properly set the DB info in the database.";
	die();
}

$fnmaDB["char"] = new MangosDatabase;
$fnmaDB["char"]->connect($fnmaConfig['mysql_host'], $fnmaConfig['mysql_user'], $fnmaConfig['mysql_passwd'], $fnmaConfig['char_db_name'], XOOPS_DB_CHARSET);
// Check the database status. 0 = cannot connect, 1 = success, 2 = DB doesnt exist
if($fnmaDB["char"]->status() != 1)
{
	echo "Cannot connect to the Realm database. Please make sure you have run the installer to properly set the DB info in the database.";
	die();
}

$fnmaDB["world"] = new MangosDatabase;
$fnmaDB["world"]->connect($fnmaConfig['mysql_host'], $fnmaConfig['mysql_user'], $fnmaConfig['mysql_passwd'], 'ytdbm', XOOPS_DB_CHARSET);
// Check the database status. 0 = cannot connect, 1 = success, 2 = DB doesnt exist
if($fnmaDB["world"]->status() != 1)
{
	echo "Cannot connect to the World database. Please make sure you have run the installer to properly set the DB info in the database.";
	die();
}

$fnmaDB["sys"] = new MangosDatabase; 
$fnmaDB["sys"]->connect(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, XOOPS_DB_NAME, XOOPS_DB_CHARSET);
// Check the database status. 0 = cannot connect, 1 = success, 2 = DB doesnt exist
if($fnmaDB["sys"]->status() != 1)
{
	echo "Cannot connect to the Realm database. Please make sure you have run the installer to properly set the DB info in the database.";
	die();
}

// include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'fn_account.php';
//  include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'fn_account.php';
include_once XOOPS_ROOT_PATH . "Frameworks". DS . "art". DS . "functions.php";
$myts =& MyTextSanitizer::getInstance();

$fnmaAccount = new fnmaAccount();




?>