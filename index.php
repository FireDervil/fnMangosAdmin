<?php

include_once("header.php");

include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'stories.php';

xoops_loadLanguage('main', 'fnMangosAdmin');
$xoopsModuleConfig['displaynav'] = 1;
$xoopsModuleConfig['storyhome'] = 5;



$xoopsOption['template_main']= 'fnma_index.html';
include XOOPS_ROOT_PATH."/header.php";
if (isset($HTTP_GET_VARS['storytopic'])) {
	$xoopsOption['storytopic'] = intval($HTTP_GET_VARS['storytopic']);
} else {
	$xoopsOption['storytopic'] = 0;
}
if ( isset($HTTP_GET_VARS['storynum']) ) {
	$xoopsOption['storynum'] = intval($HTTP_GET_VARS['storynum']);
	if ($xoopsOption['storynum'] > 30) {
		$xoopsOption['storynum'] = $xoopsModuleConfig['storyhome'];
	}
} else {
	$xoopsOption['storynum'] = $xoopsModuleConfig['storyhome'];
}

if ( isset($HTTP_GET_VARS['start']) ) {
	$start = intval($HTTP_GET_VARS['start']);
} else {
	$start = 0;
}
if ( $xoopsModuleConfig['displaynav'] == 1 ) {
	$xoopsTpl->assign('displaynav', true);
	$xt = new XoopsTopic($xoopsDB->prefix('fnma_news_topics'));

	ob_start();
	$xt->makeTopicSelBox(1, $xoopsOption['storytopic'], 'storytopic');
	$topic_select = ob_get_contents();
	ob_end_clean();
	$xoopsTpl->assign('topic_select', $topic_select);
	$storynum_options = '';
	for ( $i = 5; $i <= 30; $i = $i + 5 ) {
		$sel = '';
		if ($i == $xoopsOption['storynum']) {
			$sel = ' selected="selected"';
		}
		$storynum_options .= '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
	}
	$xoopsTpl->assign('storynum_options', $storynum_options);
} else {
	$xoopsTpl->assign('displaynav', false);
}

$sarray = fnmaNews::getAllPublished($xoopsOption['storynum'], $start, $xoopsOption['storytopic']);

$scount = count($sarray);
for ( $i = 0; $i < $scount; $i++ ) {
	$story = array();
	$story['id'] = $sarray[$i]->storyid();
	$story['poster'] = $sarray[$i]->uname();
	if ( $story['poster'] != false ) {
		$story['poster'] = "<a href='".XOOPS_URL."/userinfo.php?uid=".$sarray[$i]->uid()."'>".$story['poster']."</a>";
	} else {
		$story['poster'] = $xoopsConfig['anonymous'];
	}
	$story['posttime'] = formatTimestamp($sarray[$i]->published());
	$story['text'] = $sarray[$i]->hometext();
	$introcount = strlen($story['text']);
	$fullcount = strlen($sarray[$i]->bodytext());
	$totalcount = $introcount + $fullcount;
	$morelink = '';
	if ( $fullcount > 1 ) {
		$morelink .= '<a href="'.XOOPS_URL.'/modules/fnMangosAdmin/news.php?storyid='.$sarray[$i]->storyid().'';
		$morelink .= '">'._FNMA_READ_MORE.'</a> | ';
		$morelink .= sprintf(_FNMA_BYTES_MORE,$totalcount);
		$morelink .= ' | ';
	}
	$ccount = $sarray[$i]->comments();
	$morelink .= '<a href="'.XOOPS_URL.'/modules/fnMangosAdmin/news.php?storyid='.$sarray[$i]->storyid().'';
    $morelink2 = '<a href="'.XOOPS_URL.'/modules/fnMangosAdmin/news.php?storyid='.$sarray[$i]->storyid().'';
	if ( $ccount == 0 ) {
		$morelink .= '">'._FNMA_COMMENTS.'</a>';
	} else {
		if ( $fullcount < 1 ) {
			if ( $ccount == 1 ) {
				$morelink .= '">'._FNMA_READMORE.'</a> | '.$morelink2.'">'._FNMA_ONECOMMENT.'</a>';
			} else {
				$morelink .= '">'._FNMA_READMORE.'</a> | '.$morelink2.'">';
				$morelink .= sprintf(_FNMA_NUMCOMMENTS, $ccount);
				$morelink .= '</a>';
			}
		} else {
			if ( $ccount == 1 ) {
				$morelink .= '">'._FNMA_ONECOMMENT.'</a>';
			} else {
				$morelink .= '">';
				$morelink .= sprintf(_FNMA_NUMCOMMENTS, $ccount);
				$morelink .= '</a>';
			}
		}
	}
	$story['morelink'] = $morelink;
	$story['adminlink'] = '';
	if ( $xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid()) ) {
		$story['adminlink'] = $sarray[$i]->adminlink();
	}
    $story['mail_link'] = 'mailto:?subject='.sprintf(_FNMA_INTARTICLE,$xoopsConfig['sitename']).'&amp;body='.sprintf(_FNMA_INTARTFOUND, $xoopsConfig['sitename']).':  '.XOOPS_URL.'/modules/fnMangosAdmin/news.php?storyid='.$sarray[$i]->storyid();
	$story['imglink'] = '';
	$story['align'] = '';
	if ( $sarray[$i]->topicdisplay() ) {
		$story['imglink'] = $sarray[$i]->imglink();
		$story['align'] = $sarray[$i]->topicalign();
	}
	$story['title'] = $sarray[$i]->textlink().'&nbsp;:&nbsp;'."<a href='".XOOPS_URL."/modules/fnMangosAdmin/news.php?storyid=".$sarray[$i]->storyid()."'>".$sarray[$i]->title()."</a>";
	$story['hits'] = $sarray[$i]->counter();
	// The line below can be used to display a Permanent Link image
    // $story['title'] .= "&nbsp;&nbsp;<a href='".XOOPS_URL."/modules/news2/article.php?storyid=".$sarray[$i]->storyid()."'><img src='".XOOPS_URL."/modules/news2/images/x.gif' alt='Permanent Link' /></a>";

	$xoopsTpl->append('stories', $story);
	unset($story);
}
$totalcount = fnmaNews::countPublishedByTopic($xoopsOption['storytopic']);
if ( $totalcount > $scount ) {
	include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
	$pagenav = new XoopsPageNav($totalcount, $xoopsOption['storynum'], $start, 'start', 'storytopic='.$xoopsOption['storytopic']);
	$xoopsTpl->assign('pagenav', $pagenav->renderNav());
	//$xoopsTpl->assign('pagenav', $pagenav->renderImageNav());

} else {
	$xoopsTpl->assign('pagenav', '');
}
$xoopsTpl->assign('lang_go', _GO);
$xoopsTpl->assign('lang_on', _ON);
$xoopsTpl->assign('lang_printerpage', _FNMA_PRINTER_FRIENDLY);
$xoopsTpl->assign('lang_sendstory', _FNMA_SEND_NEWS);
$xoopsTpl->assign('lang_postedby', _POSTED_BY);
$xoopsTpl->assign('lang_reads', _READS);
include_once XOOPS_ROOT_PATH.'/footer.php';
?>