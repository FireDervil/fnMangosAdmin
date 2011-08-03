<?php

include 'header.php';

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

switch($op)
{
	case 'recustomize':
		
	global $fnmaConfig, $fnmaDB, $xoopsDB;
	$Char = new Char;
	
	if($fnmaConfig['char_customize'] == 0)
	{
		redirect_header('customize.php', 1, _MD_FNMA_TOOLS_NTRICKYH);
		exit();
	}
	
	// Check to see the user has enough points
	if($fnmaUser['web_points'] >= $fnmaConfig['char_customize_pnt'])
	{
		if($Char->setCustomize($_POST['id']) == TRUE)
		{
			$fnmaDB["sys"]->query("UPDATE ".$xoopsDB->prefix('fnma_account_extend')." SET 
				web_points=(web_points - ".$fnmaConfig['char_customize_pnt']."), 
				points_spent=(points_spent + ".$fnmaConfig['char_customize_pnt'].")  
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

	$xoopsTpl->assign('lang_points_cost', sprintf(_MD_FNMA_TOOL_COSTS, $fnmaConfig['char_customize_pnt']));
	$xoopsTpl->assign('char_list', $char_list);
	break;	
}
include_once XOOPS_ROOT_PATH.'/footer.php';

?>