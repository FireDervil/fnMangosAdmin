<?php
// Account class for MangosWebSDL written by Steven Wilson, aka Wilson212
// Some functions used from the original MangosWeb AUTH Class

class fnmaAccount
{
	var $fnmaDB;
	var $xoopsDB;
    private $mysql;


    /**
      Class constructer.
      @param $account_db_conn the conndb class variable linked to the realmd database
    */
    public function __construct()
    {
		global $xoopsDB, $fnmaDB;
        $this->mysql = $fnmaDB;
		$this->dbx = $xoopsDB;
    }

    /**
      Boolean, ban an account.
      If nothing went wrong returns true.
      @param $id the account's ID
      @param $time the ammount of time (in seconds) the account has to be banned
      @param $bannedby the GM who has banned the account (default void)
      @param $reason the reason why the account has been banned (default void)
    */
    public function ban($id,$time,$bannedby="",$reason="")
    {
        $id = mysql_real_escape_string($id);
        $bandate = time();
        $unbandate = $bandate + $time;
        $bannedby = mysql_real_escape_string($bannedby);
        $reason = mysql_real_escape_string($reason);
        $this->mysql["logon"]->send("INSERT INTO `account_banned` (id,bandate,unbandate,bannedby,banreason,active) VALUES ('$id','$bandate','$unbandate','$bannedby','$reason','1')");
        return true;
    }

    /**
      String, returns the email of an account.
      @param $id the account's ID
    */
    public function getEmail($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql["logon"]->retrieve("SELECT `email` FROM `account` WHERE `id` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['email'];
    }

    /**
      Number, returns the number of expansions the account is enabled to use.
      0 = Classic, 1 = TBC, 2 = WotLK.
      @param $id the account's ID
    */
    public function getExpansion($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql["logon"]->retrieve("SELECT `expansion` FROM `account` WHERE `id` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['expansion'];
    }

    /**
      Number, returns the account's GM level.
      0 = no GM rights, >0 GM rights.
      @param $id the account's ID
    */
    public function getGmLevel($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql["logon"]->retrieve("SELECT `gmlevel` FROM `account` WHERE `id` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['gmlevel'];
    }

    /**
      Number, returns the account's ID.
      @param $username the account's name
    */
    public function getId($username)
    {
        $username = mysql_real_escape_string($username);
        $sql = $this->mysql["logon"]->retrieve("SELECT `id` FROM `account` WHERE `username` = '$username' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['id'];
    }

    /**
      Number, returns the account's ban status.
      0 = not banned, 1 = banned
      @param $ip the IP to be checked
    */
    public function getIPBanStatus($ip)
    {
        $ip = mysql_real_escape_string($ip);
        $date = time();
        $sql = $this->mysql["logon"]->retrieve("SELECT COUNT(*) AS `count` FROM `ip_banned` WHERE `ip` = '$ip' AND `unbandate` > '$date' OR `ip` = '$ip' AND `bandate` = `unbandate` LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['count'];
    }

    /**
      Number, returns the number of online players.
    */
    public function getNumAccountsOnline()
    {
        $sql = $this->mysql["logon"]->retrieve("SELECT COUNT(*) AS `count` FROM `account` WHERE `online` = '1'");
        $row = mysql_fetch_array($sql);
        return intval($row['count']);
    }

    /**
      String, returns the account's username.
      @param $id the account's ID
    */
    public function getUsername($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql["logon"]->retrieve("SELECT `username` FROM `account` WHERE `id` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['username'];
    }
	
	/**
      String, returns the account's lock.
      @param $id the account's ID
    */
    public function getLockedAccount($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql["logon"]->retrieve("SELECT `lock` FROM `account` WHERE `id` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['lock'];
    }
	
	/**
	Public function to return the groups with ids from groups
	@param
	@param
	**/
	public function getGroups()
	{
		return 0;
	}
	
	
    /**
      Boolean, binds an account to an IP (ie bind to 127.0.0.1 to ban).
      Returns true if nothing went wrong.
      @param $id the account's ID
      @param $ip the IP the account has to be binded to
    */
    public function lock($id,$ip)
    {
        $id = mysql_real_escape_string($id);
        $ip = mysql_real_escape_string($ip);
        $this->mysql["logon"]->send("UPDATE `account` SET `locked` = '1', `last_ip` = '$ip' WHERE `id` = '$id'");
        return true;
    }

    /**
      Number, simple login function.
      1 = successfull login, not banned; 2 = banned, 0 = wrong login.
      @param $user the username of the account
      @param $pass the password of the account
    */
    public function login($user,$pass)
    {
        $user = mysql_real_escape_string($user);
        $pass = mysql_real_escape_string($pass);

        $user = strtoupper($user);
        $pass = strtoupper($pass);
        $pass_hash = SHA1($user.':'.$pass);

        $sql = $this->mysql["logon"]->retrieve("SELECT COUNT(*) AS `count`,`id` FROM `account` WHERE `username` = '".$user."' AND `sha_pass_hash` = '".$pass_hash."' GROUP BY id LIMIT 1");
        $row = mysql_fetch_array($sql);
        $count = $row['count'];
        $id = $row['id'];

        $sql = $this->mysql["logon"]->retrieve("SELECT COUNT(*) AS `count` FROM `account_banned` WHERE `id` = '".$id."' AND `active` = '1' LIMIT 1");
        $row = mysql_fetch_array($sql);

        if($row['count'] > 0) return 2;
        if($count > 0) return 1;
        return 0;
    }


 function register2($params, $account_extend = NULL)
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
			   
    /**
      Register an account.
      Returns 0 if the password or password hash is too long.
      Returns 1 if it was successful.
      Returns 2 if the IP is banned
      Returns 3 if the username or email was taken
      @param $user the desired username
      @param $pass the desired password
      @param $email the email the account will be binded to (one account per email) (default void)
      @param $ip the IP with which the account is registered (usefull to take trace of register spammers) (default void)
      @param $expansion the number of expansions the account is enabled to use (default 0, WoW Classic)
      @param $id the ID to give the account (default Auto Increment)
    */
    public function register($user,$pass,$email="",$ip="",$expansion = 0,$id="")
    {
        $user = mysql_real_escape_string($user);
        $pass = mysql_real_escape_string($pass);
        $email = mysql_real_escape_string($email);
        $ip = mysql_real_escape_string($ip);
        $expansion = mysql_real_escape_string($expansion);
        $id = mysql_real_escape_string($id);

        if(strlen($pass) > 16) return 0;

        $user = strtoupper($user);
        $pass = strtoupper($pass);
        $pass_hash = SHA1($user.':'.$pass);

        if(strlen($pass_hash) > 40) return 0;

        $query = "SELECT COUNT(*) AS `count` FROM `account` WHERE `username` = '$user'";
        if($email != "") $query .= " OR `email` = '$email'";
        $sql = $this->mysql->retrieve($query);
        $row = mysql_fetch_array($sql);
        $count = $row['count'];

        if($count > 0) return 3;

        $chk = $this->getIPBanStatus($ip);
        if(!empty($ip) && $chk == 1) return 2;

        if(!empty($id))
        {
            $this->mysql->send("INSERT INTO `account` (id,username,sha_pass_hash,email,last_ip,expansion) VALUES ('$id','$user','$pass_hash','$email','$ip','$expansion')");
        } else {
            $this->mysql->send("INSERT INTO `account` (username,sha_pass_hash,email,last_ip,expansion) VALUES ('$user','$pass_hash','$email','$ip','$expansion')");
        }
        return 1;
    }

    /**
      Sets the GM level of an account.
      @param $id the account's ID
      @param $level the desired level
    */
    public function setGmLevel($id,$level)
    {
        $id = mysql_real_escape_string($id);
        $level = mysql_real_escape_string($level);
        $this->mysql->retrieve("UPDATE `account` SET `gmlevel` = '$level' WHERE `id` = '$id' LIMIT 1");
        return true;
    }
	
    /**
      Sets the GM level of an account.
      @param $id the account's ID
      @param $level the desired level
    */
    public function setVisit($id, $time)
    {
        $id = mysql_real_escape_string($id);
        $this->mysql["logon"]->retrieve("UPDATE `account` SET `last_login` = '$time' WHERE `id` = '$id' LIMIT 1");
        return true;
    }	

    /**
      Boolean, sets the email of an account.
      Returns false if the email has just been taken.
      @param $id the account's ID
      @param $newemail the new email
    */
    public function setEmail($id,$newemail)
    {
        $id = mysql_real_escape_string($id);
        $newemail = mysql_real_escape_string($newemail);
        $sql = $this->mysql["logon"]->retrieve("SELECT COUNT(*) AS `count` FROM `account` WHERE `email` = '$newemail'");
        $row = mysql_fetch_array($sql);

        if($row['count'] > 0) return false;
        $this->mysql->send("UPDATE `account` SET `email` = '$newemail' WHERE `id` = $id");
        return true;
    }

    /**
      Boolean, sets the number of expansions an account is able to use.
      Returns true if all the things went good.
      @param $id the account's ID
      @param $nexp the number of expansions an account is able to use.
    */
    public function setExpansion($id,$nexp)
    {
        $id = mysql_real_escape_string($id);
        $nexp = mysql_real_escape_string($nexp);
        $this->mysql["logon"]->send("UPDATE `account` SET `expansion` = '$nexp' WHERE `id` = $id");
        return true;
    }

    /**
      Sets a new password for an account.
      Returns false if the password is too long.
      @param $id the account's ID
      @param $newpass the new password
    */
    public function setPassword($id,$newpass)
    {
        $id = mysql_real_escape_string($id);
        $newpass = mysql_real_escape_string($newpass);

        if(strlen($newpass) > 16) return false;

        $sql = $this->mysql["logon"]->retrieve("SELECT `username` FROM `account` WHERE `id` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);

        $pass_hash = SHA1(strtoupper($row['username'].":".strtoupper($newpass)));

        if(strlen($pass_hash) > 40) return false;

        $this->mysql["logon"]->send("UPDATE `account` SET `sha_pass_hash` = '$pass_hash', `v` = 0, `s` = 0 WHERE `id` = '$id' LIMIT 1");
        return true;
    }

    /**
      Boolean, unbans an account.
      Returns true if all went good.
      @param $id the account's ID
    */
    public function unban($id)
    {
        $id = mysql_real_escape_string($id);
        $this->mysql["logon"]->send("DELETE FROM `account_banned` WHERE `id` = '$id'");
        return true;
    }
 	/**
      Boolean, unbans an account.
      Returns true if all went good.
      @param $id the account's ID
    */
	public function getCharacterList($id)
	{
		global $fnmaDB;
		$list = $fnmaDB["char"]->select("SELECT * FROM characters WHERE account='".$id."'");
		if($list == false)
		{
			return false;
		}
		return $list;
	}
	
    /**
      Boolean, unbinds an account from any IP.
      Returns true if all went good.
      @param $id the account's ID
    */
    public function unlock($id)
    {
        $id = mysql_real_escape_string($id);
        $this->mysql["logon"]->send("UPDATE `account` SET `locked` = '0' WHERE `id` = '$id'");
        return true;
    }
	
	public function getProfile($acct_id=false)
	{
		global $xoopsModuleConfig;
		$res = $this->mysql["sys"]->selectRow("
			SELECT * FROM ".$xoopsModuleConfig["realm_db_name"].".account
			LEFT JOIN ".$this->dbx->prefix('fnma_account_extend')." ON account.id = ".$this->dbx->prefix('fnma_account_extend').".account_id
			LEFT JOIN ".$this->dbx->prefix('fnma_account_groups')." ON ".$this->dbx->prefix('fnma_account_extend').".account_level = ".$this->dbx->prefix('fnma_account_groups').".account_level
			WHERE id='".$acct_id."'");
        return $res;
    }
	
	public function isAvailableUsername($username)
	{
        $res = $this->mysql["logon"]->mysql_count("SELECT COUNT(*) FROM `account` WHERE `username`='".$username."'");
        if($res == 0) 
		{
			return true; // username is available
		}
		else
		{
			return false; // username is not available
		}
    }
	
	function setErrors($err_no, $err_str)
    {
        $this->_errors[$err_no] = trim($err_str);
    }


	function getErrors()
    {
        return $this->_errors;
    }
	

	 function getHtmlErrors()
    {
        global $xoopsConfig;
        $ret = '<br>';
        if ($xoopsConfig['debug_mode'] == 1 || $xoopsConfig['debug_mode'] == 2) {
            if (!empty($this->_errors)) {
                foreach ($this->_errors as $errstr) {
                    $ret .= $errstr . '<br/>';
                }
            } else {
                $ret .= _NONE . '<br />';
            }
            $ret .= sprintf(_AUTH_MSG_AUTH_METHOD, $this->auth_method);
        } else {
            $ret .= _US_INCORRECTLOGIN;
        }
        return $ret;
    }
	
	
}

?>