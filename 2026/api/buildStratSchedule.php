<?php
/*
  Build the Strategic Scouting match schedule
*/
class BuildStratSchedule
{
  // public function __construct()
  // {
  // }

  // Get all the teams in a match
  private static function getTeamsInMatch($match)
  {
    $teams = array();
    foreach ($match["alliances"]["red"]["team_keys"] as $redTeam)
      array_push($teams, substr($redTeam, 3));
    foreach ($match["alliances"]["blue"]["team_keys"] as $blueTeam)
      array_push($teams, substr($blueTeam, 3));
    return $teams;
  }

  // Get our strategic scouting matches from the event matches and our watch list
  public static function getMatches($evtMatches, $watchList)
  {
    // Go thru all the matches and figure out which ones are our matches.
    $ourMatches = array();  // Our matches at the event
    foreach ($evtMatches["response"] as $evtMatch)
    {
      // Put all this match's teams in $teams, then check for our teamnumber.
      $teamNums = self::getTeamsInMatch($evtMatch);

      // If a team number is ours, then this match is one of ours. 
      if (in_array((String) OURTEAM, $teamNums, true))
      {
        // error_log("  ---> found one of our matches: $matchnum");
        $myMatch = array();  // store this match's num and teamNums in myMatch
        $myMatch["comp_level"] = $evtMatch["comp_level"];
        $myMatch["match_number"] = $evtMatch["match_number"];
        $myMatch["teams"] = $teamNums;
        array_push($ourMatches, $myMatch);
      }
    }

    // Now we have the list of our matches (with the teams).
    // For each of the event matches, go thru the teams in the match. Get the full match list 
    // for each team in the match and hang on to their list of matches that are earlier 
    // (lower number) than that match. Those are matches we want to strategic scout. So store 
    // as match# and teams.
    $stratMatches = array();
    foreach ($evtMatches["response"] as $evtMatch)
    {
      // Get this match's teams; for each: get their match numbers. Any match# that is less 
      // than this match#, save it with that team number.
      if ($evtMatch["comp_level"] === "qm")   // Only care about Qual matches
      {
        // Get the basic event match info
        $matchInfo = array();
        $matchInfo["comp_level"] = $evtMatch["comp_level"];
        $matchInfo["match_number"] = $evtMatch["match_number"];
        $holdStr = $evtMatch["match_number"]; //TEST
        //error_log("==>> DOING match = $holdStr");
        $matchInfo["time"] = $evtMatch["time"];
        $matchInfo["predicted_time"] = $evtMatch["predicted_time"];
        $matchInfo["actual_time"] = $evtMatch["actual_time"];
        $matchInfo["teams"] = array();
        $stratTeams = array();

        // Build a team list for this match
        $evtTeams = self::getTeamsInMatch($evtMatch);
        $watchTeams = json_decode($watchList, true);

        // For each event team, search through our matches to see if we play them later
        foreach ($evtTeams as $evtTeam)
        {
          // First check if this team is in the watch list; if so, just push to stratTeams 
          // directly and go on to the next team.
          //error_log("   ==>> looking at team = $evtTeam");
          $sTeam = array();
          $sTeam["teamnum"] = $evtTeam;
          $sTeam["our_matchnums"] = array();
          $inWatchList = false;
          foreach ($watchTeams as $watchTeam)
          {
            if ($evtTeam === $watchTeam["teamnumber"])
            {
              $inWatchList = true;
              if ($watchTeam["status"] === "watch")
              {
                // Add to strategic team list to scout
                array_push($stratTeams, $sTeam);
                //error_log("      ==>> put watch team in stratTeams: $evtTeam");
              }
              else if ($watchTeam["status"] === "ignore")
              {
                // Do nothing, and go on to next team in evtTeams
              }
            }
          }

          // If not in watchList, scan the schedule to make a list of teams we haven't played yet
          if (!$inWatchList)
          {
            $omatchnums = array();
            $inOurMatch = false;
            // Go thru each of OUR matches and look for this evtTeam.
            foreach ($ourMatches as $ourMatch)
            {
              // If this evtMatch is earlier than ourMatchNum, check if it has a team in ourMatch
              if ((int) $evtMatch["match_number"] < (int) $ourMatch["match_number"])
              {
                foreach ($ourMatch["teams"] as $oTeam)
                {
                  // Don't check our own team number, just continue
                  if ($oTeam === OURTEAM)
                    continue;

                  // Check if this event team is in one of our matches.
                  if ($evtTeam === $oTeam)
                  {
                    //error_log("        ==>> found a team to scout: $evtTeam");
                    // This evtTeam is in one of our matches, so add to stratTeams if not yet ib.
                    $inOurMatch = true;
                    $alreadyListed = false;
                    foreach ($stratTeams as $stratTeam)
                    {
                      if ($stratTeam["teamnum"] === $evtTeam)
                      {
                        $alreadyListed = true;
                        break;
                      }
                    }

                    if (empty($stratTeams) || !$alreadyListed)
                    {
                      // This evtTeam is one that we want to scout, so add our match# to its data
                      array_push($omatchnums, $ourMatch["match_number"]);
                      $holdStr = $ourMatch["match_number"]; //TEST
                      //error_log("           ==>> it is in ourmatch: $holdStr");
                    }
                  }
                }
              }
            }
            if($inOurMatch == true)
            {
              $sTeam["our_matchnums"] = $omatchnums; 
              array_push($stratTeams, $sTeam); // put this team on the list of teams to scout
            }
          }
        }

        // Build up a string that has all the teams to scout in this match, each followed by 
        // parentheses that hold the lowest match number that they play with/against us. 
        $teamStr = "";
        foreach ($stratTeams as $sTeam)
        {
          if ($teamStr !== "")
            $teamStr .= nl2br("\n");             // Append a newline if there already are teams 
          $teamStr .= $sTeam["teamnum"];  // Append the teamnum to the teamStr

          // After the teamnum, add our earliest match number in parentheses.
          // SORT the match numbers to find which is the lowest (earliest).
          $lowestMatchNum = 1000; 
          foreach ($sTeam["our_matchnums"] as $oMatchNum)
          {
            if($oMatchNum < $lowestMatchNum)
              $lowestMatchNum = $oMatchNum; 
          }
          if($lowestMatchNum != 1000)
            $teamStr .= " ($lowestMatchNum)";        
        }
        $matchInfo["teams"] = $teamStr;
        //error_log("==>> teamStr = $teamStr");
        array_push($stratMatches, $matchInfo);
      }
    }
    return $stratMatches;
  }
}

?>

