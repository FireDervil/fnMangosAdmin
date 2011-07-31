<?php

include('header.php');
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'defines.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'functions.soap.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'sockets' . DS. 'socket.soap.php';

xoops_loadLanguage('shop', 'fnMangosAdmin');

$xoopsOption['template_main']= 'fnma_shop_checkout.html';
include XOOPS_ROOT_PATH."/header.php";
$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );

$package = $fnmaDB["sys"]->selectRow("SELECT * FROM ".$xoopsDB->prefix('fnma_shop_items')." WHERE id='".$_POST['id']."'");
$character_list = $fnmaAccount->getCharacterList($_SESSION['fnmaUserId']);
$fnmaUser = $fnmaAccount->getProfile($_SESSION['fnmaUserId']);
	
// TODO: 
// pre var, put this back in xoopsConfig after checking
$xoopsModuleConfig['send_system'] = '1'; // possible option 1 = SOAP, 2 = RA, 3 = SOCKET (=>experimental)

$op = 'default';
foreach ( $_POST as $k => $v ) { ${$k} = $v; }
foreach ( $_GET as $k => $v ) { ${$k} = $v; }

//	************************************************************
// Main sending function
function dofull_checkout($id, $sys = NULL)
{
	global $xoopsDB, $fnmaDB, $fnmaUser, $package, $commands;
	

	// lets check the main send system
	if($sys == '' || $sys > '3' || $sys < '1')
	{
		redirect_header('shop.php', 4, _FNMA_SHOP_ERROR_SYS);
		exit();
	}
	
	// Lets check to see if the realm is online before starting
	$realm = getRealmById($_SESSION['selected_realm']);
	foreach ( $realm as $r_info ){ $check = check_port_status($r_info['address'], $r_info['port'], 3); }
	if(!$check)
	{
		redirect_header('shop.php', 3,_FNMA_SHOP_ERROR_REALM);
		exit();
	}
	
	// Second check to see if the user has enough points
	if($package['wp_cost'] > $fnmaUser['web_points'])
	{
		redirect_header('shop.php',4, _FNMA_SHOP_NENOUGH_POINTS);
		exit();
	}

	// Initiate the command array
	$command = array();
	
	// If there is an item number for the selected package
	if($package['item_number'] != 0) 
	{
		$item_array = '';
		$package_array = explode(',', $package['item_number']);
		foreach($package_array as $a)
		{
			$item_array .= $a.":".$package['quanity']." ";
		}
		$command = "send items ".$_POST['char']." \""._FNMA_SHOP_INGAME_MAILSUBJECT."\" \"".
			_FNMA_SHOP_INGAME_MESSAGE."\" ".$item_array;
	}
	
	// If there is an itemset for this package, we need to make a command for that as well
	if($package['itemset'] != 0) 
	{
		$qray = $fnmaDB["world"]->select("SELECT entry FROM item_template WHERE itemset='".$package['itemset']."'");
		$items = '';
		foreach($qray as $d)
		{
			$items .= $d['entry'].":1 ";
		}
		$command = "send items ".$_POST['char']." \""._FNMA_SHOP_INGAME_MAILSUBJECT."\" \"".
				_FNMA_SHOP_INGAME_MESSAGE."\" ".$items;
	}
	
	// If there is gold in this package, make a command for that
	if($package['gold'] != 0) 
	{
		$command = "send money ".$_POST['char']." \""._FNMA_SHOP_INGAME_MAILSUBJECT."\" \"".
			_FNMA_SHOP_INGAME_MESSAGE."\" ".$package['gold'];
	}
	
	// === Send the command to the RA Class === //
	//$send = $RA->send($command, $_SESSION['selected_realm']);
	$fnSoap = new fnmaSOAPClient();
	$fnSoap->SetCommandsList($commands);
	$fnSoap->SetUsername('firemaker');
	$fnSoap->SetPassword('s68m152sm$');
	$fnSoap->SetUrl('http://85.214.249.174:27680/');

	$system_sent = $fnSoap->SendCommand($command);

	// Catch the result of send. If its a 1 or 2, then the send wasnt successful
	if(is_numeric($system_sent))
	{
		redirect_header('shop.php', 4, sprintf(_FNMA_SHOP_SEND_ERROR, $fnSoap->GetErrorMsg()));
		exit();
	} else {
		// Command was sent successfully
		// Initiate our counts
		$success = 0;
		$total_commands = count($command);
		
		// Return will be in an array, so foreach array variable, we need the result
		foreach($system_sent as $report)
		{
			// If in the string, the characters name is listed, then its a success
			if(strpos($report, $_POST['char']))
			{
				$success++;
			}				
		}

		// If the success count is equal to the total amount of commands sent
		// Then all was successful
		if($success == $total_commands)
		{
			global $xoopsDB, $fnmaDB, $package;
			
			// Update the DB, subtracting the cost of the package
			$fnmaDB["sys"]->query("UPDATE ".$xoopsDB->prefix('fnma_account_extend')." SET
									web_points=(web_points - ".$package['wp_cost']."),
									points_spent=(points_spent + ".$package['wp_cost'].")
									WHERE account_id = ".$_SESSION['fnmaUserId']." LIMIT 1"
			);
			
			redirect_header('shop.php', 4, _FNMA_SHOP_CHECKOUT_SUCCESS);
			exit();
		} else {
			redirect_header('shop.php', 4, _FNMA_SHOP_GLOBAL_ERROR);
			exit();
		}
	}
}


switch($op)
{
	
	case 'checkout':
		$sys = $xoopsModuleConfig['send_system'];
		dofull_checkout($id, $sys);
	exit();
	break;

	case 'main':
	default:
	if($package['item_number'] > '0')
	{
		$item_name = $fnmaDB["world"]->selectCell("SELECT name FROM item_template WHERE entry='".$package['item_number']."'");
		$xoopsTpl->assign("item_name", $item_name);
	}
	
	if($package['gold'] > '0')
	{
		$newGold = parse_gold($package['gold']);
		if($newGold['gold'] > '0')
		{
			$xoopsTpl->assign('print_gold', "".$newGold['gold']." &nbsp;<img src=\"images/icons/money/gold.gif\" alt=\"Gold\" border=\"0\">");
		}
		if($newGold['silver'] > '0')
		{
			$xoopsTpl->assign('print_gold', "".$newGold['silver']." &nbsp;<img src=\"images/icons/money/silver.gif\" alt=\"Silver\" border=\"0\">");
		}
		if($newGold['copper'] > '0')
		{
			$xoopsTpl->assign('print_gold', "".$newGold['copper']." &nbsp;<img src=\"images/icons/money/copper.gif\" alt=\"Copper\" border=\"0\">");
		}
	}
	$xoopsTpl->assign('package', $package);
	$xoopsTpl->assign('character_list', $character_list);
	$xoopsTpl->assign(array(
					"lang_shop_package" => _FNMA_SHOP_PACKAGE,
					"lang_recive_items"	=> _FNMA_SHOP_RECIVE_ITEM,
					"lang_cost"			=> _FNMA_SHOP_COST,
					"lang_action"		=> _FNMA_SHOP_ACTION,
					"lang_gold"			=> _FNMA_GOLD,
					"lang_select_char"	=> _FNMA_SELECT_CHAR,
					"lang_web_points_costs"	=> _FNMA_WEB_POINTS_COST,
					"lang_btn_choose"	=> _FNMA_BTN_CHOOSE,
					"lang_btn_order_now" => _FNMA_SHOPS_ORDER_NOW
					));
	$xoopsTpl->assign("cost_package", sprintf(_FNMA_SHOP_WEB_POINTS_TOTAL, $package['wp_cost']));
	
		
	break;
}

include_once XOOPS_ROOT_PATH.'/footer.php';

?>