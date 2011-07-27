<?php

include 'header.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'functions.mangos.php';

xoops_loadLanguage('misc', 'fnMangosAdmin');

$xoopsOption['template_main']= 'fnma_member.html';
include XOOPS_ROOT_PATH."/header.php";
$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );

if (empty($_SESSION['fnmaUserId'])) {
    redirect_header('user.php', 1, _MD_FNMA_NOREGISTER);

}

$xoopsTpl->assign('xoops_pagetitle', $xoops_pagetitle);
$xoopsTpl->assign('xoops_module_header', $xoops_module_header);

global $fnmaAccount;
$accData = $fnmaAccount->getProfile($_SESSION['fnmaUserId']);

$xoopsTpl->assign(array(
	"lang_welcomemsg" => sprintf(_MD_FNMA_WELCOME, htmlspecialchars($accData['username'], ENT_QUOTES)), 
	"lang_lastvisit" => sprintf(_MD_FNMA_LASTVISIT, formatTimestamp($accData['last_visit'])), 
	"lang_currenttime" => sprintf(_MD_FNMA_TIMENOW, formatTimestamp(time(),"m")))
	);

if($accData['locked'] > '0'){
	$locked = _MD_FNMA_LOCKED_TRUE;
} else {
	$locked = _MD_FNMA_LOCKED_FALSE;
}
if($accData['account_level'] == '0'){
	$xoopsTpl->assign(array(
	"uid"				=> $accData['id'],
	"account_status" 	=> $locked,
	"total_votes"		=> 0,
	"joindate"			=> $accData['joindate'],
	"last_ip"			=> $accData['last_ip'],
	"web_points"		=> 0,
	"points_earned"		=> 0,
	"points_spend"		=> 0,
	"account_level"		=> 1,
	"user_level"		=> "User",
	"total_donations"	=> 0,
	"currency"			=> '€',
	)
);
} else {
$xoopsTpl->assign(array(
	"uid"				=> $accData['id'],
	"account_status" 	=> $locked,
	"total_votes"		=> $accData['total_votes'],
	"joindate"			=> $accData['joindate'],
	"last_ip"			=> $accData['last_ip'],
	"web_points"		=> $accData['web_points'],
	"points_earned"		=> $accData['points_earned'],
	"points_spend"		=> $accData['points_spent'],
	"user_level"		=> $accData['title'],
	"total_donations"	=> $accData['total_donations'],
	"currency"			=> '€',
	)
);
}
		
include_once XOOPS_ROOT_PATH.'/footer.php';

?>