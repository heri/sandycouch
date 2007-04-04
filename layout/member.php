<?php
require_once ("menus.php");

function DisplayMember($m, $profilewarning = "", $TGroups,$CanBeEdited=false) {
	global $title;
	$title = ww('ProfilePageFor', $m->Username);
	include "header.php";

	Menu1(); // Displays the top menu

	Menu2("member.php?cid=".$m->Username);

	// Header of the profile page
	require_once ("profilepage_header.php");

	menumember("member.php?cid=" . $m->id, $m);
	echo "	<div id=\"columns\">";
	echo "		<div id=\"columns-low\">";
	// MAIN begin 3-column-part
	echo "    <div id=\"main\">";

	// Prepare the $MenuAction for ShowAction()  
	$MenuAction = "";
	$MenuAction .= "               <li><a href=\"contactmember.php?cid=" . $m->id . "\">" . ww("ContactMember") . "</a></li>\n";
	$MenuAction .= "               <li><a href=\"addcomments.php?cid=" . $m->id . "\">" . ww("addcomments") . "</a></li>\n";
	$MenuAction .= "               <li><a href=\"todo.php\">".ww("ViewForumPosts")."</a></li>\n";

	if (HasRight("Logs")) {
		$MenuAction .= "<li><a href=\"admin/adminlogs.php?Username=" . $m->Username . "\">see logs</a> </li>\n";
	}
	if ($CanBeEdited) {
		$MenuAction .= "<li><a href=\"editmyprofile.php?cid=" . $m->id . "\">".ww("TranslateProfileIn",LanguageName($_SESSION["IdLanguage"]))." ".FlagLanguage(-1,$title="Translate this profile")."</a> </li>\n";
	}
	if (HasRight("Admin")) {
		$MenuAction .= "<li><a href=\"editmyprofile.php?cid=" . $m->id . "\">Edit this profile</a> </li>\n";
	}

	if (GetPreference("PreferenceAdvanced")=="Yes") {
      if ($m->IdContact==0) {
	   	  $MenuAction .= "<li><a href=\"mycontacts.php?IdContact=" . $m->id . "&action=add\">".ww("AddToMyNotes")."</a> </li>\n";
	   }
	   else {
	   	  $MenuAction .= "<li><a href=\"mycontacts.php?IdContact=" . $m->id . "&action=view\">".ww("ViewMyNotesForThisMember")."</a> </li>\n";
	   }
	}

	if (GetPreference("PreferenceAdvanced")=="Yes") {
      if ($m->IdRelation==0) {
	   	  $MenuAction .= "<li><a href=\"myrelations.php?IdRelation=" . $m->id . "&action=add\">".ww("AddToMyRelations")."</a> </li>\n";
	   }
	   else {
	   		$MenuAction .= "<li><a href=\"myrelations.php?IdRelation=" . $m->id . "&action=view\">".ww("ViewMyRelationForThisMember")."</a> </li>\n";
	   }
	}

		
	if (HasRight("Admin")) {
		$MenuAction .= "<li><a href=\"updatemandatory.php?cid=" . $m->id . "\">update mandatory</a> </li>\n";
		$MenuAction .= "<li><a href=\"myvisitors.php?cid=" . $m->id . "\">view visits</a> </li>\n";
		$MenuAction .= "<li><a href=\"admin/adminrights.php?username=" . $m->Username . "\">Rights</a> </li>\n";
	}
	if (HasRight("Flags")) $MenuAction .= "<li><a href=\"admin/adminflags.php?username=" . $m->Username . "\">Flags</a> </li>\n";
	ShowActions($MenuAction); // Show the Actions
	ShowAds(); // Show the Ads

	// middle column
	echo "      <div id=\"col3\"> \n"; 
	echo "	    <div id=\"col3_content\" class=\"clearfix\"> \n"; 
	echo "          <div id=\"content\"> \n";

	// user content
	echo "					<div class=\"info\">\n";
	echo "					<div class=\"user-content\">\n";
	if ($m->ProfileSummary > 0) {
		echo "					<strong>", strtoupper(ww('ProfileSummary')), "</strong>";
		echo "<p>", FindTrad($m->ProfileSummary,true), "</p>";
	}

	if ($m->MotivationForHospitality != "") {
		echo "					<strong>", strtoupper(ww('MotivationForHospitality')), "</strong>";
		echo "<p>", $m->MotivationForHospitality, "</p>";
	}

	if ($m->Offer != "") {
		echo "					<strong>", strtoupper(ww('ProfileOffer')), "</strong>";
		echo "<p>", $m->Offer, "</p>";
	}

	if ($m->IdGettingThere != "") {
		echo "					<strong>", strtoupper(ww('GettingHere')), "</strong>";
		echo "<p>", $m->GettingThere, "</p>\n";
	}
	echo "					</div>\n";
	echo "				</div>\n";

	$Relations=$m->Relations;
	$iiMax=count($Relations);
	if ($iiMax>0) { // if member has declared confirmed relation
	   echo "					<div class=\"info\">\n";
	   echo "					<div class=\"user-content\">\n";
	   echo "					<strong>", ww('MyRelations'), "</strong>";
	   echo "<table>\n";
	   for ($ii=0;$ii<$iiMax;$ii++) {
		  echo "<tr><td valign=center>", LinkWithPicture($Relations[$ii]->Username,$Relations[$ii]->photo),"<br>",LinkWithUsername($Relations[$ii]->Username),"</td>";
		  echo "<td valign=center>",$Relations[$ii]->Comment,"</td>\n";
	   }
	   echo "</table>\n";
	   echo "					</div>\n";
	   echo "				</div>\n";
	} // end if member has declared confirmed relation

	
	// content info
	echo "            <div class=\"info highlight\"> \n";
	echo "					<h3>".ww("ContactInfo")."</h3>";
	echo "					<ul class=\"contact\">
							<li>
								<ul>\n  
									<li class=\"label\">", ww('Name'), "</li>
									<li>", $m->FullName, "</li>
								</ul>\n
								<ul>\n
									<li class=\"label\">", ww("Address"), "</li>
									<li>", $m->Address, "</li>
									<li>", $m->Zip, "</li>
									<li>", $m->cityname, "</li>
									<li>", $m->regionname, "</li>
									<li>", $m->countryname, "</li>
								</ul>\n
							</li>
							<li>";
	if (!empty($m->DisplayHomePhoneNumber) or 
		!empty($m->DisplayCellPhoneNumber) or 
		!empty($m->DisplayWorkPhoneNumber)) {
		echo "        <ul>";
		echo "							<li class=\"label\">", ww("ProfilePhone"), "</li>";
		if (!empty($m->DisplayHomePhoneNumber))
			echo "							<li>", ww("ProfileHomePhoneNumber"), ": ", $m->DisplayHomePhoneNumber, "</li>";
		if (!empty($m->DisplayCellPhoneNumber))
			echo "							<li>", ww("ProfileCellPhoneNumber"), ": ", $m->DisplayCellPhoneNumber, "</li>";
		if (!empty($m->DisplayWorkPhoneNumber))
			echo "							<li>", ww("ProfileWorkPhoneNumber"), ": ", $m->DisplayWorkPhoneNumber, "</li>";
		echo "				</ul>\n";
	}

	echo "							<ul>";
	echo "							  <li class=\"label\">Messenger</li>";
	if ($m->chat_SKYPE != 0)
		echo "							  <li>SKYPE: ", PublicReadCrypted($m->chat_SKYPE, ww("Hidden")), "</li>";
	if ($m->chat_ICQ != 0)
		echo "							  <li>ICQ: ", PublicReadCrypted($m->chat_ICQ, ww("Hidden")), "</li>";
	if ($m->chat_AOL != 0)
		echo "							  <li>AOL: ", PublicReadCrypted($m->chat_AOL, ww("Hidden")), "</li>";
	if ($m->chat_MSN != 0)
		echo "							  <li>MSN: ", PublicReadCrypted($m->chat_MSN, ww("Hidden")), "</li>";
	if ($m->chat_YAHOO != 0)
		echo "							  <li>YAHOO: ", PublicReadCrypted($m->chat_YAHOO, ww("Hidden")), "</li>";
	if ($m->chat_Others != 0)
		echo "							  <li>", ww("chat_others"), ": ", PublicReadCrypted($m->chat_Others, ww("Hidden")), "</li>";
	echo "							</ul>";
	if ($m->WebSite != "") {
		echo "							<ul>";
		echo "								<li class=\"label\">", ww("Website"), "</li>";
		echo "								<li><a href=\"", $m->WebSite, "\">", $m->WebSite, "</a></li>";
		echo "							</ul>";
	} // end if there is WebSite
	echo "
							</li>
						</ul>";
	echo "		<div class=\"clear\" ></div>\n";
	echo "	</div>";

	// Interests and groups
	echo "				<div class=\"info\">\n";
	echo "					<h3>", ww("InterestsAndGroups"), "</h3>\n";
	echo "					<ul class=\"information\">\n";
	$max = count($m->TLanguages);
	if ($max > 0) {
		echo "						<li class=\"label\">", ww("Languages"), "</li>";
		echo "            <li>";
		for ($ii = 0; $ii < $max; $ii++) {
			if ($ii > 0)
				echo ",";
			echo $m->TLanguages[$ii]->Name, " (", $m->TLanguages[$ii]->Level, ")";
		}
		echo "            </li>\n";
	}

	$max = count($TGroups);
	if ($max > 0) {
		//    echo "<h3>",ww("xxBelongsToTheGroups",$m->Username),"</h3>";
		for ($ii = 0; $ii < $max; $ii++) {
			echo "<li class=\"label\"><a href=\"groups.php?action=ShowMembers&IdGroup=", $TGroups[$ii]->IdGroup, "\">", ww("Group_" . $TGroups[$ii]->Name), "</a></li>";
			if ($TGroups[$ii]->Comment > 0)
				echo "<li>", FindTrad($TGroups[$ii]->Comment,true), "</li>\n";
		}
	}
	if ($m->Organizations != "") {
		echo "						<li class=\"label\">", ww("ProfileOrganizations"), "</li>";
		echo "						<li>", $m->Organizations, "</li>\n";
	}
	echo "					</ul>";
	echo "					<div class=\"clear\" ></div>\n";
	echo "				</div>\n";

	// Profile Accomodation
	echo "				<div class=\"info highlight\">\n";
	echo "					<h3>", ww("ProfileAccomodation"), "</h3>\n";

	echo "					<ul class=\"information\">\n";
	echo "						<li class=\"label\">", ww("ProfileNumberOfGuests"), "</li>";
	echo "						<li>", $m->MaxGuest, "</li>\n";

	if ($m->MaxLenghtOfStay != "") {
		echo "						<li class=\"label\">", ww("ProfileMaxLenghtOfStay"), "</li>";
		echo "						<li>", $m->MaxLenghtOfStay, "</li>\n";
	}

	// echo "						<li class=\"label\">Length of stay</li>";
	// echo "						<li>till the end</li>";

	if ($m->ILiveWith != "") {
		echo "						<li class=\"label\">", ww("ProfileILiveWith"), "</li>\n";
		echo "<li>", $m->ILiveWith, "</li>\n";
	}
	echo "					</ul>";

	echo "					<div class=\"clear\" ></div>\n";
	echo "				</div>\n";

	// Other Infos
	echo "				<div class=\"info\">\n";
	if (($m->AdditionalAccomodationInfo != "") or ($m->InformationToGuest != "")) {
		echo "					<h3> ", ww('OtherInfosForGuest'), "</h3>\n";
		echo "						<ul>";
		if ($m->AdditionalAccomodationInfo != "")
			echo "<li>", $m->AdditionalAccomodationInfo, "</li><br>";
		if ($m->InformationToGuest != "")
			echo "<li>", $m->InformationToGuest, "</li><br>";
		echo "						</ul>";
	}

	$max = count($m->TabRestrictions);
	if (($max > 0) or ($m->OtherRestrictions != "")) {
		echo "					<p><strong>", strtoupper(ww('ProfileRestrictionForGuest')), "</strong></p>";
		echo "					<ul>";
		if ($max > 0) {
			for ($ii = 0; $ii < $max; $ii++) {
				echo "<li>", ww("Restriction_" . $m->TabRestrictions[$ii]), "</li>";
			}
		}

		if ($m->OtherRestrictions != "")
			echo "<li>", $m->OtherRestrictions, "</li>";
		echo "					</ul>";
	}

echo "              <div class=\"clear\"></div>\n"; 
echo "            </div>\n"; 
echo "          </div>\n"; // end content
echo "        </div>\n"; // end col3_content

	// IE Column Clearing 
echo "        <div id=\"ie_clearing\">&nbsp;</div>\n"; 
	// End: IE Column Clearing 

echo "      </div>\n"; // end col3
	// End: MAIN 3-columns-part
	
echo "    </div>\n"; // end main


	include "footer.php";

}
?>