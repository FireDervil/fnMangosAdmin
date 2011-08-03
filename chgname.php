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

	case'changeName':
	
	global $fnmaConfig, $fnmaDB, $xoopsDB;
	$Char = new Char;
	
	// check is username empty
	if(empty($_POST['newname']))
	{
		redirect_header('chgname.php', 1, _MD_FNMA_TOOL_NEWERROR1);
		return FALSE;
	}
	// check to see if the module is active by admin
	if($fnmaConfig['char_rename'] == 0)
	{
		redirect_header('chgname.php', 1, _MD_FNMA_TOOL_MODERROR2);
		return FALSE;
	}
	
	if($fnmaUser['web_points'] >= $fnmaConfig['char_rename_pts'])
	{	
		if($Char->checkNameExists($_POST['newname']) == FALSE)
		{
			if($Char->isOnline($_POST['id']) == FALSE)
			{
				if($Char->setName($_POST['id'], $_POST['newname']) == TRUE)
				{
					$fnmaDB["sys"]->query("UPDATE ".$xoopsDB->prefix('fnma_account_extend')." SET
						web_points=(web_points - ".$fnmaConfig['char_rename_pts']."), 
						points_spent=(points_spent + ".$fnmaConfig['char_rename_pts'].")  
					   WHERE account_id = ".$fnmaUser['id']." LIMIT 1"
					);
					redirect_header('chgname.php', 1, sprintf(_MD_FNMA_TOOL_CHGNSUCESS, $_POST['newname']));
				}
			} else {
					redirect_header('chgname.php', 1, _MD_FNMA_TOOL_CHGERROR3);
			}
		} else {
			redirect_header('member.php', 1, _MD_FNMA_TOOLS_NEXISTS);
		}
	} else{
		redirect_header('member.php', 1, _MD_FNMA_TOOLS_NENOUGHP);
	}
	
	exit();
	break;

	case 'main':
	default:
	
	$xoopsOption['template_main']= 'fnma_misc_chgname.html';
	include XOOPS_ROOT_PATH."/header.php";
	$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
	$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );
	
	$xoopsTpl->assign('char_list', $char_list);
	$xoopsTpl->assign('is_active_chgname', '1');
	$xoopsTpl->assign('process_costs', sprintf(_MD_FNMA_TOOL_COSTS, $fnmaConfig['char_rename_pts']));
	break;
		
}
include_once XOOPS_ROOT_PATH.'/footer.php';

?>