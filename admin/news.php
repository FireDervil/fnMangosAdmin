<?php

include("header.php");
include_once XOOPS_ROOT_PATH . "/class/xoopstopic.php";
include_once XOOPS_ROOT_PATH . "/class/xoopslists.php";
include_once XOOPS_ROOT_PATH . "/modules/fnMangosAdmin/class/stories.php";

fnma_AdminLoadLanguage('news', 'fnMangosAdmin');

//xoops_cp_header();

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();

}
$op = !empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"default");

 if(isset($HTTP_GET_VARS['storyid']))
 {
	 $storyid = intval($HTTP_GET_VARS['storyid']);
 } 

// Shows last 10 published stories
function lastNews()
{
    global $xoopsDB, $xoopsConfig, $xoopsModule;
    echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
    echo "<div style='text-align: center;'><b>" . _AM_FNMA_LAST10ARTS . "</b><br />";
    $newsarray = fnmaNews :: getAllPublished( 10, 0, 0, 1 );
    echo "<table border='1' width='100%'><tr class='bg3'><td align='center'>" . __AM_FNMA_STORYID . "</td><td align='center'>" . _AM_FNMA_TITLE . "</td><td align='center'>" . _AM_FNMA_TOPIC . "</td><td align='center'>" . _AM_FNMA_POSTER . "</td><td align='center' class='nw'>" . _AM_FNMA_PUBLISHED . "</td><td align='center' class='nw'>" . _AM_FNMA_EXPIRED . "</td><td align='center'>" . _AM_FNMA_ACTION . "</td></tr>";
    foreach( $newsarray as $eachnews )
    {
        $published = formatTimestamp( $eachnews -> published() );
        $expired = ( $eachnews -> expired() > 0 ) ? formatTimestamp( $eachnews -> expired() ) : '---';
        $topic = $eachnews -> topic();
        echo "
        	<tr><td align='center'><b>" . $eachnews -> storyid() . "</b>
        	</td><td align='left'><a href='" . XOOPS_URL . "/modules/" . $xoopsModule -> dirname() . "/news.php?storyid=" . $eachnews -> storyid() . "'>" . $eachnews -> title() . "</a>
        	</td><td align='center'>" . $topic -> topic_title() . "
        	</td><td align='center'><a href='" . XOOPS_URL . "/userinfo.php?uid=" . $eachnews -> uid() . "'>" . $eachnews -> uname() . "</a></td><td align='center' class='nw'>" . $published . "</td><td align='center'>" . $expired . "</td><td align='center'><a href='news.php?op=edit&amp;storyid=" . $eachnews -> storyid() . "'>" . _AM_FNMA_EDIT . "</a>-<a href='news.php?op=delete&amp;storyid=" . $eachnews -> storyid() . "'>" . _AM_FNMA_DELETE . "</a>";
        echo "</td></tr>\n";
    } 
    echo "</table><br />";
    echo "<form action='news.php' method='post'>
    	" . _AM_FNMA_STORYID . " <input type='text' name='storyid' size='10' />
    	<select name='op'>
    	<option value='edit' selected='selected'>" . _AM_FNMA_EDIT . "</option>
    	<option value='delete'>" . _AM_FNMA_DELETE . "</option>
    	</select>
    	<input type='submit' value='" . _AM_FNMA_GO . "' />
    	</form>
	</div>
    	";
    echo"</td></tr></table>";
}
 

// Show new submissions
function showNewNews()
{
    global $xoopsConfig, $xoopsDB;
    $newsarray = fnmaNews :: getAllSubmitted();
    if ( count( $newsarray ) > 0 )
    {
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo "<div style='text-align: center;'><b>" . _AM_FNMA_NEWSUB . "</b><br /><table width='100%' border='1'><tr class='bg2'><td align='center'>" . _AM_FNMA_TITLE . "</td><td align='center'>" . _AM_FNMA_POSTED . "</td><td align='center'>" . _AM_FNMA_POSTER . "</td><td align='center'>" . _AM_FNMA_ACTION . "</td></tr>\n";
        foreach( $newsarray as $newnews )
        {
            echo "<tr><td>\n";
            $title = $newnews->title();
            if ( !isset( $title ) || ( $title == "" ) )
            {
                echo "<a href='news.php?op=edit&amp;storyid=" . $newnews->storyid() . "'>" . _AD_NOSUBJECT . "</a>\n";
            } 
            else
            {
                echo "&nbsp;<a href='news.php?op=edit&amp;storyid=" . $newnews->storyid() . "'>" . $title . "</a>\n";
            } 
            echo "</td><td align='center' class='nw'>" . formatTimestamp( $newnews->created() ) . "</td><td align='center'><a href='" . XOOPS_URL . "/userinfo.php?uid=" . $newnews->uid() . "'>" . $newnews -> uname() . "</a></td><td align='right'><a href='news.php?op=delete&amp;storyid=" . $newnews->storyid() . "'>" . _AM_FNMA_DELETE . "</a></td></tr>\n";
        } 
        echo "</table></div>\n";
        echo"</td></tr></table>";
        echo "<br />";
    } 
}  
 
 // Added function to display expired stories
function expiredNews()
{
    global $xoopsDB, $xoopsConfig, $xoopsModule;
    echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
    echo "<div style='text-align: center;'><b>" . _AM_FNMA_EXPARTS . "</b><br />";
    $newsrray = fnmaNews :: getAllExpired( 10, 0, 0, 1 );
    echo "<table border='1' width='100%'><tr class='bg3'><td align='center'>" . _AM_FNMA_NEWSID . "</td><td align='center'>" . _AM_FNMA_TITLE . "</td><td align='center'>" . _AM_FNMA_TOPIC . "</td><td align='center'>" . _AM_FNMA_POSTER . "</td><td align='center' class='nw'>" . _AM_FNMA_PUBLISHED . "</td><td align='center' class='nw'>" . _AM_FNMA_EXPIRED . "</td><td align='center'>" . _AM_FNMA_ACTION . "</td></tr>";
    foreach( $newsrray as $eachnews )
    {
        $published = formatTimestamp( $eachnews -> published() );
        $expired = formatTimestamp( $eachnews -> expired() );
        $topic = $eachnews -> topic(); 
        // added exired value field to table
        echo "
        	<tr><td align='center'><b>" . $eachnews -> storyid() . "</b>
        	</td><td align='left'><a href='" . XOOPS_URL . "/modules/" . $xoopsModule -> dirname() . "/news.php?storyid=" . $eachnews -> storyid() . "'>" . $eachnews -> title() . "</a>
        	</td><td align='center'>" . $topic -> topic_title() . "
        	</td><td align='center'><a href='" . XOOPS_URL . "/userinfo.php?uid=" . $eachnews -> uid() . "'>" . $eachnews -> uname() . "</a></td><td align='center' class='nw'>" . $published . "</td><td align='center' class='nw'>" . $expired . "</td><td align='center'><a href='news.php?op=edit&amp;storyid=" . $eachnews -> storyid() . "'>" ._AM_FNMA_EDIT . "</a>-<a href='news.php?op=delete&amp;storyid=" . $eachnews -> storyid() . "'>" . _AM_FNMA_DELETE . "</a>";
        echo "</td></tr>\n";
    } 
    echo "</table><br />";
    echo "<form action='news.php' method='post'>
    	" . _AM_FNMA_STORYID . " <input type='text' name='storyid' size='10' />
    	<select name='op'>
    	<option value='edit' selected='selected'>" . _AM_FNMA_EDIT . "</option>
    	<option value='delete'>" ._AM_FNMA_DELETE . "</option>
    	</select>
    	<input type='submit' value='" . _AM_FNMA_GO . "' />
    	</form>
	</div>
    	";
    echo"</td></tr></table>";
} 
 
function topicsmanager()
{
    global $xoopsDB, $xoopsConfig, $xoopsModule;
    xoops_cp_header();
    echo "<h4>" . _AM_FNMA_CONFIG . "</h4>";
    $xt = new XoopsTopic( $xoopsDB -> prefix( "topics2" ) );
    $topics_array = XoopsLists :: getImgListAsArray( XOOPS_ROOT_PATH . "/modules/news2/images/topics/" ); 
    // $xoopsModule->printAdminMenu();
    // echo "<br />";
    // Add a New Main Topic
    echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
    echo "<form method='post' action='news.php'>\n";
    echo "<h4>" . _AM_FNMA_ADDMTOPIC . "</h4><br />";
    echo "<b>" . _AM_FNMA_TOPICNAME . "</b> " . _AM_FNMA_MAX40CHAR . "<br /><input type='text' name='topic_title' size='20' maxlength='20' /><br />";
    echo "<b>" . _AM_FNMA_TOPICIMG . "</b> (" . sprintf( _AM_FNMA_IMGNAEXLOC, "modules/" . $xoopsModule -> dirname() . "/images/topics/" ) . ")<br />" . _AM_FNMA_FEXAMPLE . "<br />";
    echo "<select size='1' name='topic_imgurl'>";
    echo "<option value=' '>------</option>";
    foreach( $topics_array as $image )
    {
        echo "<option value='" . $image . "'>" . $image . "</option>";
    } 
    echo "</select><br /><br />";
    echo "<input type='hidden' name='topic_pid' value='0' />\n";
    echo "<input type='hidden' name='op' value='addTopic' />";
    echo "<input type='submit' value=" . _AM_FNMA_ADD . " /><br /></form>";
    echo"</td></tr></table>";
    echo "<br />";
    // Add a New Sub-Topic
    $result = $xoopsDB -> query( "select count(*) from " . $xoopsDB -> prefix( "topics2" ) . "" );
    list( $numrows ) = $xoopsDB -> fetchRow( $result );
    if ( $numrows > 0 )
    {
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo "<form method='post' action='news.php'>";
        echo "<h4>" . _AM_FNMA_ADDSUBTOPIC . "</h4><br />";
        echo "<b>" . _AM_FNMA_TOPICNAME . "</b> " . _AM_FNMA_MAX40CHAR . "<br /><input type='text' name='topic_title' size='20' maxlength='40' />&nbsp;" . _AM_FNMA_IN . "&nbsp;";
        $xt -> makeTopicSelBox( 0, 0, "topic_pid" );
        echo "<br />";
        echo "<b>" . _AM_FNMA_TOPICIMG . "</b> (" . sprintf( _AM_FNMA_IMGNAEXLOC, "modules/" . $xoopsModule -> dirname() . "/images/topics/" ) . ")<br />" . _AM_FNMA_FEXAMPLE . "<br />";
        echo "<select size='1' name='topic_imgurl'>";
        echo "<option value=' '>------</option>";
        foreach( $topics_array as $image )
        {
            echo "<option value='" . $image . "'>" . $image . "</option>";
        } 
        echo "</select><br /><br />";
        echo "<input type='hidden' name='op' value='addTopic' />";
        echo "<input type='submit' value='" . _AM_FNMA_ADD . "' /><br /></form>";
        echo"</td></tr></table>";
        echo "<br />";
        // Modify Topic
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo "
    		<form method='post' action='news.php'>
    		<h4>" . _AM_FNMA_MODIFYTOPIC . "</h4><br />";
        echo "<b>" . _AM_FNMA_TOPIC . "</b><br />";
        $xt -> makeTopicSelBox();
        echo "<br /><br />\n";
        echo "<input type='hidden' name='op' value='modTopic' />\n";
        echo "<input type='submit' value='" . _AM_FNMA_MODIFY . "' />\n";
        echo "</form>";
        echo"</td></tr></table>";
    } 
} 
 
function modTopic()
{
    global $xoopsDB, $HTTP_POST_VARS, $xoopsConfig;
    global $xoopsModule;
    $xt = new XoopsTopic( $xoopsDB -> prefix( "topics2" ), $HTTP_POST_VARS['topic_id'] );
    $topics_array = XoopsLists :: getImgListAsArray( XOOPS_ROOT_PATH . "/modules/news2/images/topics/" );
    xoops_cp_header();
    echo "<h4>" . _AM_FNMA_CONFIG . "</h4>"; 
    // $xoopsModule->printAdminMenu();
    // echo "<br />";
    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
    echo "<h4>" . _AM_FNMA_MODIFYTOPIC . "</h4><br />";
    if ( $xt -> topic_imgurl() )
    {
        echo "<div style='text-align: right;'><img src='" . XOOPS_URL . "/modules/" . $xoopsModule -> dirname() . "/images/topics/" . $xt -> topic_imgurl() . "'></div>";
    } 
    echo "<form action='news.php' method='post'>";
    echo "<b>" . _AM_FNMA_TOPICNAME . "</b>&nbsp;" . _AM_FNMA_MAX40CHAR . "<br /><input type='text' name='topic_title' size='20' maxlength='40' value='" . $xt -> topic_title('E') . "' /><br />";
    echo "<b>" . _AM_FNMA_TOPICIMG . "</b>&nbsp;(" . sprintf( _AM_FNMA_IMGNAEXLOC, "modules/" . $xoopsModule -> dirname() . "/images/topics/" ) . ")<br />" . _AM_FNMA_FEXAMPLE . "<br />"; 
    // echo "<input type='text' name='topic_imgurl' size='20' maxlength='20' value='".$xt->topic_imgurl()."' /><br />\n";
    echo "<select size='1' name='topic_imgurl'>";
    echo "<option value=' '>------</option>";
    foreach( $topics_array as $image )
    {
        if ( $image == $xt -> topic_imgurl() )
        {
            $opt_selected = "selected='selected'";
        } 
        else
        {
            $opt_selected = "";
        } 
        echo "<option value='" . $image . "' $opt_selected>" . $image . "</option>";
    } 
    echo "</select><br />";
    echo "<b>" . _AM_FNMA_PARENTTOPIC . "<b><br />\n";
    $xt -> makeTopicSelBox( 1, $xt -> topic_pid(), "topic_pid" );
    echo "\n<br /><br />";

    echo "<input type='hidden' name='topic_id' value='" . $xt -> topic_id() . "' />\n";
    echo "<input type='hidden' name='op' value='modTopicS' />";
    echo "<input type='submit' value='" . _AM_FNMA_SAVECHANGE . "' />&nbsp;<input type='button' value='" . _AM_FNMA_DEL . "' onclick='location=\"news.php?topic_pid=" . $xt -> topic_pid() . "&amp;topic_id=" . $xt -> topic_id() . "&amp;op=delTopic\"' />\n";
    echo "&nbsp;<input type='button' value='" . _AM_FNMA_CANCEL . "' onclick='javascript:history.go(-1)' />\n";
    echo "</form>";
    echo"</td></tr></table>";
} 

function modTopicS()
{
    global $xoopsDB, $HTTP_POST_VARS;
    $xt = new XoopsTopic( $xoopsDB -> prefix( "topics2" ), $HTTP_POST_VARS['topic_id'] );
    if ( $HTTP_POST_VARS['topic_pid'] == $HTTP_POST_VARS['topic_id'] )
    {
        echo "ERROR: Cannot select this topic for parent topic!";
        exit();
    } 
    $xt -> setTopicPid( $HTTP_POST_VARS['topic_pid'] );
    if ( empty( $HTTP_POST_VARS['topic_title'] ) )
    {
        redirect_header( "news.php?op=topicsmanager", 2, _AM_FNMA_ERRORTOPICNAME );
    } 
    $xt -> setTopicTitle( $HTTP_POST_VARS['topic_title'] );
    if ( isset( $HTTP_POST_VARS['topic_imgurl'] ) && $HTTP_POST_VARS['topic_imgurl'] != "" )
    {
        $xt -> setTopicImgurl( $HTTP_POST_VARS['topic_imgurl'] );
    } 
    $xt -> store();
    redirect_header( 'news.php?op=topicsmanager', 1, _AM_FNMA_DBUPDATED );
    exit();
} 

function delTopic()
{
    global $xoopsDB, $HTTP_POST_VARS, $HTTP_GET_VARS, $xoopsConfig, $xoopsModule;
    if ( $HTTP_POST_VARS['ok'] != 1 )
    {
        xoops_cp_header();
        echo "<h4>" . _AM_FNMA_CONFIG . "</h4>";
        xoops_confirm( array( 'op' => 'delTopic', 'topic_id' => intval( $HTTP_GET_VARS['topic_id'] ), 'ok' => 1 ), 'news.php', _AM_FNMA_WAYSYWTDTTAL );
    } 
    else
    {
        $xt = new XoopsTopic( $xoopsDB -> prefix( "topics2" ), $HTTP_POST_VARS['topic_id'] ); 
        // get all subtopics under the specified topic
        $topic_arr = $xt -> getAllChildTopics();
        array_push( $topic_arr, $xt );
        foreach( $topic_arr as $eachtopic )
        { 
            // get all stories in each topic
            $news_arr = NewsStory :: getByTopic( $eachtopic -> topic_id() );
            foreach( $news_arr as $eachstory )
            {
                if ( false != $eachstory -> delete() )
                {
                    xoops_comment_delete( $xoopsModule -> getVar( 'mid' ), $eachstory -> storyid() );
                    xoops_notification_deletebyitem( $xoopsModule -> getVar( 'mid' ), 'story', $eachstory -> storyid() );
                } 
            } 
            // all stories for each topic is deleted, now delete the topic data
            $eachtopic -> delete();
            xoops_notification_deletebyitem( $xoopsModule -> getVar( 'mid' ), 'category', $eachtopic -> topic_id );
        } 
        redirect_header( 'news.php?op=topicsmanager', 1, _AM_FNMA_DBUPDATED );
        exit();
    } 
}


function addTopic()
{
    global $xoopsDB, $HTTP_POST_VARS;
    $xt = new XoopsTopic( $xoopsDB -> prefix( "topics2" ) );
    if ( !$xt -> topicExists( $HTTP_POST_VARS['topic_pid'], $HTTP_POST_VARS['topic_pid'] ) )
    {
        $xt -> setTopicPid( $HTTP_POST_VARS['topic_pid'] );
        if ( empty( $HTTP_POST_VARS['topic_title'] ) )
        {
            redirect_header( "news.php?op=topicsmanager", 2, _AM_FNMA_ERRORTOPICNAME );
        } 
        $xt -> setTopicTitle( $HTTP_POST_VARS['topic_title'] );
        if ( isset( $HTTP_POST_VARS['topic_imgurl'] ) && $HTTP_POST_VARS['topic_imgurl'] != "" )
        {
            $xt -> setTopicImgurl( $HTTP_POST_VARS['topic_imgurl'] );
        } 
        $xt -> store();
        $notification_handler = & xoops_gethandler( 'notification' );
        $tags = array();
        $tags['TOPIC_NAME'] = $HTTP_POST_VARS['topic_title'];
        $notification_handler -> triggerEvent( 'global', 0, 'new_category', $tags );
        redirect_header( 'news.php?op=topicsmanager', 1, _AM_FNMA_DBUPDATED );
    } 
    else
    {
        echo "Topic exists!";
    } 
    exit();
} 
 
switch($op)
{
	
 		case "edit":
        xoops_cp_header();
        echo "<h4>" . _AM_FNMA_CONFIG . "</h4>"; 
        // $xoopsModule->printAdminMenu();
        // echo "<br />";
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo "<h4>" . _AM_FNMA_EDITARTICLE . "</h4>";
        $news = new fnmaNews( $storyid );
        $title = $news -> title( "Edit" );
        $hometext = $news -> hometext( "Edit" );
        $bodytext = $news -> bodytext( "Edit" );
        $nohtml = $news -> nohtml();
        $nosmiley = $news -> nosmiley();
        $ihome = $news -> ihome();
        $topicid = $news -> topicid();
        if ( $news -> published() != 0)
        {
            $published = $news -> published();
        } 
		if ( $news -> expired() != 0)
        {
            $expired = $news -> expired();
        } 

		// $notifypub = $news->notifypub();
        $type = $news -> type();
        $topicdisplay = $news -> topicdisplay();
        $topicalign = $news -> topicalign( false );
        $isedit = 1;
        include "newsform.php";
        echo"</td></tr></table>";
        break;

	 case "preview":
        xoops_cp_header();
        echo "<h4>" . _AM_FNMA_CONFIG . "</h4>";
        if ( isset( $storyid ) )
        {
            $news = new NewsStory( $storyid );
            $published = $news -> published();
            $expired = $news -> expired();
        } 
        else
        {
            $news = new fnmaNews();
        } 
        $news -> setTitle( $title );
        $news -> setHomeText( $hometext );
        $news -> setBodyText( $bodytext );
        if ( isset( $nohtml ) && ( $nohtml == 0 || $nohtml == 1 ) )
        {
            $news -> setNohtml( $nohtml );
        } 
        if ( isset( $nosmiley ) && ( $nosmiley == 0 || $nosmiley == 1 ) )
        {
            $news -> setNosmiley( $nosmiley );
        } 

        $xt = new XoopsTopic( $xoopsDB -> prefix( "topics2" ) );
        $p_title = $news -> title( "Preview" );
        $p_hometext = $news -> hometext( "Preview" );
        $p_bodytext = $news -> bodytext( "Preview" );
        $title = $news -> title( "InForm" );
        $hometext = $news -> hometext( "InForm" );
        $bodytext = $news -> bodytext( "InForm" );
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        $timage = "";
        $warning = "";
        if ( $topicdisplay )
        {
            if ( $topicalign == "L" )
            {
                $align = "left";
            } 
            else
            {
                $align = "right";
            } 
            if ( empty( $topicid ) )
            {
                $warning = "<div style='text-align: center;'><blink><b>" . _AR_SELECTTOPIC . "</b></blink></div>";
                $timage = "";
            } 
            else
            {
                $xt = new XoopsTopic( $xoopsDB -> prefix( "topics2" ), $topicid );
                if ( $xt -> topic_imgurl() != '' && file_exists( XOOPS_ROOT_PATH . '/modules/news2/images/topics/' . $xt -> topic_imgurl() ) )
                {
                    $timage = "<img src='" . XOOPS_URL . "/modules/news2/images/topics/" . $xt -> topic_imgurl() . "' align='$align' border='0' hspace='10' vspace=10' />";
                } 
            } 
        } 
        if ( isset( $p_bodytext ) && $p_bodytext != "" )
        {
            echo "<p><b>" . $p_title . "</b><br /><br />" . $timage . "" . $p_hometext . "<br /><br />" . $p_bodytext . "<br /><br /></p>";
        } 
        else
        {
            echo "<p><b>" . $p_title . "</b><br /><br />" . $timage . "" . $p_hometext . "<br /><br /></p>";
        } 
        echo $warning;
        echo"</td></tr></table><br />";
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        include "storyform.inc.php";
        echo"</td></tr></table>";
        break;

	case "save":
        if ( empty( $storyid ) )
        {
            $news = new fnmaNews();
            $news -> setUid( $xoopsUser -> uid() );
            if ( !empty( $autodate ) )
            {
                $pubdate = mktime( $autohour, $automin, 0, $automonth, $autoday, $autoyear );
                $offset = $xoopsUser -> timezone() - $xoopsConfig['server_TZ'];
                $pubdate = $pubdate - ( $offset * 3600 );
                $news -> setPublished( $pubdate );
            } 
            else
            {
                $news -> setPublished( time() );
            } 
            if ( !empty( $autoexpdate ) )
            {
                $expdate = mktime( $autoexphour, $autoexpmin, 0, $autoexpmonth, $autoexpday, $autoexpyear );
                $offset = $xoopsUser -> timezone() - $xoopsConfig['server_TZ'];
                $expdate = $expdate - ( $offset * 3600 );
                $news -> setExpired( $expdate );
            } 
            else
            {
                $news -> setExpired( 0 );
            } 
            $news -> setType( $type );
            $news -> setHostname( getenv( "REMOTE_ADDR" ) );
            // $news->setNotifyPub($notifypub);
        } 
        else
        {
            $news = new NewsStory( $storyid );
            if ( !empty( $autodate ) )
            {
                $pubdate = mktime( $autohour, $automin, 0, $automonth, $autoday, $autoyear );
                $offset = $xoopsUser -> timezone();
                $offset = $offset - $xoopsConfig['server_TZ'];
                $pubdate = $pubdate - ( $offset * 3600 );
                $news -> setPublished( $pubdate );
            } elseif ( ( $news -> published() == 0 ) && $approve )
            {
                $news -> setPublished( time() );
                $isnew = 1;
            } 
            else
            {
                if ( !empty( $movetotop ) )
                {
                    $news -> setPublished( time() );
                } 
            } 
            if ( !empty( $autoexpdate ) )
            {
                $expdate = mktime( $autoexphour, $autoexpmin, 0, $autoexpmonth, $autoexpday, $autoexpyear );
                $offset = $xoopsUser -> timezone() - $xoopsConfig['server_TZ'];
                $expdate = $expdate - ( $offset * 3600 );
                $news -> setExpired( $expdate );
            } 
        } 
        $news -> setApproved( $approve );
        $news -> setTopicId( $topicid );
        $news -> setTitle( $title );
        $news -> setHometext( $hometext );
        $news -> setBodytext( $bodytext );
        $nohtml = ( empty( $nohtml ) ) ? 0 : 1;
        $nosmiley = ( empty( $nosmiley ) ) ? 0 : 1;
        $news -> setNohtml( $nohtml );
        $news -> setNosmiley( $nosmiley );
        $news -> setIhome( $ihome );
        $news -> setTopicalign( $topicalign );
        $news -> setTopicdisplay( $topicdisplay );
        $news -> store();
       // $notification_handler = & xoops_gethandler( 'notification' );
        $tags = array();
        $tags['STORY_NAME'] = $news -> title();
        $tags['STORY_URL'] = XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/news.php?storyid=' . $news -> storyid();
         /*
		if ( !empty( $isnew ) )
        {
            $notification_handler -> triggerEvent( 'story', $news -> storyid(), 'approve', $tags );
        } 
        $notification_handler -> triggerEvent( 'global', 0, 'new_story', $tags );
       
			$poster = new XoopsUser($news->uid());
			$subject = _AM_FNMA_ARTPUBLISHED;
			$message = sprintf(_AM_FNMA_HELLO,$poster->uname());
			$message .= "\n\n"._AM_FNMA_YOURARTPUB."\n\n";
			$message .= _AM_FNMA_TITLEC.$news->title()."\n"._AM_FNMA_URLC.XOOPS_URL."/modules/".$xoopsModule->dirname()."/article.php?storyid=".$news->storyid()."\n"._AM_FNMA_PUBLISHEDC.formatTimestamp($news->published(),"m",0)."\n\n";
			$message .= $xoopsConfig['sitename']."\n".XOOPS_URL."";
			$xoopsMailer =& getMailer();
			$xoopsMailer->useMail();
			$xoopsMailer->setToEmails($poster->getVar("email"));
			$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
			$xoopsMailer->setFromName($xoopsConfig['sitename']);
			$xoopsMailer->setSubject($subject);
			$xoopsMailer->setBody($message);
			$xoopsMailer->send();
		}
		*/
        redirect_header( 'news.php?op=newNews', 1, _AM_FNMA_DBUPDATED );
        exit();
        break;

 		case "delete":
        if ( !empty( $ok ) )
        {
            if ( empty( $storyid ) )
            {
                redirect_header( 'news.php', 2, _AM_FNMA_EMPTYNODELETE );
                exit();
            } 
            $news = new fnmaNews( $storyid );
            $news -> delete();
            //xoops_comment_delete( $xoopsModule -> getVar( 'mid' ), $storyid );
            //xoops_notification_deletebyitem( $xoopsModule -> getVar( 'mid' ), 'story', $storyid );
            redirect_header( 'news.php?op=newNews', 1, _AM_FNMA_DBUPDATED );
            exit();
        } 
        else
        {
            xoops_cp_header();
            echo "<h4>" . _AM_FNMA_CONFIG . "</h4>";
            xoops_confirm( array( 'op' => 'delete', 'storyid' => $storyid, 'ok' => 1 ), 'news.php', _AM_FNMA_RUSUREDEL );
        } 
        break;

		case "edit":
        xoops_cp_header();
        echo "<h4>" . _AM_FNMA_CONFIG . "</h4>"; 
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo "<h4>" . _AM_FNMA_EDITNEWS . "</h4>";
        $news = new fnmaNews($storyid);

        $title = $news -> title( "Edit" );
        $hometext = $news -> hometext( "Edit" );
        $bodytext = $news -> bodytext( "Edit" );
        $nohtml = $news -> nohtml();
        $nosmiley = $news -> nosmiley();
        $ihome = $news -> ihome();
        $topicid = $news -> topicid();
        if ( $news -> published() != 0)
        {
            $published = $news -> published();
        } 
		if ( $news -> expired() != 0)
        {
            $expired = $news -> expired();
        } 

		// $notifypub = $news->notifypub();
        $type = $news -> type();
        $topicdisplay = $news -> topicdisplay();
        $topicalign = $news -> topicalign( false );
        $isedit = 1;
        include "newsform.php";
        echo"</td></tr></table>";
	break;

	case "newNews":
        xoops_cp_header();
        echo "<h4>" . _AM_FNMA_CONFIG . "</h4>";
        include_once XOOPS_ROOT_PATH . "/class/module.textsanitizer.php"; 
        showNewNews();
        lastNews();
        expiredNews();
        echo "<br />";
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo "<h4>" . _AM_FNMA_POSTNNEWS . "</h4>";
        $type = "admin";
        include "newsform.php";
        echo"</td></tr></table>";
	break;
	case "topicsmanager":
        topicsmanager();
        break;

    case "addTopic":
        addTopic();
        break;

    case "delTopic":
        delTopic();
        break;
    case "modTopic":
        modTopic();
        break;
    case "modTopicS":
        modTopicS();
        break;
	case "default":
    default:
        xoops_cp_header();
        echo "<h4>" . _AM_FNMA_NEWSCONFIG . "</h4>";
        echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo " - <b><a href='news.php?op=topicsmanager'>" . _AM_FNMA_TOPICSMNGR . "</a></b>";
        echo "<br /><br />\n";
        echo " - <b><a href='news.php?op=newNews'>" . _AM_FNMA_POST_NEWS . "</a></b>\n";
        echo "</td></tr></table>";
		echo "<h4>" . _AM_FNMA_NEWS_NLIST . "</h4>";
		showNewNews();
	    break;
} 

xoops_cp_footer();
