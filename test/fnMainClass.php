<?php
class msdk
{
    const DATA_FIELD_HONOR = 1648;
    const DATA_FIELD_LEVEL = 53;
    const DATA_FIELD_MONEY = 1546;

    private $project;

    public function __construct()
    {
        $this->project = array(
        'author' => 'Elegos and Maikash',
        'license' => 'gpl',
        'version' => '0.3',
        );
    }

    public static function checkServerStatus($host, $port, $timeout = 3)
    {
        if($sock = @fsockopen($host, $port, $error_no, $error_str, $timeout))
        {
            fclose($sock);
            return true;
        }
        return false;
    }

    public function getAuthor()
    {
        return $this->project['author'];
    }

    public function getLicense()
    {
        return $this->project['license'];
    }

    public function getVersion()
    {
        return $this->project['version'];
    }

    public static function getDataOffset($data,$offset)
    {
        $exp = explode(' ',$data);
        return $exp[$offset];
    }
}

class conndb
{
    private $mysql;
    public $sqlog;

    public function __construct($db_host, $db_port, $db_user, $db_pass, $db_name)
    {
        $this->mysql = @mysql_connect($db_host.":".$db_port, $db_user, $db_pass, true) or die('Incorrect MySQL Information!');
            mysql_select_db($db_name,$this->mysql) or die(mysql_error());
    }

    public function send($query)
    {
        @mysql_query($query,$this->mysql) or die(mysql_error());
        $this->sqlog .= $query."\n";
    }

    public function retrieve($query)
    {
        $sql = @mysql_query($query,$this->mysql) or die(mysql_error());
        $this->sqlog .= $query."\n";
        return $sql;
    }

    public function closeDB()
    {
        @mysql_close($this->mysql) or die(mysql_error());
    }
}

class account
{
    private $mysql;

    public function __construct($account_db_conn)
    {
        $this->mysql = $account_db_conn;
    }

    public function banAccount($id,$time,$bannedby="",$banreason="")
    {
        $id = mysql_real_escape_string($id);
        $bandate = time();
        $unbandate = $bandate + $time;
        $bannedby = mysql_real_escape_string($bannedby);
        $banreason = mysql_real_escape_string($banreason);
        $this->mysql->send("INSERT INTO `account_banned` (`id`,`bandate`,`unbandate`,`bannedby`,`banreason`,`active`) VALUES ('$id','$bandate','$unbandate','$bannedby','$banreason','1')");
        return true;
    }

    public function getEmail($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT `email` FROM `account` WHERE `id` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['email'];
    }

    public function getExpansion($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT `expansion` FROM `account` WHERE `id` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['expansion'];
    }

    public function getGmLevel($id)
    {
        $query = sprintf("SELECT `gmlevel` FROM `account` WHERE `id` = '%s' LIMIT 1",mysql_real_escape_string($id));
        $sql = $this->mysql->retrieve($query);
        $row = mysql_fetch_array($sql);
        return $row['gmlevel'];
    }

    public function getId($username)
    {
        $username = mysql_real_escape_string($username);
        $sql = $this->mysql->retrieve("SELECT `id` FROM `account` WHERE `username` = '$username' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['id'];
    }

    public function getNumAccountsOnline()
    {
        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `account` WHERE `online` = '1'");
        $row = mysql_fetch_array($sql);
        return intval($row['count']);
    }

    public function lock($id,$ip)
    {
        $id = mysql_real_escape_string($id);
        $ip = mysql_real_escape_string($ip);
        $this->mysql->send("UPDATE `account` SET `locked` = '1', `last_ip` = '$ip' WHERE `id` = '$id'");
        return true;
    }

    public function login($user,$pass)
    {
        $user = mysql_real_escape_string($user);
        $pass = mysql_real_escape_string($pass);

        $user = strtoupper($user);
        $pass = strtoupper($pass);
        $pass_hash = SHA1($user.':'.$pass);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `account` WHERE `username` = '".$user."' AND `sha_pass_hash` = '".$pass_hash."' LIMIT 1");
        $row = mysql_fetch_array($sql);
        if($row['count'] > 0) return true;
        return false;
    }

    public function register($user,$pass,$email="",$ip="",$expansion = 0)
    {
        $user = mysql_real_escape_string($user);
        $pass = mysql_real_escape_string($pass);
        $email = mysql_real_escape_string($email);
        $ip = mysql_real_escape_string($ip);
        $expansion = mysql_real_escape_string($expansion);

        $user = strtoupper($user);
        $pass = strtoupper($pass);
        $pass_hash = SHA1($user.':'.$pass);

        $query = "SELECT COUNT(*) AS `count` FROM `account` WHERE `username` = '$user'";
        if($email != "") $query .= " OR `email` = '$email'";
        $sql = $this->mysql->retrieve($query);
        $row = mysql_fetch_array($sql);

        if($row['count'] > 0) return false;
        $query = "INSERT INTO `account` (`username`,`sha_pass_hash`,`email`,`last_ip`,`expansion`) VALUES ('$user','$pass_hash','$email','$ip','$expansion')";
        $this->mysql->send($query);
        return true;
    }

    public function setGmLevel($id,$level)
    {
        $query = sprintf("UPDATE `account` SET `gmlevel` = '%s' WHERE `id` = '%s' LIMIT 1",
            mysql_real_escape_string($level),mysql_real_escape_string($id));
        $this->mysql->retrieve($query);
    }

    public function setEmail($id,$newemail)
    {
        $id = mysql_real_escape_string($id);
        $newemail = mysql_real_escape_string($newemail);
        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `account` WHERE `email` = '$newemail'");
        $row = mysql_fetch_array($sql);

        if($row['count'] > 0) return false;
        $this->mysql->send("UPDATE `account` SET `email` = '$newemail' WHERE `id` = $id");
        return true;
    }

    public function setExpansion($id,$newexp)
    {
        $id = mysql_real_escape_string($id);
        $newexp = mysql_real_escape_string($newexp);
        $this->mysql->send("UPDATE `account` SET `expansion` = '$newexp' WHERE `id` = $id");
        return true;
    }

    public function setPassword($id,$newpass)
    {
        $id = mysql_real_escape_string($id);
        $newpass = mysql_real_escape_string($newpass);

        $sql = $this->mysql->retrieve("SELECT `usename` FROM `account` WHERE `id` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);

        $pass_hash = SHA1(strtoupper($row['username'].":".strtoupper($newpass)));
        $this->mysql->send("UPDATE `account` SET `sha_pass_hash` = '$pass_hash' WHERE `id` = '$id' LIMIT 1");
    }

    public function unbanAccount($id)
    {
        $id = mysql_real_escape_string($id);
        $this->mysql->send("DELETE FROM `account_banned` WHERE `id` = '$id'");
        return true;
    }

    public function unlock($id)
    {
        $id = mysql_real_escape_string($id);
        $this->mysql->send("UPDATE `account` SET `locked` = '0' WHERE `id` = '$id'");
        return true;
    }
}

class char
{
    private $mysql;

    public function __construct($char_db_conn)
    {
        $this->mysql = $char_db_conn;
    }

    public function addSpell($guid, $spell, $active = 1, $disabled = 0)
    {
        $guid = mysql_real_escape_string($guid);
        $spell = mysql_real_escape_string($spell);
        $active = mysql_real_escape_string($active);
        $disabled = mysql_real_escape_string($disabled);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `character_spell` WHERE `guid` = '$guid' AND `spell` = '$spell'");
        $row = mysql_fetch_array($sql);
        if($row['count'] > 0) return false;

        $this->mysql->send("INSERT INTO `character_spell` (`guid`,`spell`,`active`,`disabled`) VALUES ('$guid','$spell','$active','$disabled')");
        return true;
    }

    public function adjustLevel($guid, $adjustvalue)
    {
        $guid = mysql_real_escape_string($guid);
        $adjust = mysql_real_escape_string($adjustvalue);
        $adjust = intval($adjust);

        $sql = $this->mysql->retrieve("SELECT `data` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);

        $exp = explode(' ',$row['data']);
        $exp[msdk::DATA_FIELD_LEVEL] = $exp[msdk::DATA_FIELD_LEVEL]+$adjust;
        $imp = implode(' ',$exp);
        $this->mysql->send("UPDATE `characters` SET `data` = '$imp' WHERE `guid` = '$guid' LIMIT 1");
    }

    public function adjustMoney($guid, $adjustvalue)
    {
        $guid = mysql_real_escape_string($guid);
        $adjust = mysql_real_escape_string($adjustvalue);
        $adjust = intval($adjust);

        $sql = $this->mysql->retrieve("SELECT `data` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);

        $exp = explode(' ',$row['data']);
        $exp[msdk::DATA_FIELD_MONEY] = $exp[msdk::DATA_FIELD_MONEY]+$adjust;
        $imp = implode(' ',$exp);
        $this->mysql->send("UPDATE `characters` SET `data` = '$imp' WHERE `guid` = '$guid' LIMIT 1");
    }

    public function getAccountId($guid)
    {
        $query = sprintf("SELECT `account` FROM `characters` WHERE `guid` = '%s' LIMIT 1",mysql_real_escape_string($guid));
        $sql = $this->mysql->retrieve($query);
        $row = mysql_fetch_array($sql);
        return $row['account'];
    }

    public function getDataField($guid)
    {
        $guid = mysql_real_escape_string($guid);

        $sql = $this->mysql->retrieve("SELECT `data` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);

        return $row['data'];
    }

    public function getEquippedGear($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $sql = $this->mysql->retrieve("SELECT * FROM `character_inventory` WHERE `guid` = '$guid' AND `slot` < '19'");
        while($row = mysql_fetch_array($sql))
        {
            switch($row['slot'])
            {
                case 0:
                    $result['head'] = $row['item_template'];
                    $result['head_guid'] = $row['item'];
                    break;
                case 1:
                    $result['neck'] = $row['item_template'];
                    $result['neck_guid'] = $row['item'];
                    break;
                case 2:
                    $result['shoulders'] = $row['item_template'];
                    $result['shoulders_guid'] = $row['item'];
                    break;
                case 3:
                    $result['shirt'] = $row['item_template'];
                    $result['shirt_guid'] = $row['item'];
                    break;
                case 4:
                    $result['chest'] = $row['item_template'];
                    $result['chest_guid'] = $row['item'];
                    break;
                case 5:
                    $result['waist'] = $row['item_template'];
                    $result['waist_guid'] = $row['item'];
                    break;
                case 6:
                    $result['legs'] = $row['item_template'];
                    $result['legs_guid'] = $row['item'];
                    break;
                case 7:
                    $result['feet'] = $row['item_template'];
                    $result['feet_guid'] = $row['item'];
                    break;
                case 8:
                    $result['wrists'] = $row['item_template'];
                    $result['wrists_guid'] = $row['item'];
                    break;
                case 9:
                    $result['hands'] = $row['item_template'];
                    $result['hands_guid'] = $row['item'];
                    break;
                case 10:
                    $result['ring1'] = $row['item_template'];
                    $result['ring1_guid'] = $row['item'];
                    break;
                case 11:
                    $result['ring2'] = $row['item_template'];
                    $result['ring2_guid'] = $row['item'];
                    break;
                case 12:
                    $result['trinket1'] = $row['item_template'];
                    $result['trinket1_guid'] = $row['item'];
                    break;
                case 13:
                    $result['trinket2'] = $row['item_template'];
                    $result['trinket2_guid'] = $row['item'];
                    break;
                case 14:
                    $result['back'] = $row['item_template'];
                    $result['back_guid'] = $row['item'];
                    break;
                case 15:
                    $result['mainhand'] = $row['item_template'];
                    $result['mainhand_guid'] = $row['item'];
                    break;
                case 16:
                    $result['offhand'] = $row['item_template'];
                    $result['offhand_guid'] = $row['item'];
                    break;
                case 17:
                    $result['ranged'] = $row['item_template'];
                    $result['ranged_guid'] = $row['item'];
                    break;
                case 18:
                    $result['tabard'] = $row['item_template'];
                    $result['tabard_guid'] = $row['item'];
                    break;
            }
        }
        return $result;
    }

    public function getFaction($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $ally = array("1", "3", "4", "7", "11");

        $sql = $this->mysql->retrieve("SELECT `race` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);

        if(in_array($row['race'], $ally))
        {
            return 1;
        } else {
            return 0;
        }
    }

    public function getGuid($name)
    {
        $name = mysql_real_escape_string($name);
        $name = strtolower($name);
        $name = ucfirst($name);

        $sql = $this->mysql->retrieve("SELECT `guid` FROM `characters` WHERE `name` = '$name' LIMIT 1");
        $row = mysql_fetch_array($sql);

        return $row['guid'];
    }

    public function getGuildRank($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $sql = $this->mysql->retrieve("SELECT `guildid`,`rank` FROM `guild_member` WHERE `guid` = '$guid' LIMIT 1");
        if(mysql_affected_rows() == 0) return false;
        $row = mysql_fetch_array($sql);

        $sql = $this->mysql->retrieve("SELECT `rname`, `rid` FROM `guild_rank` WHERE `rid` = '".$row['rank']."' AND `guildid` = '".$row['guildid']."' LIMIT 1");
        if(mysql_affected_rows() == 0) return false;
        $row = mysql_fetch_array($sql);

        $result = array(
            "rid" => $row['rid'],
            "rank" => $row['rname'],
        );

        return $result;
    }

    public static function getHonor($datafield)
    {
        return msdk::getDataOffset($datafield,msdk::DATA_FIELD_HONOR);
    }

    public static function getLevel($datafield)
    {
        return msdk::getDataOffset($datafield,msdk::DATA_FIELD_LEVEL);
    }

    public function getMainBag($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $sql = $this->mysql->retrieve("SELECT * FROM `character_inventory` WHERE `guid` = '$guid' AND `slot` > '22' AND `slot` < '39'");
        while($row = mysql_fetch_array($sql))
        {
            $result[$row['slot']]['item'] = $row['item_template'];
            $result[$row['slot']]['guid'] = $row['item'];
        }
        return $result;
    }

    public static function getMoney($datafield)
    {
        return msdk::getDataOffset($datafield,msdk::DATA_FIELD_MONEY);
    }

    public function getName($guid)
    {
        $query = sprintf("SELECT `name` FROM `characters` WHERE `guid` = '%s' LIMIT 1",mysql_real_escape_string($guid));
        $sql = $this->mysql->retrieve($query);
        $row = mysql_fetch_array($sql);
        return $row['name'];
    }

    public function getNumCharsOnline()
    {
        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `characters` WHERE `online` = '1'");
        $row = mysql_fetch_array($sql);
        return intval($row['count']);
    }

    public function removeSpell($guid, $spell)
    {
        $guid = mysql_real_escape_string($guid);
        $spell = mysql_real_escape_string($spell);
        $this->mysql->send("DELETE FROM `character_spell` WHERE `guid` = '$guid' AND `spell` = '$spell'");
        return true;
    }

    public function revive($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $this->mysql->send("DELETE FROM `character_aura` WHERE `guid` = '".$guid."' AND `spell` = '20584' OR `guid` = '".$guid."' AND `spell` = '8326'");
        return true;
    }

    public function setDataField($guid,$datafield,$value)
    {
        $guid = mysql_real_escape_string($guid);
        $df = mysql_real_escape_string($datafield);
        $val = mysql_real_escape_string($value);
        $val = intval($val);

        $sql = $this->mysql->retrieve("SELECT `data` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);

        $exp = explode(' ',$row['data']);
        $exp[$df] = $val;
        $imp = implode(' ',$exp);
        $this->mysql->send("UPDATE `characters` SET `data` = '$imp' WHERE `guid` = '$guid' LIMIT 1");
        return true;
    }

    public function setLevel($guid, $newlevel)
    {
        $guid = mysql_real_escape_string($guid);
        $adjust = mysql_real_escape_string($adjustvalue);
        $adjust = intval($adjust);

        $sql = $this->mysql->retrieve("SELECT `data` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);

        $exp = explode(' ',$row['data']);
        $exp[msdk::DATA_FIELD_LEVEL] = $exp[msdk::DATA_FIELD_LEVEL]+$adjust;
        $imp = implode(' ',$exp);
        $this->mysql->send("UPDATE `characters` SET `data` = '$imp' WHERE `guid` = '$guid' LIMIT 1");
    }

    public function setMoney($guid, $sumofmoney)
    {
        $guid = mysql_real_escape_string($guid);
        $sum = mysql_real_escape_string($sumofmoney);
        $sum = intval($sum);

        $sql = $this->mysql->retrieve("SELECT `data` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);

        $exp = explode(' ',$row['data']);
        $exp[msdk::DATA_FIELD_MONEY] = $sum;
        $imp = implode(' ',$exp);
        $this->mysql->send("UPDATE `characters` SET `data` = '$imp' WHERE `guid` = '$guid' LIMIT 1");
    }

    public function setName($guid,$newname)
    {
        $newname = mysql_real_escape_string(strtolower($newname));
        $newname = ucfirst($newname);
        $guid = mysql_real_escape_string($guid);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `characters` WHERE `name` = '$newname' LIMIT 1");
        $row = mysql_fetch_array($sql);
        if($row['count'] > 0) return false;
        $this->mysql->send("UPDATE `characters` SET `name` = '$newname' WHERE `guid` = '$guid' LIMIT 1");
        return true;
    }
}

class guild
{
    private $mysql;

    public function __construct($char_db_conn)
    {
        $this->mysql = $char_db_conn;
    }

    public function getGuildLeader($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT `leaderguid` FROM `guild` WHERE `guildid` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['leaderguid'];
    }

    public function getGuildList()
    {
        $sql = $this->mysql->retrieve("SELECT `guildid`,`name`,`leaderguid` FROM `guild`");
        $i = 0;
        while($row = mysql_fetch_array($sql))
        {
            $result[$i]['id'] = $row['guildid'];
            $result[$i]['name'] = $row['name'];
            $result[$i]['leaderid'] = $row['leaderguid'];
            $i++;
        }
        return $result;
    }

    public function getGuildName($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT `name` FROM `guild` WHERE `guildid` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['name'];
    }

    public function setGuildLeader($guildid,$leaderid)
    {
        $guild = mysql_real_escape_string($guildid);
        $leader = mysql_real_escape_string($leaderid);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count`, `guildid` FROM `guild_member` WHERE `guid` = '$leader'");
        $row = mysql_fetch_array($sql);
        if($row['count'] < 1 || $row['guildid'] != $guild) return false;

        $this->mysql->send("UPDATE `guild` SET `leaderguid` = '$leader' WHERE `guildid` = '$guild' LIMIT 1");
        return true;
    }

    public function setGuildName($guildid, $guildname)
    {
        $id = mysql_real_escape_string($guildid);
        $name = mysql_real_escape_string($guildname);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `guild` WHERE `name` = '$name' LIMIT 1");
        $row = mysql_fetch_array($sql);
        if($row['count'] > 0) return false;

        $this->mysql->send("UPDATE `guild` SET `name` = '$name' WHERE `guildid` = '$id' LIMIT 1");
        return true;
    }
}

class arenateam
{
    private $mysql;

    public function __construct($char_db_conn)
    {
        $this->mysql = $char_db_conn;
    }

    public function getATCaptain($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT `captainguid` FROM `arena_team` WHERE `arenateamid` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['captainguid'];
    }

    public function getATList()
    {
        $sql = $this->mysql->retrieve("SELECT `arenateamid`,`name`,`captainguid` FROM `arena_team`");
        $i = 0;
        while($row = mysql_fetch_array($sql))
        {
            $result[$i]['id'] = $row['arenateamid'];
            $result[$i]['name'] = $row['name'];
            $result[$i]['leaderid'] = $row['captainguid'];
            $i++;
        }
        return $result;
    }

    public function getATName($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT `name` FROM `arena_team` WHERE `arenateamid` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['name'];
    }

    public function setATCaptain($id,$leaderid)
    {
        $id = mysql_real_escape_string($id);
        $leader = mysql_real_escape_string($leaderid);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count`, `arenateamid` FROM `arena_team_member` WHERE `guid` = '$leader'");
        $row = mysql_fetch_array($sql);
        if($row['count'] < 1 || $row['arenateamid'] != $id) return false;

        $this->mysql->send("UPDATE `arena_team` SET `captainguid` = '$leader' WHERE `arenateamid` = '$id' LIMIT 1");
        return true;
    }

    public function setATName($id, $name)
    {
        $id = mysql_real_escape_string($id);
        $name = mysql_real_escape_string($name);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `arena_team` WHERE `name` = '$name' LIMIT 1");
        $row = mysql_fetch_array($sql);
        if($row['count'] > 0) return false;

        $this->mysql->send("UPDATE `arena_team` SET `name` = '$name' WHERE `arenateamid` = '$id' LIMIT 1");
        return true;
    }
}
class ticket
{
    private $mysql;

    public function __construct($char_db_conn)
    {
        $this->mysql = $char_db_conn;
    }

    public function delete($id)
    {
        $id = mysql_real_escape_string($id);
        $query = "DELETE FROM `character_ticket` WHERE ticket_id='".$id."'";
        $this->mysql->send($query);
        return true;
    }

    public function getTicketList()
    {
        $sql = $this->mysql->retrieve("SELECT * FROM `character_ticket`");
        $i = 0;
        while($row = mysql_fetch_array($sql))
        {
            $result[$i]['id'] = $row['ticket_id'];
            $result[$i]['char'] = $row['guid'];
            $result[$i]['text'] = $row['ticket_text'];
            $result[$i]['lastchange'] = $row['ticket_lastchange'];
            $i++;
        }
        return $result;
    }
}

class rasocket
{
    private $handle;
    private $errorstr, $errorno;
    private $auth;
    public $motto;

    function rasocket()
    {
        $this->handle = false;
    }

    public function auth($user,$password)
    {
        if(!isset($user) || !isset($password))
        {
            return false;
        }

        if(!$this->handle)
        {
            return false;
        }

        fwrite($this->handle, "USER ".$user."\n");
        usleep(50);
        fwrite($this->handle, "PASS ".$password."\n");
        usleep(300);

        if (substr(trim(fgets($this->handle)),0,1) != "+")
          return false;
        else
        {
            $this->auth = TRUE;
            return true;
        }
    }

    public function connect($host, $port = 3443)
    {
        if(!isset($host))
          return false;

        if($this->handle)
          fclose($this->handle);

        $this->handle = @fsockopen($host, $port, $errorno, $errorstr, 5);

        if(!$this->handle)
        {
            return false;
        } else {
            $this->motto = trim(fgets($this->handle));
            return true;
        }
    }

    public function disconnect()
    {
        if($this->handle)
        {
            fclose($this->handle);
            $this->auth = FALSE;
        }
    }

    public function sendcommand($command)
    {
        if(!$this->handle)
        {
            return false;
        }
        if(!$this->auth)
        {
            return false;
        }

        fwrite($this->handle, $command."\n");
        usleep(200);
        fgets($this->handle,8);
        return trim(fgets($this->handle));
    }

    public function sendcommanddelay($command, $delay = 4)
    {
        if(!$this->handle)
        {
            return false;
        }
        if(!$this->auth)
        {
            return false;
        }

        fwrite($this->handle, $command."\n");
        sleep($delay);
        return true;
    }
}
?>
