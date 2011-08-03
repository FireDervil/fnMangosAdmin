<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

xoops_loadLanguage('misc', 'fnMangosAdmin');

$user = $fnmaAccount->getProfile($_SESSION['fnmaUserId']);

// If the user is not loggedin, redirect
if (empty($_SESSION['fnmaUserId'])) {
    redirect_header('user.php', 1, _MD_FNMA_NOREGISTER);

}

// If the vote system is disabled, redirect
if($fnmaConfig['active_vote_system'] == 0)
{
    redirect_header('member.php',1, _MD_FNMA_MDOULE_VOTE_NOTACT);
}

// Check to see what realm we are using
$realm_info_new = getRealmById($_SESSION['selected_realm']);
foreach( $realm_info_new as $realm_info){
$rid = $realm_info['id'];
}

// Here we get the sites and rewards from the database
$vote_sites = $fnmaDB["sys"]->select("SELECT * FROM ".$xoopsDB->prefix('fnma_vote_sites')."");


function initUser()
{
	global $vote_sites, $fnmaConfig, $fnmaModule, $fnmaDB, $xoopsDB, $fnmaUser;
	
	$return = array();

	// Start the loop. foreach voting site, we need to check if/when the user last voted
	// and if the vote timer is up, and the user can vote on that site again
	foreach($vote_sites as $site)
	{
		$id = $site['id'];
		$get_voting = $fnmaDB["sys"]->selectRow("SELECT * FROM ".$xoopsDB->prefix('fnma_voting')." WHERE user_ip LIKE '".$_SERVER["REMOTE_ADDR"]."' AND site='".$id."' LIMIT 1");
		if($get_voting != FALSE)
		{
			// Here we find the reset time for the vote site
			$reset_time = ($get_voting['time'] + $site['reset_time']);
			$cur_reset = ($reset_time - time()) / 3600;
			
			// If the reset time is less then 1, but higher then 0, then its less that an hour
			// and the time need to be formated in minutes
			if($cur_reset < 1 && $cur_reset > 0)
			{
				$reset = ($reset_time - time()) / 60;
				$reset = round($reset)." M";
			}
			
			// If the reset time is a negative number, then you are able to vote
			elseif($cur_reset < 0)
			{
				$reset = "N/A";
			}
			
			// If higher then 1, then its that number of hours. EX: 3 = 3 hours
			else
			{
				$reset = round($cur_reset)." H";
			}
			
			// If the current time, minus the vote time is greater then the 
			// reset time, then the timer is reset
			if((time() - $get_voting['time']) > $site['reset_time'])
			{
				$return[$id] = array(
					'time' => $get_voting['time'], 
					'voted' => 0,
					'reset' => 'N/A'
				);
			} else {
				$return[$id] = array(
					'time' => $get_voting['time'], 
					'voted' => 1,
					'reset' => $reset
				);
			}
		}
		else
		{
			$fnmaDB["sys"]->query("INSERT INTO ".$xoopsDB->prefix('fnma_voting')." (site, user_ip) VALUES ('".$id."','".$_SERVER["REMOTE_ADDR"]."')");
			$return[$id] = array(
				'time' => 0, 
				'voted' => 0,
				'reset' => 'N/A'
			);
		}
	}
	return $return;
}

$Voting = initUser();
foreach($vote_sites as $new_votes){
$key = $new_votes['id'];
$Voted = $Voting[$key]['voted'];
$reset = $Voting[$key]['reset'];
}


$op = 'main';

if (isset($_POST['op'])) {
    $op = trim($_POST['op']);
} else if (isset($_GET['op'])) {
    $op = trim($_GET['op']);
}

switch($op)
{
	
	case'vote':
	
	global $fnmaConfig, $fnmaDB, $xoopsDB, $fnmaUser;
	$site = intval($_POST['site']);
	$tab_sites = $fnmaDB["sys"]->selectRow("SELECT * FROM ".$xoopsDB->prefix('fnma_vote_sites')." WHERE id=".$site."");
	
	// First we check to see the users hasnt clicked vote twice
	$get_voting = $fnmaDB["sys"]->selectRow("SELECT * FROM ".$xoopsDB->prefix('fnma_voting')." WHERE user_ip LIKE '".$_SERVER["REMOTE_ADDR"]."' AND site='".$site."' LIMIT 1");
	
	if((time() - $get_voting['time']) < $tab_sites['reset_time'])
	{
		redirect_header('voting.php', 3, _MD_FNMA_VOTE_ERROR1);
		exit(); 
	}else{
		if($tab_sites != FALSE)
		{
			if($fnmaConfig['check_vote_isonline'] == 1)
			{
				$fp = @fsockopen($tab_sites['hostname'], 80, $errno, $errstr, 3);
			} else {
				$fp = TRUE;
			}
			
			if($fp)
			{
				if($fnmaConfig['check_vote_isonline'] == 1)
				{
					fclose($fp);
				}
				
				$fnmaDB["sys"]->query("UPDATE ".$xoopsDB->prefix('fnma_voting')." SET time='".time()."' WHERE user_ip LIKE '".$_SERVER["REMOTE_ADDR"]."' AND site='".$site."' LIMIT 1");
				
				$fnmaDB["sys"]->query("UPDATE ".$xoopsDB->prefix('fnma_account_extend')." SET 
					web_points=(web_points + ".$tab_sites['points']."), 
					date_points=(date_points + ".$tab_sites['points']."),
					total_votes=(total_votes + 1), 
					points_earned=(points_earned + ".$tab_sites['points'].")  
				   WHERE account_id = ".$_SESSION['fnmaUserId']." LIMIT 1");

				redirect_header('voting.php', 1);
				redirect_header($tab_sites['votelink'], 0, _MD_FNMA_VOTES_REDIRECT);
				exit();
			} else {
				redirect_header('voting.php', 1, _MD_FNMA_VOTES_ERROR2);
				exit();
			}
		} else {
			redirect_header('voting.php', 1, _MD_FNMA_VOTES_ERROR3);
			exit();
		}
	}
	exit();	
	break;
	
	case 'main':
	default:
	
	$xoopsOption['template_main'] = 'fnma_misc_voting.html';
	include XOOPS_ROOT_PATH."/header.php";
	$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
	$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );
	
	if($Voted == '1')
	{
		$disabled = 'disabled="disabled"';
	} else {
		$disabled = '';
	}
	if($vote_sites == ''){
		$vote_sites = 'DISABLED';
	}
	
	$xoopsTpl->assign('voted', $Voted);
	$xoopsTpl->assign('vote_sites', $vote_sites);
	$xoopsTpl->assign('user', $user);
	$xoopsTpl->assign('reset', $reset);
	$xoopsTpl->assign('disabled', $disabled);
	
	$xoopsTpl->assign(array(
		"lang_vote_acct_details" => _MD_FNMA_VOTE_ACCT_DETAIL,
		"lang_vote_curacct" => _MD_FNMA_VOTE_CURRACCT,
		"lang_vote_count" => _MD_FNMA_VOTE_COUNT,
		"lang_vote_points" => _MD_FNMA_VOTE_PPOINTS,
		"lang_vote_balance" => _MD_FNMA_VOTE_BALANCE,
		"lang_vote_acct_points_today" => _MD_FNMA_VOTE_ACCT_PTODAY,
		"lang_vote_keep" => _MD_FNMA_VOTE_KEEP,
		"lang_vote_hack" => _MD_FNMA_VOTE_HACK,
		"lang_vote_info" => _MD_FNMA_VOTE_INFO,
		"lang_choose_vote" => _MD_FNMA_VOTE_CHOOSE,
		"lang_voting_sites" => _MD_FNMA_VOTE_SITES,
		"lang_voted" => _MD_FNMA_VOTED,
		"lang_resets" => _MD_FNMA_VOTE_RESETS,
		"lang_points" => _MD_FNMA_VOTE_PINCOME,
		"lang_choose" => _MD_FNMA_VOTE_CHOOSE,
		"lang_yes" => _MD_FNMA_VOTE_YES,
		"lang_no" => _MD_FNMA_VOTE_NO,
		"lang_vote" => _MD_FNMA_VOTE,
		"lang_allry_voted" => _MD_FNMA_VOTE_ALLRDY_VOTED,
		//"" => ,
		//"" => ,
			
	));
	break;

}

include XOOPS_ROOT_PATH."/footer.php";
?>