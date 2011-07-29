<?php

include_once("header.php");

$xoopsOption['pagetype'] = 'user';
fnma_AdminLoadLanguage('news', 'fnMangosAdmin');

xoops_cp_header();

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();

}
$op = !empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"default");
$gettopics = $fnmaDB["sys"]->select("SELECT title, id, posted_by, post_time FROM ".$xoopsDB->prefix('fnma_news')."");

switch($op){

	case 'addNews':
	$subj = $_POST['subject'];
	$message = $_POST['message'];
	$currUser = $fnmaAccount->getUsername($_SESSION['fnmaUserId']);
    if(!$subj | !$message)
	{
		redirect_header('news.php',4, _MD_FNMA_NEWS_FIELD_EMPTY);
		exit();
	}
	else
	{
		$post_time = time();
		$sql =  "INSERT INTO ".$xoopsDB->prefix('fnma_news')." (title, message, posted_by, post_time) VALUES('".$subj."','".$message."','".$currUser."','".$post_time."')";
        $tabs = $fnmaDB["sys"]->query($sql);
		redirect_header('news.php', 4, _MD_FNMA_NEWS_ADD_SUCCESS);
		exit();
    }
	break;

	case 'saveEdit':
	$idz = $_GET['id'];
	$mess = $_POST['editmessage'];
	if(!$mess)
	{
		redirect_header('news.php', 4, _MD_FNMA_NEWS_FIELD_EMPTY);
		exit();
	} else {
		$fnmaDB["sys"]->query("UPDATE ".$xoopsDB->prefix('fnma_news')." SET `message`='$mess' WHERE `id`='$idz'");
		redirect_header('news.php', 4, _MD_FNMA_NEWS_EDIT_SUCCESS);
		exit();
	}
	break;

	case 'delNews':
	$idzz = $_GET['id'];
	$fnmaDB["sys"]->query("DELETE FROM ".$xoopsDB->prefix('fnma_news')." WHERE `id`='$idzz'");
	redirect_header('news.php', 4, _MD_FNMA_NEWS_DEL_SUCCESS);
	exit();
	break;

case'editNews':

	$content = $fnmaDB["sys"]->selectRow("SELECT * FROM ".$xoopsDB->prefix('fnma_news')." WHERE `id`='".$_GET['id']."'");
	echo '<div class="content-header"><h4><a href="index.php">'._MD_FNMA_USER_MAINMENU.'</a> / <a href="news.php">'._MD_FNMA_USER_NEWSMMGR.'</a> / '._MD_FNMA_NEWS_EDIT.'</h4></div>	
		<div class="main-content">
			<form method="POST" action="news.php?op=saveEdit&id='.$_GET['id'].'" class="form label-inline">
				<table>
					<thead>
						<tr>
							<th><center>'._MD_FNMA_NEWS_EDIT_NEWSTEXT.'</center></th>
						</tr>
					</thead>
				</table>
				<br />
				
				<div class="field">
					<label for="Subject">'._MD_FNMA_NEWS_SUBJECT.'</label>
					<input id="Subject" name="subject" size="20" type="text" class="medium" disabled="disabled" value="'.$content['title'].'" />
					<p class="field_help">'._MD_FNMA_NEWS_SUB_DESC.'</p>
				</div>
				
				<div class="field">
					<label for="Message">'._MD_FNMA_NEWS_MESSAGE.'</label><br />
					<textarea id="Message" name="editmessage" rows="5" cols="10" class="inputbox">'.$content['message'].'</textarea>
				</div>
				<br />		
				<div class="buttonrow-border">								
					<center>
						<button ><span>'._MD_FNMA_NEWS_BTNSAVE_CHANGES.'</span></button>
					</center>
				</div>
			</form>
		</div>';
        break;


case 'add':

echo '<div class="content-header"><h4><a href="index.php">'._MD_FNMA_USER_MAINMENU.'</a> / <a href="news.php">'._MD_FNMA_USER_NEWSMMGR.'</a> / '._MD_FNMA_NEWS_ADD.'</h4></div>			
		<div class="main-content"><center>
			<form method="POST" action="news.php?op=addNews" class="form label-inline">
				<table>
					<thead>
						<tr>
							<th><center>'._MD_FNMA_NEWS_ADD_NEW_NEWS.'</center></th>
						</tr>
					</thead>
				</table>
				<br />
				
				<!-- Subject -->
				<div class="field">
					<label for="Subject">'._MD_FNMA_NEWS_SUBJECT.'</label>
					<input id="Subject" name="subject" size="20" type="text" class="medium" />
					<p class="field_help">'._MD_FNMA_NEWS_SUB_DESC.'</p>
				</div>
				
				<div class="field">
					<label for="Message">'._MD_FNMA_NEWS_MESSAGE.'</label><br />
					<textarea id="Message" name="message" rows="2" cols="2" class="inputbox"></textarea>
				</div>
				<br />		
				<div class="buttonrow-border">								
					<center><button><span>'._MD_FNMA_NEWS_BTN_ADD.'</span></button></center>			
				</div>
			</form>
		</center></div>';
break;


case 'edit':
break;
	
case 'main':
default:

echo '<div class="content-header"><h4><a href="index.php">'._MD_FNMA_USER_MAINMENU.'</a> / '._MD_FNMA_USER_NEWSMMGR.'</h4></div> 				
		<div class="main-content">
			<form method="POST" action="news.php?op=add" class="form label-inline">
				<h2><center>'._MD_FNMA_NEWS_NLIST.'</center></h2>
				<table>
					<tbody>
						<thead>
							<tr>
								<th width="10%"><center>'._MD_FNMA_NEWS_ACTION.'</center></th>
								<th width="40%"><center>'._MD_FNMA_NEWS_NTILE.'</center></th>
								<th width="30%"><center>'._MD_FNMA_NEWS_POSTBY.'</center></th>
								<th width="30%"><center>'._MD_FNMA_NEWS_POSTTIME.'</center></th>
							</tr>
						</thead>';
						if($gettopics != FALSE)
						{
							foreach($gettopics as $row) 
							{
								$date_n = formatTimestamp($row['post_time']);
								echo '<tr>
									<td align="center"><a href="news.php?op=delNews&id='.$row['id'].'">'._MD_FNMA_NEWS_DEL.'</a></td>
									<td align="center"><a href="news.php?op=editNews&id='.$row['id'].'">'.$row['title'].'</a></td>
									<td align="center">'.$row['posted_by'].'</td>
									<td align="center">'.$date_n.'</td>
								</tr>';
							} // END FOR EACH NEWS
						} // END IF
					echo '</tbody>
				</table>
				<div class="buttonrow-border">								
					<center><button><span>'._MD_FNMA_NEWS_ADDNEW.'</span></button></center>			
				</div>
			</form>
		</div>';


break;	
}

xoops_cp_footer();
?>