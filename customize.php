<?php

include 'header.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'functions.mangos.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'characters' . DS .'chars.php';
xoops_loadLanguage('misc', 'fnMangosAdmin');

if (empty($_SESSION['fnmaUserId'])) {
    redirect_header('user.php', 1, _MD_FNMA_NOREGISTER);

}

$op = 'main';

if (isset($_POST['op'])) {
    $op = trim($_POST['op']);
} else if (isset($_GET['op'])) {
    $op = trim($_GET['op']);
}

// Load the accounts character list
$char_list = $fnmaAccount->getCharacterList($_SESSION['fnmaUserId']);
$fnmaUser = $fnmaAccount->getProfile($_SESSION['fnmaUserId']);

// TODO: this goes in xoopsConfig
$xoopsModuleConfig['is_active_custoize'] = 1;
$xoopsModuleConfig['module_charcustomize'] = 1;
$xoopsModuleConfig['module_charcustomize_pts'] = 5;






switch($op)
{
	case 'recustomize':
		
	global $xoopsModuleConfig, $fnmaDB, $xoopsDB;
	$Char = new Char;
	
	if($xoopsModuleConfig['module_charcustomize'] == 0)
	{
		redirect_header('customize.php', 1, _MD_FNMA_TOOLS_NTRICKYH);
		exit();
	}
	
	// Check to see the user has enough points
	if($fnmaUser['web_points'] >= $xoopsModuleConfig['module_charcustomize_pts'])
	{
		if($Char->setCustomize($_POST['id']) == TRUE)
		{
			$fnmaDB["sys"]->query("UPDATE ".$xoopsDB->prefix('fnma_account_extend')." SET 
				web_points=(web_points - ".$xoopsModuleConfig['module_charcustomize_pts']."), 
				points_spent=(points_spent + ".$xoopsModuleConfig['module_charcustomize_pts'].")  
			   WHERE account_id = ".$fnmaUser['id']." LIMIT 1"
			);
			redirect_header('customize.php', 1, _MD_FNMA_TOOLS_CHARSETFRE);
			exit();
		} else {
			redirect_header('member.php', 1, _MD_FNMA_TOOLS_ISASET);
			exit();
		}
	} else {
		redirect_header('member.php', 1, _MD_FNMA_TOOLS_NENOUGHP);
		exit();
	}
	break;
	
	case 'main':
	default:

	$xoopsOption['template_main']= 'fnma_misc_customize.html';
	include XOOPS_ROOT_PATH."/header.php";
	$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
	$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );

	$xoopsTpl->assign('lang_points_cost', sprintf(_MD_FNMA_TOOL_COSTS, $xoopsModuleConfig['module_charcustomize_pts']));
	$xoopsTpl->assign('char_list', $char_list);


	break;	
}
include_once XOOPS_ROOT_PATH.'/footer.php';

?>