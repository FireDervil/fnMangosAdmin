<?php

defined('XOOPS_ROOT_PATH') or die('Restricted access');


$username = !isset($_POST['username']) ? '' : trim($_POST['username']);
$password = !isset($_POST['password']) ? '' : trim($_POST['password']);

if ($username == '' || $password == '') {
    redirect_header(XOOPS_URL.'/modules/fnMangosAdmin/user.php', 1, _MD_FNMA_ERROR_FIELD_EMPTY);
    exit();
}

if((strlen($username) > 20 ) || (strlen($password) > '20'))
  {
	  redirect_header(XOOPS_URL.'/modules/fnMangosAdmin/user.php', 1, _MD_FNMA_MIN_USER_PASS_LENGTH);
	  exit();
  }


$fnmaUser['selected_realm'] = $GLOBALS['selected_realm'];
$login = $fnmaAccount->login($username, $password);
$fnmaUser['id'] = $fnmaAccount->getId($username);


if (false != $login) {
    if (0 == $fnmaAccount->getGmLevel($fnmaUser['id'])) {
        redirect_header(XOOPS_URL.'/index.php', 5, _US_NOACTTPADM);
        exit();
    }
    if ($xoopsModuleConfig['closeLogin'] == 1) {
        $allowed = false;
        foreach ($fnmaAccount->getGroups() as $group) {
            if (in_array($group, $xoopsModuleConfig['closesite_okgrp']) || XOOPS_GROUP_ADMIN == $group) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            redirect_header(XOOPS_URL.'/index.php', 1, _NOPERM);
            exit();
        }
    }
 	$fnmaAccount->setVisit($fnmaUser['id'], time());
  	// Regenrate a new session id and destroy old session
    $GLOBALS["sess_handler"]->regenerate_id(true);
    $_SESSION = array();
    $_SESSION['fnmaUserId'] = $fnmaAccount->getId($username);
    $_SESSION['fnmaUserProfile'] = $fnmaAccount->getProfile();
 	$_SESSION['selected_realm'] = '1';
	
 	// Set cookie for rememberme
    if (!empty($xoopsModuleConfig['usercookie'])) {
        if (!empty($_POST["rememberme"])) {
            setcookie($xoopsModuleConfig['usercookie'], $_SESSION['fnmaUserID'] . '-' . md5($password . XOOPS_DB_NAME . XOOPS_DB_PASS . XOOPS_DB_PREFIX), time() + 31536000, '/', XOOPS_COOKIE_DOMAIN, 0);
        } else {
            setcookie($xoopsModuleConfig['usercookie'], 0, -1, '/', XOOPS_COOKIE_DOMAIN, 0);
        }
    }

    if (!empty($_POST['xoops_redirect']) && !strpos($_POST['xoops_redirect'], 'register')) {
        $_POST['xoops_redirect'] = trim($_POST['xoops_redirect']);
        $parsed = parse_url(XOOPS_URL);
        $url = isset($parsed['scheme']) ? $parsed['scheme'].'://' : 'http://';
        if (isset( $parsed['host'])) {
            $url .= $parsed['host'];
            if (isset( $parsed['port'])) {
                $url .= ':' . $parsed['port'];
            }
        } else {
            $url .= $_SERVER['HTTP_HOST'];
        }
        if (@$parsed['path']) {
            if (strncmp($parsed['path'], $_POST['xoops_redirect'], strlen( $parsed['path']))) {
                $url .= $parsed['path'];
            }
        }
        $url .= $_POST['xoops_redirect'];
    } else {
        $url = XOOPS_URL . '/modules/fnMangosAdmin/member.php';
    }
    redirect_header($url, 1, sprintf(_MD_FNMA_LOGGINGU, $fnmaAccount->getUsername($fnmaUser['id'])), false);
} else if (empty($_POST['xoops_redirect'])) {
    redirect_header(XOOPS_URL . '/modules/fnMangosAdmin/user.php', 5, $xoopsAuth->getHtmlErrors());
} else {
    redirect_header(XOOPS_URL . '/modules/fnMangosAdmin/user.php?xoops_redirect=' . urlencode(trim($_POST['xoops_redirect'])), 5, $xoopsAuth->getHtmlErrors(), false);
}
exit();
?>