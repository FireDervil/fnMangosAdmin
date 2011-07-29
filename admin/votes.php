<?php

include_once("header.php");

xoops_cp_header();

global $xoopsModule;

$xoopsOption['pagetype'] = 'user';
fnma_AdminLoadLanguage('votes', 'fnMangosAdmin');

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();

}
$op = !empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"default");
$get_sites = $fnmaDB["sys"]->select("SELECT * FROM ".$xoopsDB->prefix('fnma_vote_sites')."");

switch($op){


case 'saveNSite':
if(empty($_POST['link_host']) || empty($_POST['link']) || empty($_POST['link_image']) || empty($_POST['link_points']) || empty($_POST['reset_time']))
{
	redirect_header('votes.php', 4, _MD_FNMA_VOTE_FIELD_EMPTY);
	exit();
}
	
$query = $fnmaDB["sys"]->query("INSERT INTO ".$xoopsDB->prefix('fnma_vote_sites')." (
		`hostname`,
		`votelink`,
		`image_url`,
		`points`,
		`reset_time`)
	  VALUES(
		'".$_POST['link_host']."', 
		'".$_POST['link']."', 
		'".$_POST['link_image']."', 
		'".$_POST['link_points']."',
		'".$_POST['reset_time']."'
		)
	");
	if($query == true){
		redirect_header('votes.php', 4, _MD_FNMA_VOTE_ADD_SUCCESS);
		exit();
	} else {
		redirect_header('votes.php', 4, _MD_FNMA_VOTES_ADD_FAILURE);
		exit();
	}
	exit();
break;


case 'saveEdit':
if(empty($_POST['hostname']) || empty($_POST['votelink']) || empty($_POST['image_url']) || empty($_POST['points']) || empty($_POST['reset_time']))
{
	redirect_header('votes.php', 4, _MD_FNMA_VOTE_FIELD_EMPTY);
	exit();
}
	$id= $_GET['id'];
	$query = $fnmaDB["sys"]->query("UPDATE ".$xoopsDB->prefix('fnma_vote_sites')." SET
							hostname='".$_POST['hostname']."',
							votelink='".$_POST['votelink']."',
							image_url='".$_POST['image_url']."',
							points='".$_POST['points']."',
							reset_time='".$_POST['reset_time']."'
							WHERE id='".$id."'
							");
	if($query == true){
		redirect_header('votes.php', 4, _MD_FNMA_VOTE_EDIT_SUCCESS);
		exit();
	} else {
		redirect_header('votes.php', 4, _MD_FNMA_VOTES_EDIT_FAILURE);
		exit();
	}
exit();
break;


case 'edit':
	$id = $_GET['id'];

	echo '<div class="content">	<center>
		<div class="content-header"><h4><a href="index.php">Main Menu</a> / <a href="votes.php">Vote Links</a> / Edit</h4></div>
		<br /><br />
		<div class="main-content">
			<form method="POST" action="votes.php?op=saveEdit&&id='.$id.'" class="form label-inline">';

				$edit_info = $fnmaDB["sys"]->selectRow("SELECT * FROM ".$xoopsDB->prefix('fnma_vote_sites')." WHERE `id`='".$id."'");
			
			echo '<!-- Hostname -->
			<div class="field">
				<label for="Link_Title">'._MD_FNMA_VOTES_HOSTNAME.'</label>
				<input id="Link_Title" name="hostname" size="40" type="text" class="medium" value="'.$edit_info['hostname'].'" />
				<p class="field_help">'._MD_FNMA_VOTES_HOSTNAME_DESC.'</p>
				<br /><br />
			</div>
			
			<!-- Vote Link -->
			<div class="field">
				<label for="vote_link">'._MD_FNMA_VOTES_VOTELINK.'</label>
				<input id="vote_link" name="votelink" size="40" type="text" class="medium" value="'.$edit_info['votelink'].'"  />
				<p class="field_help">'._MD_FNMA_VOTES_VOTELINK_DESC.'</p>
				<br /><br />
			</div>
			
			<!-- Image Url -->
			<div class="field">
				<label for="image">'._MD_FNMA_VOTES_IMAGEURL.'</label>
				<input id="image" name="image_url" size="40" type="text" class="medium" value="'.$edit_info['image_url'].'"  />
				<p class="field_help">'._MD_FNMA_VOTES_IMAGEURL_DESC.'</p>
				<br /><br />
			</div>
			
			<!-- Points -->
			<div class="field">
				<label for="points">'._MD_FNMA_VOTES_POINTS.'</label>
				<input id="points" name="points" size="10" type="text" class="tiny" value="'.$edit_info['points'].'" />
				<p class="field_help">'._MD_FNMA_VOTES_POINTS_DESC.'</p>
				<br /><br />
			</div>
			
			<!-- Reset Time -->
			<div class="field">
				<label for="reset_time">'._MD_FNMA_VOTES_RESET_TIME.'</label>
				<select id="type" class="small" name="reset_time">
					<option value="43200">12 Hours</option>
					<option value="86400">24 Hours</option>
				</select>
				<p class="field_help">'._MD_FNMA_VOTES_RESETTIME_DESC.'</p>
				<br /><br />
			</div>
			
			<div class="buttonrow-border">								
				<center>
					<button><span>'._MD_FNMA_VOTE_BTN_UPDATE.'</span></button>
				</center>					
			</div>
			
			</form>
		</div>
		</center>
	</div>';
    break;


case 'addSite':
echo '<!-- ADDING LINK -->
	<div class="content"><center>
		<div class="content-header">
			<h4><a href="?p=admin">Main Menu</a> / <a href="votes.php">Vote Links</a> / ADD</h4>
		</div> <br /><br />			
		<div class="main-content">
			<form method="POST" action="votes.php?op=saveNSite" class="form label-inline">
			
			<!-- Hostname -->
			<div class="field">
				<label for="Link_Title">'._MD_FNMA_VOTES_HOSTNAME.'</label>
				<input id="Link_Title" name="link_host" size="20" type="text" class="medium" />
				<p class="field_help">'._MD_FNMA_VOTES_HOSTNAME_DESC.'</p>
				<br /><br />
			</div>
			
			<!-- Vote Link -->
			<div class="field">
				<label for="vote_link">'._MD_FNMA_VOTES_VOTELINK.'</label>
				<input id="vote_link" name="link" size="20" type="text" class="medium" />
				<p class="field_help">'._MD_FNMA_VOTES_VOTELINK_DESC.'</p>
				<br /><br />
			</div>
			
			<!-- Image Url -->
			<div class="field">
				<label for="image">'._MD_FNMA_VOTES_IMAGEURL.'</label>
				<input id="image" name="link_image" size="20" type="text" class="medium" />
				<p class="field_help">'._MD_FNMA_VOTES_IMAGEURL_DESC.'</p>
				<br /><br />
			</div>
			
			<!-- Points -->
			<div class="field">
				<label for="points">'._MD_FNMA_VOTES_POINTS.'</label>
				<input id="points" name="link_points" size="20" type="text" class="tiny" />
				<p class="field_help">'._MD_FNMA_VOTES_POINTS_DESC.'</p>
				<br /><br />
			</div>
			
			<!-- Reset Time -->
			<div class="field">
				<label for="reset_time">'._MD_FNMA_VOTES_RESET_TIME.'</label>
				<select id="type" class="small" name="reset_time">
					<option value="43200">12 Hours</option>
					<option value="86400">24 Hours</option>
				</select>
				<p class="field_help">'._MD_FNMA_VOTES_RESETTIME_DESC.'</p>
				<br /><br />
			</div>
			
			<div class="buttonrow-border">								
				<center><button><span>'._MD_FNMA_VOTES_BTN_ADDSITE.'</span></button></center>			
			</div>		
			</form>
		</div></center>
	</div>';
    break;

	
case 'main':
default:

echo'<div class="content">	
		<div class="content-header"><h4><a href="index.php">Main Menu</a> / Vote Links</h4></div>			
		<div class="main-content"><form method="POST" action="votes.php?op=addSite" class="form label-inline">
				<h5><center>List of Vote Sites</center></h5><br />
				<table>
					<thead>
						<th><center><b>'._MD_FNMA_VOTE_VSITES.'</b></center></th>
						<th><center><b>'._MD_FNMA_VOTES_VIMAGE.'</b></center></th>
						<th><center><b>'._MD_FNMA_VOTES_VLINK.'</b></center></th>
						<th><center><b>'._MD_FNMA_VOTES_VPOINTS.'</b></center></th>
					</thead>';
					if($get_sites != FALSE)
					{
						foreach($get_sites as $site)
						{
							echo '<tr>
									<td width="25%" align="center"><a href="votes.php?op=edit&id='.$site["id"].'">'.$site["hostname"].'</a></td>
									<td width="35%" align="center">'.$site["image_url"].' || <img src="'.$site['image_url'].'" width="32" height="20"></img></td>
									<td width="25%" align="center"><a href="'.$site["votelink"].'">'.$site["votelink"].'</a></td>
									<td width="15%" align="center">'.$site['points'].'</td>
								</tr>';
						}
					}
				
				echo '</table>
			<br />
			<div class="buttonrow-border">								
				<center><button><span>Add New Link</span></button></center>			
			</div>
			</form>
		</div>
	</div>';


break;	
}

xoops_cp_footer();
?>