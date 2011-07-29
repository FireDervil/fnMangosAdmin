<?php

include_once("header.php");

xoops_cp_header();

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();
}



xoops_cp_footer();
?>