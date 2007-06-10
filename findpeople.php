<?php
require_once "lib/init.php";
require_once "layout/findpeople.php";



// Tis function build the result according to params
function buildresult() {
	global $rCount ; // will be use to find the total of possibilities
	$TMember=array() ;
	
	$limitcount=GetParam("limitcount",10); // Number of records per page
	$start_rec=GetParam("start_rec",0); // Number of records per page

	if (GetParam("OrderBy",0)==0) $OrderBy=" order by members.created desc" ; // by default find the last created members
	elseif (GetParam("OrderBy",0)==2)  $OrderBy=" order by LastLogin desc" ;
	elseif (GetParam("OrderBy",0)==3)  $OrderBy=" order by LastLogin asc" ;
	elseif (GetParam("OrderBy",0)==4)  $OrderBy=" order by Accomodation desc" ;
	elseif (GetParam("OrderBy",0)==5)  $OrderBy=" order by Accomodation asc" ;
	elseif (GetParam("OrderBy",0)==6)  $OrderBy=" order by HideBirthDate,BirthDate desc" ;
	elseif (GetParam("OrderBy",0)==7)  $OrderBy=" order by HideBirthDate,BirthDate asc" ;
	elseif (GetParam("OrderBy",0)==8)  $OrderBy=" order by NbComment desc" ;
	elseif (GetParam("OrderBy",0)==9)  $OrderBy=" order by NbComment asc" ;
	elseif (GetParam("OrderBy",0)==10)  $OrderBy=" order by countries.id desc" ;
	elseif (GetParam("OrderBy",0)==11)  $OrderBy=" order by countries.id asc" ;
	elseif (GetParam("OrderBy",0)==12)  $OrderBy=" order by cities.id desc" ;
	elseif (GetParam("OrderBy",0)==13)  $OrderBy=" order by cities.id asc" ;
	
	$nocriteria=true ;
	$dblink="" ; // This will be used one day to query on another replicated database
	$tablelist=$dblink."members,".$dblink."cities,".$dblink."countries,".$dblink."comments" ;
	
	if (GetStrParam("IncludeInactive"=="on")) {
		 $where=" where (members.Status='Active' or members.Status='ChoiceInActive' or members.Status='OutOfRemind')" ; // only active and inactive members
	}
	else {
		 $where=" where members.Status='Active'" ; // only active members
	}
	
	$where.=" and comments.IdToMember=members.id" ;
	
// Process Username parameter if any
	if (GetStrParam("Username","")!="") {
	   	 $Username=GetStrParam("Username") ;
		 if (strpos($Username,"*")!==false) {
		 	$Username=str_replace("*","%",$Username) ;
		 	$where.=" and Username like '".addslashes($Username)."'" ;
		 }
		 else {
		 	$where.=" and Username ='".addslashes($Username)."'" ;
		 }
	   	 $nocriteria=false ;
	}

// Process TextToFind parameter if any
	if (GetStrParam("TextToFind","")!="") {
	   	 $TextToFind=GetStrParam("TextToFind") ;
		 $tablelist=$tablelist.",".$dblink."memberstrads";
	 	 $where=$where." and memberstrads.Sentence like '%".addslashes($TextToFind)."%' and memberstards.IdOwner=members.id" ;
	   	 $nocriteria=false ;
	}

// Process Gender parameter if any
	if (GetStrParam("Gender","0")!="0") {
	   	 $Gender=GetStrParam("Gender") ;
	 	 $where=$where." and Gender='".addslashes($Gender)."' and HideGender='No'" ;
	   	 $nocriteria=false ;
	}

// Process Age parameter if any
	if (GetStrParam("Age","")!="") {
	   	 $Age=GetStrParam("Age") ;
		 if ($Age{0}==">") {
		 	$Age=substr($Age,1) ;
		 	$operation="BirthDate<(NOW() - INTERVAL ".$Age." YEAR)" ;
		 }
		 elseif ($Age{0}=="<") {
		 	$Age=substr($Age,1) ;
		 	$operation="BirthDate>(NOW() - INTERVAL ".$Age." YEAR)" ;
		 }
		 else {
			$Age1=$Age-1 ;
		 	$operation="BirthDate>(NOW()- INTERVAL ".$Age." YEAR) and BirthDate<(NOW() - INTERVAL ".$Age1." YEAR) " ;
		 }
		 
		 
	 	 $where=$where." and ".$operation." and HideBirthDate='No'" ;
	   	 $nocriteria=false ;
	}

	$where.=" and cities.id=members.IdCity and countries.id=cities.IdCountry" ;

	if (!IsLoggedIn()) { // case user is not logged in
	   $where.=" and  memberspublicprofiles.IdMember=members.id" ; // muts be in the public profile list
	   $tablelist=$tablelist.",".$dblink."memberspublicprofiles" ;
	}
	
	if (GetParam("IdCountry",0)!=0) {
	   $where.=" and countries.id=".GetParam("IdCountry") ;
	   $nocriteria=false ;
	}
	
// if a group is chosen
	if (GetParam("IdGroup",0)!=0) {
	   $tablelist=$tablelist.",".$dblink."membersgroups" ;
	   $where.=" and membersgroups.IdGroup=".GetParam("IdGroup")." and membersgroups.Status='In' and membersgroups.IdMember=members.id" ;
	   $nocriteria=false ;
	}
	

	if ($nocriteria) {
	   die("You must specify at least one criteria\n") ;
	}

	$rCount=LoadRow("select count(*) as cnt from ".$tablelist.$where) ;
	$str="select members.id as IdMember,members.BirthDate,members.HideBirthDate,members.Accomodation,members.Username as Username,members.LastLogin as LastLogin,cities.Name as CityName,count(comments.id) as NbComment,countries.Name as CountryName,ProfileSummary,Gender,BirthDate from ".$tablelist.$where." group by members.id ".$OrderBy." limit ".$start_rec.",".$limitcount; ;
	if (HasRight("Admin")) echo "<b>$str</b><br>" ;
	$qry = sql_query($str);
	while ($rr = mysql_fetch_object($qry)) {

	  $rr->ProfileSummary=FindTrad($rr->ProfileSummary,true,$rCount->cnt);
     $photo=LoadRow("select SQL_CACHE * from membersphotos where IdMember=" . $rr->IdMember . " and SortOrder=0");
//	  echo "photo=",$photo->FilePath,"<br>" ;
	  if (isset($photo->FilePath)) $rr->photo=$photo->FilePath;
	  else $rr->photo="" ;
	  
	  if ($rr->HideBirthDate=="No") {
	  	 $rr->Age=floor(fage_value($rr->BirthDate)) ;
	  }
	  else {
	  	 $rr->Age=ww("Hidden") ;
	  }
  
	  array_push($TMember, $rr);
	}
	
	
	return($TMember) ;
} // end of buildresult

/*
if (strlen(rtrim(ltrim(GetStrParam("searchtext"))))<=1) { // if void search don't search !
	DisplayResults($TList, GetStrParam("searchtext")); // call the layout with no results
	exit(0) ;
} 
*/

// rebuild the group list
$str = "select SQL_CACHE * from groups";
$qry = sql_query($str);
$TGroup = array ();
while ($rr = mysql_fetch_object($qry)) {
	array_push($TGroup, $rr);
}

$TList=array() ;

switch (GetParam("action")) {


	case "" : // initial form displayed
		 DisplayFindPeopleForm($TGroup,$TList,0) ;
		 break ;

	case "Find" : // Compute and Show the results 
		 $TList=buildresult() ;
		 DisplayFindPeopleForm($TGroup,$TList,$rCount->cnt) ;
		 break ;
}

?>