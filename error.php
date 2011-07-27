<?php



include 'header.php';

xoops_loadLanguage('error', 'fnMangosAdmin');

$xoopsOption['template_main']= 'fnma_201.html';
include XOOPS_ROOT_PATH."/header.php";

		$xoopsOption['xoops_module_header']= $xoops_module_header;
		$xoopsTpl->assign('xoops_pagetitle', $xoops_pagetitle);
		$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
$xoopsTpl->assign('lang_under_construction', _MD_FNMA_UC);
$xoopsTpl->assign('lang_uc_text', _MD_FNMA_UC_TEXT);

include XOOPS_ROOT_PATH."/footer.php";
?>