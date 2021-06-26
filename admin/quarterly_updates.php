<?php
// load basic data
echo "Connecting to DB...";
include_once("connect.php");
include_once('displayFuncs.php');
include_once('busiFuncs.php');
include_once('mapData.php');
include_once('coordinates.inc');



//BREAK ANY POSSIBLE SEALS IF LB TIME IS UP
$cityRumors = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='50000'"));
if ($cityRumors['checktime'] > 0)
{
  $minFromBreak = intval(((time()/60)-$cityRumors['checktime']*60));
	if(43200-$minFromBreak<=0){
		echo "</br>\n TIMER UP FOR LAST BATTLE FORCE BREAKING ALL SEALS";
		mysqli_query($db,"UPDATE Items SET owner=99999 WHERE type =0");
		echo mysqli_error($db);
	}
}






echo "Starting Quarterly Updates...";

$curtime=time();
$check=intval(time()/3600);
$qcheck = intval(time()/900);
$lastBattleDone = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Contests WHERE type='99' AND done='1'"));
$msgs = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='0'"));
$funtime = 1;

echo "Updating Users...";
mysqli_query($db,"LOCK TABLES Users WRITE;");
$charq = mysqli_query($db,"SELECT * FROM Users WHERE 1");

//Loop through characters
while ($char = mysqli_fetch_array($charq))
{
  $id = $char['id'];

  if ($char['donor']) 
  {  
    $battlelimit = $battlelimit*2*$funtime;
    $maxquests = $maxquests*2;
    $maxalts = $maxalts*2;
    $maxsocalts += 1;
  }
  if ($funtime == 2) 
  {
    $battlelimit = 400;
    $maxalts=1;  
  }
  
  // UPDATE USER DATA
  if ($char['lastcheck'] > 100000000) $char['lastcheck'] = intval(time()/900)-120;

 // if (time() - ($char['lastcheck'] * 900) >= 900) // EVERY 15
  //{
    $quarterspast=floor((time() - ($char['lastcheck'] * 900))/900);
    if ($quarterspast > 960) $quarterspast = 960;
    $hourspast=$check - floor($char['lastcheck']/4);
    if ($hourspast > 960) $hourspast = 960;    
	

    // PROF. BONUSES
    if ($hourspast >= 1)
    {
		
	echo "\n</br>";
	echo "Checking character: ".$char['name']." ".$char['lastname'];
  
	echo "   Current gold: ".json_encode($char['gold']);
      $jobs= unserialize($char['jobs']);
      $pro_stats=cparse(getAllJobBonuses($jobs));
      if ($pro_stats['eV'] && $location_array[$char['location']][2])
      {
        $char['gold'] += $hourspast*round($char['level']*$jobs[2]*2);
      }
      if ($pro_stats['cV']) 
      {
        $resultf = mysqli_fetch_array(mysqli_query($db,"SELECT COUNT(*) FROM Users WHERE location='$char[location]' "));
        $numchar = $resultf[0];
        $char['gold'] += $hourspast*$jobs[3]*$numchar*10;
      }
	  
	  echo "  | New gold: ".json_encode($char['gold']);
      if ($pro_stats['eV'] || $pro_stats['cV']) mysqli_query($db,"UPDATE Users SET gold='".$char['gold']."' WHERE id='$id'");
  
      // UPDATE STAMINA
      if ($location_array[$char['location']][2]) $stamup=2;
      else $stamup=1;
      $stamina = $char['stamina']+$stamup*$hourspast;
      if ($stamina > $char['stamaxa']) $stamina= $char['stamaxa'];
      if ($stamina < 0) $stamina = 0;
      mysqli_query($db,"UPDATE Users SET stamina='$stamina' WHERE id='$id'");
		echo "   Current battles: ".json_encode($char['battlestoday']);
      // UPDATE BATTLES AND RESOURCE COLLECTING
      $battlestoday = $char['battlestoday']-2*$hourspast;
      if ($battlestoday > $battlelimit) $battlestoday = $battlelimit;
      if ($battlestoday < 0) $battlestoday = 0;
	  echo  " | New battles: ".json_encode($battlestoday);
      mysqli_query($db,"UPDATE Users SET battlestoday='$battlestoday' WHERE id='$id'");
      $char['battlestoday'] = $battlestoday;
    }


    // UPDATE LAST CHECKED
    $query = "UPDATE Users SET lastcheck='$qcheck', lastscript='".time()."' WHERE id='$id'";
    $result = mysqli_query($db,$query);

    // IF IT HAS BEEN MORE THAN 15 MIN SINCE LAST USE
    if ($quarterspast > 1) 
    {
      mysqli_query($db,"UPDATE Users SET find_battle='0' WHERE id='$id'"); // CLEAR TEN MINUTE ARRAY
    }
  //}
}
mysqli_query($db,"UNLOCK TABLES;");  


// UPDATE CLAN BATTLE PARTICIPATION
mysqli_query($db,"LOCK TABLES Contests WRITE, Soc WRITE, Users WRITE;");
$cbs = (mysqli_query($db,"SELECT id, starts, ends, contestants, results, location, participation FROM Contests WHERE starts <= $check AND done = 0"));
while ($cb = mysqli_fetch_array($cbs))
{
  echo "Update participation in CB $cb[id]...";
  $qago = ($qcheck-1)*900;
  $bhour = intval($qago/3600)-$cb['starts']+1;
  if ($bhour > 0)
  {
    $cb_clans = unserialize($cb['contestants']);
    $areas = $map_data[$cb['location']];
    $areas[4] = $cb['location'];  
    $cpart = unserialize($cb['participation']);
    if (!$cpart) $cpart = array();
    if (!$cpart[$bhour]) $cpart[$bhour] = array();
    
    // Loop over all clans in the battle
    foreach ($cb_clans as $cid => $cdata)
    {
      // Calculate the sum of the levels of all members of the clan
      $clan = mysqli_fetch_array(mysqli_query($db,"SELECT id, name FROM Soc WHERE id='$cid' "));
      $clan_levels = 0;
      $clan_mems = 0;
      $members = (mysqli_query($db,"SELECT id, level FROM Users WHERE society = '".$clan['name']."'"));
      while ($member = mysqli_fetch_array($members))
      { 
        $clan_mems++;
        $clan_levels += $member['level'];
      }
      
      // Look in each of the 5 battle areas for members of the clan that haven't moved in the last 15 minutes.
      if (!$cpart[$bhour][$cid]) $cpart[$bhour][$cid] = 0;
      for ($l= 0; $l < 5; $l++)
      {
        $members = (mysqli_query($db,"SELECT id, soc_rank, level FROM Users WHERE society = '".$clan['name']."' AND location= '".$areas[$l]."' AND depart <= '$qago' "));
        while ($member = mysqli_fetch_array($members))
        {
          // Calculate the participation points from this individual member
          $a_bonus = ($member['level']/$clan_levels)*200;
          if ($l != 4) $a_bonus = $a_bonus/2; // Half points in wilderness areas
          if ($member['soc_rank'] == 1) $a_bonus = $a_bonus + 10; // Leaders earn 10 extra points
          else if ($member['soc_rank'] == 2) $a_bonus = $a_bonus + 5; // Subleaders earn 5 extra points
          $a_bonus = ceil($a_bonus);
          if ($a_bonus > 100) $a_bonus = 100;
          $cpart[$bhour][$cid] += $a_bonus;
        }
      }
    }
    $cparts = serialize($cpart);
    mysqli_query($db,"UPDATE Contests SET participation='".$cparts."' WHERE id='$cb[id]'");
  }
}
mysqli_query($db,"UNLOCK TABLES;");

// UPDATE HEROES DATA
include_once('heroes_update.php');
?>