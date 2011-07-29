<?php

include_once("header.php");

xoops_cp_header();

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();

}
$op = !empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"default");

$get_pack = $fnmaDB["sys"]->select("SELECT * FROM ".$xoopsDB->prefix('fnma_donate_packages')."");

switch($op){
	
case 'main':
default:

echo '<div class="content">	
		<div class="content-header">
			<h4><a href="index.php">Main Menu</a> / Donate Admin</h4>
		</div>				
		<div class="main-content">
			<form method="POST" action="?donate.php?add=true" class="form label-inline">
				<h5><center>'._MD_FNMA_DONATE_PLISTD.'</center></h5><br />
				<table>
					<thead>
						<th><center><b>'._MD_FNMA_DONATE_ID.'</center></b></th>
						<th><center><b>'._MD_FNMA_DONATE_DESCRIPTION.'</center></b></th>
						<th><center><b>'._MD_FNMA_DONATE_COSTS.'</center></b></th>
						<th><center><b>'._MD_FNMA_DONATE_REWARD.'</center></b></th>
						<th><center><b>'._MD_FNMA_DONATE_ACTION.'</center></b></th>
					</thead>';
					if($get_pack != FALSE)
					{
						foreach($get_pack as $pack)
						{
							echo "
								<tr>
									<td width='10%' align='center'>".$pack['id']."</td>
									<td width='45%' align='center'>".$pack['desc']."</td>
									<td width='15%' align='center'>".$pack['cost']."</td>
									<td width='15%' align='center'>".$pack['points']."</td>
									<td width='15%' align='center'><a href='donate.php?id=".$pack['id']."'>Edit / Del</a></td>
								</tr>
							";
						}
					}
				
				echo '</table>
			<br />
			<div class="buttonrow-border">								
				<center><button><span>'._MD_FNMA_DONATE_ADDNEW_DPACKAGE.'</span></button></center>			
			</div>
			</form>
		</div>
	</div>';

break;	
}

xoops_cp_footer();
?>