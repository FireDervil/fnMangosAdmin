<?php

include_once "../../mainfile.php";
include_once XOOPS_ROOT_PATH . DS . 'modules' . DS . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'common.php';
include_once XOOPS_ROOT_PATH."/class/pagenav.php";

xoops_loadLanguage('header', 'fnMangosAdmin');
$xoopsOption['template_main']= 'fnma_header.html';
include XOOPS_ROOT_PATH."/header.php";
$time = time();
$xoopsTpl->assign('lang_currenttime', sprintf(_MH_CURRTIME, $time));

?>