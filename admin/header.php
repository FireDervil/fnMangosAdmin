<?php

include "../../../include/cp_header.php";

include_once(XOOPS_ROOT_PATH."/class/xoopsmodule.php");
include_once XOOPS_ROOT_PATH."/class/xoopstree.php";
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
include_once XOOPS_ROOT_PATH."/class/tree.php";
include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
include_once XOOPS_ROOT_PATH."/class/pagenav.php";
include_once XOOPS_ROOT_PATH."/class/xoopstopic.php";
include_once XOOPS_ROOT_PATH."/class/xoopsform/grouppermform.php";
include_once("../include/functions.php");

include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'kernel.php';

$fnmaCore = new mainCore;

include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'functions.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'functions.mangos.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'functions.admin.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'account'. DS .'account.php';

$fnmaCore->setGlobals();

include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'database'. DS .'mysql.php';

// set up databse global
$fnmaDB = array();
$fnmaDB["logon"] = new MangosDatabase;
$fnmaDB["logon"]->connect($xoopsModuleConfig['mysql_host'], $xoopsModuleConfig['mysql_user'], $xoopsModuleConfig['mysql_passwd'], $xoopsModuleConfig['realm_db_name'], XOOPS_DB_CHARSET);

// Check the database status. 0 = cannot connect, 1 = success, 2 = DB doesnt exist
if($fnmaDB["logon"]->status() != 1)
{
	echo "Cannot connect to the Realm database. Please make sure you have run the installer to properly set the DB info in the database.";
	die();
}

$fnmaDB["char"] = new MangosDatabase;
$fnmaDB["char"]->connect($xoopsModuleConfig['mysql_host'], $xoopsModuleConfig['mysql_user'], $xoopsModuleConfig['mysql_passwd'], $xoopsModuleConfig['char_db_name'], XOOPS_DB_CHARSET);
// Check the database status. 0 = cannot connect, 1 = success, 2 = DB doesnt exist
if($fnmaDB["char"]->status() != 1)
{
	echo "Cannot connect to the Realm database. Please make sure you have run the installer to properly set the DB info in the database.";
	die();
}

$fnmaDB["world"] = new MangosDatabase;
$fnmaDB["world"]->connect($xoopsModuleConfig['mysql_host'], $xoopsModuleConfig['mysql_user'], $xoopsModuleConfig['mysql_passwd'], 'ytdbm', XOOPS_DB_CHARSET);
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