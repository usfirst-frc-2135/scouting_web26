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
function insertMatchDataHeader(tableId, aliasList)
{
  console.log("==> insertMatchDataHeader: tableId " + tableId + " aliases " + aliasList.length);

  let theadRef = document.getElementById(tableId).querySelector('thead');

  theadRef.innerHTML = ""; // Clear Table

  let rowString = '';
  let rowString1 = '';
  const thGen = '<th scope="col" class="bg-warning-subtle">';       // General color
  const thMatch = '<th scope="col" class="bg-body">';               // No color
  const thAuto = '<th scope="col" class="bg-success-subtle">';        // Auton color
  const thTeleop = '<th scope="col" class="bg-primary-subtle">';      // Teleop color
  const thEndgame = '<th scope="col" class="bg-warning-subtle">';     // Endgame color

  if (aliasList.length > 0)
    rowString1 += '<th colspan="3" ' + thMatch + ' </th>';
  else rowString1 += '<th colspan="2" ' + thMatch + ' </th>';
  rowString1 += '<th colspan="1" ' + thGen + ' </th>';    // Died
  rowString1 += '<th colspan="1" ' + thGen + ' </th>';    // No Show
  rowString1 += '<th colspan="9" ' + thAuto + 'Auton' + '</th>';
  rowString1 += '<th colspan="9" ' + thTeleop + 'Teleop' + '</th>';
  rowString1 += '<th colspan="2" ' + thMatch + ' </th>';
  theadRef.insertRow().innerHTML = rowString1;

  rowString += '<th scope="col" class="bg-body sorttable_numeric">Match</th>';
  rowString += '<th scope="col" class="bg-body sorttable_numeric">Team</th>';

  // Insert column if the aliasList is not empty
  if (aliasList.length > 0)
  {
    rowString += thMatch + 'Alias</th>';
  }

  rowString += thGen + 'Died</th>';
  rowString += thGen + 'No Show</th>';
  rowString += thAuto + 'Preload Shot</th>';
  rowString += thAuto + 'Preload Acc</th>';
  rowString += thAuto + 'Hoppers Used</th>';
  rowString += thAuto + 'Hopper Acc</th>';
  rowString += thAuto + 'Alliance Zone</th>';
  rowString += thAuto + 'Depot</th>';
  rowString += thAuto + 'Outpost</th>';
  rowString += thAuto + 'Neutral Zone</th>';
  rowString += thAuto + 'Climb</th>';
  rowString += thTeleop + 'Hoppers Used</th>';
  rowString += thTeleop + 'Hopper Acc</th>';
  rowString += thTeleop + 'Intake & Shoot</th>';
  rowString += thTeleop + 'Passing Rate</th>';
  rowString += thTeleop + 'Pass From NeutralZ</th>';
  rowString += thTeleop + 'Pass From AllianceZ</th>';
  rowString += thTeleop + 'Herded Fuel</th>';
  rowString += thTeleop + 'Defense Rate</th>';
  rowString += thTeleop + 'Driver Ability</th>';
  rowString += thMatch + 'Comment</th>';
  rowString += thMatch + 'Scout Name</th>';

  theadRef.insertRow().innerHTML = rowString;
};

// Converts a given Preload Accuracy Rate number to a string
function toPreloadAcc(value)
{
  switch (String(value))
  {
    case "1": return "None";
    case "2": return "Some";
    case "3": return "Half";
    case "4": return "Most";
    case "5": return "All";
    default: return "-";
  }
}

// Converts a given Accuracy Rate number to a string
function toAccuracyRate(value)
{
  switch (String(value))
  {
    case "1": return "None";
    case "2": return "Few";
    case "3": return "25%";
    case "4": return "50%";
    case "5": return "75%";
    case "6": return "Most";
    default: return "-";
  }
}

// Converts a given Passing Rate number to a string
function toPassingRate(value)
{
  switch (String(value))
  {
    case "1": return "Low";
    case "2": return "Med";
    case "3": return "Half";
    case "4": return "Tons";
    default: return "-";
  }
}

// Converts a given Accuracy Rate number to a string
// Converts a given tower climb number to a string
function toClimbLevel(value)
{
  switch (String(value))
  {
    case "1": return "L1";
    case "2": return "L2";
    case "3": return "L3";
    default: return "-";
  }
}

// Converts a given Start Climb number to a string
function toStartClimb(value)
{
  switch (String(value))
  {
    case "1": return "Before";
    case "2": return "Bell";
    case "3": return "10s";
    case "4": return "<10s";
    default: return "-";
  }
}

// Converts a given climb position number to a string
function toClimbPosition(value)
{
  switch (String(value))
  {
    case "1": return "Back";
    case "2": return "Left";
    case "3": return "Front";
    case "4": return "Right";
    default: return "-";
  }
}

// Converts a given defense rate number to a string
function toDefenseRate(value)
{
  switch (String(value))
  {
    case "1": return "Low";
    case "2": return "M Low";
    case "3": return "Med";
    case "4": return "M High";
    case "5": return "High";
    default: return "-";
  }
}

// Converts a given Driver Ability number to a string
function toDriverAbility(value)
{
  switch (String(value))
  {
    case "1": return "Slow";
    case "2": return "Jerky";
    case "3": return "Avg";
    case "4": return "Fast";
    case "5": return "Elite";
    default: return "-";
  }
}

function toYes(value)
{
  switch (String(value))
  {
    case "1": return "Yes";
    default: return "-";
  }
}

function toDiedValue(value)
{
  switch (String(value))
  {
    case "1": return "15-30s";
    case "2": return "30-60s";
    case "3": return "60-90s";
    case "4": return "Most";
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
function insertMatchDataBody(tableId, matchData, aliasList, teamFilter)
{
  console.log("==> insertMatchDataTable: tableId " + tableId + ", matches " + matchData.length + ", aliases " + aliasList.length + ", teams " + teamFilter.length);

  let tbodyRef = document.getElementById(tableId).querySelector('tbody');;
  tbodyRef.innerHTML = ""; // Clear Table

  // Go thru each match and build the HTML string for that row.
  for (let i = 0; i < matchData.length; i++)
  {
    let matchItem = matchData[i];
    let teamNum = matchItem["teamnumber"];
    if (teamFilter.length !== 0 && !teamFilter.includes(teamNum))
      continue;

    const tdBody = "<td class='bg-body'>";
    const tdBlue = "<td class='bg-primary-subtle'>";

    let rowString = "<th class='fw-bold'>" + matchItem["matchnumber"] + "</th>";

    rowString += tdBody + "<a href='teamLookup.php?teamNum=" + teamNum + "'>" + teamNum + "</td>";
    // Insert column if the aliasList is not empty
    if (aliasList.length > 0)
    {
      rowString += tdBody + getAliasFromTeamNum(teamNum, aliasList) + "</td>";
    }

    rowString += tdBlue + toDiedValue(matchItem["died"]) + "</td>";
    rowString += tdBody + toYes(matchItem["other2"]) + "</td>"; // No Show is stored in "other2"
    rowString += tdBlue + matchItem["autonShootPreload"] + "</td>";
    rowString += tdBody + toPreloadAcc(matchItem["autonPreloadAccuracy"]) + "</td>";
    rowString += tdBlue + matchItem["autonHoppersShot"] + "</td>";
    rowString += tdBody + toAccuracyRate(matchItem["autonHopperAccuracy"]) + "</td>";
    rowString += tdBlue + matchItem["autonAllianceZone"] + "</td>";
    rowString += tdBody + matchItem["autonDepot"] + "</td>";
    rowString += tdBlue + matchItem["autonOutpost"] + "</td>";
    rowString += tdBody + matchItem["autonNeutralZone"] + "</td>";
    rowString += tdBlue + toYes(matchItem["autonClimb"]) + "</td>";
    rowString += tdBody + matchItem["teleopHoppersUsed"] + "</td>";
    rowString += tdBlue + toAccuracyRate(matchItem["teleopHopperAccuracy"]) + "</td>";
    rowString += tdBody + matchItem["teleopIntakeAndShoot"] + "</td>";
    rowString += tdBlue + toPassingRate(matchItem["teleopPassingRate"]) + "</td>";
    rowString += tdBody + matchItem["teleopNeutralToAlliance"] + "</td>";
    rowString += tdBlue + matchItem["teleopAllianceToAlliance"] + "</td>";
    rowString += tdBody + matchItem["other1"] + "</td>";
    rowString += tdBlue + toDefenseRate(matchItem["teleopDefenseLevel"]) + "</td>";
    rowString += tdBody + toDriverAbility(matchItem["driverAbility"]) + "</td>";
    rowString += tdBlue + matchItem["comment"] + "</td>";
    rowString += tdBody + matchItem["scoutname"] + "</td>";

    tbodyRef.insertRow().innerHTML = rowString;
  }

  sorttable.makeSortable(document.getElementById(tableId));

  const matchColumn = 0;
  sortTableByMatch(tableId, matchColumn);
};
