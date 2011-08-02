<?php

defined('XOOPS_ROOT_PATH') or die('Restricted access');

function fnGetOrderBy($sort) 
{
    if ($sort == "datesub") {
        return "DESC";
    } elseif ($sort == "counter") {
         return "DESC";
    } elseif ($sort == "weight") {
         return "ASC";
    } 	
}

function fn_TableExists($table)
{

    $bRetVal = false;
    //Verifies that a MySQL table exists
    $xoopsDB =& Database::getInstance();
    $realname = $xoopsDB->prefix($table);
    $ret = mysql_list_tables(XOOPS_DB_NAME, $xoopsDB->conn);
    while (list($m_table)=$xoopsDB->fetchRow($ret)) {
        
        if ($m_table ==  $realname) {
            $bRetVal = true;
            break;
        }
    }
    $xoopsDB->freeRecordSet($ret);
    return ($bRetVal);
}


function fn_highlighter($matches) {
	
	$fnmaConfig =& fnGetModuleConfig();
	$color = $fnmaConfig['highlight_color'];
	if(substr($color,0,1)!='#') {
		$color='#'.$color;
	}
	return '<span style="font-weight: bolder; background-color: '.$color.';">' . $matches[0] . '</span>';
}


function fnGetConfig($key)
{
	$configs = fnGetModuleConfig(); 
	return $configs[$key];
}

function fnGtAllowedImagesTypes()
{
	return array('jpg/jpeg', 'image/bmp', 'image/gif', 'image/jpeg', 'image/jpg', 'image/x-png', 'image/png', 'image/pjpeg');
}

function fn_module_home($withLink=true)
{
	global $fnmaModuleName, $xoopsModuleConfig;
	if(!$xoopsModuleConfig['show_mod_name_breadcrumb']){
		return	'';
	}
	if (!$withLink)	{
		return $fnmaModuleName;	
	} else {
		return '<a href="' . FNMA_URL . '">' . $fnmaModuleName . '</a>';
	}
}


function &fnGetModuleInfo()
{
    static $fnmaModule;
	if (!isset($fnmaModule)) {
	    global $xoopsModule;
	    if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == 'fnMangosAdmin') {
	        $fnmaModule =& $xoopsModule;
	    }
	    else {
	        $hModule = &xoops_gethandler('module');
	        $fnmaModule = $hModule->getByDirname('fnMangosAdmin');
	    }
	}
	return $fnmaModule;
}


function &fnGetModuleConfig()
{
    static $fnmaConfig;
    if (!$fnmaConfig) {
        global $xoopsModule;
	    if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == 'fnMangosAdmin') {
	        global $xoopsModuleConfig;
	        $fnmaConfig =& $xoopsModuleConfig;
	    }
	    else {
	        $fnmaModule =& fnGetModuleInfo();
	        $hModConfig = &xoops_gethandler('config');
	        $fnmaConfig = $hModConfig->getConfigsByCat(0, $smartModule->getVar('mid'));
	    }
    }
	return $fnmaConfig;
}

function fnGetStatusArray ()
{
	$result = array("1" => _AM_FN_STATUS1,
	"2" => _AM_FN_STATUS2,
	"3" => _AM_FN_STATUS3,
	"4" => _AM_FN_STATUS4,
	"5" => _AM_FN_STATUS5,
	"6" => _AM_FN_STATUS6,
	"7" => _AM_FN_STATUS7,
	"8" => _AM_FN_STATUS8);
	return $result;
}


function fnModFooter()
{
	global $xoopsUser, $xoopsDB, $xoopsConfig;
	
	$hModule = &xoops_gethandler('module');
	$hModConfig = &xoops_gethandler('config');
	
	$smartModule = &$hModule->getByDirname('fnMangosAdmin');
	$module_id = $smartModule->getVar('mid');
	
	$module_name = $smartModule->getVar('dirname');
	$fnmaConfig = &$hModConfig->getConfigsByCat(0, $smartModule->getVar('mid'));
	
	$module_id = $smartModule->getVar('mid');
	
	$versioninfo = &$hModule->get($smartModule->getVar('mid'));
	$modfootertxt = "Module " . $versioninfo->getInfo('name') . " - Version " . $versioninfo->getInfo('version') . "";
	
	$modfooter = "<a href='" . $versioninfo->getInfo('support_site_url') . "' target='_blank'><img src='" . XOOPS_URL . "/modules/smartsection/images/sscssbutton.gif' title='" . $modfootertxt . "' alt='" . $modfootertxt . "'/></a>";
	
	return $modfooter;
}

function fnMangosAdmin_checkModuleAdmin()
{
if ( file_exists($GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php'))){
	include_once $GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php');
	return true;
	} else {
		echo xoops_error("Error: You don't use the Frameworks \"adminmodule\".Please install this Frameworks");
		return false;
	}
		
}

function &fnGetHandler($name)
{
	static $fnma_handlers;
	
	if (!isset($fnma_handlers[$name])) {
		$fnma_handlers[$name] =& xoops_getmodulehandler($name, 'fnMangosAdmin');
	}
	return $fnma_handlers[$name];
}


include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.legacy.php';
?>