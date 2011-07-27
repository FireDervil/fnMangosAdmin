<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

xoops_loadLanguage('user', 'fnMangosAdmin');
include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');


$fnmaUser = $fnmaAccount->getProfile($_SESSION['fnmaUserId']);

// If not a user, redirect
if (!is_array($fnmaUser)) {
    redirect_header('index.php', 3, _MD_FNMA_NOEDITRIGHT);
    exit();
}


// initialize $op variable
$op = 'editprofile';
if (! empty($_POST['op'])) {
    $op = $_POST['op'];
}
if (! empty($_GET['op'])) {
    $op = $_GET['op'];
}

$myts =& MyTextSanitizer::getInstance();

if ($op == 'saveuser') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('index.php', 3, _MD_FNMA_NOEDIT_RIGHT . "<br />" . implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
        exit();
    }

	$uid = 0;
    if (!empty($_POST['uid'])) {
        $uid = intval($_POST['uid']);
    }
    if (empty($uid) || $fnmaUser['id'] != $uid) {
        redirect_header('index.php', 3, _MD_FNMA_NOEDIT_RIGHT);
        exit();
    }
	$errors = array();
    if ($xoopsModuleConfig['allow_changemail'] == 1) {
        $email = '';
        if (!empty($_POST['email'])) {
            $email = $myts->stripSlashesGPC(trim($_POST['email']));
        }
        if ($email == '' || ! checkEmail($email)) {
            $errors[] = _MD_FNMA_INVALID_MAIL;
        }
    }
	$password = '';
    if (!empty($_POST['password'])) {
        $password = $myts->stripSlashesGPC(trim($_POST['password']));
    }
    if ($password != '') {
        if (strlen($password) < $xoopsModuleConfig['minpass']) {
            $errors[] = sprintf(_MD_FNMA_PWDTOO_SHORT, $xoopsConfigUser['minpass']);
        }
        $vpass = '';
        if (!empty($_POST['vpass'])) {
            $vpass = $myts->stripSlashesGPC(trim($_POST['vpass']));
        }
        if ($password != $vpass) {
            $errors[] = _MD_FNMA_PASSWDS_NOT_SAME;
        }
    }
	if (count($errors) > 0) {
        include $GLOBALS['xoops']->path('header.php');
        echo '<div>';
        foreach ($errors as $er) {
            echo '<span class="red bold">' . $er . '</span><br />';
        }
        echo '</div><br />';
        $op = 'editprofile';
    } else {
		//$member_handler =& xoops_gethandler('member');
        $edituser['id'] = $uid;
        $edituser['username'] = $_POST['username'];
        if ($xoopsModuleConfig['allow_changemail'] == 1) {
            $email = $email;
        }else{
			$email = $_POST['email']; // TODO: set this to other var if next release
		}
        $user_viewemail = (!empty($_POST['user_viewemail'])) ? 1 : 0;
        $edituser['user_viewemail'] = $user_viewemail;
        $edituser['user_mailok'] = $_POST['user_mailok'];
        if (!empty($_POST['usecookie'])) {
            setcookie($xoopsModuleConfig['usercookie'], $fnmaUser['username'], time() + 31536000, '/', XOOPS_COOKIE_DOMAIN);
        } else {
            setcookie($xoopsModuleConfig['usercookie']);
        }
		$sq1 = $fnmaAccount->setEmail($uid, $email);
		$sq2 = $fnmaAccount->setPassword($uid, $password);

        if (!$sq1 || !$sq2) {
            include $GLOBALS['xoops']->path('header.php');
            echo "ERROR!";
            include $GLOBALS['xoops']->path('footer.php');
        } else {
            redirect_header('userinfo.php?uid=' . $uid, 1, _MD_FNMA_PROF_UPDATED);
        }
        exit();
    }
}

if ($op == 'editprofile') {
    include_once $GLOBALS['xoops']->path('header.php');
    include_once $GLOBALS['xoops']->path('include/xoopscodes.php');
    
	$fnmaUser = $fnmaAccount->getProfile($_SESSION['fnmaUserId']);
	
	echo '<a href="userinfo.php?uid=' . $fnmaUser['id'] . '" title="">' . _MD_FNMA_PROFILE . '</a>&nbsp;<span class="bold">&raquo;&raquo;</span>&nbsp;' . _MD_FNMA_EDIT_PROFILE . '<br /><br />';
	
	$form = new XoopsThemeForm(_MD_FNMA_EDIT_PROFILE, 'userinfo', 'edituser.php', 'post', true);
    $uname_label = new XoopsFormLabel(_MD_FNMA_USERNAME, $fnmaUser['username']);
    $form->addElement($uname_label);
    //$name_text = new XoopsFormText(_MD_FNMA_REALNAME, 'name', 30, 60, $xoopsUser->getVar('name', 'E'));
    //$form->addElement($name_text);
    $email_tray = new XoopsFormElementTray(_MD_FNMA_EMAIL, '<br />');
	
    if ($xoopsModuleConfig['allow_changemail'] == 1) {
        $email_text = new XoopsFormText('', 'email', 30, 60, $fnmaUser['email']);
    } else {
        $email_text = new XoopsFormLabel('', $fnmaUser['email']);
		$email_hidden = new XoopsFormHidden('email', $fnmaUser['email']);
    }
	$email_tray->addElement($email_text);
    $email_cbox_value = $xoopsUser->user_viewemail() ? 1 : 0;
    $email_cbox = new XoopsFormCheckBox('', 'user_viewemail', $email_cbox_value);
    $email_cbox->addOption(1, _MD_FNMA_ALLOW_VIEW_EMAIL);
    $email_tray->addElement($email_cbox);
    $form->addElement($email_tray);
    //$url_text = new XoopsFormText(_MD_FNMA_ACCOUNT_NAME, 'url', 30, 100, $fnmaUser['acc_name']);
    //$form->addElement($url_text);
	
    $cookie_radio_value = empty($_COOKIE[$xoopsModuleConfig['usercookie']]) ? 0 : 1;
    $cookie_radio = new XoopsFormRadioYN(_MD_FNMA_USECOOKIE, 'usecookie', $cookie_radio_value, _YES, _NO);
    $pwd_text = new XoopsFormPassword('', 'password', 10, 32);
    $pwd_text2 = new XoopsFormPassword('', 'vpass', 10, 32);
    $pwd_tray = new XoopsFormElementTray(_MD_FNMA_PASSWORD . '<br />' . _MD_FNMA_PASSWD_RETYPE);
    $pwd_tray->addElement($pwd_text);
    $pwd_tray->addElement($pwd_text2);
    $mailok_radio = new XoopsFormRadioYN(_MD_FNMA_MAIL_OK, 'user_mailok', $fnmaUser['user_mailok']);
	$username_hidden = new XoopsFormHidden('username', $fnmaUser['username']);
    $uid_hidden = new XoopsFormHidden('uid', $fnmaUser['id']);
    $op_hidden = new XoopsFormHidden('op', 'saveuser');
    $submit_button = new XoopsFormButton('', 'submit', _MD_FNMA_SAVECHANGES, 'submit');
	
    $form->addElement($pwd_tray);
    $form->addElement($cookie_radio);
    $form->addElement($mailok_radio);
	$form->addElement($email_hidden);
    $form->addElement($uid_hidden);
    $form->addElement($username_hidden);
    $form->addElement($op_hidden);
    $form->addElement($token_hidden);
    $form->addElement($submit_button);
	if ($xoopsConfigUser['allow_changemail'] == 1) {
        $form->setRequired($email_text);
    }
    $form->display();
    include $GLOBALS['xoops']->path('footer.php');
}	
?>