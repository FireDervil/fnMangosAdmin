<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

xoops_loadLanguage('user', 'fnMangosAdmin');

$op = 'main';

if (isset($_POST['op'])) {
    $op = trim($_POST['op']);
} else if (isset($_GET['op'])) {
    $op = trim($_GET['op']);
}

if ($op == 'login') {
	include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include/checklogin.realm.php';
	// TODO: take new redirect to check login via ajax and redirect once
    exit();
}

if ( $op == 'main' ) {
 if (!$fnmaUser) {
        $xoopsOption['template_main'] = 'fnma_login_form_realm.html';
        include XOOPS_ROOT_PATH."/header.php";
		$xoopsOption['xoops_module_header']= $xoops_module_header;
		$xoopsTpl->assign('xoops_pagetitle', $xoops_pagetitle);
		$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
	
        $GLOBALS['xoopsTpl']->assign('lang_login', _MD_FNMA_LOGIN);
		$GLOBALS['xoopsTpl']->assign('lang_username', _MD_FNMA_USERNAME);
        if (isset($_GET['xoops_redirect'])) {
            $GLOBALS['xoopsTpl']->assign('redirect_page', htmlspecialchars(trim($_GET['xoops_redirect']), ENT_QUOTES));
        }
        if ($GLOBALS['fnmaConfig']['useusercookie']) {
            $GLOBALS['xoopsTpl']->assign('lang_rememberme', _MD_FNMA_REMEMBERME);
        }
        $GLOBALS['xoopsTpl']->assign('lang_password', _MD_FNMA_PASSWORD);
        $GLOBALS['xoopsTpl']->assign('lang_notregister', _MD_FNMA_NOTREGISTERED);
        $GLOBALS['xoopsTpl']->assign('lang_lostpassword', _MD_FNMA_LOSTPASSWORD);
        $GLOBALS['xoopsTpl']->assign('lang_noproblem', _MD_FNMA_NOPROBLEM);
        $GLOBALS['xoopsTpl']->assign('lang_youremail', _MD_FNMA_YOUREMAIL);
        $GLOBALS['xoopsTpl']->assign('lang_sendpassword', _MD_FNMA_SENDPASSWORD);
        $GLOBALS['xoopsTpl']->assign('mailpasswd_token', $GLOBALS['xoopsSecurity']->createToken());
        include XOOPS_ROOT_PATH."/footer.php";
        exit();
 }
    if (!empty($_GET['xoops_redirect'])) {
        $redirect = trim($_GET['xoops_redirect']);
        $isExternal = false;
        if ($pos = strpos($redirect, '://')) {
            $xoopsLocation = substr( XOOPS_URL, strpos( XOOPS_URL, '://' ) + 3);
            if (strcasecmp(substr($redirect, $pos + 3, strlen($xoopsLocation)), $xoopsLocation)) {
                $isExternal = true;
            }
        }
        if (!$isExternal) {
            header('Location: ' . $redirect);
            exit();
        }
    }
    header('Location: ./userinfo.php?uid=' . $GLOBALS['fnmaUser']['id']);
    exit();
}


if ($op == 'logout') {
    $message = '';
    $_SESSION = array();
    // Regenerate a new session id and destroy old session
    session_regenerate_id(true);
    setcookie($xoopsModuleConfig['usercookie'], 0, - 1, '/', XOOPS_COOKIE_DOMAIN, 0);
    setcookie($xoopsModuleConfig['usercookie'], 0, - 1, '/');
   /*
    // clear entry from online users table
    if (is_object($xoopsUser)) {
        $online_handler =& xoops_gethandler('online');
        $online_handler->destroy($xoopsUser->getVar('uid'));
    }
	*/
    $message = _MD_FNMA_LOGGEDOUT . '<br />' . _MD_FNMA_THANKYOUFORVISIT;
    redirect_header('index.php', 1, $message);
    exit();
}
	
	
	


?>