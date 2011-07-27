<?php

include_once("header.php");

xoops_cp_header();

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$mangosAdmin = new ModuleAdmin();
}

$mangosAdmin->addInfoBox("Title");
$mangosAdmin->addInfoBoxLine("Title", 'Text %s ...', 'value', 'red', 'default');
echo $mangosAdmin->addNavigation('about.php');
echo $mangosAdmin->renderabout('9MYQB7GUK5MCS', true);




xoops_cp_footer();
?>