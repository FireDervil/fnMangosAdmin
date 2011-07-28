<?php







if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
if(defined("XOOPS_MODULE_FNMA_FUCTIONS")) exit();
define("XOOPS_MODULE_FNMA_FUCTIONS", 1);

@include_once XOOPS_ROOT_PATH.'/modules/fnMangosAdmin/include/plugin.php';
include_once XOOPS_ROOT_PATH.'/modules/fnMangosAdmin/include/functions.php';


function xoops_module_update_fnma(&$module, $oldversion = null) 
{
	$fnmaConfig = fnma_load_config();
	
	if ($oldversion >= 100) {
		$result = $GLOBALS['fnmaDB']["sys"]->query("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_extend")." ( account_id ) SELECT realmd.account.id FROM realmd.account");
	if (!$result) {
		$module->setErrors("Could not add field in fnma_account_extend");
	}	
	}
	
	//$oldversion = $module->getVar('version');
    //$oldconfig = $module->getVar('hasconfig');	
	if ($oldversion == 100) {
	    include_once dirname(__FILE__)."/module.v100.php";
	    xoops_module_update_fnma_v100($module);
    }
	// change group permission name
    // change forum moderators
    if ($oldversion < 220) {
	    include_once dirname(__FILE__)."/module.v220.php";
	    xoops_module_update_fnma_v220($module);
    }

	if ($oldversion < 230) {
        $GLOBALS['xoopsDB']->queryFromFile(XOOPS_ROOT_PATH."/modules/fnMangosAdmin/sql/upgrade_230.sql");
		//$module->setErrors("bb_moderates table inserted");
    }

    if ($oldversion < 304) {
        $GLOBALS['xoopsDB']->queryFromFile(XOOPS_ROOT_PATH."/modules/fnMangosAdmin/sql/mysql.304.sql");
    }
    
	if(!empty($fnmaConfig["syncOnUpdate"])){
		fnma_synchronization();
	}
	
	return true;
}

function xoops_module_pre_update_fnma(&$module) 
{
	return fnma_setModuleConfig($module, true);
}

function xoops_module_pre_install_fnma(&$module)
{
	$mod_tables = $module->getInfo("tables");
	foreach($mod_tables as $table){
		$GLOBALS["xoopsDB"]->queryF("DROP TABLE IF EXISTS ".$GLOBALS["xoopsDB"]->prefix($table).";");
	}
	return fnma_setModuleConfig($module);
}

function xoops_module_install_fnma(&$module)
{
	
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_groups")." VALUES ('1', 'Guest')";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_groups")." VALUES ('2', 'Member')";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_groups")." VALUES ('3', 'Admin')";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_groups")." VALUES ('4', 'Super Admin')";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_account_groups")." VALUES ('5', 'Banned')";
		$q[] = "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("fnma_news")." VALUES ('1', 'Welcome!', '<center><b><p>Thank you for installing fnMangosAdmin!</p></b> <p>Please login with your Admin account username and password to configure the Global Functions further.</p></center>', 'FireDervil', '1288727884');";
		
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