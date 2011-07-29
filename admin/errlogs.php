<?php

include_once("header.php");

xoops_cp_header();

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();

}
$op = !empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"default");


switch($op){
	
case 'main':
default:



break;	
}

xoops_cp_footer();
?>