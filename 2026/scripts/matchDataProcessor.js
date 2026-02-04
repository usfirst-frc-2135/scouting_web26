/*
  Match Data Processor
  Takes in match data from source and calculates averages and other derived data from it.
  Data types:
    jMatchData - the JSON parsed match data from our scouting database
    matchId - the string used to identify a match competition level and match number (e.g. qm5)
    matchTuple - a two entry tuple that identifies a match (e.g. ["qm", "5"])
*/
class matchDataProcessor {
  mData = {};   // Match data from scouting database
  pData = [];   // Processed data after totals and averages calculated

  constructor(jMatchData) {
    this.mData = jMatchData;
    this.siteFilter = null;
    console.log("matchDataProcessor: MatchData: num of matches = " + this.mData.length);

    // Organize the match data by team number
    for (let i = 0; i < this.mData.length; i++) {
      let teamNum = this.mData[i]["teamnumber"];
      if (this.pData[teamNum] === undefined) {
        this.pData[teamNum] = { teamNum: teamNum, matches: [] };
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
  // Initialize match item statistics
  //
  initializeItem(item, itemField) {
    if (!Object.prototype.hasOwnProperty.call(item, itemField)) {
      item[itemField] = { val: 0, sum: 0, max: 0, avg: 0, acc: 0 };
    }
  }

  //
  // Retrieve match data into item statistics
  //
  getMatchItem(item, itemField, match, matchField) {
    this.initializeItem(item, itemField);

    // If the match data field is null, use the item field name instead
    if (match[matchField] === null) {
      matchField = itemField;
    }

    let value = parseInt(match[matchField]);
    item[itemField].val = value;
    item[itemField].sum += value;
    item[itemField].max = Math.max(item[itemField].max, value);

    return value;
  }

  //
  // Update match array into item statistics
  //
  getMatchArray(item, itemField, arraySize, match, matchField) {
    if (!Object.prototype.hasOwnProperty.call(item, itemField)) {
      item[itemField] = { val: 0, arr: [] };
      for (let i = 0; i < arraySize; i++) {
        item[itemField].arr[i] = { sum: 0, max: 0, avg: 0, acc: 0 };
      }
    }

    // If the match data field is null, use the item field name instead
    if (match[matchField] === null) {
      matchField = itemField;
    }

    let value = parseFloat(match[matchField]);
    item[itemField].val = value;
    if (value >= arraySize) {
      console.error("getMatchArray: array index out of bounds! " + value + " >= " + arraySize);
      return -1;
    }

    item[itemField].arr[value].sum += 1;
    return value;
  }

  //
  // Update match item statistics
  //
  updateItem(item, itemField, value) {
    this.initializeItem(item, itemField);

    item[itemField].sum += value;
    item[itemField].max = Math.max(item[itemField].max, value);
  }

  //
  // Update match item average
  //
  calcAverage(item, itemField, denominator) {
    item[itemField].avg = (item[denominator] != 0) ? this.roundOnePlace(item[itemField].sum / item[denominator]) : 0;
  }

  //
  // Update match item accuracy
  //
  calcAccuracy(item, itemField, denominator) {
    item[itemField].acc = (item[denominator].sum != 0) ? this.toPercent(item[itemField].sum / item[denominator].sum) : 0;
  }

  //
  // Update match item percent array
  //
  calcArray(item, itemField, denominator) {
    for (const i in item[itemField].arr) {
      item[itemField].arr[i].avg = (item[denominator] != 0) ? this.toPercent(item[itemField].arr[i].sum / item[denominator]) : 0;
    }
  }

  //
  // Get event averages by calculating averages from the match data
  //
  // pData - processed data structure is an array of team numbered objects with match data and calculated averages
  //  structure:
  //  teamNumber: {
  //   matches: [match1, match2, ...]
  //   totalMatches: int
  //   autonLeave: { sum: int, max: int, avg: float }
  //   autonCoralL1: { sum: int, max: int, avg: float }
  //   ...
  //   scoutNames: [name1, name2, ...]
  //   commentList: [comment1, comment2, ...]
  //  }
  //
  getEventAverages() {
    console.log("matchDataProcessor: getEventAverages:");

    //////////////////// PROCESS ALL TEAMS ////////////////////

    //  For each team, go thru all its matches and do the calculations for this event
    for (const i in this.pData) {
      let team = this.pData[i];
      let matchList = team["matches"];
      console.log("===> MDP calcs team: " + team["teamNum"] + " matches: " + matchList.length);  // TEST

      // Initialize text data for matches
      team["scoutNames"] = [];
      team["commentList"] = [];

      // Initialize team processed data
      team["totalDefenseMatches"] = 0;  // incremented each match this team played defense
      team["totalMatches"] = matchList.length;

      //////////////////// PROCESS MATCHES INTO TEAM OBJECT ////////////////////

      for (const j in matchList) {
        let match = matchList[j];

        // NOTE: The field names on the right side of getMatchXXX must match the DB field names in the scouting database
        //        The field names on the left side of getMatchXXX must match the field names in this class

        // Autonomous mode
        this.getMatchItem(team, "autonShootPreload", match, "autonShootPreload");
        this.getMatchItem(team, "autonPreloadAccuracy", match, "autonPreloadAccuracy");
        this.getMatchItem(team, "autonHoppersShot", match, "autonHoppersShot");
        this.getMatchItem(team, "autonHopperAccuracy", match, "autonHopperAccuracy");
        this.getMatchItem(team, "autonAllianceZone", match, "autonAllianceZone");
        this.getMatchItem(team, "autonDepot", match, "autonDepot");
        this.getMatchItem(team, "autonOutpost", match, "autonOutpost");
        this.getMatchItem(team, "autonNeutralZone", match, "autonNeutralZone");
        this.getMatchItem(team, "autonClimb", match, "autonClimb");

        // Teleop mode
        this.getMatchItem(team, "teleopHoppersUsed", match, "teleopHoppersUsed");
        this.getMatchItem(team, "teleopHopperAccuracy", match, "teleopHopperAccuracy");
        this.getMatchItem(team, "teleopIntakeAndShoot", match, "teleopIntakeAndShoot");
        this.getMatchItem(team, "teleopNeutralToAlliance", match, "teleopNeutralToAlliance");
        this.getMatchItem(team, "teleopAllianceToAlliance", match, "teleopAllianceToAlliance");
        this.getMatchItem(team, "teleopPassingRate", match, "teleopPassingRate");

        let matchDefenseLevel = this.getMatchItem(team, "teleopDefenseLevel", match, "teleopDefenseLevel");
        if (matchDefenseLevel != 0) {
          team["totalDefenseMatches"] += 1;  // increment if this team played defense
        }

        // Endgame
        this.getMatchArray(team, "endgameStartClimb", 4, match, "endgameStartClimb");
        this.getMatchArray(team, "endgameCageClimb", 5, match, "endgameCageClimb");

        this.getMatchItem(team, "died", match, "died");

        // Append text data for matches
        team["scoutNames"].push(match["matchnumber"] + " - " + match["scoutname"]);
        team["commentList"].push(match["matchnumber"] + " - " + match["comment"]);

        //////////////////// GAME PIECE TOTALS ////////////////////

        let hopperCap = 0;
        let preloadShot = match["autonShootPreload"];
        let hoppersShot = match["autonHoppersShot"];
        let preloadAcc = match["autonPreloadAccuracy"];
        let hopperAcc = match["autonHopperAccuracy"];
        let autonEstFuel = calcAutonTotalFuel(hopperCap, preloadShot, hoppersShot, preloadAcc, hopperAcc);

        let autonClimbPoints = 0;
        switch (String(team["autonClimb"].val)) {
          case "1": autonClimbPoints = 15; break;  // Back
          case "2": autonClimbPoints = 15; break;  // Left
          case "3": autonClimbPoints = 15; break;  // Front
          case "4": autonClimbPoints = 15; break; // Right
          default: autonClimbPoints = 0; break;   // No climb
        }

        let autonTotalPoints = autonEstFuel + autonClimbPoints;

        let teleopHoppersShot = match["teleopHoppersUsed"];
        let teleopHopperAcc = match["teleopHopperAccuracy"];

        let teleopEstFuel = calcTeleopTotalFuel(hopperCap, teleopHoppersShot, teleopHopperAcc);

        //let totalPoints = autonTotalPoints + teleopEstFuel;

        // Store piece values
        this.updateItem(team, "autonFuelEst", autonEstFuel);
        this.updateItem(team, "autonClimbPoints", autonClimbPoints);
        this.updateItem(team, "autonTotalPoints", autonTotalPoints);

        this.updateItem(team, "teleopEstFuel", teleopEstFuel);

        //this.updateItem(team, "totalPoints", totalPoints);

        //////////////////// POINT TOTALS ////////////////////

        let endgameClimbPoints = 0;
        switch (String(team["endgameCageClimb"].val)) {
          case "1": endgameClimbPoints = 2; break;  // Parked
          case "2": endgameClimbPoints = 2; break;  // Fell
          case "3": endgameClimbPoints = 6; break;  // Shallow
          case "4": endgameClimbPoints = 12; break; // Deep
          default: endgameClimbPoints = 0; break;   // No climb
        }

        let totalMatchPoints = autonEstFuel + teleopEstFuel + endgameClimbPoints;

        // Store point values
        this.updateItem(team, "endgamePoints", endgameClimbPoints);
        this.updateItem(team, "totalMatchPoints", totalMatchPoints);
      }

      //////////////////// CALCULATE AVERAGES USING TOTAL MATCH COUNT ////////////////////

      // console.log("===> doing MDP averages, max for team: " + key);  // TEST

      // Autonomous mode
      this.calcAverage(team, "autonFuelEst", "totalMatches");
      this.calcAverage(team, "autonClimbPoints", "totalMatches");

      // Teleop mode
      this.calcAverage(team, "teleopEstFuel", "totalMatches");

      /*// Divide coral/algae pieces by acquired pieces
      this.calcAccuracy(team, "teleopCoralPieces", "teleopCoralAcquired");
      this.calcAccuracy(team, "teleopAlgaePieces", "teleopAlgaeAcquired");*/

      // Defense avg - only calculate this if this team played defense in a match
      this.calcAverage(team, "teleopDefenseLevel", "totalDefenseMatches");

      this.calcAverage(team, "died", "totalMatches");

      // endgame
      this.calcArray(team, "endgameStartClimb", "totalMatches");
      this.calcArray(team, "endgameCageClimb", "totalMatches");

      // points by game phase
      this.calcAverage(team, "autonTotalPoints", "totalMatches");
      this.calcAverage(team, "totalMatchPoints", "totalMatches");

      this.calcAverage(team, "endgamePoints", "totalMatches");
    }

    return this.pData;
  }

}
