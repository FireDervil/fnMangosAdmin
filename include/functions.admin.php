<?php



function getOptions4Admin() {
global $xoopsDB;
	$sql = "SELECT conf_id, conf_name, conf_value FROM ".$xoopsDB->prefix("fnma_main_config");
	$result = $xoopsDB->query($sql);
	$i=0;
	while($myrow = $xoopsDB->fetchArray($result)) {
		$arr_conf[$i]['conf_id'] = $myrow['conf_id'];
		$arr_conf[$i]['conf_name'] = $myrow['conf_name'];
		$arr_conf[$i]['conf_value'] = $myrow['conf_value'];
		$arr_conf[$i]['conf_formtype'] = $myrow['conf_formtype'];
		$i++;
	}
	return $arr_conf;
}


function admin_paginate($totalrows, $limit, $page, $link_to)
{
	if($page != 1) 
	{ 
		$pageprev = $page-1;
		echo "<a href=\"".$link_to."&page=1\">&laquo; First</a>&nbsp;&nbsp;&nbsp;";  
		echo "<a href=\"".$link_to."&page=".$pageprev."\">&laquo; Previous</a>&nbsp;&nbsp;";  
	}
	else
	{
		echo "<span class='disabled'>&laquo; First </span>&nbsp;&nbsp;&nbsp;";
		echo "<span class='disabled'>&laquo; Previous</span>&nbsp;&nbsp;";
	}
	$numofpages = ceil($totalrows / $limit);

	// === START BUTTON LOADING === //
		// If the current page is higher then 5
		if($page > 5)
		{
			$start = $page - 5; # start at the current page, 5 back
		}
		else
		{
			$start = 1; # start at 1, because 5 back is a negative number
		}
		
		// If page is lower then 5, we have less then 5 previous, so we want more
		// Nexts to equal out to 10
		if($start < 5)
		{
			$end = (10 - $start);
		}
		else
		{
			$end = $page + 5; # End number is 5 plus our current page
		}
		
		// If the end number is greater then our number of pages, we want
		// more previous page number to equal 10 total
		if($numofpages <= $end)
		{
			$overpage = $end - $numofpages; # find out how many under 5 we have
			$start = $start - $overpage; # set the new start to (page -5) - how many over
		}
		if($start <= 0)
		{
			$start = 1;
		}
		
	// Now that we hae a start and finish, lets add out buttons
	for($j = $start; $j <= $end && $j <= $numofpages; $j++)
	{
		if($j == $page)
		{
			echo "<a  class='current'  href=\"".$link_to."&page=".$j."\">".$j."</a>&nbsp;&nbsp;";
		}
		else
		{
			echo "<a href=\"".$link_to."&page=".$j."\">$j</a>&nbsp;&nbsp;"; 
		}
	}
	if(($totalrows % $limit) != 0)
	{
		if($j == $page)
		{
			echo "<a  class='current'  href=\"".$link_to."&page=".$j."\">".$j."</a>&nbsp;&nbsp;";
		}
		else
		{
			echo "<a href=\"".$link_to."&page=".$j."\">$j</a>&nbsp;&nbsp;";
		}
	}	
	if(($totalrows - ($limit * $page)) > 0)
	{
		$pagenext   = $page + 1;
		echo "<a href=\"".$link_to."&page=".$pagenext."\">Next &raquo;</a>&nbsp;&nbsp;&nbsp;";
		echo "<a href=\"".$link_to."&page=".$numofpages."\">Last &raquo;</a>&nbsp;&nbsp;";
	}
	else
	{
		echo "<span class='disabled'>Next &raquo;</span>&nbsp;&nbsp;&nbsp;";
		echo "<span class='disabled'>Last &raquo;</span>"; 
	} 
}

function fnma_AdminLoadLanguage($name, $domain = '', $language = null)
{
    /**
     * We must check later for an empty value. As xoops_getPageOption could be empty
     */
    if (empty($name)) {
        return false;
    }
    $language = empty($language) ? $GLOBALS['xoopsConfig']['language'] : $language;
    $path = 'modules/' . $domain . '/language/';
    if ( file_exists($file = $GLOBALS['xoops']->path( $path . $language . '/admin/' . $name . '.php') ) ) {
        $ret = include_once $file;
    } else {
        $ret = include_once $GLOBALS['xoops']->path( $path . 'english/admin/' . $name . '.php');
    }
    return $ret;
}

function fnma_adminVersion($version, $value = '')
{
    static $tblVersion = array();
    if (is_array($tblVersion) && array_key_exists($version . '.' . $value, $tblVersion)) {
        return $tblVersion[$version . '.' . $value];
    }
    $path = XOOPS_ROOT_PATH . '/modules/fnMangosAdmin/admin/' . $version . '/xoops_version.php';
    if (file_exists($path)) {

        include $path;

        $retvalue = $modversion[$value];
        $tblVersion[$version . '.' . $value] = $retvalue;
        return $retvalue;
    }
}

function fnma_AdminIcons($img)
{
    $style='default';

    $url = $GLOBALS['xoops']->url('modules/fnMangosAdmin/images/icons/' . $style . '/' . $img);
    return $url;
}

function fnma_CleanVars(&$global, $key, $default = '', $type = 'int')
{
    switch ($type) {
        case 'array':
            $ret = (isset($global[$key]) && is_array($global[$key])) ? $global[$key] : $default;
            break;
        case 'date':
            $ret = (isset($global[$key])) ? strtotime($global[$key]) : $default;
            break;
        case 'string':
            $ret = (isset($global[$key])) ? filter_var($global[$key], FILTER_SANITIZE_MAGIC_QUOTES) : $default;
            break;
        case 'int': default:
            $ret = (isset($global[$key])) ? filter_var($global[$key], FILTER_SANITIZE_NUMBER_INT) : $default;
            break;
    }
    if ($ret === false) {
        return $default;
    }
    return $ret;
}

?>