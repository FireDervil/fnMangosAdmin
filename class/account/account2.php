<?php
// Account class for MangosWebSDL written by Steven Wilson, aka Wilson212
// Some functions used from the original MangosWeb AUTH Class

class fnmaAccount
{
	var $fnmaDB;
	var $xoopsDB;
    var $User = array(
		'id'    => '',
		'username'  => '',
		'account_level' => 1,
    );

//	************************************************************	
// Initialize with checking for user cookies, and getting their IP

    function __construct()
    {
        global $xoopsModuleConfig, $fnmaDB, $xoopsDB;
        $this->db = $fnmaDB;
		$this->dbx = $xoopsDB;
        $this->check();
        $this->User['ip'] = $_SERVER['REMOTE_ADDR'];
	}

//	************************************************************	
// Checks if user is logged in already by reading the cookie

    function check()
    {
        global $xoopsModuleConfig;
		
		// Check if a cookie is set
        if(isset($_COOKIE[((string)$xoopsModuleConfig['site_cookie'])]))
		{
            list($cookie['user_id'], $cookie['account_key']) = @unserialize(stripslashes($_COOKIE[((string)$xoopsModuleConfig['site_cookie'])]));
            if($cookie['user_id'] < 1)
			{
				return false;
			}
			
			// Get the user info from the DB
            $res = $this->db["logon"]->selectRow("
                SELECT * FROM account WHERE id ='".$cookie['user_id']."'");
			
			// Check to see if account is banned
            if($this->isBannedAccount($res['id']) == true)
			{
                $this->setgroup();
                $this->logout();
                return false;
            }
			
			
			// Match the cookie account key with the DB account key
            if($this->matchAccountKey($cookie['user_id'], $cookie['account_key']))
			{
                unset($res['sha_pass_hash']);
                $this->User = $res;
                return true;
            }
			else
			{
				// If ther return is false on the account key matching, then
				// we must logout to delete the key, and set group to guest
				$this->logout($cookie['user_id']);
                $this->setgroup();
                return false;
            }
        }
		else # Cookie is not set
		{
            $this->setgroup();
            return false;
        }
    }

/*	************************************************************
* Main login script. 
* @$params = array('username' => username, 'sha_pass_hash' => encrypted password
* returns 0 if the username doesnt exist
* returns 1 on success
* returns 2 if some params are empty
* returns 3 if the password is wrong
* returns 4 if the account is banned
* returns 5 if the account is not activated
// **************************************************************/

    function login($params)
    {
        global $xoopsModuleConfig;
        $success = 1;
		
		// If the params are emtpy, return 2
        if(empty($params)) 
		{
			return 2;
		}
		
		// if the username is empty, return 2
        if(empty($params['username']))
		{
            return 2;
            $success = 0;
        }
		
		// If the sha_pass_hash is empty, return 2
        if(empty($params['sha_pass_hash']))
		{
            return 2;
            $success = 0;
        }
		
		// Load the users info from the DB
        $res = $this->db["logon"]->selectRow("SELECT * FROM `account` WHERE `username`='".$params['username']."'");
			
		// If the result was false, then username is no good, return 0.
        if($res == false)
		{
			$success = 0;
			return 0;
		}
		else
		{
			$res2 = $this->db["sys"]->selectRow("SELECT * FROM ".$this->dbx->prefix('fnma_account_extend')." WHERE `account_id`='".$res['id']."'");
		}
		
		// Check to see if the account is banned, if so return 4
        if($this->isBannedAccount($res['id']) == true)
		{
            $success = 0;
			return 4;
        }
		
		// If the activation code is not NULL, the account is not activated, return 5
        if($res2['activation_code'] != NULL)
		{
            $success = 0;
			return 5;
        }
		
		// If any of the above checks returnes $success = 0; then login fails
        if($success != 1) 
		{
			return false;
		}
		else
		{
			// Lets check to see if the posted password matches the DB password
			if(strtoupper($res['sha_pass_hash']) == strtoupper($params['sha_pass_hash']))
			{
				$this->User['id'] = $res['id'];
				$this->User['name'] = $res['username'];
				
				// generate an account key, and set the account key in the DB for login cookie checks
				$generated_key = $this->generate_key();
				//$this->addOrUpdateAccountKeys($res['id'],$generated_key);
				$uservars_hash = serialize(array($res['id'], $generated_key));
				
				// Prepare for cookie setting
				$cookie_expire_time = intval($xoopsModuleConfig['account_key_retain_length']);
				if(!$cookie_expire_time) 
				{
					$cookie_expire_time = (60*60*24*365);   //default is 1 year
				}
				(string)$cookie_name = $xoopsModuleConfig['site_cookie'];
				(int)$cookie_delay = (time() + $cookie_expire_time);
				
				// Set cookie and return 1
				setcookie($cookie_name, $uservars_hash, $cookie_delay);
				return 1;
			}
			else # Passwords didnt match in the DB, return 3
			{
				return 3;
			}
		}
    }

//	************************************************************
// Main logout function, Sets an expired cookie over the current
// cookie and removes the DB account key

    function logout()
    {
        global $xoopsModuleConfig;
        setcookie((string)$xoopsModuleConfig['site_cookie'], '', time()-3600,(string)XOOPS_COOKIE_DOMAIN);
        $this->removeAccountKeyForUser($this->User['id']);
    }
	
/*	************************************************************
* Main register script
* @$params = array('username' => 'username', 'sha_pass_hash' => 'encrypted_pass', 'sha_pass_hash2' => 'encrypted_pass2', 
*		'email' => 'email', 'expansion' => 'expansion', 'password' => 'clean password');
* @$account_extend = array('secretq1' => '', 'secreta1' => '', 'secretq2' => '', 'secreta2 => '');
* returns 0 if the params are emtpy
* returns 1 on success
* returns 2 if the username is empty
* returns 3 if the passwords didnt match or are empty
* returns 4 if the email is empty
* returns 5 if the IP is banned
* returns 6 upon fatal error
// **************************************************************/

    function register($params, $account_extend = NULL)
    {
        global $xoopsModuleConfig;
        $success = 1;
		
		// Check to see if the params is empty, if so return 0
        if(empty($params)) 
		{
			return 0;
		}
		
		// If the param username is empty
        if(empty($params['username']))
		{
			return 2;
            $success = 0;
        }
		
		// If the password hash is emtpy, OR the 2 posted passwords dont match
        if(empty($params['sha_pass_hash']) || $params['sha_pass_hash'] != $params['sha_pass_hash2'])
		{
			return 3;
            $success = 0;
        }
		
		// Is email is empty
        if(empty($params['email']))
		{
			return 4;
            $success = 0;
        }
		
		// check to see if the users IP is banned
		if($this->isBannedIp($_SERVER['REMOTE_ADDR']) == true)
		{
			return 5;
            $success = 0;
        }
		
		// If any of the above checks are flase, then reigster failed
        if($success != 1) 
		{
			return true;
		}
        unset($params['sha_pass_hash2']);
        $password = $params['password'];
        unset($params['password']);
		
		// If email activation is set in the config
        if((int)$xoopsModuleConfig['require_act_activation'] == 1)
		{
			// Setup an activation key, Set locked to 1 so the user cant login, insert into DB
            $tmp_act_key = $this->generate_key();
            $params['locked'] = 1;
			$acc_id = $this->db["logon"]->query("INSERT INTO account(
				`username`,
				`sha_pass_hash`,
				`email`,
				`locked`,
				`expansion`)
			   VALUES(
				'".$params['username']."',
				'".$params['sha_pass_hash']."',
				'".$params['email']."',
				'".$params['locked']."',
				'".$params['expansion']."')
			   ");
			   
			// If the insert into account query was successful
            if($acc_id == true)
			{
				$u_id = $this->db["logon"]->selectCell("SELECT `id` FROM `account` WHERE `username` LIKE '".$params['username']."'");
				
                // If we dont want to insert special stuff in account_extend...
                if ($account_extend == NULL)
				{
                    $this->db["sys"]->query("INSERT INTO ".$this->dbx->prefix('fnma_account_extend')."(
						`account_id`,
						`account_level`,
						`registration_ip`,
						`activation_code`)
					   VALUES(
						'".$u_id."',
						'2',
						'".$_SERVER['REMOTE_ADDR']."',
						'".$tmp_act_key."')
					");
                } 
                else # We do want to insert into account extend
				{
                    $this->db["sys"]->query("INSERT INTO ".$this->dbx->prefix('fnma_account_extend')."(
						`account_id`,
						`account_level`,
						`registration_ip`, 
						`activation_code`, 
						`secret_q1`, 
						`secret_a1`, 
						`secret_q2`, 
						`secret_a2`)
					   VALUES(
						'".$u_id."',
						'2',
						'".$_SERVER['REMOTE_ADDR']."',
						'".$tmp_act_key."',
						'".$account_extend['secretq1']."', 
						'".$account_extend['secreta1']."', 
						'".$account_extend['secretq2']."', 
						'".$account_extend['secreta2']."')
					");
                }
				
				// Send the activation email
                $act_link = (string)$Config->get('site_base_href').'?p=account&sub=activate&id='.$u_id.'&key='.$tmp_act_key;
                $email_text  = '== Account activation =='."\n\n";
                $email_text .= 'Username: '.$params['username']."\n";
                $email_text .= 'Password: '.$password."\n";
                $email_text .= 'This is your activation key: '.$tmp_act_key."\n";
                $email_text .= 'CLICK HERE : '.$act_link."\n";
                send_email($params['email'],$params['username'],'== '.(string)$Config->get('site_title').' account activation ==',$email_text);
                return 1;
            }
			
			// Insert into account table failed
			else
			{
                return 6;
            }
        }
		
		// Email activation disabled
		else
		{
			$acc_id = $this->db["logon"]->query("INSERT INTO account(
				`username`,
				`sha_pass_hash`,
				`email`,
				`expansion`)
			   VALUES(
				'".$params['username']."',
				'".$params['sha_pass_hash']."',
				'".$params['email']."',
				'".$params['expansion']."')
			");
			
			// If insert into account table was successfull
            if($acc_id == true)
			{
				$u_id = $this->db["logon"]->selectCell("SELECT `id` FROM `account` WHERE `username` LIKE '".$params['username']."'");
                if ($account_extend == NULL)
				{
                    $this->db["sys"]->query("INSERT INTO ".$this->dbx->prefix('fnma_account_extend')."(
						`account_id`,
						`account_level`,
						`registration_ip`)
					   VALUES(
						'".$u_id."',
						'2',
						'".$_SERVER['REMOTE_ADDR']."'
					   )
					");
                }
				else
				{
                    $this->db["sys"]->query("INSERT INTO ".$this->dbx->prefix('fnma_account_extend')."(
						`account_id`,
						`account_level`,
						`registration_ip`, 
						`secret_q1`, 
						`secret_a1`, 
						`secret_q2`, 
						`secret_a2`)
					   VALUES(
						'".$u_id."',
						'2',
						'".$_SERVER['REMOTE_ADDR']."',
						'".$account_extend['secretq1']."', 
						'".$account_extend['secreta1']."', 
						'".$account_extend['secretq2']."', 
						'".$account_extend['secreta2']."')
					");
                }
                return 1;
            }
            else
			{
                return 6;
            }
        }
    }
	
/*************************************************************
* Last update set the current time under the account_extend database to get
* an approximate time when the user was last online.
* @userparams all $user params ($user)
**************************************************************/


	function lastvisit_update($uservars)
    {
        if($uservars['id'] > 0)
		{
            if(time() - $uservars['last_visit'] > 60*10)
			{
                $this->db["sys"]->query("UPDATE ".$this->dbx->prefix('fnma_account_extend')."SET last_visit='".time()."' WHERE account_id='".$uservars['id']."' LIMIT 1");
            }
        }
    }

//	************************************************************
// Get the account level information for a group level
// @$group_id = the account level
	
	function getgroup($group_id = false)
	{
        $res = $this->db["sys"]->selectRow("SELECT * FROM ".$this->dbx->prefix('fnma_account_groups')." WHERE `account_level`='".$group_id."'");
        return $res;
    }

//	************************************************************
// Sets the group of the user
// @$gid = the account level
	
	function setgroup($gid = 1) // 1 = guest
    {
        $guest_g = $this->getgroup($gid);
        $this->User = array_merge($this->User, $guest_g);
    }

//	************************************************************	
// Converts the username:password into a SHA1 encryption

	function sha_password($user, $pass)
	{
		$user = strtoupper($user);
		$pass = strtoupper($pass);
		return sha1($user.':'.$pass);
	}

	
//	************************************************************	
// Checks if the user is logged in. Returns false if user is guest

	function isLoggedIn()
	{
		if($this->User['id'] > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

//	************************************************************	
// Check if the username is available. Post the username here.
// returns returns true if the name is available.

    function isAvailableUsername($username)
	{
        $res = $this->db["logon"]->count("SELECT COUNT(*) FROM `account` WHERE `username`='".$username."'");
        if($res == 0) 
		{
			return true; // username is available
		}
		else
		{
			return false; // username is not available
		}
    }

//	************************************************************
// Check if the email is available. Post an email address here.
// returns returns true if the email is available.

    function isAvailableEmail($email)
	{
        $res = $this->db["logon"]->mysql_count("SELECT COUNT(*) FROM `account` WHERE `email`='".$email."'");
        if($res == 0) 
		{
			return true; // email is available
		}
		else
		{
			return false; // email is not available
		}
    }

//	************************************************************
// Check if the email is available. Post an email address here.
// returns returns true if the email is available.

    public function isLostPassEmail($email)
	{
        $res = $this->db["logon"]->mysql_count("SELECT COUNT(*) FROM account WHERE email='".$email."'");
        if($res == 1) 
		{
			return true; // email is available for lostpass.php
		}
		else
		{
			return false; // email is not available for lostpass.php
		}
    }

	public function returnEmailUserData($email)
	{
		
		$res = $this->db["logon"]->selectRow("SELECT * FROM account	WHERE email='".$email."'");
        return $res;
		
	}

//	************************************************************	
// Checks if the email is in valid format.
// returns returns true if the email is a valid email

    function isValidEmail($email)
	{
        if(preg_match('#^.{1,}@.{2,}\..{2,}$#', $email) == 1)
		{
            return true; // email is valid
        }
		else
		{
            return false; // email is not valid
        }
    }

//	************************************************************	
// Checks if the register key is valid
// @$key is the Register key

    function isValidRegkey($key)
	{
        $res = $this->db["logon"]->selectCell("SELECT `id` FROM `mw_regkeys` WHERE `key`='".$key."'");
        if($res != false) 
		{
			return true; // key is valid
		}
        else
		{
			return false; // key is not valid
		}
    }

//	************************************************************
// Checks is the account activation key is valid
// @$key is the activiation key

    function isValidActivationKey($key)
	{
        $res = $this->db["sys"]->selectCell("SELECT `account_id` FROM ".$this->dbx->prefix('fnma_account_extend')." WHERE `activation_code`='".$key."'");
        if($res != true) 
		{
			return $res; // key is valid
		}
		else
		{
			return false; // key is not valid
		}
    }

//	************************************************************
// Checks to see if the account is banned
// Returns true is the account id is banned
// @$account_id is the account id

	function isBannedAccount($account_id)
	{
		global $fnmaDB;
		$check = $fnmaDB["logon"]->mysql_count("SELECT COUNT(*) FROM account_banned WHERE id='".$account_id."' AND `active`=1");
		if ($check > 0)
		{
			return true; // Account is banned
		}
		else
		{
			return false; // Account is not banned
		}
	}

//	************************************************************
// Checks to see of an IP address is banned
// Returns true if the IP is banned
	
	function isBannedIp()
	{
		global $fnmaDB;
		$check = $fnmaDB["logon"]->mysql_count("SELECT COUNT(*) FROM ip_banned WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
		if ($check > 0)
		{
			return true; // IP is banned
		}
		else
		{
			return false; // IP is not banned
		}
	}
	
//	************************************************************
// For mangos and trinity. Used to determine if the account is locked or not
// Returns true of the account is locked, else false

	function isLockedAccount($id)
	{
		global $fnmaDB;
		$check = $fnmaDB["logon"]->selectCell("SELECT `locked` FROM `account` WHERE `id`='".$id."'");
		if($check == 1)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

//	************************************************************	
// Generate a unique key

    function generate_key()
    {
        $str = microtime(1);
        return sha1(base64_encode(pack("H*", md5(utf8_encode($str)))));
    }
	
//	************************************************************
// Generate multiple keys. Post amount of keys needed

    function generate_keys($n)
    {
        set_time_limit(600);
        for($i=1;$i<=$n;$i++)
        {
            if($i > 1000)
			{
				exit;
			}
            $keys[] = $this->generate_key();
            $slt = 15000;
            usleep($slt);
        }
        return $keys;
    }

//	************************************************************	
// Deletes a register key

    function delete_key($key)
	{
        $this->db["logon"]->query("DELETE FROM `mw_regkeys` WHERE `key`='".$key."'");
		return true;
    }

//	************************************************************
// This function bans an account, 
// POST account id, reason, and banned by.
// @$banip: 1 = yes, ban the IP as well, 0 = Dont ban IP
	
	function banAccount($bannid, $banreason, $bannedby, $banip = 0)
	{
		$timez = time();
		$unban = $timez - 10;
		$this->db["logon"]->query("INSERT INTO `account_banned`(
			`id`, 
			`bandate`, 
			`unbandate`, 
			`bannedby`, 
			`banreason`, 
			`active`) 
		   VALUES(
			'".$bannid."', 
			'".$timez."', 
			'". $unban ."',
			'".$bannedby."',
			'".$banreason."',
			'1')
		");
		
		// If banip is set to 1, then we need to ban the IP
		if($banip == 1)
		{
			$getip = $this->db["logon"]->selectCell("SELECT `last_ip` FROM `account` WHERE `id`='".$bannid."'");
			$this->db["logon"]->query("INSERT INTO `ip_banned`(
				`ip`, 
				`bandate`, 
				`unbandate`, 
				`bannedby`, 
				`banreason`) 
			   VALUES(
				'". $getip ."', 
				'". $timez ."', 
				'". $unban ."',
				'". $bannedby ."', 
				'". $banreason. "')
			");
		}
		
		$this->db["sys"]->query("UPDATE ".$this->dbx->prefix('fnma_account_extend')." SET `account_level`=5 WHERE account_id='".$bannid."'");
		return true;
	}
	
//	************************************************************
// Un Bans an account. Just need to post the account ID

	function unbanAccount($id)
	{
		$this->db["logon"]->query("UPDATE account_banned SET active='0' WHERE `id`='".$id."'");
		$ipd = $this->db["logon"]->selectCell("SELECT `last_ip` FROM `account` WHERE `id`='".$id."'");
		$this->db["logon"]->query("DELETE FROM ip_banned WHERE ip='".$ipd."'");
        $this->db["logon"]->query("UPDATE ".$this->dbx->prefix('fnma_account_extend')." SET `account_level`='2' WHERE `account_id`='".$id."'");
		return true;
	}

//	************************************************************	
// Gets all the users info from the database including username, email
// account level, id, and all sorts. post an account id here

	function getProfile($acct_id=false)
	{
		global $xoopsModuleConfig;
		$res = $this->db["sys"]->selectRow("
			SELECT * FROM ".$xoopsModuleConfig["realm_db_name"].".account
			LEFT JOIN ".$this->dbx->prefix('fnma_account_extend')." ON account.id = ".$this->dbx->prefix('fnma_account_extend').".account_id
			LEFT JOIN ".$this->dbx->prefix('fnma_account_groups')." ON ".$this->dbx->prefix('fnma_account_extend').".account_level = ".$this->dbx->prefix('fnma_account_groups').".account_level
			WHERE id='".$acct_id."'");
        return $res;
    }
	

	
//	************************************************************
// Returns an account username. Post an account ID here.

    function getLogin($acct_id=false)
	{
        $res = $this->db["logon"]->selectCell("SELECT `username` FROM `account` WHERE `id`='".$acct_id."'");
        if($res == false)
		{
			return false;  // no such account
		}
		else
		{
			return $res;
		}
    }

//	************************************************************	
// Gets an account id. Post username here
    function getAccountId($acct_name=false)
	{
        $res = $this->db["logon"]->selectCell("SELECT id FROM account WHERE username='".$acct_name."'");
        if($res == false)
		{
			return false;  // no such account
		}
		else
		{
			return $res;
		}
    }

//	************************************************************	
// Loads characters list for a specific account

	function getCharacterList($id)
	{
		global $fnmaDB;
		$list = $fnmaDB["char"]->select("SELECT * FROM `characters` WHERE `account`='".$id."'");
		if($list == false)
		{
			return false;
		}
		return $list;
	}



//	************************************************************
// Loads secret questions from the Database and returns them in an array.

	function getSecretQuestions()
	{
		$getsc = $this->db["logon"]->select("SELECT * FROM `mw_secret_questions`");
		return $getsc;
	}
	
//	************************************************************
// For mangos and trinity. Set locked to the $lock value

	function setLock($id, $lock)
	{
		$this->db["logon"]->query("UPDATE `account` SET `locked`='".$lock."' WHERE `id`='".$id."'");
		return true;
	}
	
//	************************************************************
// Sets an accounts email. Post an account id and new email address.

	function setEmail($id, $newemail)
	{
		$id = mysql_real_escape_string($id);
        $newemail = mysql_real_escape_string($newemail);
		$this->db["logon"]->query("UPDATE `account` SET `email`='".$newemail."' WHERE `id`='$id' LIMIT 1");
		return true;
	}

//	************************************************************	
// Sets the expansion for an account. Post an account id and Expansion number here.
// 2 = WotLK, 1 = TBC, 0 = Base

	function setExpansion($id, $nexp)
    {
        $id = mysql_real_escape_string($id);
        $nexp = mysql_real_escape_string($nexp);
        $this->db["logon"]->query("UPDATE `account` SET `expansion`='$nexp' WHERE `id`=$id");
        return true;
    }

//	************************************************************	
// Sets a password for an account. Post an account id and New password here.

	function setPassword($id, $newpass)
    {
        $id = mysql_real_escape_string($id);
        $newpass = mysql_real_escape_string($newpass);
        $username = $this->db["logon"]->selectCell("SELECT `username` FROM `account` WHERE `id`='$id' LIMIT 1");
		if($username != false)
		{
			$pass_hash = $this->sha_password($username, $newpass);
			$this->db["logon"]->query("UPDATE `account` SET `sha_pass_hash`='$pass_hash' WHERE `id`='$id' LIMIT 1");
			return true;
		}
		else
		{
			return false;
		}
    }

//	************************************************************	
// Sets the secret questions and answers for an account.
// Post in order, account id, question 1 ,  answer 1, question 2, answer 2.

	function setSecretQuestions($id, $sq1, $sa1, $sq2, $sa2)
	{
		$sq1 = strip_if_magic_quotes($sq1);
		$sa1 = strip_if_magic_quotes($sa1);
		$sq2 = strip_if_magic_quotes($sq2);
		$sa2 = strip_if_magic_quotes($sa2);
		
		// Check for symbols
		if(check_for_symbols($sa1) == false && check_for_symbols($sa2) == false && $sq1 != '0' && $sq2!= '0')
		{
			if(strlen($sa1) >= 4 && strlen($sa2) >= 4)
			{
				if($sa1 != $sa2 && $sq1 != $sq2)
				{
					$this->DB->query("UPDATE `mw_account_extend` SET `secret_q1`='$sq1', `secret_q2`='$sq2', `secret_a1`='$sa1', `secret_a2`='$sa2' WHERE `account_id`='$id'");
					return 1; // 1 = Set
				}
				else
				{
					return 2; // 2 = Answers or questions where the same
				}
			}
			else
			{
				return 3; // Answers where less then 4 characters long
			}
		}
		else
		{
			return 4; // Answers contained symbols
		}
	}

//	************************************************************
// Resets users secret questions
	
	function resetSecretQuestions($id)
	{
		$this->DB->query("UPDATE mw_account_extend SET secret_q1=NULL, secret_q2=NULL, secret_a1=NULL, secret_a2=NULL WHERE account_id='".$id."'");
		return true;
	}
	
	
// === ONLINE FUNCTIONS === //

//	************************************************************
// Updates the online list based off of whos been online in the last 5 minutes
// Deletes the old

    function onlinelist_update()
    {
        $GLOBALS['guests_online'] = 0;
        $rows  = $this->DB->select("SELECT * FROM `mw_online`");
        foreach($rows as $result_row)
        {
            if(time()-$result_row['logged'] <= 60*5)
            {
                if($result_row['user_id'] > 0)
				{
					$GLOBALS['users_online'][] = $result_row['user_name'];
                }
				else
				{
					$GLOBALS['guests_online']++;
                }
            }
            else
            {
                $this->DB->query("DELETE FROM `mw_online` WHERE `id`='".$result_row['id']."' LIMIT 1");
            }
        }
    }

//	************************************************************
// Adds the user to the update list

    function onlinelist_add()
    {
        global $user;

        $result = $this->DB->count("SELECT COUNT(*) FROM `mw_online` WHERE `user_id`='".$this->User['id']."'");
        if($result > 0)
        {
            $this->DB->query("UPDATE `mw_online` SET 
				`user_ip`='".$this->User['ip']."',
				`logged`='".time()."',
				`currenturl`='".$_SERVER['REQUEST_URI']."' 
			  WHERE `user_id`='".$this->User['id']."' LIMIT 1
			");
        }
        else
        {
            $this->DB->query("INSERT INTO `mw_online`(
				`user_id`,
				`user_name`,
				`user_ip`,
				`logged`,
				`currenturl`) 
			  VALUES(
				'".$this->User['id']."',
				'".$this->User['username']."',
				'".$this->User['ip']."',
				'".time()."',
				'".$_SERVER['REQUEST_URI']."')
			");
        }
    }

//	************************************************************
// Adds a guest to the online list

    function onlinelist_addguest()
    {
        global $user;

        $result = $this->DB->count("SELECT  COUNT(*) FROM `mw_online` WHERE `user_id`='0' AND `user_ip`='".$this->User['ip']."'");
        if($result > 0)
        {
            $this->DB->query("UPDATE `mw_online` SET 
				`user_ip`='".$this->User['ip']."',
				`logged`='".time()."',
				`currenturl`='".$_SERVER['REQUEST_URI']."' 
			  WHERE `user_id`='0' AND `user_ip`='".$this->User['ip']."' LIMIT 1");
        }
        else
        {
            $this->DB->query("INSERT INTO `mw_online`(
				`user_ip`,
				`logged`,
				`currenturl`) 
			  VALUES(
				'".$this->User['ip']."',
				'".time()."',
				'".$_SERVER['REQUEST_URI']."')
			");
        }
    }

	
// === ACCOUNT KEY FUNCTIONS === //

//	************************************************************
// Checks to see if the posted account key matches the DB account key

	function matchAccountKey($id, $key) 
	{
		$this->clearOldAccountKeys();

		$count = $this->db["sys"]->selectRow("SELECT * FROM ".$this->dbx->prefix('fnma_account_keys')." WHERE id='$id'");
		if($count == false) 
		{
			return false;
		}
		else
		{
			$account_key = $this->db["sys"]->selectRow("SELECT * FROM ".$this->dbx->prefix('fnma_account_keys')." WHERE id='$id'");
			if($key == $account_key['key']) 
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
	}

//	************************************************************
// Deletes all the old account keys that are expired

	function clearOldAccountKeys() 
	{
		global $xoopsModuleConfig;

		$cookie_expire_time = $xoopsModuleConfig['account_key_retain_length'];
		if(!$cookie_expire_time) 
		{
			$cookie_expire_time = (60*60*24*365);   //default is 1 year
		}

		$expire_time = time() - $cookie_expire_time;

		$this->db["sys"]->query("DELETE FROM ".$this->dbx->prefix('fnma_account_keys')." WHERE assign_time < ".$expire_time."");
	}

//	************************************************************
// Adds or updates the account keys in the DB for a user

	function addOrUpdateAccountKeys($id, $key) 
	{
		global $fnmaDB, $xoopsDB;

		$current_time = time();
		$go = $fnmaDB["sys"]->selectRow("SELECT * FROM ".$xoopsDB->prefix('fnma_account_keys')." WHERE id = '".$id."'");
		if($go == false) //need to INSERT
		{
			$this->db["sys"]->query("INSERT INTO ".$this->dbx->prefix('fnma_account_keys')." (`id`, `key`, `assign_time`) VALUES ('$id', '$key', '$current_time')");
		}
		else //need to UPDATE
		{              
			$this->db["sys"]->query("UPDATE ".$this->dbx->prefix('fnma_account_keys')." SET `key`='$key', `assign_time`='$current_time' WHERE `id`='$id'");
		}
	}

//	************************************************************
// Removes the account key for a user ( basically logout )

	function removeAccountKeyForUser($id) 
	{
		$count = $this->db["sys"]->selectRow("SELECT * FROM ".$this->dbx->prefix('fnma_account_keys')." where id ='$id'");
		if($count == false) 
		{
			//do nothing
		}
		else 
		{
			$this->db["sys"]->query("DELETE FROM ".$this->dbx->prefix('fnma_account_keys')." WHERE id ='$id'");
		}
	}
}
?>