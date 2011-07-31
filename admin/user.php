<?php

include_once("header.php");

$xoopsOption['pagetype'] = 'user';
fnma_AdminLoadLanguage('user', 'fnMangosAdmin');

xoops_cp_header();

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();

}
$op = !empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"default");


if(isset($_POST['action']))
{
	switch($action)
	{
	case 'sort':
		redirect_header('user.php?sortby='.$_POST['sortby'],1);
		exit();
	break;
	
	case 'delete':
	
	break;
	
	}
}
//====== Pagination Code ======/
$limit =25; // Sets how many results shown per page	
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
$_GET['sortby'] = '';
if($_GET['sortby'] && preg_match("/[a-z]/", $_GET['sortby']))
{
	$filter = "WHERE username LIKE '" . $_GET['sortby'] . "%'";
}
elseif($_GET['sortby'] == 1)
{
	$filter = "WHERE username REGEXP '^[^A-Za-z]'";
}
else
{
	$filter = '';
}
// Get all users
$getusers = $fnmaDB["logon"]->select("SELECT * FROM account $filter ORDER BY username ASC LIMIT $limitvalue, $limit");
$totalrows = $fnmaDB["logon"]->mysql_count("SELECT COUNT(*) FROM account $filter");

switch($op){
	
	case 'edit':
		$uid = $_GET['id'];
		$profile = $fnmaAccount->getProfile($uid);
		$lastvisit = date("Y-m-d, g:i a", $profile['last_visit']);
			$seebanned = $fnmaDB["logon"]->mysql_count("SELECT COUNT(*) FROM account_banned WHERE id='".$uid."' AND `active`=1");
			if($seebanned > 0) 
			{
				$bann = 1;
			} else {
				$bann = 0;
			}
			
		echo '<!-- Viewing an account -->
		<div class="content">	
			<div class="content-header"><h4><a href="index.php">'._MD_FNMA_USER_MAINMENU.'</a> / <a href="user.php">'._MD_FNMA_USER_USERMMGR.'</a> / '._MD_FNMA_USER_CURUSER.$profile['username'].'</h4></div>			
			<div class="main-content">
			<!-- Table for general account details -->
				<table style="border-bottom: 1px solid #E5E2E2;">
					<thead>
						<th colspan="4"><center><b>'._MD_FNMA_USER_GENERAL_PROFIL.'</center></b></th>
					</thead>
					<tbody>
						<tr>
							<td width="25%" align="right">'._MD_FNMA_USER_REG_DATE.'</td>
							<td width="25%" align="left">'.$profile['joindate'].'</td>
							<td width="25%" align="right">'._MD_FNMA_USER_VOTECOUNT.'</td>
							<td width="25%" align="left">'.$profile['total_votes'].'</td>
						</tr>
						<tr>
							<td width="25%" align="right">'._MD_FNMA_USER_REG_IP.'</td>
							<td width="25%" align="left">'.$profile['registration_ip'].'</td>
							<!-- Web Point Balance -->
							<td width="25%" align="right">'._MD_FNMA_USER_WPOINTS_BALANCE.'</td>
							<td width="25%" align="left">'.$profile['web_points'].'</td>
						</tr>
						<tr>
							<td width="25%" align="right">'._MD_FNMA_USER_LAST_ACT_GAME.'</td>
							<td width="25%" align="left">'.$profile['last_login'].'</td>
							<!-- Points earned/spent -->
							<td width="25%" align="right">'._MD_FNMA_USER_PEARNSPENT.'</td>
							<td width="25%" align="left">'.$profile['points_earned'].' / '.$profile['points_spent'].'</td>
						</tr>
						<tr>
							
							<td width="25%" align="right">'._MD_FNMA_USER_LACTSITE.'</td>
							<td width="25%" align="left">'.$lastvisit.'</td>
							<td width="25%" align="right">'._MD_FNMA_USER_TOT_DONATIONS.'</td>
							<td width="25%" align="left">'.$profile['total_donations'].'</td>
						</tr>
						
					</tbody>
				</table>
				<center>
				<table>
					<tr>
						<td align="center" style="padding: 5px 5px 5px 5px;">
						<a href="user.php?op=delete&id='.$uid.'" onclick="javascript:alert ('._MD_FNMA_USER_SHURE_DEL.');">
							<b><font color="red">'._MD_FNMA_USER_DEL_ACCT.'</font></b></a> || ';
							if($bann == 1) 
							{
								echo '<a href="user.php?op=unBan&id='.$uid.'"><b><font color="red">'._MD_FNMA_USER_UNBAN.'</font></b></a>';
							}
							elseif($bann == 0) 
							{ 
								echo '<a href="user.php?op=banForm&id='.$uid.'"><b><font color="red">'._MD_FNMA_USER_BAN_ACCT.'</font></b></a>';
							}
						echo '</td>
					</tr>
				</table>
				
				<!-- EDIT PROFILE -->
				<br />
				<table>
					<thead>
						<th><center><b>'._MD_FNMA_USER_EDIT_PROFIL.'</center></b></th>
					</thead>
				</table>
				<form method="POST" action="user?id='.$uid.'" class="form label-inline">
					<input type="hidden" name="op" value="editProfile">
					
					<div class="field">
						<label for="Username">'._MD_FNMA_USER_UNAME.'</label>
						<input id="Username" name="username" size="20" type="text" class="medium" disabled="disbled" value="'.$profile['username'].'"/>
					</div>
					
					<div class="field">
						<label for="Email">'._MD_FNMA_USER_EMAIL.'</label>
						<input id="Email" name="email" size="20" type="text" class="medium" value="'.$profile['email'].'"/>
					</div>
					
					<div class="field">
						<label for="Locked">'._MD_FNMA_USER_LOCKED.'</label>
						<select name="locked" class="xsmall">';
								if($profile['locked'] == 1)
								{
									echo '<option value="1" selected="selected">'._MD_FNMA_USER_YES.'</option><option value="0">'._MD_FNMA_USER_.'</option>';
								}
								else
								{
									echo '<option value="1">'._MD_FNMA_USER_YES.'</option><option value="0" selected="selected">'._MD_FNMA_USER_NO.'</option>';
								}
						echo '</select>
					</div>
					
					<div class="field">
						<label for="Exp">'._MD_FNMA_USER_EXPANSION.'</label>
						<select name="expansion" class="small">';
								if($profile['expansion'] == 2)
								{
									echo '<option value="2" selected="selected">WotLK</option><option value="1">TBC</option><option value="0">Classic</option>';
								}
								elseif($profile['expansion'] == 1)
								{
									echo '<option value="2">WotLK</option><option value="1" selected="selected">TBC</option><option value="0">Classic</option>';
								}
								else
								{
									echo '<option value="2">WotLK</option><option value="1">TBC</option><option value="0" selected="selected">Classic</option>';
								}
						echo '</select>
					</div>
					
					<div class="buttonrow-border">								
						<center><button><span>'._MD_FNMA_USER_BTN_UPDATE.'</span></button></center>			
					</div>
				</form>
				<br />
				<br />
				<table>
					<thead>
						<th><center><b>'._MD_FNMA_USER_CHANGEPASS.'</center></b></th>
					</thead>
				</table>
				<form method="POST" action="user.php?id='.$uid.'" class="form label-inline">
					<input type="hidden" name="op" value="changePass">
					<div class="field">
						<label for="Password">'._MD_FNMA_USER_NEWPASS.'</label>
						<input id="Password" name="password" size="20" type="text" class="medium" />
					</div>
					
					<div class="buttonrow-border">								
						<center><button><span>'._MD_FNMA_USER_BTN_UPDATE.'</span></button></center>			
					</div>
				</form>
				<br />
				<br />
				<table>
					<thead>
						<th><center><b>'._MD_FNMA_USER_WEBDESTAILS.'</center></b></th>
					</thead>
				</table>
				<form method="POST" action="user.php?id='.$uid.'" class="form label-inline">
					<input type="hidden" name="op" value="editUser">
					<div class="field">
						<label for="account_level">'._MD_FNMA_USER_ACCT_LEVEL.'</label>
						<select name="account_level" class="small">';
								if($profile['account_level'] == 5)
								{
									echo '<option value="5" selected="selected">'._MD_FNMA_USER_BANNED.'</option>';
								}
								elseif($profile['account_level'] == 4)
								{
									echo '<option value="4" selected="selected">'._MD_FNMA_USER_SUPERADMIN.'</option>
										  <option value="3">'._MD_FNMA_USER_ADMIN.'</option>
										  <option value="2">'._MD_FNMA_USER_MEMBER.'</option>'; 
								}
								elseif($profile['account_level'] == 3)
								{
									echo "<option value='4'>Super Admin</option>
										  <option value='3' selected='selected'>Admin</option>
										  <option value='2'>Member</option>"; 
								}
								else
								{
									echo "<option value='4'>Super Admin</option>
										  <option value='3' selected='selected'>Admin</option>
										  <option value='2' selected='selected'>Member</option>"; 
								}

						echo '</select>
					</div>
					<div class="field">
						<label for="Web_Points">'._MD_FNMA_USER_WEBPOINTS.'</label>
						<input id="Web_Points" name="web_points" size="2" type="text" class="xsmall" value="'.$profile['web_points'].'"/>
					</div>
					
					<!-- Total Donations -->
					<div class="field">
						<label for="Web Points">'._MD_FNMA_USER_TOT_DONATIONS.'</label>
						<input id="Web Points" name="total_donations" size="2" type="text" class="xsmall" value="'.$profile['total_donations'].'"/>
					</div>
					
					<div class="buttonrow-border">								
						<center><button><span>'._MD_FNMA_USER_BTN_UPDATE.'</span></button></center>			
					</div>
				</form>
			</center>
			</div>
		</div>';
	break;
	
	case 'changePass':
	$newpass = trim($_POST['password']);
	if(strlen($newpass)>3)
	{
		if($fnmaAccount->setPassword($_GET['id'], $newpass) == TRUE)
		{
			redirect_header('user.php?id='.$_GET['id'].'',4, _MD_FNMA_USER_CHGPASS_SUCCESS);
			exit();
		} else {
			redirect_header('user.php', 4, _MD_FNMA_USER_ERROR_CHG_PASSFAILD);
			exit();
		}
	} else {
		redirect_heder('user.php?id='.$_GET['id'].'', 1, _MD_FNMA_USER_CHG_PASS_TOSHORT);
		exit();
	}
	break;
	
	case 'changeDetails':
	
	$success = 0;
	
	if($fnmaAccount->setEmail($_GET['id'], $_POST['email']) == TRUE)
	{
		$success++;
	} else {
		redirect_header('user.php', 1, _MD_FNMA_USER_ERROR_MAILSET);
		exit();
	}
	
	if($fnmaAccount->setLock($_GET['id'], $_POST['locked']) == TRUE)
	{
		$success++;
	} else {
		redirect_header('user.php', 1, _MD_FNMA_USER_ERROR_LOCKSET);
		exit();
	}
	
	if($fnmaAccount->setExpansion($_GET['id'], $_POST['expansion']) == TRUE)
	{
		$success++;
	} else {
		redirect_header('user.php', 1, _MD_FNMA_USER_ERROR_EXPASET);
		exit();
	}
	
	if($success == 3)
	{
		redirect_header('user.php?id=id='.$_GET['id'].'', 1, _MD_FNMA_USER_DETAILS_SUCCESS);
		return TRUE;
		exit();
	}
	break;
	
	case 'editUser':
	
	if($profile['account_level'] <= $_POST['account_level'] && $profile['account_level'] != '4')
	{
		redirect_header('user.php', 1, _MD_FNMA_USER_ERROR_NOACTPERM);
		exit();
	} else {
		$fnmaDB["sys"]->query("UPDATE ".$xoopsDB->prefix('fnma_account_extend')." SET 
								account_level='".$_POST['account_level']."',
								web_points='".$_POST['web_points']."',
								total_donations='".$_POST['total_donations']."'
								WHERE account_id='".$_GET['id']."'
								");
		redirect_header('user.php?id='.$_GET['id'].'', 1, _MD_FNMA_USER_UPDATE_SUCCESS);
		exit();						
	}
	exit();
	break;
	
	
	case 'delete':
	redirect_header('user.php', 4, _MD_FNMA_USER_FUNCTION_DEXISTS);
	exit();
	break;
		

	case 'unBan':
	if($fnmaAccount->unbanAccount($_GET['id']) == TRUE)
	{
		$fnmaAccount->unlock($_GET['id']);
		redirect_header('user.php?id='.$_GET['id'].'', 1, sprintf(_MD_FNMA_USER_UNBANN_SUCCESS, $id));
		exit();
	}	
	exit();
	break;
	


	case 'banUser':
	$banreason = $_POST['ban_reason'];
	$user = $_POST['ban_user'];
	$id = $_GET['id'];
	if(!$banreason){
		$banreason = "Not Specified";
	}
	$fnmaUser = $fnmaAccount->getProfile($_SESSION['fnmaUserId']);
	if($fnmaAccount->banAccount($id, $banreason, $fnmaUser['username']) == TRUE){
		$fnmaAccount->lock($id);
		redirect_header('user.php', 1, sprintf(_MD_FNMA_USER_BANN_SUCCESS, $user, $banreason));
		exit();
	}else{
		redirect_header('user.php', 1, _MD_FNMA_USER_BANN_FAILD);
		exit();
	}
	exit();
break;


case 'banForm':
/*
if(empty($_POST['id']))
{
		redirect_header('user.php',1, _MD_FNMA_USER_ERROR1);
		exit();
}*/

global $fnmaDB, $xoopsDB;

	$uname = $fnmaDB["logon"]->selectCell("SELECT username FROM account WHERE id='".$_GET['id']."'");

		echo '<div class="content">	
			<div class="content-header"><h4><a href="index.php">'._MD_FNMA_USER_MAINMENU.'</a> / <a href="user.php\">'._MD_FNMA_USER_USERMMGR.'</a> / '.$uname.' / Ban</h4></div>			
			<div class="main-content">
			<form method="POST" action="user.php?op=banUser&id='.$_GET['id'].'" name="adminform" class="form label-inline">
			<input type="hidden" name="ban_user"  value="'.$uname.'" />
			<table>
				<thead>
					<th><center><b>'._MD_FNMA_USER_BANN_ACCT.' #'.$_GET['id'].' ('.$uname.')</b></center></th>
				</thead>
			</table>
			<br />
			<div class="field">
				<label for="Username">'._MD_FNMA_USER_BAN_REASON.'</label>
				<input id="ban-reason" name="ban_reason" size="40" type="text" class="large" />
			</div>		
			<div class="buttonrow-border">								
				<center><button><span>'._MD_FNMA_USER_BTN_BAN.'</span></button></center>			
			</div>

		</form>
	</div>';

break;
	
case 'main':
default:

	echo '<div id="main">			
			<div class="content">	
				<div class="content-header"><h4><a href="index.php">'._MD_FNMA_USER_MAINMENU.'</a> / '._MD_FNMA_USER_USERMMGR.'</h4></div>		
			<div class="main-content">	
			<center><h2>'._MD_FNMA_USER_ULIST.'</h2></center>
			<table>
			<tr>
				<td colspan="4" align="center">
					<b>'._MD_FNMA_USER_SORTBYLETTER.'</b>&nbsp;&nbsp;
					<small>
					<a href="user.php">'._MD_FNMA_USER_ALL.'</a> | 
					<a href="user.php?sortby=1">#</a> 
					<a href="user.php?sortby=a">A</a> 
					<a href="user.php?sortby=b">B</a> 
					<a href="user.php?sortby=c">C</a> 
					<a href="user.php?sortby=d">D</a> 
					<a href="user.php?sortby=e">E</a> 
					<a href="user.php?sortby=f">F</a> 
					<a href="user.php?sortby=g">G</a> 
					<a href="user.php?sortby=h">H</a> 
					<a href="user.php?sortby=i">I</a> 
					<a href="user.php?sortby=j">J</a> 
					<a href="user.php?sortby=k">K</a> 
					<a href="user.php?sortby=l">L</a> 
					<a href="user.php?sortby=m">M</a> 
					<a href="user.php?sortby=n">N</a> 
					<a href="user.php?sortby=o">O</a> 
					<a href="user.php?sortby=p">P</a> 
					<a href="user.php?sortby=q">Q</a> 
					<a href="user.php?sortby=r">R</a> 
					<a href="user.php?sortby=s">S</a> 
					<a href="user.php?sortby=t">T</a> 
					<a href="user.php?sortby=u">U</a> 
					<a href="user.php?sortby=v">V</a> 
					<a href="user.php?sortby=w">W</a> 
					<a href="user.php?sortby=x">X</a> 
					<a href="user.php?sortby=y">Y</a> 
					<a href="user.php?sortby=z">Z</a>              
					</small>           
				</td>
			</tr>
			<tr>
				<td>
					<form method="POST" action="user.php" name="adminform" class="form label-inline">
					<input type="hidden" name="action" value="sort">
						<div class="field">
							<center>
								<b><font size="2">'._MD_FNMA_USER_NAMESEARCH.' </font></b><input name="sortby" size="20" type="text" class="medium">
								<button><span>'._MD_FNMA_BTN_SEARCH.'</span></button>
							</center>
						</div>
					</form>
				</td>
			</tr>
		</table>
		<form method="POST" action="user.php" name="adminform" class="form label-inline">
			<table width="95%">
				<thead>
					<tr>
						<th width="120"><b><center>'._MD_FNMA_USER_UNAME.'</center></b></th>
						<th width="140"><b><center>'._MD_FNMA_USER_EMAIL.'</center></b></th>
						<th width="120"><b><center>'._MD_FNMA_USER_REGDATE.'</center></b></th>
						<th width="40"><b><center>'._MD_FNMA_USER_BAN_ACTIVE.'</center></b></th>
					</tr>
				</thead>';

				foreach($getusers as $row) { 
				
				echo '<tr class="even">
					<td align="center" class="odd"><a href="user.php?op=edit&id='.$row['id'].'">'.$row['username'].'</a></td>
					<td align="center" class="odd">'.$row['email'].'</td>
					<td align="center" class="odd">'.$row['joindate'].'</td>
					<td align="center" class="odd">'.checkLock($row['locked']).'</td>
				</tr>';
}
			echo '</table>
			<br /><br />
			<div id="pg" align="center">';
				// If there is going to be more then 1 page, then show page nav at the bottom
				if($totalrows > $limit)
				{
					if(isset($_GET['sortby']))
					{
						admin_paginate($totalrows, $limit, $page, 'user.php?sortby='.$_GET['sortby']);
					}
					else
					{
						admin_paginate($totalrows, $limit, $page, 'user.php');
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