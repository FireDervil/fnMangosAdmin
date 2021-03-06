<?php

if (!defined('XOOPS_ROOT_PATH')) {
	die('XOOPS root path not defined');
}


$modversion["name"] = _MI_FNMA_MANAGER_NAME;
$modversion["version"] = '1.18'; 
$modversion["description"] = _MI_FNMA_MANAGER_DESC;
$modversion["image"] = "images/logo/fnMangosAdmin_logo.png";
$modversion["dirname"] = "fnMangosAdmin";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 1;

$modversion['author'] = 'Stefan Stroth' ;
$modversion['pseudo'] = 'FireDervil';
$modversion['website'] = 'www.firenetworks.de';
$modversion['name_website'] = 'FireNetworks.de';
$modversion['status_version'] = 'BETA';
$modversion['release_date'] = '2011/11/21';
$modversion['help'] = 'help.html';
$modversion['min_php'] = '5.2';
$modversion['min_xoops'] = '2.5';
$modversion['system_menu'] = 1;

include_once(XOOPS_ROOT_PATH."/Frameworks/art/functions.ini.php");
// Is performing module install/update?
$isModuleAction = mod_isModuleAction($modversion['dirname']);

// Sql file 
$i=0;
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
//$modversion['sqlfile']['postgresql'] = "sql/pgsql.sql";
$modversion['tables'][$i] = "fnma_voting";
$i++;
$modversion['tables'][$i] = "fnma_vote_sites";
$i++;
$modversion['tables'][$i] = "fnma_donate_transactions";
$i++;
$modversion['tables'][$i] = "fnma_donate_packages";
$i++;
$modversion['tables'][$i] = "fnma_account_groups";
$i++;
$modversion['tables'][$i] = "fnma_account_extend";
$i++;
$modversion['tables'][$i] = "fnma_account_keys";
$i++;
$modversion['tables'][$i] = "fnma_online";
$i++;
$modversion['tables'][$i] = "fnma_news";
$i++;
$modversion['tables'][$i] = "fnma_news_topics";
$i++;
$modversion['tables'][$i] = "fnma_shop_items";
$i++;
$modversion['tables'][$i] = "fnma_acc_captcha";
$i++;


//install
$modversion['onInstall'] = 'include/module.php';

//update things
$modversion['onUpdate'] = 'include/module.php';


$i=0;
$modversion["hasMain"] = 1;
if(isset($_SESSION['fnmaUserId'])){
$modversion['sub'][$i]['name'] = _MI_FNMA_SMNAME7;
$modversion['sub'][$i]['url'] = "user.php?op=logout";
$i++;
$modversion['sub'][$i]['name'] = _MI_FNMA_SMNAME8;
$modversion['sub'][$i]['url'] = "member.php";
$i++;
$modversion['sub'][$i]['name'] = _MI_FNMA_SMNAME9;
$modversion['sub'][$i]['url'] = "shop.php";
$i++;
} else {
$modversion['sub'][$i]['name'] = _MI_FNMA_SMNAME6;
$modversion['sub'][$i]['url'] = "user.php";
$i++;
}
$modversion['sub'][$i]['name'] = _MI_FNMA_SMNAME1;
$modversion['sub'][$i]['url'] = "status.php";
$i++;
$modversion['sub'][$i]['name'] = _MI_FNMA_SMNAME2;
$modversion['sub'][$i]['url'] = "top_kills.php";
$i++;
$modversion['sub'][$i]['name'] = _MI_FNMA_SMNAME3;
$modversion['sub'][$i]['url'] = "tot_chars.php";
$i++;
$modversion['sub'][$i]['name'] = _MI_FNMA_SMNAME4;
$modversion['sub'][$i]['url'] = "online.php";
$i++;
$modversion['sub'][$i]['name'] = _MI_FNMA_SMNAME5;
$modversion['sub'][$i]['url'] = "search.php";
$i++;

// Admin things
$modversion["hasAdmin"] = 1;
$modversion["adminindex"] = "admin/index.php";
$modversion["adminmenu"] = "admin/menu.php";

// Site Search
$modversion["hasSearch"] = 0;
$modversion["search"]["file"] = "include/search.inc.php";
$modversion["search"]["func"] = "";


// Templates
$i=0;
$modversion['templates'][$i]['file'] = 'fnma_login_form_realm.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_register_form_realm.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_index.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_status.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_201.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_server_chars.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_server_topkills.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_userinfo.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_header.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_server_online.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_member.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_misc_voting.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_misc_chgname.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_misc_customize.html';
$modversion['templates'][$i]['description'] = '';
$i++;
//Test and debuggin templates
$modversion['templates'][$i]['file'] = 'fnma_news_article.html'; // added v1.14
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_news_item.html'; // added v1.14
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_shop.html'; // added v1.15
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'fnma_shop_checkout.html'; // added v1.15
$modversion['templates'][$i]['description'] = '';
$i++;

// config
$i = 1;
$modversion['config'][$i] = array(
	'name'        => 'mysql_host',
	'title'       => '_MI_FNMA_MYSQL_HOST',
	'description' => '_MI_FNMA_MYSQL_HOST_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => '127.0.0.1'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'mysql_port',
	'title'       => '_MI_FNMA_MYSQL_PORT',
	'description' => '_MI_FNMA_MYSQL_PORT_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '3306'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'mysql_user',
	'title'       => '_MI_FNMA_MYSQL_USER',
	'description' => '_MI_FNMA_MYSQL_USER_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'username'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'mysql_passwd',
	'title'       => '_MI_FNMA_MYSQL_PASSWD',
	'description' => '_MI_FNMA_MYSQL_PASSWD_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'password'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'server_system',
	'title'       => '_MI_FNMA_SERVER_SYSTEM',
	'description' => '_MI_FNMA_SERVER_SYSTEM_DESC',
	"formtype"	  => 'select',
	"valuetype"   => 'text',
	"options"	  =>  array(
					_MI_FNMA_SYS_ARCEMU  => 'arcemu',
					_MI_FNMA_SYS_MANGOS  => 'mangos',
					_MI_FNMA_SYS_TRINITY => 'trinity'
					),
	"default" 	  => "mangos"
	);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'server_ip',
	'title'       => '_MI_FNMA_SERVER_IP',
	'description' => '_MI_FNMA_SERVER_IP_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => '127.0.0.1'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'world_server_port',
	'title'       => '_MI_FNMA_WORLDD_SERVER_PORT',
	'description' => '_MI_FNMA_WORLDD_SERVER_PORT_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '16897'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'realmd_server_port',
	'title'       => '_MI_FNMA_REALMD_SERVER_PORT',
	'description' => '_MI_FNMA_REALMD_SERVER_PORT_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '3724'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'ra_consolen_port',
	'title'       => '_MI_FNMA_RA_CONSOLEN_PORT',
	'description' => '_MI_FNMA_RA_CONSOLEN_PORT_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '28585'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'soap_consolen_port',
	'title'       => '_MI_FNMA_SOAP_CONSOLEN_PORT',
	'description' => '_MI_FNMA_SOAP_CONSOLEN_PORT_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '27585'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'soap_consolen_name',
	'title'       => '_MI_FNMA_SOAP_CONSOLEN_NAME',
	'description' => '_MI_FNMA_SOAP_CONSOLEN_NAME_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'a gm user'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'soap_consolen_passwd',
	'title'       => '_MI_FNMA_SOAP_CONSOLEN_PASS',
	'description' => '_MI_FNMA_SOAP_CONSOLEN_PASS_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'a gm passwd'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'char_db_name',
	'title'       => '_MI_FNMA_CHAR_DB_NAME',
	'description' => '_MI_FNMA_CHAR_DB_NAME_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'characters'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'realm_db_name',
	'title'       => '_MI_FNMA_DB_REALM_NAME',
	'description' => '_MI_FNMA_DB_REALM_NAME_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'realmd'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'default_realm_id',
	'title'       => '_MI_FNMA_DEFAULT_REALM_ID',
	'description' => '_MI_FNMA_DEFAULT_REALM_ID_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '1'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'show_max_players_block',
	'title'       => '_MI_FNMA_SHOW_MAX_PLAYERS',
	'description' => '_MI_FNMA_SHOW_MAX_PLAYERS_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '0'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'show_realm_uptime_block',
	'title'       => '_MI_FNMA_SHOW_REALM_UPTIME',
	'description' => '_MI_FNMA_SHOW_REALM_UPTIME_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '0'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'site_cookie',
	'title'       => '_MI_FNMA_SITE_COOKIE',
	'description' => '_MI_FNMA_SITE_COOKIE_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'string',
	'default'     => 'fnmaCookie'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'require_act_activation',
	'title'       => '_MI_FNMA_REQ_ACTIVATION',
	'description' => '_MI_FNMA_REQ_ACTIVATION_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '0'
);
$i++;

// TODO: will be modded or deleted in coming updates
$modversion['config'][$i] = array(
	'name'        => 'account_key_retain_length',
	'title'       => '_MI_FNMA_KEY_LENGTH',
	'description' => '_MI_FNMA_KEY_LENGTH_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '15'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'allow_changemail',
	'title'       => '_MI_FNMA_ALLOW_CHG_MAIL',
	'description' => '_MI_FNMA_ALLOW_CHG_MAIL_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '0'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'allow_change_addon',
	'title'       => '_MI_FNMA_ALLOW_CHG_ADDON',
	'description' => '_MI_FNMA_ALLOW_CHG_ADDON_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '0'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'minpass',
	'title'       => '_MI_FNMA_MINPASS',
	'description' => '_MI_FNMA_MINPASS_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '8'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'allow_registration',
	'title'       => '_MI_FNMA_ALLOW_REG',
	'description' => '_MI_FNMA_ALLOW_REG_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1'
);
$i++;

// added rev:1.17 
$modversion['config'][$i] = array(
	'name'        => 'do_debug',
	'title'       => '_MI_FNMA_DO_DEBUG',
	'description' => '_MI_FNMA_DO_DEBUG_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1'
);
$i++;

// added Rev: 1.18
$modversion['config'][$i] = array(
	'name'        => 'send_system',
	'title'       => '_MI_FNMA_SEND_SYSTEM',
	'description' => '_MI_FNMA_SEND_SYSTEM_DESC',
	'formtype'    => 'select',
	'valuetype'   => 'text',
	"options"	  =>  array(
					_MI_FNMA_SYS_DEFAULT  => 'default',	// default => socket
					_MI_FNMA_SYS_SOAP  => 'soap',		// soap	
					_MI_FNMA_SYS_RC => 'rpc',		// rc
					_MI_FNMA_SYS_SOCKET	 => 'stream'	// socket
					),
	"default" 	  => "default"
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'is_active_chgname',
	'title'       => '_MI_FNMA_CHGNAME_ACTIVE',
	'description' => '_MI_FNMA_CHGNAME_ACTIVE_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'char_rename_pts',
	'title'       => '_MI_FNMA_CHAR_RENAME_POINTS',
	'description' => '_MI_FNMA_CHAR_RENAME_POINTS_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '5'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'char_rename',
	'title'       => '_MI_FNMA_CHAR_RENAME_AKTIV',
	'description' => '_MI_FNMA_CHAR_RENAME_AKTIV_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'char_customize',
	'title'       => '_MFI_FNMA_ACTIVE_CHR_CUST',
	'description' => '_MI_FNMA_ACTIVE_CHR_CUST_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'char_customize_pnt',
	'title'       => '_MI_FNMA_CHAR_CPOINTS',
	'description' => '_MI_FNMA_CHAR_CPOINTS_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '5'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'max_acct_per_ip',
	'title'       => '_MI_FNMA_MAX_ACCT_PER_IP',
	'description' => '_MI_FNMA_MAX_ACCT_PER_IP_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => '10'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'user_must_activate',
	'title'       => '_MI_FNMA_USER_MUST_ACTIV',
	'description' => '_MI_FNMA_USER_MUST_ACTIV_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '0'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'reg_with_captcha',
	'title'       => '_MI_FNMA_REG_WITH_CAPTCHA',
	'description' => '_MI_FNMA_REG_WITH_CAPTCHA_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'reg_secret_quest',
	'title'       => '_MI_FNMA_REG_WITH_ACCSECRET',
	'description' => '_MI_FNMA_REG_WITH_ACCSECRET_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '0'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'allow_global_reg',
	'title'       => '_MI_FNMA_ALLOW_GLOBAL_REG',
	'description' => '_MI_FNMA_ALLOW_GLOBAL_REG_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'active_vote_system',
	'title'       => '_MI_FNMA_ACTIV_VOTESYS',
	'description' => '_MI_FNMA_ACTIV_VOTESYS_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1'
);
$i++;

$modversion['config'][$i] = array(
	'name'        => 'check_vote_isonline',
	'title'       => '_MI_FNMA_CHECK_V_ISONLINE',
	'description' => '_MI_FNMA_CHECK_V_ISONLINE_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1'
);
$i++;


// Blocks
$i = 1;
$modversion['blocks'][$i] = array(
	'file' => 'fnma_global_block.php',
	'name' => _MI_XFWA_BLOCK_SRVINFO_NAME,
	'description' => _MI_XFWA_BLOCK_SRVINFO_DESC,
	'show_func' => 'b_mangosadmin_srvinfo_show',
	'options' => 'title|10|360|0|1|0|0',
	//'edit_func' => '',
	'template' => 'fnma_blocks_pinfo.html'
	);
$i++;


?>