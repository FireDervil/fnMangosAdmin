<?php

if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
if(defined("XOOPS_MODULE_FNMA_FUCTIONS")) exit();
define("XOOPS_MODULE_FNMA_FUCTIONS", 1);

@include_once XOOPS_ROOT_PATH.'/modules/fnMangosAdmin/include/plugin.php';
include_once XOOPS_ROOT_PATH.'/modules/fnMangosAdmin/include/functions.php';


function xoops_module_update_fnMangosAdmin(&$module, $oldversion = null) 
{
	$fnmaConfig = fnma_load_config();
	
	if ($oldversion >= 100) {
		$result = $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_extend")." ( account_id ) SELECT realmd.account.id FROM realmd.account");
	if (!$result) {
		$module->setErrors("Could not add field in fnma_account_extend");
	}	
	}
    
	if(!empty($fnmaConfig["syncOnUpdate"])){
		fnma_synchronization();
	}
	
	return true;
}

function xoops_module_pre_update_fnMangosAdmin(&$module) 
{
	return fnma_setModuleConfig($module, true);
}

function xoops_module_pre_install_fnMangosAdmin(&$module)
{
	$mod_tables = $module->getInfo("tables");
	foreach($mod_tables as $table){
		$GLOBALS["xoopsDB"]->queryF("DROP TABLE IF EXISTS ".$GLOBALS["xoopsDB"]->prefix($table).";");
	}
	return fnma_setModuleConfig($module);
}

function xoops_module_install_fnMangosAdmin(&$module)
{
	
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_groups")." VALUES ('1', 'Guest')";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_groups")." VALUES ('2', 'Member')";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_groups")." VALUES ('3', 'Admin')";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_groups")." VALUES ('4', 'Super Admin')";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_groups")." VALUES ('5', 'Banned')";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_news")." VALUES ('1', 'Welcome!', '<center><b><p>Thank you for installing fnMangosAdmin!</p></b> <p>Please login with your Admin account username and password to configure the Global Functions further.</p></center>', 'FireDervil', '1288727884');";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_extend")." ( account_id ) SELECT realmd.account.id FROM realmd.account";
		
		foreach($q as $query)
		{
			$result = $GLOBALS['xoopsDB']->queryF($query);
				if (!$result) {
				$module->setErrors("Could not add data during install");
				}
		}
		return true;
}

function fnma_setModuleConfig(&$module, $isUpdate = false) 
{
	return true;
}
?>