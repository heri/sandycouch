<?php
require_once ("menus.php");
function DisplayForm($m,$JoinMemberPict="") {
	global $title;
	$title = ww('InviteAFriendPage');
	include "header.php";

	Menu1("", ww('MainPage')); // Displays the top menu

	Menu2("inviteafriend.php", ww('MainPage')); // Displays the second menu

	DisplayHeaderWithColumns("inviteafriend.php","",""); // Display the header

	echo "<center>\n";

	// TODO: check the meaning of the next row. $TData is not defined
	$iiMax = count($TData);
	$CurrentCategory="";
	echo "<table cellspacing=4 align=left>";
	echo "<form method=post action=inviteafriend.php>\n";
	echo "<input type=hidden name=action value=Send>";
	echo "<tr><td>",ww("InviteAFriendRule",$m->FullName),"</td>\n";
	echo "<tr><td>",ww("EmailOfYourFriend")," <input type=text name=Email value=\"",GetParam("Email"),"\">";
	echo "<tr><td>","<textarea name=Message rows=20 cols=80>",str_replace("<br />","\n",ww("InviteAFriendStandardText","<a href=\"http://www.bewelcome.org/member.php?cid=".$_SESSION["Username"]."\">".$_SESSION["Username"]."</a>")),"</textarea></td>\n";
	echo "<tr><td>",ww("JoinMyPicture")," <input type=checkbox name=JoinMemberPict ";
	if ($JoinMemberPict=="on") echo "checked";
	echo "></td>\n<tr><td align=center><input type=submit></td>\n";
	echo "</table>\n";
	echo "</center>\n";

	include "footer.php";

} // DisplayForm


function DisplayResults($m,$Message) {
	global $title;
	$title = ww('InviteAFriendPage');
	include "header.php";

	Menu1("inviteafriend.php", ww('MainPage')); // Displays the top menu

	Menu2($_SERVER["PHP_SELF"]);

	DisplayHeaderWithColumns(ww("InviteAFriendPage")); // Display the header
	
   echo $Message;
	include "footer.php";
} // end of DisplayResults


?>