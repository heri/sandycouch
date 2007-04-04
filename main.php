<?php
require_once "lib/init.php";
require_once "lib/FunctionsLogin.php";
require_once "layout/error.php";

switch (GetParam("action")) {
	case "confirmsignup" : // case a new signupper confirm his mail
		$m = LoadRow("select * from members where Username='" . GetParam("username") . "'");

		if (isset ($m->id)) {

			if ($m->Status != "MailToConfirm") {
				$errcode = "ErrorMailAllreadyConfimed";
				LogStr("action confirm signup ErrorMailAllreadyConfimed Status=" . $m->Status, "login");
				DisplayError(ww($errcode, $m->Status));
				exit (0);
			}

			$_SESSION['IdMember'] = $m->id; // In this case we must have an identified member

			// todo here use something else that AdminReadCrypted (will not work when crypted right will be added)
			$key = CreateKey($m->Username, AdminReadCrypted($m->LastName), $m->id, "registration"); // retrieve the nearly unique key

			/*				
							  echo "key=",$key,"<br>";
							  echo " GetParam(\"key\")=",GetParam("key"),"<br>"; 
								echo "\$m->id=",$m->id,"<br>";
								echo "ReadCrypted(\$m->LastName)=",AdminReadCrypted($m->LastName),"<br>";
								echo "\$m->Username=",$m->Username,"<br>";
			*/

			if ($key != GetParam("key")) {
				$errcode = "ErrorBadKey";
				LogStr("Bad Key", "hacking");
				DisplayError(ww($errcode));
				exit (0);
			}
			$str = "update members set Status='Pending' where id=" . $m->id; // The email is confirmed make the status Pending
			sql_query($str);
			$m->Status = "Pending";
		}
		break;
	case "logout" :
		Logout("index.php");
		exit (0);
}
if ($m->Status == "Pending") { // Members with Pending status can only update ther profile
	if ($m->IdCity > 0) {
		$rWhere = LoadRow("select cities.Name as cityname,regions.Name as regionname,countries.Name as countryname from cities,countries,regions where cities.IdRegion=regions.id and countries.id=cities.IdCountry and cities.id=" . $m->IdCity);
	}
	include "layout/editmyprofile.php";
	$Message = ww("YouCanCompleteProfAndWait", $m->Username);
	DisplayEditMyProfile($m, "", "", 0, $rWhere->cityname, $rWhere->regionname, $rWhere->countryname, $Message, array ());
	exit (0);
}

if (IsLoggedIn()) {
	$m = LoadRow("select * from members where id=" . $_SESSION['IdMember']);
	$rr=LoadRow("select count(*) as cnt from mycontacts where IdMember=".$_SESSION['IdMember']);
	$m->NbContacts=$rr->cnt;
	include "layout/main.php";
	DisplayMain($m);
} else {
	Logout("index.php");
	exit (0);
}
?>