<?php

include_once XOOPS_ROOT_PATH."/class/xoopsstory.php";

class fnmaNews extends XoopsStory
{
	var $newstopic;   // XoopsTopic object
	
	function fnmaNews($storyid=-1)
	{
		$this->db =& Database::getInstance();
		$this->table = $this->db->prefix("fnma_news");
		$this->topicstable = $this->db->prefix("fnma_news_topics");
		if (is_array($storyid)) {
			$this->makeStory($storyid);
			$this->newstopic = $this->topic();
		} elseif($storyid != -1) {
			$this->getStory(intval($storyid));
			$this->newstopic = $this->topic();
		}
	}
	
		function getAllPublished($limit=0, $start=0, $topic=0, $ihome=0, $asobject=true)
	{
		$db =& Database::getInstance();
		$myts =& MyTextSanitizer::getInstance();
		$ret = array();
		$sql = "SELECT * FROM ".$db->prefix("fnma_news")." WHERE published > 0 AND published <= ".time()." AND (expired = 0 OR expired > ".time().")";
		if ( !empty($topic) ) {
			$sql .= " AND topicid=".intval($topic)." AND (ihome=1 OR ihome=0)";
		} else {
			if ( $ihome == 0 ) {
				$sql .= " AND ihome=0";
			}
		}
		if (!empty($uid) && intval($uid) > 0) {
			$sql .= ' AND uid='.$uid;
		}
 		$sql .= " ORDER BY published DESC";
		$result = $db->query($sql,intval($limit),intval($start));
		while ( $myrow = $db->fetchArray($result) ) {
			if ( $asobject ) {
				$ret[] = new fnmaNews($myrow);
			} else {
				$ret[$myrow['storyid']] = $myts->makeTboxData4Show($myrow['title']);
			}
		}
		return $ret;
	}
	
	// added new function to get all expired stories
	function getAllExpired($limit=0, $start=0, $topic=0, $ihome=0, $asobject=true)
	{
		$db =& Database::getInstance();
		$myts =& MyTextSanitizer::getInstance();
		$ret = array();
		$sql = "SELECT * FROM ".$db->prefix("fnma_news")." WHERE expired <= ".time()." AND expired > 0";
		if ( !empty($topic) ) {
			$sql .= " AND topicid=".intval($topic)." AND (ihome=1 OR ihome=0)";
		} else {
			if ( $ihome == 0 ) {
				$sql .= " AND ihome=0";
			}
		}
		if (!empty($uid) && intval($uid) > 0) {
			$sql .= ' AND uid='.$uid;
		}
 		$sql .= " ORDER BY expired DESC";
		$result = $db->query($sql,intval($limit),intval($start));
		while ( $myrow = $db->fetchArray($result) ) {
			if ( $asobject ) {
				$ret[] = new fnmaNews($myrow);
			} else {
				$ret[$myrow['storyid']] = $myts->makeTboxData4Show($myrow['title']);
			}
		}
		return $ret;
	}
	
	function getAllSubmitted($limit=0, $asobject=true)
	{
		$db =& Database::getInstance();
		$myts =& MyTextSanitizer::getInstance();
		$ret = array();
		$sql = "SELECT * FROM ".$db->prefix("fnma_news")." WHERE published=0 ORDER BY created DESC";
		$result = $db->query($sql,$limit,0);
		while ( $myrow = $db->fetchArray($result) ) {
			if ( $asobject ) {
				$ret[] = new fnmaNews($myrow);
			} else {
				$ret[$myrow['storyid']] = $myts->makeTboxData4Show($myrow['title']);
			}
		}
		return $ret;
	}
	
	function getByTopic($topicid)
	{
		$ret = array();
		$db =& Database::getInstance();
		$result = $db->query("SELECT * FROM ".$db->prefix("fnma_news")." WHERE topicid=".intval($topicid)."");
		while( $myrow = $db->fetchArray($result) ){
			$ret[] = new NewsStory($myrow);
		}
		return $ret;
	}

	function countByTopic($topicid=0)
	{
		$db =& Database::getInstance();
		$sql = "SELECT COUNT(*) FROM ".$db->prefix("fnma_news")."
		WHERE expired >= ".time()."";
		if ( $topicid != 0 ) {
			$sql .= " AND  topicid=".intval($topicid);
		}
		$result = $db->query($sql);
		list($count) = $db->fetchRow($result);
		return $count;
	}

	function countPublishedByTopic($topicid=0)
	{
		$db =& Database::getInstance();
		$sql = "SELECT COUNT(*) FROM ".$db->prefix("fnma_news")." WHERE published > 0 AND published <= ".time()." AND (expired = 0 OR expired > ".time().")";
		if ( !empty($topicid) ) {
			$sql .= " AND topicid=".intval($topicid);
		} else {
			$sql .= " AND ihome=0";
		}
		$result = $db->query($sql);
		list($count) = $db->fetchRow($result);
		return $count;
	}


	function topic_title()
	{
		return $this->newstopic->topic_title();
	}
	
	function adminlink()
	{
		$ret = "&nbsp;[ <a href='".XOOPS_URL."/modules/fnmMangosAdmin/admin/news.php?op=edit&amp;storyid=".$this->storyid."'>"._EDIT."</a> | <a href='".XOOPS_URL."/modules/fnmMangosAdmin/admin/index.php?op=delete&amp;storyid=".$this->storyid."'>"._DELETE."</a> ]&nbsp;";
		return $ret;
	}

	function imglink()
	{
		$ret = '';
		if ($this->newstopic->topic_imgurl() != '' && file_exists(XOOPS_ROOT_PATH."/modules/fnmMangosAdmin/images/topics/".$this->newstopic->topic_imgurl())) {
			$ret = "<a href='".XOOPS_URL."/modules/fnmMangosAdmin/news.php?storytopic=".$this->topicid."'><img src='".XOOPS_URL."/modules/fnMangosAdmin/images/topics/".$this->newstopic->topic_imgurl()."' alt='".$this->newstopic->topic_title()."' hspace='10' vspace='10' align='".$this->topicalign()."' /></a>";
		}
		return $ret;
	}

	function textlink()
	{
		$ret = "<a href='".XOOPS_URL."/modules/fnMangosAdmin/news.php?storytopic=".$this->topicid()."'>".$this->newstopic->topic_title()."</a>";
		return $ret;
	}
}