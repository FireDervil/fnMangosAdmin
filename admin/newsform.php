<?php

if ( !preg_match("/news.php/i", $_SERVER['PHP_SELF']) ) {
	exit("Access Denied");
}
include XOOPS_ROOT_PATH."/include/xoopscodes.php";
if(!isset($submit_page)){
	$submit_page = $_SERVER['PHP_SELF'];
}
?>
<table><tr><td>
<form action='<?php echo $submit_page;?>' method='post' name='coolsus'>
<?php
echo "<p><b>"._AM_FNMA_TITLE."</b><br />";
echo "<input type='text' name='title' id='title' value='";
if(isset($title)){
	echo $title;
}
echo "' size='70' maxlength='80' />";
echo "</p><p>";
echo "<b>"._AM_FNMA_TOPIC."</b>&nbsp;";
$xt = new XoopsTopic($xoopsDB->prefix("fnma_news_topics"));
if(isset($topicid)){
	$xt->makeTopicSelBox(0, $topicid, "topicid");
}else{
	$xt->makeTopicSelBox(0, 0, "topicid");
}
echo "<br /><b>"._AM_FNMA_TOPICDISPLAY."</b>&nbsp;&nbsp;<input type='radio' name='topicdisplay' value='1'";
if ( !isset($topicdisplay) || $topicdisplay==1 ) {
	echo " checked='checked'";
}
echo " />"._AM_FNMA_YES."&nbsp;<input type='radio' name='topicdisplay' value='0'";
if (empty($topicdisplay)) {
	echo " checked='checked'";
}
echo " />"._AM_FNMA_NO."&nbsp;&nbsp;&nbsp;";
echo "<b>"._AM_FNMA_TOPICALIGN."</b>&nbsp;<select name='topicalign'>\n";
	if ( "L" == $topicalign) {
	    	$sel = " selected='selected'";
	} else {
	    	$sel = "";
	}
echo "<option value='R'>"._AM_FNMA_RIGHT."</option>\n";
echo "<option value='L'".$sel.">"._AM_FNMA_LEFT."</option>\n";
echo "</select>\n";
echo "<br />";
echo "</p><p><b>"._AM_FNMA_INTROTEXT."</b><br /><br />\n";
xoopsCodeTarea("hometext", 60, 15);
xoopsSmilies("hometext");
echo "<br /></p><p><b>"._AM_FNMA_EXTEXT."</b><br /><br />\n";
xoopsCodeTarea("bodytext", 60, 15, 2);
xoopsSmilies("bodytext");
echo "</p>"._MULTIPAGE;
if ( !empty($xoopsConfig['allow_html']) ) {
	echo "<p>"._AM_FNMA_ALLOWEDHTML."<br />";
	//echo get_allowed_html();
	echo "</p>";
}
echo "<p><input type='checkbox' name='nosmiley' value='1'";
if(isset($nosmiley) && $nosmiley==1){
	echo " checked='checked'";
}
echo " /> "._AM_FNMA_DISAMILEY."<br />";
echo "<input type='checkbox' name='nohtml' value='1'";
if(isset($nohtml) && $nohtml==1){
	echo " checked='checked'";
}
echo " /> "._AM_FNMA_DISHTML."<br />";

echo "<br /><input type='checkbox' name='autodate' value='1'";
if(isset($autodate) && $autodate==1){
	echo " checked='checked'";
}
echo "> ";
$time = time();
if(isset($isedit) && $isedit==1 && $published >$time){
	echo _AM_FNMA_CHANGEDATETIME."<br />";
	printf(_AM_FNMA_NOWSETTIME,formatTimestamp($published));
	echo "<br />";
	$published = xoops_getUserTimestamp($published);
	printf(_AM_FNMA_CURRENTTIME,formatTimestamp($time));
	echo "<br />";
	echo "<input type='hidden' name='isedit' value='1' />";
}else{
	echo _AM_FNMA_SETDATETIME."<br />";
	printf(_AM_FNMA_CURRENTTIME,formatTimestamp($time));
	echo "<br />";
}

echo "<br /> &nbsp; "._AM_FNMA_MONTHC." <select name='automonth'>";
if (isset($automonth)) {
	$automonth = intval($automonth);
} elseif (isset($published)) {
	$automonth = date('m', $published);
} else {
	$automonth = date('m');
}
for ($xmonth=1; $xmonth<13; $xmonth++) {
	if ($xmonth == $automonth) {
		$sel = 'selected="selected"';
	} else {
		$sel = '';
	}
	echo "<option value='$xmonth' $sel>$xmonth</option>";
}
echo "</select>&nbsp;";

echo _AM_FNMA_DAYC." <select name='autoday'>";
if (isset($autoday)) {
	$autoday = intval($autoday);
} elseif (isset($published)) {
	$autoday = date('d', $published);
} else {
	$autoday = date('d');
}

for ($xday=1; $xday<32; $xday++) {
	if ($xday == $autoday) {
		$sel = 'selected="selected"';
	} else {
		$sel = '';
	}
	echo "<option value='$xday' $sel>$xday</option>";
}
echo "</select>&nbsp;";

echo _AM_FNMA_YEARC." <select name='autoyear'>";
if (isset($autoyear)) {
	$autoyear = intval($autoyear);
} elseif (isset($published)) {
	$autoyear = date('Y', $published);
} else {
	$autoyear = date('Y');
}

$cyear    = date('Y');
for ($xyear=($autoyear-8); $xyear < ($cyear+2); $xyear++) {
	if ($xyear == $autoyear) {
		$sel = 'selected="selected"';
	} else {
		$sel = '';
	}
	echo "<option value='$xyear' $sel>$xyear</option>";
}
echo "</select>";

echo "&nbsp;"._AM_FNMA_TIMEC." <select name='autohour'>";
if (isset($autohour)) {
	$autohour = intval($autohour);
} elseif (isset($published)) {
	$autohour = date('H', $published);
} else {
	$autohour = date('H');
}

for ($xhour=0; $xhour<24; $xhour++) {
	if ($xhour == $autohour) {
		$sel = 'selected="selected"';
	} else {
		$sel = '';
	}
	echo "<option value='$xhour' $sel>$xhour</option>";
}
echo "</select>";

echo " : <select name='automin'>";
if (isset($automin)) {
	$automin = intval($automin);
} elseif (isset($published)) {
	$automin = date('i', $published);
} else {
	$automin = date('i');
}

for ($xmin=0; $xmin<61; $xmin++) {
	if ($xmin == $automin) {
		$sel = 'selected="selected"';
	} else {
		$sel = '';
	}
	$xxmin = $xmin;
	if ($xxmin < 10) {
		$xxmin = "0$xmin";
	}
	echo "<option value='$xmin' $sel>$xxmin</option>";
}
echo "</select></br />";

echo "<br /><input type='checkbox' name='autoexpdate' value='1'";
if(isset($autoexpdate) && $autoexpdate==1){
	echo " checked='checked'";
}
echo "> ";
$time = time();
if(isset($isedit) && $isedit == 1 && $expired > 0){
	echo _AM_FNMA_CHANGEEXPDATETIME."<br />";
	printf(_AM_FNMA_NOWSETEXPTIME,formatTimestamp($expired));
	echo "<br />";
	$expired = xoops_getUserTimestamp($expired);

	printf(_AM_FNMA_CURRENTTIME,formatTimestamp($time));
	echo "<br />";
	echo "<input type='hidden' name='isedit' value='1' />";
}else{
	echo _AM_FNMA_SETEXPDATETIME."<br />";
	printf(_AM_FNMA_CURRENTTIME,formatTimestamp($time));
	echo "<br />";
}

echo "<br /> &nbsp; "._AM_FNMA_MONTHC." <select name='autoexpmonth'>";
if (isset($autoexpmonth)) {
	$autoexpmonth = intval($autoexpmonth);
} elseif (isset($expired)) {
	$autoexpmonth = date('m', $expired);
} else {
	$autoexpmonth = date('m');
	$autoexpmonth = $autoexpmonth + 1;
}
for ($xmonth=1; $xmonth<13; $xmonth++) {
	if ($xmonth == $autoexpmonth) {
		$sel = 'selected="selected"';
	} else {
		$sel = '';
	}
	echo "<option value='$xmonth' $sel>$xmonth</option>";
}
echo "</select>&nbsp;";

echo _AM_FNMA_DAYC." <select name='autoexpday'>";
if (isset($autoexpday)) {
	$autoexpday = intval($autoexpday);
} elseif (isset($expired)) {
	$autoexpday = date('d', $expired);
} else {
	$autoexpday = date('d');
}

for ($xday=1; $xday<32; $xday++) {
	if ($xday == $autoexpday) {
		$sel = 'selected="selected"';
	} else {
		$sel = '';
	}
	echo "<option value='$xday' $sel>$xday</option>";
}echo "</select>&nbsp;";

echo _AM_FNMA_YEARC." <select name='autoexpyear'>";
if (isset($autoexpyear)) {
	$autoyear = intval($autoexpyear);
} elseif (isset($expired)) {
	$autoexpyear = date('Y', $expired);
} else {
	$autoexpyear = date('Y');
}

$cyear = date('Y');
for ($xyear=($autoexpyear-8); $xyear < ($cyear+2); $xyear++) {
	if ($xyear == $autoexpyear) {
		$sel = 'selected="selected"';
	} else {
		$sel = '';
	}
	echo "<option value='$xyear' $sel>$xyear</option>";
}
echo "</select>";

echo "&nbsp;"._AM_FNMA_TIMEC." <select name='autoexphour'>";
if (isset($autoexphour)) {
	$autoexphour = intval($autoexphour);
} elseif (isset($expired)) {
	$autoexphour = date('H', $expired);
} else {
	$autoexphour = date('H');
}

for ($xhour=0; $xhour<24; $xhour++) {
	if ($xhour == $autoexphour) {
		$sel = 'selected="selected"';
	} else {
		$sel = '';
	}
	echo "<option value='$xhour' $sel>$xhour</option>";
}
echo "</select>";

echo " : <select name='autoexpmin'>";
if (isset($autoexpmin)) {
	$autoexpmin = intval($autoexpmin);
} elseif (isset($expired)) {
	$autoexpmin = date('i', $expired);
} else {
	$autoexpmin = date('i');
}

for ($xmin=0; $xmin<61; $xmin++) {
	if ($xmin == $autoexpmin) {
		$sel = 'selected="selected"';
	} else {
		$sel = '';
	}
	$xxmin = $xmin;
	if ($xxmin < 10) {
		$xxmin = "0$xmin";
	}
	echo "<option value='$xmin' $sel>$xxmin</option>";
}
echo "</select><br /><br />";

if(isset($published) && $published == 0 && isset($type) && $type == "user"){
	echo "<br /><input type='checkbox' name='approve' value='1'";
	if(isset($approve) && $approve==1){
		echo " checked='checked'";
	}
	echo " />&nbsp;<b>"._AM_FNMA_APPROVE."</b><br />";
} else {
	if(isset($isedit) && $isedit==1){
		echo "<br /><input type='checkbox' name='movetotop' value='1'";
		if(isset($movetotop) && $movetotop==1){
			echo " checked='checked'";
		}
		echo " />&nbsp;<b>"._AM_FNMA_MOVETOTOP."</b><br />";
		echo "<input type='hidden' name='isedit' value='1' />";
	}
	echo "<input type='hidden' name='approve' value='1' />";
}
echo "<select name='op'>\n";
echo "<option value='preview' selected='selected'>"._AM_FNMA_PREVIEW."</option>\n";
echo "<option value='save'>"._AM_FNMA_SAVE."</option>\n";
if (!empty($newsid)) {
	echo "<option value='delete'>"._AM_FNMA_DELETE."</option>\n";
}
echo "</select>";
if(isset($newsid)){
	echo "<input type='hidden' name='newsid' value='".$newsid."' />\n";
}
echo "<input type='hidden' name='type' value='".$type."' />\n";
echo "<input type='hidden' name='fct' value='news' />\n";
echo "<input type='submit' value='"._AM_FNMA_GO."' />\n";
echo "</p></form>";
echo "</td></tr></table>";

unset($submit_page);

function puthome($ihome="") {
    	echo "<br /><b>"._AM_FNMA_PUBINHOME."</b>&nbsp;&nbsp;";
    	if (($ihome == 0) OR ($ihome == "")) {
		$sel1 = "checked='checked'";
		$sel2 = "";
    	}
    	if ($ihome == 1) {
		$sel1 = "";
		$sel2 = "checked='checked'";
    	}
    	echo "<input type='radio' name='ihome' value='0' $sel1 />"._AM_FNMA_YES."&nbsp;";
    	echo "<input type='radio' name='ihome' value='1' $sel2 />"._AM_FNMA_NO."<br />";
}

?>