<?php

if (!defined("XOOPS_ROOT_PATH")) {
	exit();
}

class fnShop extends XoopsObject
{
	
	var $table;
    var $id;
    var $item_number="";
    var $itemset;
    var $gold;
    var $quanity;
    var $desc="";
    var $wp_cost="";
    var $realms;
    var $poster_name;
    var $rating;

	
	/**
	* constructor
	*/
	function fnShop($id = null)
	{
		$this->db =& Database::getInstance();
		$this->initVar("id", XOBJ_DTYPE_INT, 1, false);
		$this->initVar("item_number", XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar("itemset", XOBJ_DTYPE_TXTBOX, null, true, 255);
		$this->initVar("gold", XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar("quanity", XOBJ_DTYPE_INT, 0, false);
		$this->initVar("desc", XOBJ_DTYPE_TXTAREA, null, true);
		$this->initVar("wp_cost", XOBJ_DTYPE_TXTBOX, 0, false);
		$this->initVar("realms", XOBJ_DTYPE_INT, null, 0,  false);
		$this->initVar("poster_name", XOBJ_DTYPE_INT, 0, false);
		$this->initVar("rating", XOBJ_DTYPE_INT, 0, false, 255);
		// Non consistent values
		$this->initVar("pagescount", XOBJ_DTYPE_INT, 0, false);
		
		if (isset($id)) {
			$fnma_shops_handler =& fn_gethandler('fnShop');
			$shop =& $fnma_shops_handler->get($id);
			foreach ($shop->vars as $k => $v) {
				$this->assignVar($k, $v['value']);
			}
			//$this->assignOtherProperties();
		}
	}
		
		
		
		
	function Shops($shopid=-1)
    {
        $this->db =& XoopsDatabaseFactory::getDatabaseConnection();;
        $this->table = "fnma_shop_items";
        if ( is_array($shopid) ) {
            $this->makeShops($shopid);
        } elseif ( $shopid != -1 ) {
            $this->getShops(intval($shopid));
        }
    }
	
	function setShopId($value)
    {
        $this->shopid = intval($value);
    }
	function setItemNumber($value)
    {
        $this->item_number = intval($value);
    }

    function setItemSet($value)
    {
        $this->itemset = intval($value);
    }

    function setGold($value)
    {
        $this->gold = $value;
    }

    function setQuantity($value)
    {
        $this->quanity = $value;
    }

    function setDescription($value)
    {
        $this->desc = $value;
    }

    function setWebPointsCost($value)
    {
        $this->wp_cost = intval($value);
    }
	function setRealms($value)
    {
        $this->realms = $value;
    }

    function setPosterName($value)
    {
        $this->poster_name = intval($value);
    }
    function setRating($value)
    {
        $this->rating = intval($value);
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

 	function getShops($shopid)
    {
        $shopid = intval($shopid);
        $sql = "SELECT * FROM ".$this->table." WHERE id=" . $shopid . "";
        $array = $this->db->fetchArray($this->db->query($sql));
        $this->makeShops($array);
    }
	
	function makeShops($array)
    {
        foreach ($array as $key => $value){
            $this->$key = $value;
        }
    }
}
	
	
class FnMangosAdminShopsHandler extends XoopsObjectHandler
{
	
	function &create($isNew = true)
	{
		$shop = new fnShop();
		if ($isNew) {
			//$shop->setDefaultPermissions();
			$shop->setNew();
		}
		return $shop;
	}
	

	function &get($id)
	{
		if (intval($id) > 0) {
			$sql = 'SELECT * FROM '.$this->db->prefix('fnma_shop_items').' WHERE id='.$id;
			
			if (!$result = $this->db->query($sql)) {
				return false;
			}
			
			$numrows = $this->db->getRowsNum($result);
			if ($numrows == 1) {
				$shop = new fnShop();
				$shop->assignVars($this->db->fetchArray($result));
				return $shop;
			}
		}
		return false;
	}
	
		function insert(&$item, $force = false)
	{

        if (strtolower(get_class($item)) != 'fnShop') {
            return false;
        }

		if (!$item->isDirty()) {
			return true;
		}

		if (!$item->cleanVars()) {
			return false;
		}
	
		foreach ($item->cleanVars as $k => $v) {
            ${$k} = $v;
        }
		
		if ($item->isNew()) {
			$sql = sprintf("INSERT INTO %s (itemid, categoryid, title, summary, display_summary, body, uid, datesub, `status`, image, counter, weight, dohtml, dosmiley, doxcode, doimage, dobr, cancomment, comments, notifypub) VALUES ('', %u, %s, %s, %u, %s, %u, %u, %u, %s, %s, %u, %u, %u, %u, %u, %u, %u, %u, %u)", $this->db->prefix('smartsection_items'), $categoryid, $this->db->quoteString($title), $this->db->quoteString($summary), $display_summary, $this->db->quoteString($body), $uid, $datesub, $status, $this->db->quoteString($image), $counter, $weight, $dohtml, $dosmiley, $doxcode, $doimage, $dobr, $cancomment, $comments, $notifypub);
		} else {
			$sql = sprintf("UPDATE %s SET categoryid = %u, title = %s, summary = %s, display_summary = %u, body = %s, uid = %u, datesub = %u, `status` = %u, image = %s, counter = %u, weight = %u, dohtml = %u, dosmiley = %u, doxcode = %u, doimage = %u, dobr = %u, cancomment = %u, comments = %u, notifypub = %u WHERE itemid = %u", $this->db->prefix('smartsection_items'), $categoryid, $this->db->quoteString($title), $this->db->quoteString($summary), $display_summary, $this->db->quoteString($body), $uid, $datesub, $status, $this->db->quoteString($image), $counter, $weight, $dohtml, $dosmiley, $doxcode, $doimage, $dobr, $cancomment, $comments, $notifypub, $itemid);
		}

		//echo "<br />" . $sql . "<br />";
		
		if (false != $force) {
			$result = $this->db->queryF($sql);
		} else {
			$result = $this->db->query($sql);
		}

		if (!$result) {
			$item->setErrors('The query returned an error.');
			return false;
		}
		if ($item->isNew()) {
			$item->assignVar('id', $this->db->getInsertId());
		}

		// Saving permissions
		ss_saveItemPermissions($item->getGroups_read(), $item->itemid());
		
		return true;
	}
	
	function delete(&$item, $force = false)
	{
	    $hModule =& xoops_gethandler('module');
    	$smartModule =& $hModule->getByDirname('fnMangosAdmin');
    	$module_id = $smartModule->getVar('mid');
		
		if (strtolower(get_class($item)) != 'fnShop') {
			return false;
		}
		
		// Deleting the files
		global $smartsection_file_handler;
		If (!$smartsection_file_handler->deleteItemFiles($item)) {
			$item->setErrors('An error while deleting a file.');
		}
				
		$sql = sprintf("DELETE FROM %s WHERE id = %u", $this->db->prefix("fnma_shop_items"), $item->itemid());

		if (false != $force) {
			$result = $this->db->queryF($sql);
		} else {
			$result = $this->db->query($sql);
		}
		if (!$result) {
			$item->setErrors('An error while deleting.');
			return false;
		}
		
		xoops_groupperm_deletebymoditem ($module_id, "item_read", $item->is());
		return true;
	}
	
	function &getObjects($criteria = null, $id_as_key = false, $notNullFields='')
	{
		$ret = false;
		$limit = $start = 0;
		$sql = 'SELECT * FROM '.$this->db->prefix('fnma_shop_items');
		
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
			$shop = new fnShop();
			$shop->assignVars($myrow);
			//$shop->assignOtherProperties();
			
			if (!$id_as_key) {
				$ret[] =& $shop;
			} else {
				$ret[$myrow['id']] =& $shop;
			}
			unset($shop);
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
	
	function getItemsCount($categoryid=-1, $status='', $notNullFields='')
	{

		global $xoopsUser;
		
	//	If ( ($categoryid = -1) && (empty($status) || ($status == -1)) ) {
			//return $this->getCount();
		//}

	    $hModule =& xoops_gethandler('module');
	    $hModConfig =& xoops_gethandler('config');
    	$smartModule =& $hModule->getByDirname('smartsection');
    	$module_id = $smartModule->getVar('mid');
		
		$gperm_handler = &xoops_gethandler('groupperm');
		$groups = ($xoopsUser) ? ($xoopsUser->getGroups()) : XOOPS_GROUP_ANONYMOUS;
		
		$ret = array();
		
		$userIsAdmin = ss_userIsAdmin();
		// Categories for which user has access
		if (!$userIsAdmin) {
			$categoriesGranted = $gperm_handler->getItemIds('category_read', $groups, $module_id);
			$grantedCategories = new Criteria('categoryid', "(".implode(',', $categoriesGranted).")", 'IN');
		}
		// ITEMs for which user has access
		if (!$userIsAdmin) {
			$itemsGranted = $gperm_handler->getItemIds('item_read', $groups, $module_id);
			$grantedItem = new Criteria('itemid', "(".implode(',', $itemsGranted).")", 'IN');
		}
			
		If (isset($categoryid) && ($categoryid != -1)) {
			$criteriaCategory = new criteria('categoryid', $categoryid);
		}

		$criteriaStatus = new CriteriaCompo();
		If ( !empty($status) && (is_array($status)) ) {
			foreach ($status as $v) {
				$criteriaStatus->add(new Criteria('status', $v), 'OR');
			}
		} elseif ( !empty($status) && ($status != -1)) {
			$criteriaStatus->add(new Criteria('status', $status), 'OR');
		}

		$criteriaPermissions = new CriteriaCompo();
		if (!$userIsAdmin) {
			$criteriaPermissions->add($grantedCategories, 'AND');
			$criteriaPermissions->add($grantedItem, 'AND');
		}
		
		$criteria = new CriteriaCompo();
		If (!empty($criteriaCategory)) {
			$criteria->add($criteriaCategory);
		}
		
		If (!empty($criteriaPermissions) && (!$userIsAdmin)) {
			$criteria->add($criteriaPermissions);
		}		

		if (!empty($criteriaStatus)) {
			$criteria->add($criteriaStatus);		
		}
		
		if (!empty($otherCriteria)) {
			$criteria->add($otherCriteria);		
		}

		return $this->getCount($criteria, $notNullFields);
	}	

	function getAllPublished($limit=0, $start=0, $categoryid=-1, $sort='datesub', $order='DESC', $notNullFields='', $asobject=true, $id_as_key=false)
	{
		return $this->getItems($limit, $start, array(_SS_STATUS_PUBLISHED), $categoryid, $sort, $order, $notNullFields, $asobject, null, $id_as_key);
	}
	
	function getItems($limit=0, $start=0, $status='', $categoryid=-1, $sort='datesub', $order='DESC', $notNullFields='', $asobject=true, $otherCriteria=null, $id_as_key=false)
	{
		include_once XOOPS_ROOT_PATH.'/modules/smartsection/include/functions.php';
		
		global $xoopsUser;

		//if ( ($categoryid == -1) && (empty($status) || ($status == -1)) && ($limit == 0) && ($start ==0) ) {
		//	return $this->getObjects();
		//}

	    $hModule =& xoops_gethandler('module');
	    $hModConfig =& xoops_gethandler('config');
    	$smartModule =& $hModule->getByDirname('smartsection');
    	$module_id = $smartModule->getVar('mid');
		
		$gperm_handler = &xoops_gethandler('groupperm');
		$groups = ($xoopsUser) ? ($xoopsUser->getGroups()) : XOOPS_GROUP_ANONYMOUS;
		
		$ret = array();
		
		$userIsAdmin = ss_userIsAdmin();
		// Categories for which user has access
		if (!$userIsAdmin) {
			$categoriesGranted = $gperm_handler->getItemIds('category_read', $groups, $module_id);
			$grantedCategories = new Criteria('categoryid', "(".implode(',', $categoriesGranted).")", 'IN');
		}
		// ITEMs for which user has access
		if (!$userIsAdmin) {
			$itemsGranted = $gperm_handler->getItemIds('item_read', $groups, $module_id);
			$grantedItem = new Criteria('itemid', "(".implode(',', $itemsGranted).")", 'IN');
		}
			
		If (isset($categoryid) && ($categoryid != -1)) {
			$criteriaCategory = new criteria('categoryid', $categoryid);
		}

		If ( !empty($status) && (is_array($status)) ) {
			$criteriaStatus = new CriteriaCompo();
			foreach ($status as $v) {
				$criteriaStatus->add(new Criteria('status', $v), 'OR');
			}
		} elseif ( !empty($status) && ($status != -1)) {
			$criteriaStatus = new CriteriaCompo();
			$criteriaStatus->add(new Criteria('status', $status), 'OR');
		}

		$criteriaPermissions = new CriteriaCompo();
		if (!$userIsAdmin) {
			$criteriaPermissions->add($grantedCategories, 'AND');
			$criteriaPermissions->add($grantedItem, 'AND');
		}
		
		$criteria = new CriteriaCompo();
		If (!empty($criteriaCategory)) {
			$criteria->add($criteriaCategory);
		}
		
		If (!empty($criteriaPermissions) && (!$userIsAdmin)) {
			$criteria->add($criteriaPermissions);
		}		

		if (!empty($criteriaStatus)) {
			$criteria->add($criteriaStatus);		
		}
		
		if (!empty($otherCriteria)) {
			$criteria->add($otherCriteria);		
		}

		$criteria->setLimit($limit);
		$criteria->setStart($start);
		$criteria->setSort($sort);
		$criteria->setOrder($order);
		$ret =& $this->getObjects($criteria, $id_as_key, $notNullFields);
		
		return $ret;
	}		
	
	
	function getRandomItem($field='', $status='', $categoryId=-1)
	{
		$ret = false;

		$notNullFields = $field;
		
		// Getting the number of published Items   
		$totalItems = $this->getItemsCount($categoryId, $status, $notNullFields);
		
		if ($totalItems > 0) {
			$totalItems = $totalItems - 1;
        	mt_srand((double)microtime() * 1000000);
        	$entrynumber = mt_rand(0, $totalItems); 
        	$item =& $this->getItems(1, $entrynumber, $status, $categoryId, $sort='datesub', $order='DESC', $notNullFields);
			If ($item) {
				$ret =& $item[0];
			}
		}	
		return $ret;
		
	}
	
	/**
	* delete Items matching a set of conditions
	*
	* @param object $criteria {@link CriteriaElement}
	* @return bool FALSE if deletion failed
	*/
	function deleteAll($criteria = null)
	{
		$sql = 'DELETE FROM '.$this->db->prefix('smartsection_items');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		if (!$result = $this->db->query($sql)) {
			return false;
			// TODO : Also delete the permissions related to each ITEM
		}
		return true;
	}
	
	/**
	* Change a value for Item with a certain criteria
	*
	* @param   string  $fieldname  Name of the field
	* @param   string  $fieldvalue Value to write
	* @param   object  $criteria   {@link CriteriaElement}
	*
	* @return  bool
	**/
	function updateAll($fieldname, $fieldvalue, $criteria = null)
	{
		$set_clause = is_numeric($fieldvalue) ? $fieldname.' = '.$fieldvalue : $fieldname.' = '.$this->db->quoteString($fieldvalue);
		$sql = 'UPDATE '.$this->db->prefix('smartsection_items').' SET '.$set_clause;
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		if (!$result = $this->db->queryF($sql)) {
			return false;
		}
		return true;
	}
	
	function updateCounter($itemid)
	{
		$sql = "UPDATE " . $this->db->prefix("smartsection_items") . " SET counter=counter+1 WHERE itemid = " . $itemid;
		If ($this->db->queryF($sql)) {
			return true;
		} else {
			return false;
		}	
	}
	
	function NotNullFieldClause($notNullFields='', $withAnd=false)
	{
		$ret = '';
		If ($withAnd) {
			$ret .= " AND ";
		}
		If ( !empty($notNullFields) && (is_array($notNullFields)) ) {
			foreach ($notNullFields as $v) {
				$ret .= " ($v IS NOT NULL AND $v <> ' ' )";
			}
		} elseif ( !empty($notNullFields)) {
			$ret .= " ($notNullFields IS NOT NULL AND $notNullFields <> ' ' )";
		}
		return $ret;
	}
	
	function getItemsFromSearch($queryarray = array(), $andor = 'AND', $limit = 0, $offset = 0, $userid = 0)
	{
	
	Global $xoopsUser;
	
	$ret = array();
		
	$hModule =& xoops_gethandler('module');
	$hModConfig =& xoops_gethandler('config');
	$smartModule =& $hModule->getByDirname('smartsection');
	$module_id = $smartModule->getVar('mid');

	$gperm_handler = &xoops_gethandler('groupperm');
	$groups = ($xoopsUser) ? ($xoopsUser->getGroups()) : XOOPS_GROUP_ANONYMOUS;
	$userIsAdmin = ss_userIsAdmin();
	

	if ($userid != 0) {
		$criteriaUser = new CriteriaCompo();
		$criteriaUser->add(new Criteria('item.uid', $userid), 'OR');
	}

	If ($queryarray) {
		$criteriaKeywords = new CriteriaCompo();	
		for ($i = 0; $i < count($queryarray); $i++) {
			$criteriaKeyword = new CriteriaCompo();
			$criteriaKeyword->add(new Criteria('item.title', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
			$criteriaKeyword->add(new Criteria('item.body', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
			$criteriaKeyword->add(new Criteria('item.summary', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
			$criteriaKeywords->add($criteriaKeyword, $andor);
			unset($criteriaKeyword);
		}
	}

	// Categories for which user has access
	if (!$userIsAdmin) {
		$categoriesGranted = $gperm_handler->getItemIds('category_read', $groups, $module_id);
		If (!$categoriesGranted) {
			return $ret;
		}		
		$grantedCategories = new Criteria('item.categoryid', "(".implode(',', $categoriesGranted).")", 'IN');
	}
	// items for which user has access
	if (!$userIsAdmin) {
		$itemsGranted = $gperm_handler->getItemIds('item_read', $groups, $module_id);
		If (!$itemsGranted) {
			return $ret;
		}	
		$grantedItem = new Criteria('item.itemid', "(".implode(',', $itemsGranted).")", 'IN');
	}
			
	$criteriaPermissions = new CriteriaCompo();
	if (!$userIsAdmin) {
		$criteriaPermissions->add($grantedCategories, 'AND');
		$criteriaPermissions->add($grantedItem, 'AND');
	}
	
	$criteriaItemsStatus = new CriteriaCompo();
	$criteriaItemsStatus->add(new Criteria('item.status', _SS_STATUS_PUBLISHED, 'OR'));	
		
	$criteria = new CriteriaCompo();	
	If (!empty($criteriaUser)) {
		$criteria->add($criteriaUser, 'AND');
	}
	
	If (!empty($criteriaKeywords)) {
		$criteria->add($criteriaKeywords, 'AND');
	}	
	
	If (!empty($criteriaPermissions) && (!$userIsAdmin)) {
		$criteria->add($criteriaPermissions);
	}		
	
	If (!empty($criteriaItemsStatus)) {
		$criteria->add($criteriaItemsStatus, 'AND');
	}		

	$criteria->setLimit($limit);
	$criteria->setStart($offset);
	$criteria->setSort('item.datesub');
	$criteria->setOrder('DESC');


	$sql = 'SELECT item.itemid, item.title, item.datesub, item.uid FROM ('.$this->db->prefix('smartsection_items') . ' as item) ';
		
	if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
		$whereClause = $criteria->renderWhere();
			
		If ($whereClause != 'WHERE ()') {
			$sql .= ' '.$criteria->renderWhere();
			if ($criteria->getSort() != '') {
				$sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
			}
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
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
		$item['id'] = $myrow['itemid'];
		$item['title'] = $myrow['title'];
		$item['datesub'] = $myrow['datesub'];
		$item['uid'] = $myrow['uid'];
		$ret[] = $item;
		unset($item);
	}
	return $ret;
	}
	
	function getLastPublishedByCat($status = array(_SS_STATUS_PUBLISHED)) {
		
		$ret = array();
	    $itemclause = "";
   	    if (!ss_userIsAdmin()) {
	        $smartsectionPermHandler =& xoops_getmodulehandler('permission', 'smartsection');
	        $items = $smartsectionPermHandler->getGrantedItems('item');
	        $itemclause = " AND itemid IN (".implode(',', $items).")";
	    }

	    $sql = "CREATE TEMPORARY TABLE tmp (categoryid INT(8) UNSIGNED NOT NULL,datesub int(11) DEFAULT '0' NOT NULL);";
	    $sql2 = " LOCK TABLES ".$this->db->prefix('smartsection_items')." READ;";
	    $sql3 = " INSERT INTO tmp SELECT categoryid, MAX(datesub) FROM ".$this->db->prefix('smartsection_items')." WHERE status IN (". implode(',', $status).") $itemclause GROUP BY categoryid;";
	    $sql4 = " SELECT ".$this->db->prefix('smartsection_items').".categoryid, itemid, title, uid, ".$this->db->prefix('smartsection_items').".datesub FROM ".$this->db->prefix('smartsection_items').", tmp
	                  WHERE ".$this->db->prefix('smartsection_items').".categoryid=tmp.categoryid AND ".$this->db->prefix('smartsection_items').".datesub=tmp.datesub;";
        /*
	    //Old implementation
	    $sql = "SELECT categoryid, itemid, question, uid, MAX(datesub) AS datesub FROM ".$this->db->prefix("smartitem_item")." 
	           WHERE status IN (". implode(',', $status).")";
	    $sql .= " GROUP BY categoryid";
	    */
	    $this->db->queryF($sql);
	    $this->db->queryF($sql2);
	    $this->db->queryF($sql3);
	    $result = $this->db->query($sql4);
	    $error = $this->db->error();
	    $this->db->queryF("UNLOCK TABLES;");
	    $this->db->queryF("DROP TABLE tmp;");
	    if (!$result) {
	        trigger_error("Error in getLastPublishedByCat SQL: ".$error);
	        return $ret;
	    }
		while ($row = $this->db->fetchArray($result)) {
		    $item = new fnShop();
			$item->assignVars($row);
			$ret[$row['categoryid']] =& $item;
			unset($item);
		}
		return $ret;
	}	
	
	function getCountsByCat($cat_id = 0, $status) {
	    $ret = array();
	    $sql = 'SELECT categoryid, COUNT(*) AS count FROM '.$this->db->prefix('smartsection_items');
	    if (intval($cat_id) > 0) {
	        $sql .= ' WHERE categoryid = '.intval($cat_id);
	        $sql .= ' AND status IN ('.implode(',', $status).')';
	    }
	    else {
	        $sql .= ' WHERE status IN ('.implode(',', $status).')';
	        if (!ss_userIsAdmin()) {
	            $smartsectionPermHandler =& xoops_getmodulehandler('permission', 'smartsection');
	            $items = $smartsectionPermHandler->getGrantedItems('item');
	            $sql .= ' AND itemid IN ('.implode(',', $items).')';
	        }
	    }
	    $sql .= ' GROUP BY categoryid';

		$result = $this->db->query($sql);
		if (!$result) {
			return $ret;
		}
		while ($row = $this->db->fetchArray($result)) {
		    $ret[$row['categoryid']] = intval($row['count']);
		}
	    return $ret;
	}	
}
?>