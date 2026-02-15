/*
  Global Variable Definition
*/

/*
  Function Definition
*/

//
//  Provide strategic data table utilities that:
//    1) insert a header row for a strategic data table
//    2) insert a body row for strategic data table
//

//
//  Insert a strategic data table header (all rows)
//    Params
//      tableId     - the HTML ID where the table header is inserted
//      aliasList   - list of aliases at the event
//
function insertStrategicDataHeader(tableId, aliasList) {
  console.log("==> insertStrategicDataHeader: tableId " + tableId + " aliases " + aliasList.length);

  let theadRef = document.getElementById(tableId).querySelector('thead');;
  theadRef.innerHTML = ""; // Clear Table

  const thActive = '<th scope="col" class="bg-success-subtle">';        // Auton color
  const thInactive = '<th scope="col" class="bg-primary-subtle">';      // Teleop color
  const thBump = '<th scope="col" class="bg-warning-subtle">';     // Endgame color

  let rowString1 = '';
  rowString1 += '<th colspan="1" class="bg-body"> </th>';
  // Insert column if the aliasList is not empty
  if (aliasList.length > 0) {
    rowString1 += '<th colspan="1" class="bg-body"> </th>';
  }
  rowString1 += '<th colspan="1" class="bg-body"> </th>';
  rowString1 += '<th colspan="7" class="bg-success-subtle">' + 'Active Shift' + '</th>';
  rowString1 += '<th colspan="7" class="bg-primary-subtle">' + 'Inactive Shift' + '</th>';
  rowString1 += '<th colspan="1" class="bg-body">' + 'Evading Defense' + '</th>';
  rowString1 += '<th colspan="4" class="bg-warning-subtle">' + 'Bump' + '</th>';
  rowString1 += '<th colspan="1" class="bg-body">' + 'Fouls' + '</th>';
  rowString1 += '<th colspan="2" class="bg-body">Notes' + '</th>';
  rowString1 += '<th colspan="1" class="bg-body"> </th>';

  theadRef.insertRow().innerHTML = rowString1;

  let rowString2 = '';
  const thBody = '<th scope="col" class="bg-body">';
  const thBlue = '<th scope="col" class="bg-primary-subtle">';
  rowString2 += '<th scope="col" class="bg-body sorttable_numeric">' + 'Team' + '</th>';
  // Insert column if the aliasList is not empty
  if (aliasList.length > 0) {
    rowString2 += thBody + 'Alias' + '</th>';
  }
  rowString2 += thBody + 'Match' + '</th>';
  rowString2 += thActive + 'Loaded Hopper' + '</th>';
  rowString2 += thActive + 'Shot Hopper' + '</th>';
  rowString2 += thActive + 'Passed Fuel From Other Alliance' + '</th>';
  rowString2 += thActive + 'Passed Fuel From Neutral Zone' + '</th>';
  rowString2 += thActive + 'Played Defense Against Shooter' + '</th>';
  rowString2 += thActive + 'Played Defense At Bump' + '</th>';
  rowString2 += thActive + 'Played Defense At Trench' + '</th>';
  rowString2 += thInactive + 'Loaded Hopper' + '</th>';
  rowString2 += thInactive + 'Shot Hopper' + '</th>';
  rowString2 += thInactive + 'Passed Fuel From Other Alliance' + '</th>';
  rowString2 += thInactive + 'Passed Fuel From Neutral Zone' + '</th>';
  rowString2 += thInactive + 'Played Defense Against Shooter' + '</th>';
  rowString2 += thInactive + 'Played Defense At Bump' + '</th>';
  rowString2 += thInactive + 'Played Defense At Trench' + '</th>';
  rowString2 += thBody + 'Effectiveness' + '</th>';
  rowString2 += thBump + 'Tipped Over' + '</th>';
  rowString2 += thBump + 'Bottomed Out' + '</th>';
  rowString2 += thBump + 'Avoided Defender' + '</th>';
  rowString2 += thBump + 'Got Stuck on Fuel' + '</th>';
  rowString2 += thBody + 'Fouls' + '</th>';
  rowString2 += thBlue + 'Problem Note' + '</th>';
  rowString2 += thBody + 'General Note' + '</th>';
  rowString2 += thBlue + 'Scout Name' + '</th>';

  theadRef.insertRow().innerHTML = rowString2;
};

//
// Converts a given "1" to yes, "2" to no, anything else to a dash.
//
function toYesNo(value) {
  switch (String(value)) {
    case "1": return "Yes";
    case "2": return "No";
    default: return "-";
  }
};

//
//  Insert a strategic data table body (all rows)
//    Params
//      tableId     - the HTML ID where the table header is inserted
//      stratData   - the list of available stategic matches to include in this table
//      aliasList   - list of aliases at the event (length 0 if none)
//      teamFilter  - list of teams to include in table (length 0 if all)
//
function insertStrategicDataBody(tableId, stratData, aliasList, teamFilter) {
  console.log("==> insertStrategicDataBody: tableId " + tableId + " stratMatches " + stratData.length + " aliases " + aliasList.length + " teams " + teamFilter.length);

  let tbodyRef = document.getElementById(tableId).querySelector('tbody');;
  tbodyRef.innerHTML = ""; // Clear Table

  // Go thru each strategic and build the HTML string for that row.
  for (let i = 0; i < stratData.length; i++) {
    let stratItem = stratData[i];
    let teamNum = stratItem["teamnumber"];
    if (teamFilter.length !== 0 && !teamFilter.includes(teamNum))
      continue;

    const tdPrefix0 = "<td class='bg-body'>";
    const tdPrefix0Bold = "<td class='bg-body fw-bold'>";
    const tdPrefix1 = "<td class='bg-primary-subtle'>";

    let defEffectiveness = "";
    switch (String(stratItem["againstDefenseEffectiveness"])) {
      case "0": defEffectiveness = "Low"; break;
      case "1": defEffectiveness = "Med Low"; break;
      case "2": defEffectiveness = "Average"; break;
      case "3": defEffectiveness = "Med High"; break;
      case "4": defEffectiveness = "High"; break;
    }

    let rowString = "";
    rowString += tdPrefix0 + "<a href='teamLookup.php?teamNum=" + teamNum + "'>" + teamNum + "</td>";
    // Insert column if the aliasList is not empty
    if (aliasList.length > 0) {
      rowString += tdPrefix0 + getAliasFromTeamNum(teamNum, aliasList) + "</td>";
    }
    rowString += tdPrefix0Bold + stratItem["matchnumber"] + "</td>";
    rowString += tdPrefix0 + toYesNo(stratItem["activeShiftLoadedHopper"]) + "</td>";
    rowString += tdPrefix1 + toYesNo(stratItem["activeShiftShotHopper"]) + "</td>";
    rowString += tdPrefix0 + toYesNo(stratItem["activeShiftPassingFromAlliance"]) + "</td>";
    rowString += tdPrefix1 + toYesNo(stratItem["activeShiftPassingFromNeutral"]) + "</td>";
    rowString += tdPrefix0 + toYesNo(stratItem["activeShiftDefenseAgainstShooter"]) + "</td>";
    rowString += tdPrefix1 + toYesNo(stratItem["activeShiftDefenseAtBump"]) + "</td>";
    rowString += tdPrefix0 + toYesNo(stratItem["activeShiftDefenseAtTrench"]) + "</td>";
    rowString += tdPrefix1 + toYesNo(stratItem["inactiveShiftLoadedHopper"]) + "</td>";
    rowString += tdPrefix0 + toYesNo(stratItem["inactiveShiftShotHopper"]) + "</td>";
    rowString += tdPrefix1 + toYesNo(stratItem["inactiveShiftPassingFromAlliance"]) + "</td>";
    rowString += tdPrefix0 + toYesNo(stratItem["inactiveShiftPassingFromNeutral"]) + "</td>";
    rowString += tdPrefix1 + toYesNo(stratItem["inactiveShiftDefenseAgainstShooter"]) + "</td>";
    rowString += tdPrefix0 + toYesNo(stratItem["inactiveShiftDefenseAtBump"]) + "</td>";
    rowString += tdPrefix1 + toYesNo(stratItem["inactiveShiftDefenseAtTrench"]) + "</td>";
    rowString += tdPrefix0 + defEffectiveness + "</td>";
    rowString += tdPrefix1 + toYesNo(stratItem["bumpTippedOver"]) + "</td>";
    rowString += tdPrefix0 + toYesNo(stratItem["bumpBottomedOut"]) + "</td>";
    rowString += tdPrefix1 + toYesNo(stratItem["bumpAvoidedDefender"]) + "</td>";
    rowString += tdPrefix0 + toYesNo(stratItem["bumpGotStuckOnFuel"]) + "</td>";
    rowString += tdPrefix1 + toYesNo(stratItem["fouls"]) + "</td>";
    rowString += tdPrefix0 + stratItem["problem_comment"] + "</td>";
    rowString += tdPrefix1 + stratItem["general_comment"] + "</td>";
    rowString += tdPrefix0 + stratItem["scoutname"] + "</td>";

    tbodyRef.insertRow().innerHTML = rowString;
  }

  sorttable.makeSortable(document.getElementById(tableId));

  const teamColumn = 0;
  let matchColumn = (aliasList.length > 0) ? 2 : 1;

  sortTableByMatchAndTeam(tableId, teamColumn, matchColumn);
};
