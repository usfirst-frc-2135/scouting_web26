/*
  Match Data Processor
  Takes in match data from source and calculates averages and other derived data from it.
  Data types:
    jMatchData   - the JSON parsed match data from our scouting database
    tbaMatchData - For REBUILT: the event match data from TBA 
    pitData      - For REBUILT: the event pit scouting data  
    matchId      - the string used to identify a match competition level and match number (e.g. qm5)
    matchTuple   - a two entry tuple that identifies a match (e.g. ["qm", "5"])
*/

class matchDataProcessor {
  mData = {};          // Match (raw) data from scouting database
  tbaMatchData = {};   // For REBUILT: TBA event Match data 
  pitData = {};        // For REBUILT: pit scoutimg data 
  pData = [];          // Processed data after totals and averages calculated
  hopperCapData = [];  // Hopper Capacity table data 

  // matchDataProcess constructor 
  constructor(jMatchData, tbaMatchData, pitData, hopperCapData) {
    if(tbaMatchData == null) //TEST
      console.log("   ===>>> matchDataProcessor constructor tbaMatchData is null");
    if(pitData == null) //TEST
      console.log("   ===>>> matchDataProcessor constructor pitData is null");
    if(hopperCapData == null) //TEST
      console.log("   ===>>> matchDataProcessor constructor hopperCapData is null");
    else if(hopperCapData.length == 0)   // TEST
      console.log("   ===>>> matchDataProcessor constructor hopperCapData is empty");

    this.mData = jMatchData;
    this.tbaMatchData = tbaMatchData;   // For REBUILT
    this.pitData = pitData;             // For REBUILT
    this.hopperCapData = hopperCapData; // For REBUILT
    this.siteFilter = null;
    console.log("mdp constr: mData: num of matches = " + this.mData.length);

    // Organize the mData (raw match data) by team number
    for (let i = 0; i < this.mData.length; i++) {
      let teamNum = this.mData[i]["teamnumber"];
      if (this.pData[teamNum] === undefined) {
        this.pData[teamNum] = { teamNum: teamNum, matches: [], fuelD: [] };  // fuelD for REBUILT
      }
      this.pData[teamNum]["matches"].push(this.mData[i]);
    }

    // Sort the matches for each team by match number
    for (const teamNum in this.pData) {
      let matches = this.pData[teamNum]["matches"];
      matches.sort((a, b) => { return compareMatchNumbers(a["matchnumber"], b["matchnumber"]) });
    }
  }

  //
  // Convert to integer percent (one decimal digit)
  //
  toPercent(val) {
    return this.roundOnePlace(((val + Number.EPSILON) * 1000) / 10);
  }

  //
  // Round data to no more than one decimal digit
  //
  roundOnePlace(val) {
    return Math.round((val + Number.EPSILON) * 10) / 10;
  }

  //
  // Round data to no more than two decimal digits
  //
  roundTwoPlaces(val) {
    return Math.round((val + Number.EPSILON) * 100) / 100;
  }

  // Get the hopper capacity from the HopperCap table for the given team.  For REBUILT
  // (Also found in rebuiltFuelEstimates.js)
  getHopperCapForTeam(hopperCapData, teamnum) {
    let hopperCap = 0;
    for (let entry of hopperCapData) {
      let tnum = entry["teamnumber"];
      console.log( "    HOPPERCAP check---> comparing teamnum " + teamnum + " with tnum " + tnum);
      if (tnum === teamnum) {
        hopperCap = entry["hoppercap"]
        console.log("   !!!-> FOUND HOPPERCAP: teamnum "+teamnum+", hopperCap = " + hopperCap);
        break;
      }
    }
    return hopperCap;
  }

  //
  // Fix match IDs that are missing the comp level
  //
  getFixedMatchId(matchId) {
    if (matchId != 0) {
      matchId = matchId.toLowerCase();
      if ((matchId.search("p") != -1) || (matchId.search("qm") != -1) ||
        (matchId.search("sf") != -1) || (matchId.search("f") != -1)) {
        return matchId;
      }
      else {  // Attempt to repair bad match IDs but log them
        console.warn("getMatchTuple: Invalid matchId! " + matchId);
        return "qm" + matchId;
      }
    }
    else {
      console.warn("getMatchTuple: Invalid matchId! " + matchId);
      return "qm" + matchId;
    }
  }

  //
  // Get the comp level and match number from the match ID string (ex. [qm, 25] from qm25)
  //
  getMatchTuple(matchId) {
    matchId = this.getFixedMatchId(matchId);
    if (matchId.search("p") != -1) {
      return ["p", parseInt(matchId.substring(1))];
    }
    else if (matchId.search("qm") != -1) {
      return ["qm", parseInt(matchId.substring(2))];
    }
    else if (matchId.search("sf") != -1) {
      return ["sf", parseInt(matchId.substring(2))];
    }
    else if (matchId.search("f") != -1) {
      return ["f", parseInt(matchId.substring(1))];
    }
    else {  // Repair bad match IDs but report them
      console.warn("getMatchTuple: Invalid match prefix! " + matchId);
    }
    return null;
  }

  //
  // Compare if second match ID is larget than first match ID
  //
  isMatchLessThanOrEqual(startMatchId, endMatchId) {
    let smt = this.getMatchTuple(startMatchId);
    let emt = this.getMatchTuple(endMatchId);

    let compLevel = { "p": 0, "qm": 1, "sf": 3, "f": 4 };
    if (smt === null || emt === null) {
      return false;
    }
    if (compLevel[smt[0]] < compLevel[emt[0]]) {
      return true;
    }
    if (compLevel[smt[0]] > compLevel[emt[0]]) {
      return false;
    }
    return smt[1] <= emt[1];
  }

  //
  // Compare if match ID string is within two match ID endpoints
  //
  ifMatchInRange(startMatchId, matchId, endMatchId) {
    return this.isMatchLessThanOrEqual(startMatchId, matchId) && this.isMatchLessThanOrEqual(matchId, endMatchId);
  }

  //
  // Filters out all matches in this.mData not within the specified range (destructively changes this.mData)
  //
  filterMatchRange(startMatchId, endMatchId) {
    let newData = [];
    for (let i = 0; i < this.mData.length; i++) {
      let matchId = this.mData[i]["matchnumber"];
      if (this.ifMatchInRange(startMatchId, matchId, endMatchId)) {
        newData.push(this.mData[i]);
      }
    }
    this.mData = newData;
  }

  //
  // Sorts the data by match number (ignores comp_level)
  //
  sortMatches(newData) {
    console.log("matchDataProcessor: sortMatches:");
    newData.sort(function (a, b) {
      let compare = this.isMatchLessThanOrEqual(a["matchnumber"], b["matchnumber"]);
      return (compare) ? -1 : 1;
    });
  }

  //
  //  Modify match data to only include matches specified by the site filter
  //
  applySiteFilter() {
    let newData = [];
    for (let i = 0; i < this.mData.length; i++) {
      let matchId = this.mData[i]["matchnumber"];
      let mt = this.getMatchTuple(matchId);
      if (mt === null) {
        mt = ["qm", null];
      }
      if (mt[0] === "p" && this.siteFilter["useP"]) { newData.push(this.mData[i]); }
      else if (mt[0] === "qm" && this.siteFilter["useQm"]) { newData.push(this.mData[i]); }
      else if (mt[0] === "sf" && this.siteFilter["useSf"]) { newData.push(this.mData[i]); }
      else if (mt[0] === "f" && this.siteFilter["useF"]) { newData.push(this.mData[i]); }
    }
    this.mData = [...newData];
  }

  //
  // Filters match data based on the retrieved site filter from DB config
  //
  getSiteFilteredAverages(processorFunction) {
    let tempThis = this;
    $.post("api/dbAPI.php", {
      getDBStatus: true
    }, function (dbStatus) {
      let jDbStatus = JSON.parse(dbStatus);
      let newSiteFilter = {};
      newSiteFilter["useP"] = jDbStatus["useP"];
      newSiteFilter["useQm"] = jDbStatus["useQm"];
      newSiteFilter["useSf"] = jDbStatus["useSf"];
      newSiteFilter["useF"] = jDbStatus["useF"];
      tempThis.siteFilter = { ...newSiteFilter };

      tempThis.applySiteFilter();

      processorFunction(tempThis.mData, tempThis.getEventAverages());
    });
  }

  //  
  // Initialize match item statistics: 
  // Sets up a new data entry for the given team array, if none yet exists for this keyword, where 
  //    "item" is the array holding match raw data for a specific team (from this.pData) and
  //    "itemField" is the mdp keyword for this data (i.e. "autonShootPreload").
  // Sets up the new entry to have these fields and defaults:
  //     val: 0, sum: 0, max: 0, avg: 0, acc: 0 
  //
  initializeItem(item, itemField) {
    if (!Object.prototype.hasOwnProperty.call(item, itemField)) {
      item[itemField] = { val: 0, sum: 0, max: 0, avg: 0, acc: 0 };
    }
  }

  //
  // Updates the pData match data for the given keyword (which holds an integer value) with the 
  // given raw match data and stores it in the given team array, where
  //    "item"       -> the array holding mdp data for a specific team (from this.pData). 
  //    "mdpKeyword" -> the mdp keyword for this data (i.e. "autonShootPreload").
  //    "match"      -> the raw match data table entry for a specific match and 
  //    "mtKeyword"  -> the (raw) matchTable keyword for this data (as defined in dbHandler.php).
  //
  getMatchItem(item, mdpKeyword, match, mtKeyword) {
    this.initializeItem(item, mdpKeyword);

    // If the match table keyword does not exist for the match table, use the mdp keyword instead.
    if (match[mtKeyword] === null) {
      mtKeyword = mdpKeyword;
    }

    // Saves the (raw) value for this keyword from this match in .val
    // Adds the value to the stored sum of all the matches' values for this keyword in .sum
    // Updates the max value if this value is the new max) in .max
    let value = parseInt(match[mtKeyword]);
    item[mdpKeyword].val = value;
    item[mdpKeyword].sum += value;
    item[mdpKeyword].max = Math.max(item[mdpKeyword].max, value);

    return value;
  }

  //
  // Updates the pData match data for the given keyword (which holds an array of possible values) 
  // with the given raw match data and stores it in the given team array, where
  //    "item"       -> the array holding mdp data for a specific team (from this.pData).
  //    "mdpKeyword" -> the mdp keyword for this data (i.e. "autonShootPreload").
  //    "arraySize"  -> the number of possible values (radio buttons).
  //    "match"      -> the raw match data table entry for a specific match and 
  //    "mtKeyword"  -> the (raw) matchTable keyword for this data (as defined in dbHandler.php).
  //
  getMatchArray(item, mdpKeyword, arraySize, match, mtKeyword) {

    // Initialize this entry if none exists yet for this keyword.
    if (!Object.prototype.hasOwnProperty.call(item, mdpKeyword)) {
      // The entry holds the value (val) and an array (arr) to hold info for each possible value.
      item[mdpKeyword] = { val: 0, arr: [] };

      // Sets up the arr (array) to hold this info: 
      for (let i = 0; i < arraySize; i++) {
        // Holds this data for each possible value->  sum, max, avg, acc
        item[mdpKeyword].arr[i] = { sum: 0, max: 0, avg: 0, acc: 0 };
      }
    }

    // If the match table keyword does not exist for the match table, use the mdp keyword instead.
    if (match[mtKeyword] === null) {
      mtKeyword = mdpKeyword;
    }

    // Saves the (raw) value for this keyword from this match in .val
    let value = parseFloat(match[mtKeyword]);
    item[mdpKeyword].val = value;

    // If the value (radio button value) is greater than the array size, that's BAD so exit!
    if (value >= arraySize) {
      console.error("getMatchArray: array index out of bounds! " + value + " >= " + arraySize);
      return -1;
    }
    console.log("getMatchArray: mdpKeyword = " + mdpKeyword + " value = " + value);
    // Increment the number of times this value (radio button) was used in .sum
    item[mdpKeyword].arr[value].sum += 1;
    return value;
  }

  //
  // Used for mdp team data that does not have a matching raw match table entry. 
  // Stores the given value in the given team array where 
  //    "item"       -> the array holding mdp data for a specific team (from this.pData).
  //    "mdpKeyword" -> the mdp keyword for this data 
  //    "value"      -> the value for this data 
  //
  updateItem(item, mdpKeyword, value) {
    this.initializeItem(item, mdpKeyword);
//HOLD    console.log("   XXXXX - updateItem for "+mdpKeyword+", value = "+value);
    let currSum = parseInt(item[mdpKeyword].sum);
//HOLD    console.log("     XXXXX - currSum = "+currSum);
    let newSum = parseInt(currSum + value);
    item[mdpKeyword].sum = newSum;           // Add value to the 'sum' for this mdpKeyword 
//HOLD    console.log("       XXXXX - new sum = "+item[mdpKeyword].sum);
    item[mdpKeyword].max = Math.max(item[mdpKeyword].max, value);
  }

  //
  // For REBUILT: Used for mdp fuelD data to hold the fuel estimates for each match. 
  // Stores the given value in the given team fuelD array where 
  //    "item"       -> the array holding mdp data for a specific team (pData[teamnum]).
  //    "matchnum"   -> the match number for this data
  //    "key"        -> "autonFE", "teleopFE", "tbaAutonFE", or "tbaTeleopFE"
  //    "value"      -> the value for this data 
  //
  updateMatchFuelDItem(item, matchnum, key, value)
  {
    if(key != "autonFE" && key != "teleopFE" && key != "tbaAutonFE" && key != "tbaTeleopFE") {
      console.log("!!!! ERROR! updateMatchFuelDItem() keyword '"+key+"' not recognized!!");
      return;
    }
    // Initialize this fuelD entry if not yet used.
    if (item["fuelD"][matchnum] === undefined) {
      item["fuelD"][matchnum] = { matchnum: matchnum, autonFE: 0.0, teleopFE: 0.0 };  
    }
    let teamnum = item["teamNum"]; //TEST
    item["fuelD"][matchnum][key] = value;   
//HOLD    let tvalue = this.pData[teamnum]["fuelD"][matchnum][key];
//HOLD    console.log("    !!!!!!!!!->> verifying update fuelD: "+ tvalue);
  }

  //
  // Update match item average
  //
  calcAverage(item, mdpKeyword, denominator) {
    if(item[mdpKeyword] == null) {
      console.log("calcAverage(): key doesn't exist in pData: "+mdpKeyword);
      return;
    }
    item[mdpKeyword].avg = (item[denominator] != 0) ? this.roundOnePlace(item[mdpKeyword].sum / item[denominator]) : 0;
//HOLD    console.log("    !! calcAverage( "+mdpKeyword+" ) denominator = "+item[denominator]);
//HOLD    console.log("    !! calcAverage( "+mdpKeyword+" ) sum = "+item[mdpKeyword].sum);
//HOLD    console.log("    !! calcAverage( "+mdpKeyword+" ) avg = "+item[mdpKeyword].avg);
  }

  //
  // Update match item accuracy
  //
  calcAccuracy(item, mdpKeyword, denominator) {
    item[mdpKeyword].acc = (item[denominator].sum != 0) ? this.toPercent(item[mdpKeyword].sum / item[denominator].sum) : 0;
  }

  //
  // Update match item percent array
  //
  calcArray(item, mdpKeyword, denominator) {
    for (const i in item[mdpKeyword].arr) {
      item[mdpKeyword].arr[i].avg = (item[denominator] != 0) ? this.toPercent(item[mdpKeyword].arr[i].sum / item[denominator]) : 0;
    }
  }

  // Find this team entry in pData.
  findPDataTeamItem(pData, teamnum) {
    for (const i in pData) {
      let teamItem = this.pData[i];
      let teamNum = teamItem["teamNum"];
      if(teamNum == teamnum)
        return teamItem;
    }
//    console.log("===> findPDataTeamItem() could not find for team: " + teamnum);  
    return null;
  }

  // For REBUILT: Calculate the estimated fuel totals based on the TBA match data and store the 
  // results in the given MDP pData for each team.
  calcMatchesFuelEstTBA(pData, tbaMatchData) 
  {
    // Go thru the TBA matches and process each match (all 6 teams) to get the auton and teleop
    // fuel estimates using the TBA auton fuel total and teleop fuel total. Distribute those TBA
    // totals between the 3 teams based on the ratio of the basic fuel estimates.
    for (let emi in tbaMatchData) {
      let match = tbaMatchData[emi];
      if (match["comp_level"] !== "qm") { // Limit to qual matches for now
        continue;
      }

      let matchNum = match["match_number"]; 
      let matchId = match["comp_level"] + matchNum;   // i.e. "qm3"

      this.calcAllianceFuelEstTBA(pData, match, "red");
      this.calcAllianceFuelEstTBA(pData, match, "blue");
    }
  } 

  // For REBUILT: Calculate the final (TBA) estimated fuel totals for the given match and alliance.
  // Save the results for each team in pData, where "aColor" (alliance color) is "red" or "blue".
  calcAllianceFuelEstTBA(pData, match, aColor)
  {
    let alliances = match["alliances"];
    let matchnum = match["comp_level"]+match["match_number"];
//HOLD    console.log("---> calcAllianceFuelEstTBA() matchnum = "+matchnum);

    // Get the teams in this alliance
    let teams = [];
    teams[0] = alliances[aColor]["team_keys"][0];
    teams[1] = alliances[aColor]["team_keys"][1];
    teams[2] = alliances[aColor]["team_keys"][2];

    for (let i = 0; i < teams.length; i++) {
      // Remove leading "frc" if any
      if (teams[i].startsWith("frc")) {
        teams[i] = teams[i].substring(3);
      }
    }
//HOLD    console.log("   ---> "+aColor+" Teams = "+teams[0]+", "+teams[1]+", "+teams[2]);

    // Get the 3 teams' current fuel estimates and also get the tbaMatch total fuel counts.
    if(match["score_breakdown"] != null && match["score_breakdown"][aColor] != null && match["score_breakdown"][aColor]["hubScore"] != null && match["score_breakdown"][aColor]["hubScore"]["autoPoints"] != null) 
    {
      let tbaBreakdown = match["score_breakdown"];
      let tbaBreakdownAFuel = parseFloat(tbaBreakdown[aColor]["hubScore"]["autoPoints"]); 
      console.log("    ---> tbaBreakdownAFuel ("+aColor+") = "+tbaBreakdownAFuel);
      let tbaBreakdownTFuel = parseFloat(tbaBreakdown[aColor]["hubScore"]["teleopPoints"]);
      console.log("    ---> tbaBreakdownTFuel ("+aColor+") = "+tbaBreakdownTFuel);

      // Find the corresponding team pData item.
      let pDataTeam1 = this.findPDataTeamItem(pData, teams[0]);
      let pDataTeam2 = pData[teams[1]];
      let pDataTeam3 = pData[teams[2]];

      // Return if any of the teams don't have a pData item.
      if ( pData[teams[0]] == null || pData[teams[1]] == null || pData[teams[2]] == null) {
        console.log("  --> calcAllianceFuelEstTBA(): team pData not found!");
        return;
      }
      // Return if any of the teams fuelD doesn't have an entry for this matchnum.
      if ( pData[teams[0]]["fuelD"][matchnum] == null || pDataTeam2["fuelD"][matchnum] == null || pDataTeam3["fuelD"][matchnum] == null) {
        console.log("  -->>> calcAllianceFuelEstTBA(): pData team fuelD not found!");
        return;
      }
  
      // Now get the existing fuelD data (should be the basic fuel estimates w/o TBA data)
      let autoFuel1 = parseFloat(pDataTeam1["fuelD"][matchnum]["autonFE"]);
      let autoFuel2 = parseFloat(pDataTeam2["fuelD"][matchnum]["autonFE"]);
      let autoFuel3 = parseFloat(pDataTeam3["fuelD"][matchnum]["autonFE"]);
      let teleopFuel1 = parseFloat(pDataTeam1["fuelD"][matchnum]["teleopFE"]);
      let teleopFuel2 = parseFloat(pDataTeam2["fuelD"][matchnum]["teleopFE"]);
      let teleopFuel3 = parseFloat(pDataTeam3["fuelD"][matchnum]["teleopFE"]);
      console.log("  ---->>> basic autoFuel = "+autoFuel1+", "+autoFuel2+", "+autoFuel3);

      // Calculate ratio of each robot's contribution to the the base auton total fuel.
      let autoSum = Number(autoFuel1 + autoFuel2 + autoFuel3).toFixed(2);
      console.log("    ---->> autoSum = "+autoSum);

      let autoRatio1 = Number(autoFuel1 / autoSum).toFixed(2);
      let autoRatio2 = Number(autoFuel2 / autoSum).toFixed(2);
      let autoRatio3 = Number(autoFuel3 / autoSum).toFixed(2);
      console.log("    ---->> autoRatios = "+autoRatio1+", "+autoRatio2+", "+autoRatio3);

      // Now use the ratios to calc each team's contribution to the actual (tba) auton fuel count.
      let autoFinal1 = Number(autoRatio1 * tbaBreakdownAFuel).toFixed(2);
      let autoFinal2 = Number(autoRatio2 * tbaBreakdownAFuel).toFixed(2);
      let autoFinal3 = Number(autoRatio3 * tbaBreakdownAFuel).toFixed(2);
      console.log("      ---->> autoFinals = "+autoFinal1+", "+autoFinal2+", "+autoFinal3);

      // Save the new tba-based fuel est for this match in fuelD..
      this.updateMatchFuelDItem(pDataTeam1, matchnum, "tbaAutonFE", autoFinal1);
      this.updateMatchFuelDItem(pDataTeam2, matchnum, "tbaAutonFE", autoFinal2);
      this.updateMatchFuelDItem(pDataTeam3, matchnum, "tbaAutonFE", autoFinal3);

      // Now do teleop calcs.
      console.log("  ---->>> basic teleopFuel = "+teleopFuel1+", "+teleopFuel2+", "+teleopFuel3);
      let teleSum = Number(teleopFuel1 + teleopFuel2 + teleopFuel3).toFixed(2);
      console.log("    ---->>> teleSum = "+teleSum);
      let teleRatio1 = Number(teleopFuel1 / teleSum).toFixed(2);
      let teleRatio2 = Number(teleopFuel2 / teleSum).toFixed(2);
      let teleRatio3 = Number(teleopFuel3 / teleSum).toFixed(2);
      console.log("    ---->> teleopRatios = "+teleRatio1+", "+teleRatio2+", "+teleRatio3);

      // Use the ratios to calc each team's contribution to the actual (tba) teleop fuel count.
      let teleFinal1 = Number(teleRatio1 * tbaBreakdownTFuel).toFixed(2);
      let teleFinal2 = Number(teleRatio2 * tbaBreakdownTFuel).toFixed(2);
      let teleFinal3 = Number(teleRatio3 * tbaBreakdownTFuel).toFixed(2);
      console.log("      ---->> teleFinals = "+teleFinal1+", "+teleFinal2+", "+teleFinal3);

      // Save the new tba-based fuel est for this match in fuelD..
      this.updateMatchFuelDItem(pDataTeam1, matchnum, "tbaTeleopFE", teleFinal1);
      this.updateMatchFuelDItem(pDataTeam2, matchnum, "tbaTeleopFE", teleFinal2);
      this.updateMatchFuelDItem(pDataTeam3, matchnum, "tbaTeleopFE", teleFinal3);

//REMOVE    // Calculate the total fuel estimates.
//REMOVE    let totalFinal1 = autoFinal1 + teleFinal1;
//REMOVE    let totalFinal2 = autoFinal2 + teleFinal2;
//REMOVE    let totalFinal3 = autoFinal3 + teleFinal3;

//REMOVE    // Update this field in regular team pData, to be used for max and avgs.
//REMOVE    this.updateItem(pDataTeam1, "totalFuelEst", totalFinal1);
//REMOVE    this.updateItem(pDataTeam2, "totalFuelEst", totalFinal2);
//REMOVE    this.updateItem(pDataTeam3, "totalFuelEst", totalFinal3);
    }
    else console.log("  --> Can't access TBA score breakdown data, so no TBA fuel ests!");
  };

  //
  // Get event averages by calculating averages from the match data
  //
  // pData - processed data structure is an array of team numbered objects with match data and 
  // calculated averages/max/percentages, with this structure: 
  //   teamNumber: {
  //     matches: [match1, match2, ...]         -> this is the raw match data from QR code 
  //     totalMatches: int
  //          -- Game specific data with corresponding QR code data:
  //     autonClimb: { val: int, sum: int, max: int, avg: float, acc: float }  -> for integer data
  //     endgameStartClimb: { val: 0, arr: [] };                               -> for array data
  //                  where arr[i] = { val: int,  sum: int, max: int, avg: float, acc: float }
  //     ...   -> Same for each piece of data (keyword) from the QR code information
  //     scoutNames: [name1, name2, ...]
  //     commentList: [comment1, comment2, ...]
  //          -- Additional data for REBUILT (has no corresponding QR code data):
  //     autonFinalFuelEst { val: float, sum: float, max: float, avg: float }  // For REBUILT
  //     teleopTotalPoints { val: float, sum: float, max: float, avg: float }  // For REBUILT
  //     totalFuelEst { val: float, sum: float, max: float, avg: float }  // For REBUILT
  //     fuelD: { matchid: matchnum, autonFE: float, teleopFE: float, tbaAutonFE: float, tbaTeleopFE: float }  // REBUILT fuelEst stored by matchnum
  //    }
  //
  getEventAverages() {
    console.log("mdp: getEventAverages starting");

    //////////////////// PROCESS ALL TEAMS ////////////////////

    //  For each team, go thru all its matches and do the calculations for this event
    for (const i in this.pData) {
      let teamItem = this.pData[i];
      let matchList = teamItem["matches"];
      let teamnum = teamItem["teamNum"];
      console.log("MDP doing team " + teamItem["teamNum"] + ", # of matches: " + matchList.length);  

      // Initialize text data for matches
      teamItem["scoutNames"] = [];
      teamItem["commentList"] = [];

      // Initialize teamItem processed data
      teamItem["totalDefenseMatches"] = 0;  // incremented each match this team played defense
      teamItem["totalMatches"] = matchList.length;

      //////////////////// PROCESS MATCHES INTO TEAM OBJECT ////////////////////
      // Save the match (raw) table data in the mdp pData for this team.
      for (const j in matchList) {
        let match = matchList[j];
        let matchnum = match["matchnumber"];
        console.log("MDP for team "+teamnum+", doing match "+matchnum);

        // NOTE: The field names on the right side of getMatchXXX must match the DB field names in the matchtable (raw scouted) database
        //       The field names on the left side of getMatchXXX must match the field names in this class (mdp)

        // For REBUILT: get hopper capacity from hopperCapData, then if not there, try pit data.
        let hopperCap = 0;  // default
        if(this.hopperCapData != null && this.hopperCapData.length > 0) 
          hopperCap = this.getHopperCapForTeam(this.hopperCapData,teamnum);
        if(hopperCap == 0) {  // Now check the pitData
          if(this.pitData != null && this.pitData[teamnum] != null && this.pitData[teamnum]["caphopper"] != null) 
             hopperCap = this.pitData[teamnum]["caphopper"];   
          else console.log("!!! NO pitData for team "+teamnum);
        }

        // Autonomous mode - save this raw match data to the mdp pData for this team.
        let preloadShot = this.getMatchItem(teamItem, "autonShootPreload", match, "autonShootPreload");
        let preloadAcc = this.getMatchItem(teamItem, "autonPreloadAccuracy", match, "autonPreloadAccuracy");
        let autonHopperShot = this.getMatchItem(teamItem, "autonHoppersShot", match, "autonHoppersShot");
        let autonHopperAcc = this.getMatchItem(teamItem, "autonHopperAccuracy", match, "autonHopperAccuracy");
        this.getMatchItem(teamItem, "autonAllianceZone", match, "autonAllianceZone");
        this.getMatchItem(teamItem, "autonDepot", match, "autonDepot");
        this.getMatchItem(teamItem, "autonOutpost", match, "autonOutpost");
        this.getMatchItem(teamItem, "autonNeutralZone", match, "autonNeutralZone");
        this.getMatchArray(teamItem, "autonClimb", 5, match, "autonClimb");
//??        this.getMatchItem(teamItem, "autonClimb", match, "autonClimb");
         
        // For REBUILT: calc basic auton fuel estimate for this team/match, store in pData:fuelD
        let autonEst = calcAutonTotalFuel(hopperCap, preloadShot, autonHopperShot, preloadAcc, autonHopperAcc);
        this.updateMatchFuelDItem(teamItem, matchnum, "autonFE", autonEst);

        // Teleop mode: save raw match data to mdp
        let teleopHoppersShot = this.getMatchItem(teamItem, "teleopHoppersUsed", match, "teleopHoppersUsed");
        let teleopHopperAcc = this.getMatchItem(teamItem, "teleopHopperAccuracy", match, "teleopHopperAccuracy");
        this.getMatchItem(teamItem, "teleopIntakeAndShoot", match, "teleopIntakeAndShoot");
        this.getMatchItem(teamItem, "teleopNeutralToAlliance", match, "teleopNeutralToAlliance");
        this.getMatchItem(teamItem, "teleopAllianceToAlliance", match, "teleopAllianceToAlliance");
        this.getMatchItem(teamItem, "teleopPassingRate", match, "teleopPassingRate");

        let matchDefenseLevel = this.getMatchItem(teamItem, "teleopDefenseLevel", match, "teleopDefenseLevel");
        if (matchDefenseLevel != 0) {
          teamItem["totalDefenseMatches"] += 1;  // increment if this team played defense
        }

        // For REBUILT: calculate basic teleop fuel est for this team/match and store in mdp pData.
        let teleopFuelEst = calcTeleopTotalFuel(hopperCap, teleopHoppersShot, teleopHopperAcc);
        this.updateMatchFuelDItem(teamItem, matchnum, "teleopFE", teleopFuelEst);

        // Endgame
        this.getMatchArray(teamItem, "endgameStartClimb", 5, match, "endgameStartClimb");
        this.getMatchArray(teamItem, "endgameClimbLevel", 4, match, "endgameClimbLevel");

        this.getMatchArray(teamItem, "died", 6, match, "died");

        // Append text data for matches
        teamItem["scoutNames"].push(match["matchnumber"] + " - " + match["scoutname"]);
        teamItem["commentList"].push(match["matchnumber"] + " - " + match["comment"]);
      }
    }

    /////////// Calculate final fuel estimates using TBA data
    // For REBUILT: Calc auton and teleop fuel estimates using the TBA match data, for all teams 
    // and matches; (Note: this requires the basic fuel estimates to already be done.)
    // Store the calc'd data in pData for each team.
    this.calcMatchesFuelEstTBA(this.pData,this.tbaMatchData);    // for REBUILT

    /////////// Calculate Total Points  
    // Now go thru the pData again and calc total points and final numbers.
    // For each team, go thru all its matches and do the calculations for this event
    for (const i in this.pData) {
      let teamItem = this.pData[i];
      let teamNum = i; 
      console.log(">>>>> Calculating total points for team: "+teamNum);
      let matchList = teamItem["matches"];
      for (const j in matchList) {
        let match = matchList[j];
        let matchnum = match["matchnumber"]

        ////////////  Calculate scoring totals that use fuel estimates.
        // Calc the total auton points for this match (fuel est plus climb).
        // Get the final auton fuel est for this match from the fuelD data.
        let autonFinalFuelEst = Math.round(teamItem["fuelD"][matchnum]["autonFE"]);  // default with basic est 

        // If there's a TBA-based auton est, use it instead of basic est.
        if(teamItem["fuelD"][matchnum]["tbaAutonFE"] != null) {
          autonFinalFuelEst = Math.round(teamItem["fuelD"][matchnum]["tbaAutonFE"]);
//HOLD          console.log("     --> Team "+teamNum+", match "+matchnum+" - ttl auto pts is using TBA value: "+autonFinalFuelEst);
        }
//HOLD        else console.log("    --> match "+matchnum+" - ttl auto pts is using basic value: "+autonFinalFuelEst);

        // Get automn climb points.
        let autonClimbPoints = 0;
        let autonClimb = match["autonClimb"];
        switch (String(autonClimb)) {
          case "1": autonClimbPoints = 15; break;  // Back
          case "2": autonClimbPoints = 15; break;  // Left
          case "3": autonClimbPoints = 15; break;  // Front
          case "4": autonClimbPoints = 15; break; // Right
          default: autonClimbPoints = 0; break;   // No climb
        }
        let autonTotalPoints = parseInt(autonFinalFuelEst) + parseInt(autonClimbPoints);

        this.updateItem(teamItem, "autonClimbPoints", autonClimbPoints);
        this.updateItem(teamItem, "autonTotalPoints", autonTotalPoints);
        this.updateItem(teamItem, "autonFinalFuelEst", autonFinalFuelEst);

        let teleopFinalFuelEst = parseInt(teamItem["fuelD"][matchnum]["teleopFE"]);   // default  w basic est

        // If there's a TBA-based teleop est, use it instead of basic est.
        if(teamItem["fuelD"][matchnum]["tbaTeleopFE"] != null) {
          teleopFinalFuelEst = parseInt(teamItem["fuelD"][matchnum]["tbaTeleopFE"]);
//HOLD          console.log("    --> final teleop fuel est is using TBA value: "+teleopFinalFuelEst);
        }
//HOLD        else console.log("     --> final teleop fuel points is using basic value: "+teleopFinalFuelEst);
        this.updateItem(teamItem, "teleopTotalPoints", teleopFinalFuelEst);

        let endgameClimbPoints = 0;
        switch (String(teamItem["endgameClimbLevel"].val)) {
          case "1": endgameClimbPoints = 10; break;  // L1
          case "2": endgameClimbPoints = 20; break;  // L2
          case "3": endgameClimbPoints = 30; break;  // L3
          default: endgameClimbPoints = 0; break;   // No climb
        }
        this.updateItem(teamItem, "endgamePoints", endgameClimbPoints);

        let totalMatchPoints = parseInt(autonTotalPoints) + parseInt(teleopFinalFuelEst) + parseInt(endgameClimbPoints);
        this.updateItem(teamItem, "totalMatchPoints", totalMatchPoints);
      }

      //////////////////// CALCULATE AVERAGES USING TOTAL MATCH COUNT ////////////////////
      // NOTE: some of the keywords used may not exist in pData, if there was no TBA calculations.
      // Autonomous mode
      console.log(">>>>> Calculating averages for team: "+teamNum);
      this.calcAverage(teamItem, "autonFinalFuelEst", "totalMatches");
      this.calcAverage(teamItem, "autonClimbPoints", "totalMatches");

      // Teleop mode
      this.calcAverage(teamItem, "teleopTotalPoints", "totalMatches");

//HOLD for example      // Divide coral/algae pieces by acquired pieces
//HOLD for example      this.calcAccuracy(teamItem, "teleopCoralPieces", "teleopCoralAcquired");
//HOLD for example      this.calcAccuracy(teamItem, "teleopAlgaePieces", "teleopAlgaeAcquired");

      // Defense avg 
      this.calcAverage(teamItem, "teleopDefenseLevel", "totalDefenseMatches");

      this.calcArray(teamItem, "died", "totalMatches");

      // Endgame 
      this.calcArray(teamItem, "endgameStartClimb", "totalMatches");
      this.calcArray(teamItem, "endgameClimbLevel", "totalMatches");

      // Points by game phase
      this.calcAverage(teamItem, "autonTotalPoints", "totalMatches");
      this.calcAverage(teamItem, "totalMatchPoints", "totalMatches");
      this.calcAverage(teamItem, "endgamePoints", "totalMatches");
    }
    return this.pData;
  }
}

