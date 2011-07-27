<?php

	include_once XOOPS_ROOT_PATH."/modules/fnMangosAdmin/include/functions.mangos.php";
	include_once XOOPS_ROOT_PATH."/modules/fnMangosAdmin/class/database/mysql.php";
	
function b_mangosadmin_srvinfo_show($options)
{

	// Load module info and configs

	global $xoopsConfig, $xoopsModuleConfig, $xoopsModule, $fnmaDB;
	
	$modhandler = &xoops_gethandler('module');
	$xoopsModule = &$modhandler->getByDirname("fnMangosAdmin");
	$config_handler = &xoops_gethandler('config');
	$xoopsModuleConfig = &$config_handler->getConfigsByCat(0,$xoopsModule->getVar('mid'));
	
	// Block Functions

	
	if($xoopsModuleConfig['mysql_host'] == '127.0.0.1' or $xoopsModuleConfig['mysql_user'] == 'username' or $xoopsModuleConfig['mysql_passwd'] == 'password'| $xoopsModuleConfig['server_ip'] == '127.0.0.1' | $xoopsModuleConfig['world_server_port'] == '00000')
	{
		$block = array();
		$block['error'] = 1;
		return $block;
		unset($block);
	} else {
		
	$block = array();
	$block['error'] = 0;
	// set up databse global
	$fnmaDB = array();
	$fnmaDB["logon"] = new MangosDatabase;
	$fnmaDB["logon"]->connect($xoopsModuleConfig['mysql_host'], $xoopsModuleConfig['mysql_user'], $xoopsModuleConfig['mysql_passwd'], $xoopsModuleConfig['realm_db_name'], XOOPS_DB_CHARSET);
	$fnmaDB["char"] = new MangosDatabase;
	$fnmaDB["char"]->connect($xoopsModuleConfig['mysql_host'], $xoopsModuleConfig['mysql_user'], $xoopsModuleConfig['mysql_passwd'], $xoopsModuleConfig['char_db_name'], XOOPS_DB_CHARSET);
	
	// $pdata = array(); used in later version
	// $qdata = array(); used in later version
	$lat_query = "SELECT COUNT(*) FROM characters WHERE online=1";
	$lat_result = $fnmaDB["char"]->query($lat_query);
    $lat_fields = $fnmaDB["char"]->fetch_assoc($lat_result);
	$online = $lat_fields["COUNT(*)"];

	$block['online'] = $online;

	
	
	if($xoopsModuleConfig['show_max_players_block'] == 1){
	$block['show_max_online'] = 1;
	$max = $fnmaDB["logon"]->fetch_assoc($fnmaDB["logon"]->query("SELECT max(maxplayers) as maxplayers from uptime"), 0); 
	$block['maxonline'] = $max['maxplayers'];
	}
	
	if($xoopsModuleConfig['show_realm_uptime_block'] == 1){
	$block['show_uptime'] = 1;
	$stats = $fnmaDB["logon"]->fetch_assoc($fnmaDB["logon"]->query("SELECT starttime, maxplayers FROM uptime WHERE realmid=1 ORDER BY starttime DESC LIMIT 1"), 0);
	$uptimetime = time() - $stats["starttime"];
	
	if($stats["starttime"] <> 0)
	{
		$staticUptime .= ' seit '.format_uptime($uptimetime).'<br />';
	} else {
		$staticUptime .= ' seit '.format_uptime($uptimetime).'<br />';
	}
	$block['uptime'] = $staticUptime;
	}
	return $block;
	}
}

?>