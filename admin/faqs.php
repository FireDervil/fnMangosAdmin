<?php

include_once("header.php");

xoops_cp_header();

global $xoopsModule;

if (fnMangosAdmin_checkModuleAdmin()){
$MangosAdmin = new ModuleAdmin();

}
$op = !empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"default");


switch($op){
	
case 'main':
default:

echo '<div class="content">	
		<div class="content-header">
			<h4><a href="index.php">Main Menu</a> / Faqs</h4>
		</div> 			
		<div class="main-content">
			<form method="POST" action="faqs.php?add=true" class="form label-inline">
				<h5><center>'._MD_FNMA_FAQ_LISTOFFAQ.'</center></h5><br />
				<table>
					<thead>
						<th><center><b>'._MD_FNMA_FAQ_QUESTION.'</center></b></th>
						<th><center><b>'._MD_FNMA_FAQ_ANSWER.'</center></b></th>
					</thead>';
					if($get_faq != FALSE)
					{
						foreach($get_faq as $faq)
						{
							echo "
								<tr>
									<td width='40%' align='center'><a href='faqs.php?id=".$faq['id']."'>".$faq['question']."</a></td>
									<td width='60%' align='center'>".$faq['answer']."</td>
								</tr>
							";
						}
					}
				echo '</table>
			<br />
			<div class="buttonrow-border">								
				<center><button><span>'._MD_FNMA_FAQ_ADD_NEW.'</span></button></center>			
			</div>
			</form>
		</div>
	</div>';

break;	
}

xoops_cp_footer();
?>