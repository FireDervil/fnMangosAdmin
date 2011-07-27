<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

xoops_loadLanguage('user', 'fnMangosAdmin');

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : $email;

if ($email == '') {
    redirect_header("user.php", 2, _US_SORRYNOTFOUND);
    exit();
}

$myts =& MyTextSanitizer::getInstance();
$getuser = $fnmaAccount->isLostPassEmail($email);

if (empty($getuser)) {
    $msg = _MD_FNMA_SORRYNOTFOUND;
    redirect_header("user.php", 2, $msg);
    exit();
} else {
	 $code = isset($_GET['code']) ? trim($_GET['code']) : '';
	 $passData = $fnmaAccount->returnEmailUserData($email);
	 $areyou = substr($passData['sha_pass_hash'], 0, 5);
	 $newpass = '';
	 $newpass_hash = '';
	 if ($code != '' && $areyou == $code) {
		$newpass = xoops_makepass();
	 	$newpass_hash = $fnmaAccount->sha_password($passData['username'], $newpass); 
		
		$xoopsMailer =& xoops_getMailer(); 
		$xoopsMailer->useMail(); 
		//Pfad für Template definieren 
		if(file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/'. $xoopsConfig['language'] .'/')) {
    		$xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/'. $xoopsConfig['language'] .'/mail_template/');
		} else {
			$xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/german/mail_template/');
		}
		$xoopsMailer->setTemplate('lostpass_wow2.tpl'); 
		$xoopsMailer->setToEmails($passData['email']); 
		$xoopsMailer->setFromEmail($xoopsConfig['adminmail']); 
		$xoopsMailer->setFromName($xoopsConfig['sitename']); 
		$xoopsMailer->setSubject(sprintf(_MD_FNMA_NEWPWDREQ, XOOPS_URL)); 
		$xoopsMailer->assign("UNAME", $passData['username']);
		$xoopsMailer->assign("SITENAME", $xoopsConfig['sitename']);
        $xoopsMailer->assign("ADMINMAIL", $xoopsConfig['adminmail']);
        $xoopsMailer->assign("SITEURL", XOOPS_URL . "/");
        $xoopsMailer->assign("IP", $_SERVER['REMOTE_ADDR']);
		$xoopsMailer->assign("NEWPWD", $newpass);
		if (! $xoopsMailer->send()) {
            echo $xoopsMailer->getErrors();
        }
		// Next step: add the new password to the database
        $sql = sprintf("UPDATE %s SET sha_pass_hash = '%s' WHERE id = %u", 'account', $newpass_hash, $passData['id']);
		if (!$fnmaDB["logon"]->query($sql)) {
		include $GLOBALS['xoops']->path('header.php');
            echo _MD_FNMA_MAILPWDNG;
            include $GLOBALS['xoops']->path('footer.php');
            exit();
        }
		redirect_header("user.php", 3, sprintf(_MD_FNMA_PWDMAILED, $passData["username"]), false);
        exit();
        // If no Code, send it
	 } else {
		$xoopsMailer =& xoops_getMailer();
        $xoopsMailer->useMail();
		//Pfad für Template definieren 
		if(file_exists(XOOPS_ROOT_PATH . 'modules/' . $xoopsModule->getVar('dirname') . '/language/'. $xoopsConfig['language'] .'/')) {
    		$xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . 'modules/' . $xoopsModule->getVar('dirname') . '/language/'. $xoopsConfig['language'] .'/mail_template/');
		} else {
			$xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . 'modules/' . $xoopsModule->getVar('dirname') . '/language/german/mail_template/');
		}
		$xoopsMailer->setTemplate("lostpass_wow1.tpl");		
		$xoopsMailer->assign("UNAME", $passData['username']);
		$xoopsMailer->assign("SITENAME", $xoopsConfig['sitename']);
        $xoopsMailer->assign("ADMINMAIL", $xoopsConfig['adminmail']);
        $xoopsMailer->assign("SITEURL", XOOPS_URL . "/");
        $xoopsMailer->assign("IP", $_SERVER['REMOTE_ADDR']);
        $xoopsMailer->assign("NEWPWD_LINK", XOOPS_URL . "/modules/fnMangosAdmin/lostpass.php?email=" . $email . "&code=" . $areyou);
		$xoopsMailer->setToEmails($passData['email']); 
		$xoopsMailer->setFromEmail($xoopsConfig['adminmail']); 
		$xoopsMailer->setFromName($xoopsConfig['sitename']); 
		$xoopsMailer->setSubject(sprintf(_MD_FNMA_NEWPWDREQ, $xoopsConfig['sitename']));
 		include $GLOBALS['xoops']->path('header.php');
        if (! $xoopsMailer->send()) {
            echo $xoopsMailer->getErrors();
        }
        echo "<h4>";
        printf(_MD_FNMA_CONFMAIL, $passData["username"]);
        echo "</h4>";
        include $GLOBALS['xoops']->path('footer.php');
    }
}

?>