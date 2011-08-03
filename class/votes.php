<?php

if (!defined("XOOPS_ROOT_PATH")) {
	exit();
}

class fnVotes extends XoopsObject
{
	
	var $table;
    var $id;
    var $hostname="";
    var $votelink="";
    var $image_url="";
    var $points;
    var $reset_time="";
	
	/**
	* constructor
	*/
	function fnVotes($id = null)
	{
		$this->db =& Database::getInstance();
		$this->initVar("id", XOBJ_DTYPE_INT, 1, false);
		$this->initVar("hostname", XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar("votelink", XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar("image_url", XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar("points", XOBJ_DTYPE_INT, 0, false);
		$this->initVar("reset_time", XOBJ_DTYPE_TXTBOX, null, false);

		// Non consistent values
		$this->initVar("pagescount", XOBJ_DTYPE_INT, 0, false);
		
		if (isset($id)) {
			$fnma_votes_handler =& fnGetHandler('fnVotes');
			$votes =& $fnma_votes_handler->get($id);
			foreach ($votes->vars as $k => $v) {
				$this->assignVar($k, $v['value']);
			}
			//$this->assignOtherProperties();
		}
	}

	function Vote($shopid=-1)
    {
        $this->db =& XoopsDatabaseFactory::getDatabaseConnection();;
        $this->table = "fnma_vote_sites";
        if ( is_array($votes) ) {
            $this->makeVote($voteid);
        } elseif ( $voteid != -1 ) {
            $this->getVotes(intval($voteid));
        }
    }
	
	function setVotesId($value)
    {
        $this->id = intval($value);
    }
	function setHostname($value)
    {
        $this->hostname = $value;
    }

    function setVoteLink($value)
    {
        $this->vote_link = $value;
    }

    function setImageURL($value)
    {
        $this->image_url = $value;
    }

    function setPoints($value)
    {
        $this->points = intval($value);
    }

    function setResetTime($value)
    {
        $this->reset_time = intval($value);
    }

   function store($approved=false)
    {
        $myts =& MyTextSanitizer::getInstance();
        $title =$myts->censorString($this->title);
        $hometext =$myts->censorString($this->hometext);
        $bodytext =$myts->censorString($this->bodytext);
        $title = $myts->addSlashes($title);
        $hometext = $myts->addSlashes($hometext);
        $bodytext = $myts->addSlashes($bodytext);
        if (!isset($this->nohtml) || $this->nohtml != 1) {
            $this->nohtml = 0;
        }
        if (!isset($this->nosmiley) || $this->nosmiley != 1) {
            $this->nosmiley = 0;
        }
        if (!isset($this->notifypub) || $this->notifypub != 1) {
            $this->notifypub = 0;
        }
        if (!isset($this->topicdisplay) || $this->topicdisplay != 0) {
            $this->topicdisplay = 1;
        }
        $expired = !empty($this->expired) ? $this->expired : 0;
        if (!isset($this->storyid)) {
            //$newpost = 1;
            $newstoryid = $this->db->genId($this->table."_storyid_seq");
            $created = time();
            $published = ($this->approved) ? $this->published : 0;

            $sql = sprintf("INSERT INTO %s (storyid, uid, title, created, published, expired, hostname, nohtml, nosmiley, hometext, bodytext, counter, topicid, ihome, notifypub, story_type, topicdisplay, topicalign, comments) VALUES (%u, %u, '%s', %u, %u, %u, '%s', %u, %u, '%s', '%s', %u, %u, %u, %u, '%s', %u, '%s', %u)", $this->table, $newstoryid, $this->uid, $title, $created, $published, $expired, $this->hostname, $this->nohtml, $this->nosmiley, $hometext, $bodytext, 0, $this->topicid, $this->ihome, $this->notifypub, $this->type, $this->topicdisplay, $this->topicalign, $this->comments);
        } else {
            if ($this->approved) {
                $sql = sprintf("UPDATE %s SET title = '%s', published = %u, expired = %u, nohtml = %u, nosmiley = %u, hometext = '%s', bodytext = '%s', topicid = %u, ihome = %u, topicdisplay = %u, topicalign = '%s', comments = %u WHERE storyid = %u", $this->table, $title, $this->published, $expired, $this->nohtml, $this->nosmiley, $hometext, $bodytext, $this->topicid, $this->ihome, $this->topicdisplay, $this->topicalign, $this->comments, $this->storyid);
            } else {
                $sql = sprintf("UPDATE %s SET title = '%s', expired = %u, nohtml = %u, nosmiley = %u, hometext = '%s', bodytext = '%s', topicid = %u, ihome = %u, topicdisplay = %u, topicalign = '%s', comments = %u WHERE storyid = %u", $this->table, $title, $expired, $this->nohtml, $this->nosmiley, $hometext, $bodytext, $this->topicid, $this->ihome, $this->topicdisplay, $this->topicalign, $this->comments, $this->storyid);
            }
            $newstoryid = $this->storyid;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($newstoryid)) {
            $newstoryid = $this->db->getInsertId();
            $this->storyid = $newstoryid;
        }
        return $newstoryid;
    }


function getVotes($voteid)
    {
        $voteid = intval($voteid);
        $sql = "SELECT * FROM ".$this->table." WHERE id=" . $voteid . "";
        $array = $this->db->fetchArray($this->db->query($sql));
        $this->makeVotes($array);
    }
	
	function makeVotes($array)
    {
        foreach ($array as $key => $value){
            $this->$key = $value;
        }
    }
}



class FnMangosAdminVotesHandler extends XoopsObjectHandler
{
	
	function &create($isNew = true)
	{
		$votes = new fnVotes();
		if ($isNew) {
			//$shop->setDefaultPermissions();
			$votes->setNew();
		}
		return $votes;
	}
	

	function &get($id)
	{
		if (intval($id) > 0) {
			$sql = 'SELECT * FROM '.$this->db->prefix('fnma_vote_sites').' WHERE id='.$id;
			
			if (!$result = $this->db->query($sql)) {
				return false;
			}
			
			$numrows = $this->db->getRowsNum($result);
			if ($numrows == 1) {
				$votes = new fnVotes();
				$votes->assignVars($this->db->fetchArray($result));
				return $votes;
			}
		}
		return false;
	}


	function &getObjects($criteria = null, $id_as_key = false, $notNullFields='')
	{
		$ret = false;
		$limit = $start = 0;
		$sql = 'SELECT * FROM '.$this->db->prefix('fnma_vote_sites');
		
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$whereClause = $criteria->renderWhere();
			
			If ($whereClause != 'WHERE ()') {
				$sql .= ' '.$criteria->renderWhere();
				If (!empty($notNullFields)) {
					$sql .= $this->NotNullFieldClause($notNullFields, true);
				}
			} elseif (!empty($notNullFields)) {
				$sql .= " WHERE " . $this->NotNullFieldClause($notNullFields);
			}
			if ($criteria->getSort() != '') {
				$sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
			}
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		} elseif (!empty($notNullFields)) {
			$sql .= $sql .= " WHERE " . $this->NotNullFieldClause($notNullFields);
		}
		
		//echo "<br />" . $sql . "<br />";
		$result = $this->db->query($sql, $limit, $start);
		if (!$result) {
			return $ret;
		}
		
		If (count($result) == 0) {
			return $ret;
		}
		
		while ($myrow = $this->db->fetchArray($result)) {
			$votes = new fnVotes();
			$votes->assignVars($myrow);
			//$shop->assignOtherProperties();
			
			if (!$id_as_key) {
				$ret[] =& $votes;
			} else {
				$ret[$myrow['id']] =& $votes;
			}
			unset($votes);
		}
		return $ret;
	}

function getCount($criteria = null, $notNullFields='')
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('smartsection_items');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$whereClause = $criteria->renderWhere();
			If ($whereClause != 'WHERE ()') {
				$sql .= ' '.$criteria->renderWhere();
				If (!empty($notNullFields)) {
					$sql .= $this->NotNullFieldClause($notNullFields, true);
				}
			} elseif (!empty($notNullFields)) {
				$sql .= " WHERE " . $this->NotNullFieldClause($notNullFields);	
			}
		} elseif (!empty($notNullFields)) {
			$sql .= " WHERE " . $this->NotNullFieldClause($notNullFields);	
		}
			
		//echo "<br />" . $sql . "<br />";
		$result = $this->db->query($sql);
		if (!$result) {
			return 0;
		}
		list($count) = $this->db->fetchRow($result);
		return $count;
	}

































}
?>