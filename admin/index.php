<?php

include_once("header.php");

xoops_cp_header();

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();



$MangosAdmin->addInfoBox("fnMangosAdmin - News");

$MangosAdmin->addInfoBoxLine("fnMangosAdmin - News", 'The actual News Pages  are %s by the fnMangosAdmin News Editor', 'Updated', 'red', 'default');

$MangosAdmin->addConfigBoxLine('----Hello world----', 'default');

$folder = XOOPS_ROOT_PATH . '/uploads/fnMangosAdmin/';
$MangosAdmin->addConfigBoxLine($folder, 'folder');
$MangosAdmin->addConfigBoxLine(array($folder, '777'), 'chmod');

echo $MangosAdmin->addNavigation('index.php');
echo $MangosAdmin->renderIndex();
}
xoops_cp_footer();
?>