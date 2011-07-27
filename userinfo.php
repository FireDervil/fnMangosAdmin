<?php


include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'defines.php';

xoops_loadLanguage('user', 'fnMangosAdmin');
xoops_loadLanguage('misc', 'fnMangosAdmin');

// check for $_SESSION var
$uid = intval($_GET['uid']);
if ($uid <= 0) {
    redirect_header('index.php', 3, _MD_FNMA_SELECTNG);
    exit();
}

// First we need to load the users profile
$profile = $fnmaAccount->getProfile($uid);

if (is_array($profile)) {	
        $xoopsOption['template_main'] = 'fnma_userinfo.html';
        include $GLOBALS['xoops']->path('header.php');
		$xoopsOption['xoops_pagetitle'] = sprintf(_MD_FNMA_ALL_ABOUT, $fnmaAccount->getUsername($_SESSION['fnmaUserId']));
		$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
		
		$xoopsTpl->assign(array(
					"lang_welcomemsg" => sprintf(_MD_FNMA_WELCOME, htmlspecialchars($profile['username'], ENT_QUOTES)), 
				"lang_lastvisit" => sprintf(_MD_FNMA_LASTVISIT, formatTimestamp($profile['last_visit'])), 
				"lang_currenttime" => sprintf(_MD_FNMA_TIMENOW, formatTimestamp(time(),"m")))
		);
		// array for language
		// TODO: next updates set translation in template
		$xoopsTpl->assign(array(
					'lang_allaboutuser' => sprintf(_MD_FNMA_ALL_ABOUT, $fnmaAccount->getUsername($_SESSION['fnmaUserId'])),
					'lang_avatar' => _MD_FNMA_AVATAR,
					'lang_home' => _MD_FNMA_HOME,
					'lang_editprofile' => _MD_FNMA_EDIT_PROFILE,
					'lang_inbox' => _MD_FNMA_INBOX,
					'lang_logout' => _MD_FNMA_LOGOUT,
					'lang_deleteaccount' => _MD_FNMA_DELETE_ACCOUNT,
					'lang_name' => _MD_FNMA_NAME,
					'lang_expension' => _MD_FNMA_EXPENSION,
					'lang_email' => _MD_FNMA_EMAIL,
					'lang_privmsg' => _MD_FNMA_PRIVMSG,
					'lang_on_realm' => _MD_FNMA_ON_REALM,
					'lang_locale' => _MD_FNMA_LOCALE,
					'lang_statistics' => _MD_FNMA_STATISTIK,
					'lang_membersince' => _MD_FNMA_MEMBER_SINCE,
					'lang_rank' => _MD_FNMA_MEMBER_RANK,
					'lang_user_rank_level' => _MD_FNMA_RANK_LEVEL,
					'lang_lastlogin' => _MD_FNMA_LAST_LOGIN,
					'lang_signature' => _MD_FNMA_SIGNATURE,
					'lang_allaboutuser' => _MD_FNMA_ACCOUNT_FACTS,
					'lang_logout' => _MD_FNMA_LOGOUT,
					)
		);			
		$realm_name = getRealmById($profile['active_realm_id']);

		// data array for smarty
		$xoopsTpl->assign(array(
					"user_name" => $profile['username'],
					"user_lastlogin" => formatTimestamp($profile['last_visit']),
					"user_ranktitle" => $profile["title"], 
					"user_expansion" => getExpensionById($profile['expansion']),
					"user_joindate" => $profile["joindate"],
					"user_rankimage" => 'pixel.gif',
					"user_email" => $profile['email'],
					"user_rank_level" => $profile['account_level'],
					"user_on_realm"  => $realm_name['name'],
					"user_locale" => $realm_timezone_def[$profile['locale']],
					
					));
					
		$xoopsTpl->assign('user_ownpage', true);
		$xoopsTpl->assign('user_candelete', false);					
		include $GLOBALS['xoops']->path('footer.php');
} else {


}

?>