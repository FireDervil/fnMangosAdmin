<?php

include_once("header.php");
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'defines.php';
xoops_cp_header();

$xoopsOption['pagetype'] = 'user';
fnma_AdminLoadLanguage('realms', 'fnMangosAdmin');

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();
}

$op = !empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"default");

$getrealms = $fnmaDB["logon"]->select("SELECT * FROM realmlist ORDER BY id ASC");

switch($op){
	
case 'main':
default:
echo '<div class="content">	
	<div class="content-header">
		<h4><a href="index.php">Main Menu</a> / Manage Realms</h4>
	</div>
	<div class="main-content">
		<table>
			<thead>
				<th width="5%"><center><b>'._MD_FNMA_REALMS_RID.'</b></center></th>
				<th width="30%"><center><b>'._MD_FNMA_REALMS_NAME.'</b></center></th>
				<th width="20%"><center><b>'._MD_FNMA_REALMS_ADDRESS.'</b></center></th>
				<th width="10%"><center><b>'._MD_FNMA_REALMS_PORT.'</b></center></th>
				<th width="15%"><center><b>'._MD_FNMA_REALMS_RTYPE.'</b></center></th>
				<th width="20%"><center><b>'._MD_FNMA_REALMS_TZONE.'</b></center></th>
			</thead>';
		foreach($getrealms as $row) {
			echo'<tr>
				<td align="center">'.$row['id'].'</td>
				<td align="center"><a href="realms.php?id='.$row['id'].'">'.$row['name'].'</a></td>
				<td align="center">'.$row['address'].'</td>
				<td align="center">'.$row['port'].'</td>
				<td align="center">'.$realm_type_def[$row['icon']].'</td>
				<td align="center">'.$realm_timezone_def[$row['timezone']].'</td>
			</tr>';
		}
	echo'</table>
	</div>
</div>';

break;	
}

xoops_cp_footer();
?>