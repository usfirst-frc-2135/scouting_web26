/*
  Global Variable Definition
*/

/*
  Function Definition
*/

//
//  Provide match data table utilities that:
//    1) insert a header row for a match data table
//    2) insert a body row for match data table
//

//
//  Insert a match data table header (all rows)
//    Params
//      tableId     - the HTML ID where the table header is inserted
//      aliasList   - list of aliases at the event
//
function insertMatchDataHeader(tableId, aliasList) {
  console.log("==> insertMatchDataHeader: tableId " + tableId + " aliases " + aliasList.length);

  let theadRef = document.getElementById(tableId).querySelector('thead');

  theadRef.innerHTML = ""; // Clear Table

  let rowString = '';
  let rowString1 = '';
  const thMatch = '<th scope="col" class="bg-body">';               // No color
  const thAuto = '<th scope="col" class="bg-success-subtle">';        // Auton color
  const thTeleop = '<th scope="col" class="bg-primary-subtle">';      // Teleop color
  const thEndgame = '<th scope="col" class="bg-warning-subtle">';     // Endgame color

  if (aliasList.length > 0) 
    rowString1 += '<th colspan="3" ' + thMatch + ' </th>';
  else rowString1 += '<th colspan="2" ' + thMatch + ' </th>';
  rowString1 += '<th colspan="1" ' + thMatch + ' </th>';
  rowString1 += '<th colspan="9" ' + thAuto + 'Auton' + '</th>';
  rowString1 += '<th colspan="8" ' + thTeleop + 'Teleop' + '</th>';
  rowString1 += '<th colspan="3" ' + thEndgame + 'Endgame' + '</th>';
  rowString1 += '<th colspan="2" ' + thMatch + ' </th>';
  theadRef.insertRow().innerHTML = rowString1;

  rowString += '<th scope="col" class="bg-body sorttable_numeric">Match</th>';
  rowString += '<th scope="col" class="bg-body sorttable_numeric">Team</th>';

  // Insert column if the aliasList is not empty
  if (aliasList.length > 0) {
    rowString += thMatch + 'Alias</th>';
  }

  rowString += thMatch + 'Died</th>';
  rowString += thAuto + 'Preload</th>';
  rowString += thAuto + 'Preload Acc</th>';
  rowString += thAuto + 'Hopper</th>';
  rowString += thAuto + 'Hopper Acc</th>';
  rowString += thAuto + 'Alliance Zone</th>';
  rowString += thAuto + 'Depot</th>';
  rowString += thAuto + 'Outpost</th>';
  rowString += thAuto + 'Neutral Zone</th>';
  rowString += thAuto + 'Climb</th>';
  rowString += thTeleop + 'Hopper</th>';
  rowString += thTeleop + 'Hopper Acc</th>';
  rowString += thTeleop + 'Intake & Shoot</th>';
  rowString += thTeleop + 'Passing Rate</th>';
  rowString += thTeleop + 'Defense Rate</th>';
  rowString += thTeleop + 'Driver Ability</th>';
  rowString += thTeleop + 'Pass From NeutralZ</th>';
  rowString += thTeleop + 'Pass From AllianceZ</th>';
  rowString += thEndgame + 'Start Climb</th>';
  rowString += thEndgame + 'Climb Level</th>';
  rowString += thEndgame + 'Climb Position</th>';
  rowString += thMatch + 'Comment</th>';
  rowString += thMatch + 'Scout Name</th>';

  theadRef.insertRow().innerHTML = rowString;
};

// Converts a given Preload Accuracy Rate number to a string
function toPreloadAcc(value) {
  switch (String(value)) {
    case "1": return "1:All";
    case "2": return "2:Most";
    case "3": return "3:Half";
    case "4": return "4:Some";
    case "5": return "5:None";
    default: return "-";
  }
}

// Converts a given Accuracy Rate number to a string
function toAccuracyRate(value) {
  switch (String(value)) {
    case "1": return "1:Most";
    case "2": return "2:3/4";
    case "3": return "3:1/2";
    case "4": return "4:1/4";
    case "5": return "5:Few";
    case "6": return "6:None";
    default: return "-";
  }
}

// Converts a given Passing Rate number to a string
function toPassingRate(value) {
  switch (String(value)) {
    case "1": return "1:Low";
    case "2": return "2:Med";
    case "3": return "3:Half";
    case "4": return "4:Tons";
    default: return "-";
  }
}

// Converts a given Accuracy Rate number to a string
// Converts a given tower climb number to a string
function toClimbLevel(value) {
  switch (String(value)) {
    case "1": return "1:L1";
    case "2": return "2:L2";
    case "3": return "3:L3";
    default: return "-";
  }
}

// Converts a given Start Climb number to a string
function toStartClimb(value) {
  switch (String(value)) {
    case "1": return "1:Before";
    case "2": return "2:Bell";
    case "3": return "3:10s";
    case "4": return "4:<10s";
    default: return "-";
  }
}

// Converts a given climb position number to a string
function toClimbPosition(value) {
  switch (String(value)) {
    case "1": return "1:Back";
    case "2": return "2:Left";
    case "3": return "3:Front";
    case "4": return "4:Right";
    default: return "-";
  }
}

// Converts a given driver ability number to a string
function toDriverAbility(value) {
  switch (String(value)) {
    case "1": return "1:Slow";
    case "2": return "2:Jerky";
    case "3": return "3:Avg";
    case "4": return "4:Fast";
    case "5": return "5:Elite";
    default: return "-";
  }
}

// Converts a given defense rate number to a string
function toDefenseRate(value) {
  switch (String(value)) {
    case "1": return "1:Low";
    case "2": return "2:M Low";
    case "3": return "3:Med";
    case "4": return "4:M High";
    case "5": return "5:High";
    default: return "-";
  }
}

// Converts a given Driver Ability number to a string
function toDriverAbility(value) {
  switch (String(value)) {
    case "1": return "1:Slow";
    case "2": return "2:Jerky";
    case "3": return "3:Avg";
    case "4": return "4:Fast";
    case "5": return "5:Elite";
    default: return "-";
  }
}

function toDiedValue(value) {
  switch (String(value)) {
    case "1": return "1:Most";
    case "2": return "2:1m+";
    case "3": return "3:30s+";
    case "4": return "4:15-30s";
    case "5": return "5:No Show";
    default: return "-";
  }
}

//
//  Insert a match data table body (all rows)
//    Params
//      tableId     - the HTML ID where the table header is inserted
//      matchData   - the list of available matches to include in this table
//      aliasList   - list of aliases at the event (length 0 if none)
//      teamFilter  - list of teams to include in table (length 0 if all)
//
function insertMatchDataBody(tableId, matchData, aliasList, teamFilter) {
  console.log("==> insertMatchDataTable: tableId " + tableId + " matches " + matchData.length + " aliases " + aliasList.length + " teams " + teamFilter.length);

  let tbodyRef = document.getElementById(tableId).querySelector('tbody');;
  tbodyRef.innerHTML = ""; // Clear Table

  // Go thru each match and build the HTML string for that row.
  for (let i = 0; i < matchData.length; i++) {
    let matchItem = matchData[i];
    let teamNum = matchItem["teamnumber"];
    if (teamFilter.length !== 0 && !teamFilter.includes(teamNum))
      continue;

    const tdBody = "<td class='bg-body'>";
    const tdBlue = "<td class='bg-primary-subtle'>";

    let rowString = "<th class='fw-bold'>" + matchItem["matchnumber"] + "</th>";

    rowString += tdBody + "<a href='teamLookup.php?teamNum=" + teamNum + "'>" + teamNum + "</td>";
    // Insert column if the aliasList is not empty
    if (aliasList.length > 0) {
      rowString += tdBody + getAliasFromTeamNum(teamNum, aliasList) + "</td>";
    }

    rowString += tdBlue + toDiedValue(matchItem["died"]) + "</td>";
    rowString += tdBody + matchItem["autonShootPreload"] + "</td>";
    rowString += tdBlue + toPreloadAcc(matchItem["autonPreloadAccuracy"]) + "</td>";
    rowString += tdBody + matchItem["autonHoppersShot"] + "</td>";
    rowString += tdBlue + toAccuracyRate(matchItem["autonHopperAccuracy"]) + "</td>";
    rowString += tdBody + matchItem["autonAllianceZone"] + "</td>";
    rowString += tdBlue + matchItem["autonDepot"] + "</td>";
    rowString += tdBody + matchItem["autonOutpost"] + "</td>";
    rowString += tdBlue + matchItem["autonNeutralZone"] + "</td>";
    rowString += tdBody + toClimbPosition(matchItem["autonClimb"]) + "</td>";
    rowString += tdBlue + matchItem["teleopHoppersUsed"] + "</td>";
    rowString += tdBody + toAccuracyRate(matchItem["teleopHopperAccuracy"]) + "</td>";
    rowString += tdBlue + matchItem["teleopIntakeAndShoot"] + "</td>";
    rowString += tdBody + toPassingRate(matchItem["teleopPassingRate"]) + "</td>";
    rowString += tdBlue + toDefenseRate(matchItem["teleopDefenseLevel"]) + "</td>";
    rowString += tdBody + toDriverAbility(matchItem["driverAbility"]) + "</td>";
    rowString += tdBlue + matchItem["teleopNeutralToAlliance"] + "</td>";
    rowString += tdBody + matchItem["teleopAllianceToAlliance"] + "</td>";
    rowString += tdBlue + toStartClimb(matchItem["endgameStartClimb"]) + "</td>";
    rowString += tdBody + toClimbLevel(matchItem["endgameClimbLevel"]) + "</td>";
    rowString += tdBlue + toClimbPosition(matchItem["endgameClimbPosition"]) + "</td>";
    rowString += tdBody + matchItem["comment"] + "</td>";
    rowString += tdBlue + matchItem["scoutname"] + "</td>";

    tbodyRef.insertRow().innerHTML = rowString;
  }

  sorttable.makeSortable(document.getElementById(tableId));

  const matchColumn = 0;
  sortTableByMatch(tableId, matchColumn);
};
