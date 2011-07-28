<?php

function dev($data, $level = NULL)
{
	
	echo "<pre>";
	print_r($data);
	echo  "</pre>";

}

function &fnma_load_config()
{
	static $moduleConfig;
	if(isset($moduleConfig)){
		return $moduleConfig;
	}
	
    if(isset($GLOBALS["xoopsModule"]) && is_object($GLOBALS["xoopsModule"]) && $GLOBALS["xoopsModule"]->getVar("dirname", "n") == "fnMangosAdmin"){
	    if(!empty($GLOBALS["xoopsModuleConfig"])) {
		    $moduleConfig =& $GLOBALS["xoopsModuleConfig"];
	    }else{
		    return null;
	    }
    }else{
		$module_handler = &xoops_gethandler('module');
		$module = $module_handler->getByDirname("fnMangosAdmin");
	
	    $config_handler = &xoops_gethandler('config');
	    $criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
	    $configs =& $config_handler->getConfigs($criteria);
	    foreach(array_keys($configs) as $i){
		    $moduleConfig[$configs[$i]->getVar('conf_name')] = $configs[$i]->getConfValueForOutput();
	    }
	    unset($configs);
    }
	if($customConfig = @include(XOOPS_ROOT_PATH."/modules/fnMangosAdmin/include/plugin.php")){
		$moduleConfig = array_merge($moduleConfig, $customConfig);
	}
    return $moduleConfig;
}






?>