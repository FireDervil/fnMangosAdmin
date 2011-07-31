<?php

include 'header.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'stories.php';
xoops_loadLanguage('news', 'fnMangosAdmin');

$storyid = (isset($HTTP_GET_VARS['storyid'])) ? $HTTP_GET_VARS['storyid'] : 0;
$storyid = intval($storyid);
if (empty($storyid)) {
	redirect_header("index.php",2, _FNMA_NO_STORY);
	exit();
}

$xoopsOption['template_main'] = 'fnma_news_article.html';

include_once XOOPS_ROOT_PATH.'/header.php';
$myts =& MyTextSanitizer::getInstance();
// set comment mode if not set


$article = new fnmaNews($storyid);
if ( $article->published() == 0 || $article->published() > time() ) {
	redirect_header('index.php', 2, _FNMA_NOSTORY);
	exit();
}
$storypage = isset($HTTP_GET_VARS['page']) ? intval($HTTP_GET_VARS['page']) : 0;
// update counter only when viewing top page
if (empty($HTTP_GET_VARS['com_id']) && $storypage == 0) {
	$article->updateCounter();
}
$story['id'] = $storyid;
$story['posttime'] = formatTimestamp($article->published());
$story['title'] = $article->textlink()."&nbsp;:&nbsp;".$article->title();
$story['text'] = $article->hometext();
$bodytext = $article->bodytext();

if ( trim($bodytext) != '' ) {
	$articletext = explode("[pagebreak]", $bodytext);
	$story_pages = count($articletext);

	if ($story_pages > 1) {
		include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
		$pagenav = new XoopsPageNav($story_pages, 1, $storypage, 'page', 'storyid='.$storyid);
		$xoopsTpl->assign('pagenav', $pagenav->renderNav());
		//$xoopsTpl->assign('pagenav', $pagenav->renderImageNav());

		if ($storypage == 0) {
	    	$story['text'] = $story['text'].'<br /><br />'.$articletext[$storypage];
		} else {
			$story['text'] = $articletext[$storypage];
		}
	} else {
		$story['text'] = $story['text'].'<br /><br />'.$bodytext;
	}
}

$story['poster'] = $article->uname();
if ( $story['poster'] ) {
	$story['posterid'] = $article->uid();
	$story['poster'] = '<a href="'.XOOPS_URL.'/userinfo.php?uid='.$story['posterid'].'">'.$story['poster'].'</a>';
} else {
	$story['poster'] = '';
	$story['posterid'] = 0;
	$story['poster'] = $xoopsConfig['anonymous'];
}
$story['morelink'] = '';
$story['adminlink'] = '';
unset($isadmin);
if ( $xoopsUser && $xoopsUser->isAdmin($xoopsModule->getVar('mid')) ) {
	$isadmin = true;
	$story['adminlink'] = $article->adminlink();
}
$story['topicid'] = $article->topicid();
$story['imglink'] = '';
$story['align'] = '';
if ( $article->topicdisplay() ) {
	$story['imglink'] = $article->imglink();
	$story['align'] = $article->topicalign();
}
$story['hits'] = $article->counter();
$story['mail_link'] = 'mailto:?subject='.sprintf(_FNMA_INTARTICLE,$xoopsConfig['sitename']).'&amp;body='.sprintf(_FNMA_INTARTFOUND, $xoopsConfig['sitename']).':  '.XOOPS_URL.'/modules/fnMangosAdmin/news.php?storyid='.$article->storyid();
$xoopsTpl->assign('story', $story);
$xoopsTpl->assign('lang_printerpage', _FNMA_PRINTERFRIENDLY);
$xoopsTpl->assign('lang_sendstory', _FNMA_SENDSTORY);
$xoopsTpl->assign('lang_on', _ON);
$xoopsTpl->assign('lang_postedby', _POSTEDBY);
$xoopsTpl->assign('lang_reads', _READS);
$xoopsTpl->assign('mail_link', 'mailto:?subject='.sprintf(_FNMA_INTARTICLE,$xoopsConfig['sitename']).'&amp;body='.sprintf(_FNMA_INTARTFOUND, $xoopsConfig['sitename']).':  '.XOOPS_URL.'/modules/fnMangosAdmin/news.php?storyid='.$article->storyid());


include XOOPS_ROOT_PATH.'/footer.php';
?>













