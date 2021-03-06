<?php

include 'header.php';
include_once(FNMA_ROOT_PATH . 'class' . DS . 'captcha' . DS .'fncaptcha.php');

xoops_loadLanguage('user', 'fnMangosAdmin');

$op = 'form';
foreach ( $_POST as $k => $v ) { ${$k} = $v; }
foreach ( $_GET as $k => $v ) { ${$k} = $v; }

switch ($op) {
case "cancel":
	redirect_header("index.php", 0);
	break;
	

case "register":

	// Define that users can register (for error reporting)
	global $fnmaConfig, $fnmaModule;
	
	// Init the error array
	$err_array = array();

	
	// Check to see if we still are allowed to register
	if($fnmaConfig['allow_global_reg'] == '1')
	{
		// Inizialize variable, we use this after. Use this to add extensions.
		$notreturn = false;
		
		// Extensions
		// Each extention you see down-under will check for specific user input,
		// In this step we set "requirements" for what user may input.

		// Ext 1 - Image verification
		// We need to see if its enabled, and if the user put in the right code
		if($fnmaConfig['reg_with_captcha'] == '1')
		{
			$image_key = $_POST['image_key'];
			$filename = mysql_real_escape_string($_POST['filename_image']);
			$correctkey = $fnmaDB["sys"]->select("SELECT * FROM ".$xoopsDB->prefix('fnma_acc_captcha')." WHERE filename='".$filename."'");

			// Check for key match
			if(strtolower($correctkey[1]['key']) != strtolower($image_key) || $image_key == '')
			{
				$notreturn = true;
				$err_array[] = _MD_FNMA_USER_CAPTCHA_WRONG;
			} else {
				// Delete the key from the DB, and delete the image from the cache folder
				$fnmaDB["sys"]->query("DELETE FROM ".$xoopsDB->prefix('fnma_acc_captcha')." WHERE filename='".$filename."'");
				@unlink($filename);
			}
		}
		
		
		// Ext 2 - secret questions
		// Check if user questions are required, if so we need to check for symbols, and character lenght
		if ($fnmaConfig['reg_secret_quest'] == '1')
		{
			if ($_POST['secretq1'] && $_POST['secretq2'] && $_POST['secreta1'] && $_POST['secreta2']) 
			{
				if(check_for_symbols($_POST['secreta1']) || check_for_symbols($_POST['secreta2']))
				{
					$notreturn = true;
					$err_array[] = $lang['secretq_error_symbols'];
				}
				if($_POST['secretq1'] == $_POST['secretq2']) 
				{
					$notreturn = true;
					$err_array[] = $lang['secretq_error_same'];
				}
				if($_POST['secreta1'] == $_POST['secreta2']) 
				{
					$notreturn = true;
					$err_array[] = $lang['secretq_error_same'];
				}
				if(strlen($_POST['secreta1']) < 4 || strlen($_POST['secreta2']) < 4) 
				{
					$notreturn = true;
					$err_array[] = $lang['secretq_error_short'];
				}
			}
			else 
			{
				$notreturn = true;
				$err_array[] = $lang['secretq_error_empty'];
			}
		}
		
	// Ext 3 - make sure the username isnt already in use
	if($fnmaAccount->isAvailableUsername($_POST['r_login']) == false)
	{
		$notreturn = true;
		$err_array[] = _MD_FNMA_REG_USERNAME_EXISTS;
	}
	
	// Ext 4 - make sure password is not username
	if($_POST['r_login'] == $_POST['r_pass']) 
	{
		$notreturn = true;
		$err_array[] = _MD_FNMA_REG_USER_PASSWORD_FAIL;
	}
	
	// Ext 5 - make sure secret ist not empty
	if($_POST['r_secret'] == '')
	{
		$noreturn = true;
		$err_array[] = _MD_FNMA_REG_SECRET_WRONG;
	}
		
	// Main add into the database
	if($notreturn == false)
	{
		// @$Enter is the main input arrays into the function
		$Enter = $fnmaAccount->register2(
			array(
				'r_login' => $_POST['r_login'],
				'r_pass' => $_POST['r_pass'],
				'r_cpass' => $_POST['r_cpass'],
				'r_email' => $_POST['r_email'],
				'r_account_type' => $_POST['r_account_type']
				), true
			);
		// lets catch the return on the register function
		if($Enter == 1) # 1 = success
		{
			$reg_succ = true;
		} 
		elseif($Enter == 0) # All params are emtpy
		{
			$reg_succ = false;
			$err_array[] = _MD_FNMA_REG_SOME_PARAMS_EMPTY;
		}
		elseif($Enter == 2) # empty username
		{
			$reg_succ = false;
			$err_array[] = _MD_FNMA_REG_PARAM_USERNAME_EMPTY;
		}
		elseif($Enter == 3) # passwords dont match
		{
			$reg_succ = false;
			$err_array[] = _MD_FNMA_REG_PASSWD_NOTMATCH;
		}
		elseif($Enter == 4) # empty email
		{
			$reg_succ = false;
			$err_array[] = _MD_FNMA_REG_MAIL_EMPTY;
		}
		elseif($Enter == 5) # IP Banned
		{
			$reg_succ = false;
			$err_array[] = _MD_FNMA_REG_IP_IS_BANNED;
		} else {
			// fatal error display
			$reg_succ = false;
			$err_array[] = "Account Creation [FATAL ERROR]: User cannot be created, likely due to incorrect database configuration.  Contact the administrator.";
		}
	} else {
		$reg_succ = false; // set errorcode false to global
	}

	// If there were any errors, then they are outputed here
	if($reg_succ == false) 
	{
		if(!$err_array[0])
		{
			$err_array[0] = _MD_FNMA_UNKNOWN;
		}
		$output_error = $lang['register_failed'];
		$output_error .= "<ul><li>";
		$output_error .= implode("</li><li>", $err_array);
		$output_error .= "</li></ul>";
		redirect_header('register.php', 3, sprintf(_MD_FNMA_REG_FAILD, $output_error));
	} else {
		// there is no error everything fine here
		redirect_header('index.php', 2, _MD_FNMA_REG_SUCCESS);
	}
	}

break;
	
case 'form':
default:


	$xoopsOption['template_main'] = 'fnma_register_form_realm.html';
	
	include XOOPS_ROOT_PATH."/header.php";
	$xoopsOption['xoops_module_header']= $xoops_module_header;
	$xoopsTpl->assign('xoops_pagetitle', $xoops_pagetitle);
	$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
	// Initialize random image:
	$captcha = new Captcha;
	$captcha->load_ttf();
	$captcha->make_captcha();
	$captcha->delold();
	$filename = $captcha->filename;
	$privkey = $captcha->privkey;
	$xoopsTpl->assign('filename', $filename);
	$xoopsTpl->assign('privkey', $privkey);
	$fnmaDB["sys"]->query("INSERT INTO ".$xoopsDB->prefix('fnma_acc_captcha')." VALUES('', '$filename','$privkey')");
	
	// language Array
	$xoopsTpl->assign(array(
		"lang_register" => _MD_FNMA_REGISTER,
		"lang_username" => _MD_FNMA_USERNAME,
		"lang_password" => _MD_FNMA_PASSWORD,
		"lang_password_confirm" => _MD_FNMA_PASS_CONFIRM,
		"lang_email" => _MD_FNMA_EMAIL,
		"lang_account_type" => _MD_FNMA_ACCOUNT_TYPE,
		"lang_wotlk" => _MD_FNMA_WOTLK,
		"lang_tbc" => _MD_FNMA_TBC,
		"lang_classic" => _MD_FNMA_CLASSIC,
		"lang_captcha_info" => _MD_FNMA_CAPTCHA_INFO,
		"lang_have_account" => _MD_FNMA_HAVE_ACCOUNT,
		"lang_noproblem" => _MD_FNMA_REG_NO_PROBLEM,
		"lang_notregister" => _MD_FNMA_NOT_REGSITER,
		"lang_reg_secret" => _MD_FNMA_REG_SECRET)
		);		
		
	include XOOPS_ROOT_PATH."/footer.php";
	break;
}
?>