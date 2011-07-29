<?php

include_once("header.php");

xoops_cp_header();

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();

}
$op = !empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"default");

// For the search bar
if(isset($_POST['action']))
{
	if($_POST['action'] == 'sort')
	{
		redirect_header('chrtools.php?sort='.$_POST['sortby'],1);
	}
}

// Get the realm name
$Realm = getRealmById($_SESSION['selected_realm']);
$Realms = getRealmlist('0');

// Include the SDL files
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'characters'. DS .'chars.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'zones'. DS .'fnma_zone.php';
$fnmaChar = new Char;
$fnmaZone = new Zone;

//====== Pagination Code ======/
$limit = 50; // Sets how many results shown per page	
if(!isset($_GET['page']) || (!is_numeric($_GET['page'])))
{
    $page = 1;
} 
else 
{
	$page = $_GET['page'];
}
$limitvalue = $page * $limit - ($limit);	// Ex: (2 * 25) - 25 = 25 <- data starts at 25

//===== Filter ==========// 
if($_GET['sort'] && preg_match("/[a-z]/", $_GET['sort']))
{
	$filter = "WHERE name LIKE '" . $_GET['sort'] . "%'";
}
elseif($_GET['sort'] == 1)
{
	$filter = "WHERE name REGEXP '^[^A-Za-z]'";
}
else
{
	$filter = '';
}

// Get all characters
$characters = $fnmaDB["char"]->select("SELECT * FROM characters $filter ORDER BY name ASC LIMIT $limitvalue, $limit;");
$totalrows = $fnmaDB["char"]->mysql_count("SELECT COUNT(*) FROM characters $filter");
$fnmaUser = $fnmaAccount->getProfile($_SESSION['fnmaUserId']);

switch($op){
	
case 'main':
default:
echo '<div class="content">	
		<div class="content-header">
			<h4><a href="index.php">Main Menu</a> / Character tools</h4>
		</div>			
		<div class="main-content">
			<center><h2>'._MD_FNMA_CHAR_CLIST.'</h2></center>
		<table>
			<tr>
				<td align="center"><b>'._MD_FNMA_CHAR_CREALM.'</b><br />|';
					foreach($Realms as $Rlm)
					{
						echo "<a href=\"javascript:setcookie('selected_realm', '". $Rlm['id'] ."'); window.location.reload();\">";
						if($fnmaUser['selected_realm'] == $Rlm['id'])
						{
							echo "<b>".$Rlm['name']."</b>"; 
						}
						else
						{
							echo $Rlm['name'];
						}
						echo "</a> |";
					}
				echo '</td>
			<tr>
				<td colspan="4" align="center">
					<b>'._MD_FNMA_CHAR_SORTBYLETTER.'</b>&nbsp;&nbsp;
					<small>
					<a href="?p=admin&sub=chartools">All</a> | 
					<a href="chrtools.php?sort=1">#</a> 
					<a href="chrtools.php?sort=a">A</a> 
					<a href="chrtools.php?sort=b">B</a> 
					<a href="chrtools.php?sort=c">C</a> 
					<a href="chrtools.php?sort=d">D</a> 
					<a href="chrtools.php?sort=e">E</a> 
					<a href="chrtools.php?sort=f">F</a> 
					<a href="chrtools.php?sort=g">G</a> 
					<a href="chrtools.php?sort=h">H</a> 
					<a href="chrtools.php?sort=i">I</a> 
					<a href="chrtools.php?sort=j">J</a> 
					<a href="chrtools.php?sort=k">K</a> 
					<a href="chrtools.php?sort=l">L</a> 
					<a href="chrtools.php?sort=m">M</a> 
					<a href="chrtools.php?sort=n">N</a> 
					<a href="chrtools.php?sort=o">O</a> 
					<a href="chrtools.php?sort=p">P</a> 
					<a href="chrtools.php?sort=q">Q</a> 
					<a href="chrtools.php?sort=r">R</a> 
					<a href="chrtools.php?sort=s">S</a> 
					<a href="chrtools.php?sort=t">T</a> 
					<a href="chrtools.php?sort=u">U</a> 
					<a href="chrtools.php?sort=v">V</a> 
					<a href="chrtools.php?sort=w">W</a> 
					<a href="chrtools.php?sort=x">X</a> 
					<a href="chrtools.php?sort=y">Y</a> 
					<a href="chrtools.php?sort=z">Z</a>              
					</small>           
				</td>
			</tr>
			<tr>
				<td>
					<form method="POST" action="chrtools.php" name="adminform" class="form label-inline">
					<input type="hidden" name="action" value="sort">
						<div class="field">
							<center>
								<b><font size="2">'._MD_FNMA_SEARCH_BY_NAME.'</font></b> <input name="sortby" size="20" type="text" class="medium">
								<button><span>'._MD_FNMA_BTN_SEARCH.'</span></button>
							</center>
						</div>
					</form>
				</td>
			</tr>
		</table>
		<form method="POST" action="chrtools.php" name="adminform" class="form label-inline">
			<table width="95%">
				<thead>
					<tr>
						<th width="30%"><b><center>'._MD_FNMA_CHAR_NAME.'</center></b></th>
						<th width="10%"><b><center>'._MD_FNMA_CHAR_LEVEL.'</center></b></th>
						<th width="20%"><b><center>'._MD_FNMA_CHAR_RACE.'</center></b></th>
						<th width="20%"><b><center>'._MD_FNMA_CHAR_CLASS.'</center></b></th>
						<th width="20%"><b><center>'._MD_FNMA_CHAR_LOCATION.'</center></b></th>
					</tr>
				</thead>';
				foreach($characters as $row) 
				{ 
					echo '<tr class="content">
						<td align="center"><a href="chrtools.php?id='.$row['guid'].'">'.$row['name'].'</a></td>
						<td align="center">'.$row['level'].'</td>
						<td align="center">'.$fnmaChar->charInfo['race'][$row['race']].'</td>
						<td align="center">'.$fnmaChar->charInfo['class'][$row['class']].'</td>
						<td align="center">'.$fnmaZone->getZoneName($row['zone']).'</td>
					</tr>';
				}
			echo '</table>
			<div id="pg">';

				// If there is going to be more then 1 page, then show page nav at the bottom
				if($totalrows > $limit)
				{
					if(isset($_GET['sort']))
					{
						admin_paginate($totalrows, $limit, $page, 'chrtools.php?sort='.$_GET['sort']);
					} else {
						admin_paginate($totalrows, $limit, $page, 'chrtools.php');
					}
				}
			echo '</div>
		</form>
		</div>
	</div>';


break;	
}

xoops_cp_footer();
?>