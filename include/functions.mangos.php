<?php
// dumy function so far
function getLevelFormUser()
{
	return NULL;
}
	
function showDonationsTotal()
{
	return NULL;
}

// Calculates the rank of the character based on the number of
// Kills. Param $honor_points = Total Kills
function calc_character_rank($honor_points)
{
    $rank = 0;
    if($honor_points <= 0)
	{
        $rank = 0;
    }
	else
	{
        if($honor_points < 100)
		{
			$rank = 1;
		}
        else
		{
			$rank = ceil($honor_points / 1000) + 1;
		}
    }
    if($rank > 14)
	{
		$rank = 14;
	}
    return $rank;
}

function getExpensionById($id)
{
	if($id == '0')
	{
		$expension = _MD_FNMA_CLASSIC;
	} elseif ($id == '1') 
	{
		$expension = _MD_FNMA_TBC;
	} elseif ($id == '2')
	{
		$expension = _MD_FNMA_WOTLK;
	} 
	return $expension;
}
	
function getAccountList($id)
	{
		global $fnmaDB;
		$list = $fnmaDB["logon"]->fetch_assoc($fnmaDB["logon"]->query("SELECT * FROM account WHERE id='".$id."'"));
		if($list == false)
		{
			return false;
		}else{
		return $list;
		}
	}	
function checkLock($row){
	if($row == 1){
		return _MD_FNMA_USER_ACT_LOCKED;
	} elseif ($row == 0){
		return _MD_FNMA_USER_ACT_UNLOCKED;
	}
}

function getRealmlist()
{
	global $fnmaDB;
		$realms = $fnmaDB["logon"]->select("SELECT * FROM realmlist ORDER BY id ASC");
	return $realms;
}


function getRealmById($id)
{
    global $fnmaDB;
    $search_q = $fnmaDB["logon"]->select("SELECT * FROM realmlist WHERE id='".$id."'");
    return $search_q;
}

function check_port_status($ip, $port, $timeout='1')
{
	if(!isset($timeout))
	{
		$timeout = 1;
	}
	$fp1 = @fsockopen($ip, $port, $ERROR_NO, $ERROR_STR, $timeout);
    if($fp1)
	{
        fclose($fp1);
		return TRUE;
    }
	else
	{
        return FALSE;
    }
}

function population_view($n) 
{
    
    $maxlow = 100;
    $maxmedium = 500;
    
	if($n <= $maxlow)
	{
        return '<font color="green">' . _MD_FNMA_POP_LOW . '</font>';
    } elseif($n > $maxlow && $n <= $maxmedium){
        	return '<font color="orange">' . _MD_FNMA_POP_MED . '</font>';
    	} else{
        return '<font color="red">' . _MD_FNMA_POP_HIGH . '</font>';
    	}
}
	
function print_time($time_array) 
{
	global $lang;
	$count = 0;
	if($time_array['d'] > 0) 
	{
		echo $time_array['d'];
		echo "Days";
		$count++;
	}
	if($time_array['h'] > 0) 
	{
        if ($count > 0) 
		{
			echo ',';
		}
		echo $time_array['h'];
		echo "h";
		$count++;
	}
	if($time_array['m'] > 0) 
	{
		if ($count > 0)
		{
			echo ',';
		}
		echo $time_array['m'];
		echo "m";
		$count++;
	}
	if($time_array['s'] > 0) 
	{
		if ($count > 0)
		{
			echo ',';
		}
		echo $time_array['s'];
		echo "s";
	}
}

function parse_time($number) 
{
	$time = array();
    $time['d'] = intval($number/3600/24);
	$time['h'] = intval(($number % (3600*24))/3600);
	$time['m'] = intval(($number % 3600)/60);
	$time['s'] = (($number % 3600) % 60);

	return $time;
}

function paginate($num_pages, $cur_page, $link_to)
{
	$pages = array();
    $link_to_all = false;
    if($cur_page == -1)
    {
        $cur_page = 1;
        $link_to_all = true;
    }
    if($num_pages <= 1)
	{
        $pages = array('1');
	}
    else
    {
        $tens = floor($num_pages/10);
        for ($i=1; $i <= $tens; $i++)
        {
            $tp = $i*10;
            $pages[$tp] = "<a href='$link_to&page=$tp'>$tp</a>";
        }
        if($cur_page > 3)
        {
            $pages[1] = "<a href='$link_to&p=1'>1</a>";
        }
        for($current = $cur_page - 2, $stop = $cur_page + 3; $current < $stop; $current++)
        {
            if($current < 1 || $current > $num_pages) 
			{
                continue;
            } 
			elseif($current != $cur_page || $link_to_all) 
			{
                $pages[$current] = "<a href='$link_to&page=$current'>$current</a>";
            } 
			else 
			{
                $pages[$current] = '['.$current.']';
            }
        }
        if($cur_page <= ($num_pages-3))
        {
            $pages[$num_pages] = "<a href='$link_to&page=$num_pages'>$num_pages</a>";
        }
    }
    $pages = array_unique($pages);
    ksort($pages);
    $pp = implode(' ', $pages);
    return $pp;
}


	// testing for open port is only for the block display
	function test_port($server,$port)
	{
  	$sock = @fsockopen($server, $port, $ERROR_NO, $ERROR_STR, (float)0.5);
  		if ( $sock )
  		{
   			@fclose($sock);
    		return true;
  		} else {
    		return false;
		}
	}


	function format_uptime($seconds)
      {
        $secs  = intval($seconds % 60);
        $mins  = intval($seconds / 60 % 60);
        $hours = intval($seconds / 3600 % 24);
        $days  = intval($seconds / 86400);
        if ( $days > 365 )
        {
          $days  = intval($seconds / 86400 % 365.24);
          $years = intval($seconds / 31556926);
        }

        $uptimeString = '';

        if ( $years )
        {
          // we have a server that has been up for over a year? O_o
          // actually, it's probably because the server didn't write a useful
          // value to the uptime table's starttime field.
          $uptimeString .= $years;
          $uptimeString .= ( ( $years == 1 ) ? ' year' : ' years' );
          if ( $days )
          {
            $uptimeString .= ( ( $years > 0 ) ? ', ' : '' ).$days;
            $uptimeString .= ( ( $days == 1 ) ? ' day' : ' days');
          }
        }
        else
        {
          if ( $days )
          {
            $uptimeString .= $days;
            $uptimeString .= ( ( $days == 1 ) ? ' day' : ' days' );
          }
        }
        if ( $hours )
        {
          $uptimeString .= ( ( $days > 0 ) ? ', ' : '' ).$hours;
          $uptimeString .= ( ( $hours == 1 ) ? ' hour' : ' hours' );
        }
        if ( $mins )
        {
          $uptimeString .= ( ( $days > 0 || $hours > 0 ) ? ', ' : '' ).$mins;
          $uptimeString .= ( ( $mins == 1) ? ' minute' : ' minutes' );
        }
        if ( $secs )
        {
          $uptimeString .= ( ( $days > 0 || $hours > 0 || $mins > 0 ) ? ', ' : '' ).$secs;
          $uptimeString .= ( ( $secs == 1 ) ? ' second' : ' seconds' );
        }
        return $uptimeString;
      }

	function format_uptime_short($seconds)
	{
    $secs  = intval($seconds % 60);
    $mins  = intval($seconds / 60 % 60);
    $hours = intval($seconds / 3600 % 24);
    $days  = intval($seconds / 86400);

    $uptimeString='';

    if ( $days )
    {
      $uptimeString .= $days;
      $uptimeString .= ( ( $days === 1 ) ? ' day' : ' days' );
    }
    if ( $hours )
    {
      $uptimeString .= ( ( $days > 0 ) ? ', ' : '' ).$hours;
      $uptimeString .= ( ( $hours === 1 ) ? ' hour' : ' hours');
    }
    if ( $mins )
    {
      $uptimeString .= ( ( ( $days > 0 ) || ( $hours > 0 ) ) ? ', ' : '' ).$mins;
      $uptimeString .= ( ( $mins === 1 ) ? ' minute' : ' minutes' );
    }
    if ( $secs )
    {
      $uptimeString .= ( ( ( $days > 0 ) || ( $hours > 0 ) || ( $mins > 0 ) ) ? ', ' : '' ).$secs;
      $uptimeString .= ( ( $secs === 1 ) ? ' second' : ' seconds' );
    }
    return $uptimeString;
  }
 

	function sha_password($user, $pass)
	{
		$user = strtoupper($user);
		$pass = strtoupper($pass);
		return SHA1($user.':'.$pass);
	}
	
	
function parse_gold($varnumber) 
{

	$gold = array();
	$gold['gold'] = intval($varnumber/10000);
	$gold['silver'] = intval(($varnumber % 10000)/100);
	$gold['copper'] = (($varnumber % 10000) % 100);

	return $gold;
}			

function get_print_gold($gold_array) 
{
	if($gold_array['gold'] > 0) 
	{
		echo $gold_array['gold'];
		echo "&nbsp;<img src=\"images/icons/money/gold.gif\" border=\"0\">&nbsp;";
	}
	if($gold_array['silver'] > 0) 
	{
		echo $gold_array['silver'];
		echo "&nbsp;<img src=\"images/icons/money/silver.gif\" border=\"0\">&nbsp;";
	}
	if($gold_array['copper'] > 0) 
	{
		echo $gold_array['copper'];
		echo "&nbsp;<img src=\"images/icons/money/copper.gif\" border=\"0\">&nbsp;";
	}
}

function print_gold($gvar) 
{
	if($gvar == '---') 
	{
		echo $gvar;
	}
	else 
	{
		get_print_gold(parse_gold($gvar));
	}
}

// Send Mail
function send_email($goingto, $toname, $sbj, $messg) 
{
	global $Config;
	define('DISPLAY_XPM4_ERRORS', true); // display XPM4 errors
	$core_em = $Config->get('site_email');
		
	// If email type "0" (SMTP)
	if($Config->get('email_type') == 0) 
	{ 
		require_once 'core/mail/SMTP.php'; // path to 'SMTP.php' file from XPM4 package

		$f = ''.$core_em.''; // from mail address
		$t = ''.$goingto.''; // to mail address

		// standard mail message RFC2822
		$m = 'From: '.$f."\r\n".
			'To: '.$t."\r\n".
			'Subject: '.$sbj."\r\n".
			'Content-Type: text/plain'."\r\n\r\n".
			''.$messg.'';

		$h = explode('@', $t); // get client hostname
		$c = SMTP::MXconnect($h[1]); // connect to SMTP server (direct) from MX hosts list
		$s = SMTP::Send($c, array($t), $m, $f); // send mail
		// print result
		if ($s) output_message('success', 'Mail Sent!');
		else output_message('error', print_r($_RESULT));
		SMTP::Disconnect($c); // disconnect
	}
	elseif($Config->get('email_type') == 1) 	// If email type "1" (MIME)
	{
		require_once 'core/mail/MIME.php'; // path to 'MIME.php' file from XPM4 package

		// compose message in MIME format
		$mess = MIME::compose($messg);
		// send mail
		$send = mail($goingto, $sbj, $mess['content'], 'From: '.$core_em.''."\n".$mess['header']);
		// print result
		echo $send ? output_message('success', 'Mail Sent!') : output_message('error', 'Error!');
	}
	elseif($Config->get('email_type') == 2)	// If email type "2" (MTA Relay)
	{
		require_once 'core/mail/MAIL.php'; // path to 'MAIL.php' file from XPM4 package

		$m = new MAIL; // initialize MAIL class
		$m->From($core_em); // set from address
		$m->AddTo($goingto); // add to address
		$m->Subject($sbj); // set subject 
		$m->Html($messg); // set html message

		// connect to MTA server 'smtp.hostname.net' port '25' with authentication: 'username'/'password'
		if($Config->get('email_use_secure') == 1) 
		{
			$c = $m->Connect($Config->get('email_smtp_host'), $Config->get('email_smtp_port'), $Config->get('email_smtp_user'), $Config->get('email_smtp_pass'), $Config->get('email_smtp_secure')) 
				or die(print_r($m->Result));
		}
		else
		{
			$c = $m->Connect($Config->get('email_smtp_host'), $Config->get('email_smtp_port'), $Config->get('email_smtp_user'), $Config->get('email_smtp_pass')) 
				or die(print_r($m->Result));
		}

		// send mail relay using the '$c' resource connection
		echo $m->Send($c) ? output_message('success', 'Mail Sent!') : output_message('error', 'Error! Please check your config and make sure you inserted your MTA info correctly.');

		$m->Disconnect(); // disconnect from server
		// print_r($m->History); // optional, for debugging
	}
}


function bitCompare($bit, $key)
{
    if(($bit & $key) == TRUE)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

function check_for_symbols($string, $space_check = 0)
{
    //$space_check=1 means space is not allowed
    $len=strlen($string);
    $allowed_chars="abcdefghijklmnopqrstuvwxyzæøåABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ0123456789";
    if(!$space_check) 
	{
        $allowed_chars .= " ";
    }
    for($i=0;$i<$len;$i++)
        if(strstr($allowed_chars,$string[$i]) == FALSE)
            return TRUE;
    return FALSE;
}


function strip_if_magic_quotes($value)
{
    if (get_magic_quotes_gpc()) 
	{
        $value = stripslashes($value);
    }
    return $value;
}

function random_string($counts)
{
    $str = "abcdefghijklmnopqrstuvwxyz"; //Count 0-25
    $o = 0;
    for($i=0; $i < $counts; $i++)
	{
        if($o == 1)
		{
            $output .= rand(0,9);
            $o = 0;
        }
		else
		{
            $o++;
            $output .= $str[rand(0,25)];
        }
    }
    return $output;
}

		
?>