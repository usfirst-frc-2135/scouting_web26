/*
  Global Variable Definition
*/

/*
  Function Definition
*/

//
//  Provide event averages table utilities that:
//    1) insert a header row for a event averages table
//    2) insert a body row for event averages table
//
const thBody = 'class="bg-body"';
const thBodySort = 'class="bg-body sorttable_numeric"';
const thBlueSort = 'class="bg-primary-subtle sorttable_numeric"';
const thAuto = 'class="bg-success-subtle"';
const thTeleop = 'class="bg-primary-subtle"';
const thEndgame = 'class="bg-warning-subtle"';
const thMatch = 'class="bg-danger-subtle"';

//
//  Insert a strategic data table header (all rows)
//    Params
//      tableId     - the HTML ID where the table header is inserted
//      aliasList   - list of aliases at the event
//
function insertEventAveragesHeader(tableId, aliasList) {
  console.log("==> insertEventAveragesHeader: tableId " + tableId + " aliases " + aliasList.length);

  let theadRef = document.getElementById(tableId).querySelector('thead');;
  theadRef.innerHTML = ""; // Clear Table

  let rowString1 = '';
  rowString1 += '<th colspan="1 ' + thBody + '> </th>';
  // Insert column if the aliasList is not empty
  if (aliasList.length > 0) {
    rowString1 += '<th colspan="1" ' + thBody + '> </th>';
  }
  rowString1 += '<th colspan="1" ' + thMatch + '> </th>';
  rowString1 += '<th colspan="1" ' + thMatch + '> </th>';
  rowString1 += '<th colspan="1" ' + thMatch + '>' + '</th>';

  // points by game phase
  rowString1 += '<th colspan="8" ' + thMatch + '>Match Points' + '</th>';
  rowString1 += '<th colspan="4" ' + thAuto + '>Auton Pts' + '</th>';
  rowString1 += '<th colspan="3" ' + thTeleop + '>Teleop Pts' + '</th>';
  rowString1 += '<th colspan="9" ' + thEndgame + '>Endgame' + '</th>';

  theadRef.insertRow().innerHTML = rowString1;

  let rowString2 = '';
  // team number
  rowString2 += '<th colspan="1" ' + thBodySort + '> </th>';
  // Insert column if the aliasList is not empty
  if (aliasList.length > 0) {
    rowString2 += '<th colspan="1" ' + thBody + '> </th>';
  }
  rowString2 += '<th colspan="1" ' + thMatch + '> </th>';
  rowString2 += '<th colspan="1" ' + thMatch + '>M</th>';

  // died 
  rowString2 += '<th colspan="1" ' + thMatch + '>Died' + '</th>';

  // points by game phase
  rowString2 += '<th colspan="2" ' + thMatch + '>Total Pts' + '</th>';
  rowString2 += '<th colspan="2" ' + thAuto + '>Auton Pts' + '</th>';
  rowString2 += '<th colspan="2" ' + thTeleop + '>Teleop Pts' + '</th>';
  rowString2 += '<th colspan="2" ' + thEndgame + '>Endgame Pts' + '</th>';

  // auton
  rowString2 += '<th colspan="2" ' + thAuto + '>Est Fuel Pts' + '</th>';
  rowString2 += '<th colspan="2" ' + thAuto + '>Climb Pts' + '</th>';

  // teleop 
  rowString2 += '<th colspan="2" ' + thTeleop + '>Est Fuel Pts' + '</th>';
  rowString2 += '<th colspan="1" ' + thTeleop + '>Def' + '</th>';

  // endgame 
  rowString2 += '<th colspan="4" ' + thEndgame + '>Start Climb%' + '</th>';
  rowString2 += '<th colspan="5" ' + thEndgame + '>Climb%' + '</th>';

  theadRef.insertRow().innerHTML = rowString2;

  let rowString3 = '';
  const thPrefix0 = '<th scope="col" ' + thBodySort + '>';
  const thPrefix1 = '<th scope="col" ' + thBlueSort + '>';
  // team number
  rowString3 += thPrefix0 + 'Team' + '</th>';
  if (aliasList.length > 0) {
    rowString3 += thPrefix0 + 'Alias' + '</th>';
  }
  rowString3 += thPrefix0 + 'COPRs' + '</th>';
  rowString3 += thPrefix0 + '#' + '</th>';

  // died 
  rowString3 += thPrefix0 + '#' + '</th>';

  // points by game phase
  rowString3 += thPrefix1 + 'Avg' + '</th>';
  rowString3 += thPrefix1 + 'Max' + '</th>';
  rowString3 += thPrefix0 + 'Avg' + '</th>';
  rowString3 += thPrefix0 + 'Max' + '</th>';
  rowString3 += thPrefix1 + 'Avg' + '</th>';
  rowString3 += thPrefix1 + 'Max' + '</th>';
  rowString3 += thPrefix0 + 'Avg' + '</th>';
  rowString3 += thPrefix0 + 'Max' + '</th>';

  // auton 
  rowString3 += thPrefix1 + 'Avg' + '</th>';
  rowString3 += thPrefix1 + 'Max' + '</th>';
  rowString3 += thPrefix0 + 'Avg' + '</th>';
  rowString3 += thPrefix0 + 'Max' + '</th>';

  // teleop coral
  rowString3 += thPrefix1 + 'Avg' + '</th>';
  rowString3 += thPrefix1 + 'Max' + '</th>';
  rowString3 += thPrefix1 + 'Avg' + '</th>';

  // endgame(start climb)
  rowString3 += thPrefix0 + 'NO' + '</th>';
  rowString3 += thPrefix0 + 'B20' + '</th>';
  rowString3 += thPrefix0 + 'A10' + '</th>';
  rowString3 += thPrefix0 + 'L5' + '</th>';

  // endgame(climb)
  rowString3 += thPrefix1 + 'NO' + '</th>';
  rowString3 += thPrefix1 + 'PK' + '</th>';
  rowString3 += thPrefix1 + 'FL' + '</th>';
  rowString3 += thPrefix1 + 'SH' + '</th>';
  rowString3 += thPrefix1 + 'DP' + '</th>';

  theadRef.insertRow().innerHTML = rowString3;
};

// Add a team (key) to the final team list
function getTeamListFromData(matchData) {
  console.log("==> eventAverages: getTeamListFromData()");
  let keyList = [];
  for (let teamNum in matchData) {
    keyList.push(teamNum);
  }
  return keyList;
}

//
// Lookup value for a key in the passed dictionary - team in match data
//
function getDataValue(dict, key, field) {
  if (!dict) {
    console.warn("getDataValue: Dictionary not found! " + dict);
  }
  else if (key in dict) {
    if (field != undefined)
      return dict[key][field];
    else
      return dict[key];
  }
  else {
    console.warn("getDataValue: Key not found in dictionary! " + key + " " + dict);
  }
  return "";
}

//
//  Insert a strategic data table body (all rows)
//    Params
//      tableId       - the HTML ID where the table header is inserted
//      eventAverages - the list of available stategic matches to include in this table
//      aliasList     - list of aliases at the event (length 0 if none)
//      teamFilter    - list of teams to include in table (length 0 if all)
//
function insertEventAveragesBody(tableId, eventAverages, coprData, aliasList, teamFilter) {
  console.log("==> insertEventAveragesBody: tableId " + tableId + " eventAverages " + Object.keys(eventAverages).length + " aliases " + aliasList.length + " teams " + teamFilter.length);

  let tbodyRef = document.getElementById(tableId).querySelector('tbody');;
  tbodyRef.innerHTML = ""; // Clear Table

  let teamList = getTeamListFromData(eventAverages);

  // Go thru each strategic and build the HTML string for that row.
  for (let teamNum of teamList) {
    let avgItem = eventAverages[teamNum];
    if (teamFilter.length !== 0 && !teamFilter.includes(teamNum))
      continue;

    const tdPrefix0 = '<td ' + thBody + '>';
    const tdPrefix1 = '<td ' + thBlueSort + '>';
    let rowString = "";
    rowString += tdPrefix0 + "<a href='teamLookup.php?teamNum=" + teamNum + "'>" + teamNum + "</td>";
    // Insert column if the aliasList is not empty
    if (aliasList.length > 0) {
      rowString += tdPrefix0 + getAliasFromTeamNum(teamNum, aliasList) + "</td>";
    }
    let coprEntry = (coprData.length !== 0) ? getDataValue(coprData[teamNum], "totalPoints") : "";  // TODO: Load COPR data from TBA and pass in here
    rowString += tdPrefix0 + coprEntry + "</td>";
    rowString += tdPrefix0 + getDataValue(avgItem, "totalMatches") + "</td>";
    rowString += tdPrefix0 + getDataValue(avgItem, "died", "sum") + "</td>";

    // points by game phase
    rowString += tdPrefix1 + getDataValue(avgItem, "totalMatchPoints", "avg") + "</td>";
    rowString += tdPrefix1 + getDataValue(avgItem, "totalMatchPoints", "max") + "</td>";
    rowString += tdPrefix0 + getDataValue(avgItem, "autonTotalPoints", "avg") + "</td>";
    rowString += tdPrefix0 + getDataValue(avgItem, "autonTotalPoints", "max") + "</td>";
    rowString += tdPrefix1 + getDataValue(avgItem, "autonTotalPoints", "avg") + "</td>";
    rowString += tdPrefix1 + getDataValue(avgItem, "autonTotalPoints", "max") + "</td>";
    rowString += tdPrefix0 + getDataValue(avgItem, "endgamePoints", "avg") + "</td>";
    rowString += tdPrefix0 + getDataValue(avgItem, "endgamePoints", "max") + "</td>";

    // auton 
    rowString += tdPrefix1 + getDataValue(avgItem, "autonFuelEst", "avg") + "</td>";
    rowString += tdPrefix1 + getDataValue(avgItem, "autonFuelEst", "max") + "</td>";
    rowString += tdPrefix0 + getDataValue(avgItem, "autonClimbPoints", "avg") + "</td>";
    rowString += tdPrefix0 + getDataValue(avgItem, "autonClimbPoints", "max") + "</td>";

    // teleop coral
    rowString += tdPrefix1 + getDataValue(avgItem, "teleopEstFuel", "avg") + "</td>";
    rowString += tdPrefix1 + getDataValue(avgItem, "teleopEstFuel", "max") + "</td>";
    rowString += tdPrefix0 + getDataValue(avgItem, "teleopDefenseLevel", "avg") + "</td>";

    // endgame
    let endgameClimbStartPercentage = getDataValue(avgItem, "endgameStartClimb", "arr");
    rowString += tdPrefix0 + getDataValue(endgameClimbStartPercentage, 0, "avg") + "</td>";
    rowString += tdPrefix0 + getDataValue(endgameClimbStartPercentage, 1, "avg") + "</td>";
    rowString += tdPrefix0 + getDataValue(endgameClimbStartPercentage, 2, "avg") + "</td>";
    rowString += tdPrefix0 + getDataValue(endgameClimbStartPercentage, 3, "avg") + "</td>";

    let endgameClimbPercentage = getDataValue(avgItem, "endgameCageClimb", "arr");
    rowString += tdPrefix1 + getDataValue(endgameClimbPercentage, 0, "avg") + "</td>";
    rowString += tdPrefix1 + getDataValue(endgameClimbPercentage, 1, "avg") + "</td>";
    rowString += tdPrefix1 + getDataValue(endgameClimbPercentage, 2, "avg") + "</td>";
    rowString += tdPrefix1 + getDataValue(endgameClimbPercentage, 3, "avg") + "</td>";
    rowString += tdPrefix1 + getDataValue(endgameClimbPercentage, 4, "avg") + "</td>";

    tbodyRef.insertRow().innerHTML = rowString;
  }

  sorttable.makeSortable(document.getElementById(tableId));

  const teamColumn = 0;
  sortTableByTeam(tableId, teamColumn);
};
