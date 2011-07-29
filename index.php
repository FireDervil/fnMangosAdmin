<?php

include 'header.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'functions.mangos.php';

xoops_loadLanguage('misc', 'fnMangosAdmin');

$xoopsOption['template_main']= 'fnma_index.html';
include XOOPS_ROOT_PATH."/header.php";
$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );
$news_array = $fnmaDB["sys"]->select("SELECT * FROM ".$xoopsDB->prefix('fnma_news')." ORDER BY id ASC");

$xoopsTpl->assign('result', $news_array);
$config['date'] = "%d.%m.%Y %H:%M";
$config['time'] = '%H:%M:%S';

$xoopsTpl->assign(array(
	"lang_welcomemsg" => sprintf(_MD_FNMA_WELCOME, htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES)), 
	"lang_currenttime" => sprintf(_MD_FNMA_TIMENOW, formatTimestamp(time(),"m")),
	"config" => $config,
	));

		
include_once XOOPS_ROOT_PATH.'/footer.php';

?>