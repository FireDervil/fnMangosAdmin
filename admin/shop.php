<?php

include_once("header.php");

$xoopsOption['pagetype'] = 'shops';
fnma_AdminLoadLanguage('shops', 'fnMangosAdmin');

xoops_cp_header();
global $xoopsModule;
if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();
}
$op = !empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"default");

//====== Pagination Code ======/
$limit = 20; // Sets how many results shown per page	
if(!isset($_GET['page']) || (!is_numeric($_GET['page'])))
{
    $page = 1;
} 
else 
{
	$page = $_GET['page'];
}
$limitvalue = $page * $limit - ($limit);	// Ex: (2 * 25) - 25 = 25 <- data starts at 25

// Get all items
$getitems = $fnmaDB["sys"]->select("SELECT * FROM ".$xoopsDB->prefix('fnma_shop_items')." ORDER BY id ASC LIMIT $limitvalue, $limit;");
$getcnt = $fnmaDB["sys"]->select("SELECT item_number FROM ".$xoopsDB->prefix('fnma_shop_items')."");
$totalrows = count($getcnt);

// Get alist of all the realms
$realms = getRealmlist(0);
foreach($realms as $aaa) 
{
	$realmzlist .= "<option value='".$aaa['id']."'>".$aaa['name']."</option>";
}

switch($op){
	
case 'main':
default:
echo '<div class="content">	
		<div class="content-header">
			<h4><a href="index.php">Main Menu</a> / Shop Items</h4>
		</div>				
		<div class="main-content">
			<form method="POST" action="shop.php?additem=true" class="form label-inline">
				<h5><center>'._MA_FNMA_SHOPS_LISTSITEM.'</center></h5><br />
				<table>
					<thead>
						<th><center><b>'._MA_FNMA_SHOPS_ID.'</b></center></th>
						<th><center><b>'._MA_FNMA_SHOPS_REWARD.'</b></center></th>
						<th><center><b>'._MA_FNMA_SHOPS_QUANTITY.'</b></center></th>
						<th><center><b>'._MA_FNMA_SHOPS_COSTS.'</b></center></th>
						<th><center><b>'._MA_FNMA_SHOPS_REALM.'</b></center></th>
						<th><center><b>'._MA_FNMA_SHOPS_ACTION.'</b></center></th>
					</thead>';
				
					if($getitems != FALSE)
					{
						foreach($getitems as $row)
						{
							echo "
								<tr>
									<td width='10%' align='center'>".$row['id']."</td>
									<td width='40% align='center'><center>";
									if($row['item_number'] != 0)
									{
										$item_name = $fnmaDB["world"]->selectCell("SELECT name FROM item_template WHERE entry='".$row['item_number']."'");
										if($item_name == FALSE) 
										{ 
											echo "<font color='red'> INVALID ITEM ID!</font>"; 
										}
										else
										{ 
											echo "<a href='http://www.wowhead.com/?item=".$row['item_number']."' target='_blank'>".$item_name."</a>"; 
										}
									}
									else
									{
										echo _MA_FNMA_NOT_ITEM;
									}
									if($row['itemset'] != 0) 
									{ 
										echo "<br /><a href='http://www.wowhead.com/?itemset=".$row['itemset']."' target='_blank'>"._MA_FNMA_ITESET." ".$row['itemset']."</a>"; 
									}
									if($row['gold'] != 0) 
									{ 
										echo "<br />"._MA_FNMA_GOLD.": "; print_gold($row['gold']); 
									}
							echo "</center></td>								
									<td width='10%' align='center'>".$row['quanity']."</td>
									<td width='10%' align='center'>".$row['wp_cost']."</a></td>
									<td width='15%' align='center'>
									";
									if ($row['realms'] == 0) 
									{ 
										echo _MA_FNMA_ALL; 
									}
									else
									{ 
										echo $row['realms']; 
									}
							echo "<td width='15%' align='center'><a href='shop.php?op=edit&id=".$row['id']."'>"._MA_FNMA_EDIT."</a> | <a href='shop.php?op=del&id=".$row['id']."'>"._MA_FNMA_DELETE."</a></td>
									</td>
								</tr>
							";
						}
					}
				
				echo'</table>
				<div id="pg">';
				// If there is going to be more then 1 page, then show page nav at the bottom
					if($totalrows > $limit)
					{
						admin_paginate($totalrows, $limit, $page, 'shop.php');
					}
				
				echo' </div>
			<br />
			<div class="buttonrow-border">								
				<center><button><span>'._MA_FNMA_SHOPS_ADD_PACKAGE.'</span></button></center>			
			</div>
			</form>
		</div>
	</div>';

break;	
}

xoops_cp_footer();
?>