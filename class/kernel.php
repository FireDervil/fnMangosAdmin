<?php


class mainCore
{
	var $version = '1.0.1';
	var $version_date = '17.07.2011';
	var $db_version = '1.0';

	function Core()
	{
		$this->Initialize();
	}


	function Initialize()
	{
		$this->copyright = 'Powered by FireNetworks.de - Version '.$this->version.' &copy; 2010-2011, <a href="http://firenetworks.de">FireNetworks.de Dev Team</a>.<br />All Rights Reserved.';
		
		return true;
	}
	
	function setGlobals()
	{
		global $xoopsModuleConfig;
		
		// Setup the site globals
		$_SESSION['users_online'] = array(); // set useronline for block info
		$_SESSION['guests_online'] = 0; // set guast or by geust id
		$_SESSION['messages'] = '';		// For server messages
		$_SESSION['debug_messages'] = array(); // for developing only
		$_SESSION['selected_realm'] = 1; //stores the current realm choosen by user
		
		// Finds out what realm we are viewing. Sets cookie with realm info for later use
		if(isset($xoopsModuleConfig['default_realm_id'])) 
		{
			$_SESSION['selected_realm'] = (int)$xoopsModuleConfig['default_realm_id'];
		}
		else
		{
			$_SESSION['selected_realm'] = $xoopsModuleConfig['default_realm_id'];
			setcookie("selected_realm", (int)$xoopsModuleConfig['default_realm_id'], time() + (3600 * 24 * 365));
		}
	}
	
	// check permission for fsockopen and put also for fopen later		
	function load_permissions()
	{
		$allow_url_fopen = ini_get('allow_url_fopen');
		if(function_exists("fsockopen")) 
		{
			$fsock = 1;
		}
		else
		{
			$fsock = 0;
		}
		$ret = array('allow_url_fopen' => $allow_url_fopen, 'allow_fsockopen' => $fsock);
		return $ret;
	}
	
	// here we put later functions for cachefiles and so on.
	
}
?>