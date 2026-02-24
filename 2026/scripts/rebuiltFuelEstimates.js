//
//  Rebuilt game: provide FuelEstimates utilities that:
//    1) insert a header row for a FuelEstimates data table
//    2) insert a body row for a FuelEstimates data table
//
////////////////////////////////////////////

// CONSTANTS used in fuel estimate calculations
const DEF_HOPPER_CAP = 40;
const AUTON_ALL_ACC_RATE = 1;
const AUTON_MOST_ACC_RATE_PRE = 0.85;
const AUTON_HALF_ACC_RATE = 0.5;
const AUTON_SOME_ACC_RATE_PRE = 0.3;
const AUTON_NONE_ACC_RATE = 0;
const AUTON_IDK_ACC_RATE = 0.5;
const AUTON_MOST_ACC_RATE_EXTRA = 0.9;
const AUTON_3_4_ACC_RATE = 0.75;
const AUTON_QUARTER_ACC_RATE = 0.25;
const AUTON_FEW_ACC_RATE = 0.1;
const TELEOP_MOST_ACC_RATE = 0.9;
const TELEOP_HALF_ACC_RATE = 0.5;
const TELEOP_FEW_ACC_RATE = 0.1;
const TELEOP_NONE_ACC_RATE = 0;
const TELEOP_IDK_ACC_RATE = 0.5;
const TELEOP_3_4_ACC_RATE = 0.75;
const TELEOP_QUARTER_ACC_RATE = 0.25;

///////////////////// FUNCTIONS /////////

//  Insert a match data table header (all rows)
//    Params
//      tableId     - the HTML ID where the table header is inserted
//      aliasList   - list of aliases at the event
//
function insertFuelEstimatesHeader(tableId, aliasList) {
  console.log("==> insertFuelEstimatesDataHeader: tableId " + tableId + " aliases " + aliasList.length);
  let theadRef = document.getElementById(tableId).querySelector('thead');
  theadRef.innerHTML = ""; // Clear Table

  let rowString = '';
  let rowString1 = '';
  const thMatch = '<th scope="col" class="bg-body">';               // No color
  const thAuto = '<th scope="col" class="bg-success-subtle">';        // Auton color
  const thTeleop = '<th scope="col" class="bg-primary-subtle">';      // Teleop color
  const thEndgame = '<th scope="col" class="bg-warning-subtle">';     // Endgame color

  if (aliasList.length > 0) {
    rowString1 += '<th colspan="1" ' + thBody + '> </th>';
  }

  rowString1 += '<th colspan="1" ' + thMatch + ' </th>';
  rowString1 += '<th colspan="1" ' + thMatch + ' </th>';
  rowString1 += '<th colspan="2" ' + thMatch + ' Auton Estimates' + '</th>';
  rowString1 += '<th colspan="2" ' + thMatch + ' Teleop Estimates' + '</th>';

  theadRef.insertRow().innerHTML = rowString1;

  rowString += '<th scope="col" class="bg-body sorttable_numeric">Match</th>';
  rowString += '<th scope="col" class="bg-body sorttable_numeric">Team</th>';
  // Insert column if the aliasList is not empty

  if (aliasList.length > 0) {
    rowString += thMatch + 'Alias</th>';
  }

  rowString += thAuto + 'Fuel Est</th>';
  rowString += thAuto + 'TBA Est</th>';
  rowString += thTeleop + 'Fuel Est</th>';
  rowString += thTeleop + 'TBA Est</th>';

  theadRef.insertRow().innerHTML = rowString;
};

//
//  Insert a fuel estimates data table body (all rows)
//    Params
//      tableId     - the HTML ID where the table header is inserted
//      matchData   - the list of matches with the raw scouted data
//      pData       - the mdp processed data 
//      aliasList   - list of aliases at the event (length 0 if none)
//      teamFilter  - list of teams to include in table (length 0 if all teams)
//      pitData     - pit scouting data 
//
function insertFuelEstimatesBody(tableId, matchData, pData, aliasList, teamFilter, pitData) {

  let tbodyRef = document.getElementById(tableId).querySelector('tbody');;
  tbodyRef.innerHTML = ""; // Clear Table

  // Go thru each match and build the HTML string for that row.
  for (let i = 0; i < matchData.length; i++) {
    let matchItem = matchData[i];
    let teamNum = matchItem["teamnumber"];
    let matchnum = matchItem["matchnumber"];
    if (teamFilter.length !== 0 && !teamFilter.includes(teamNum))
      continue;
    console.log(">>> Building FuelEst table: Doing team = "+teamNum+", match = "+matchnum);

    // Getting hopperCapacity from pitData so we know if default was used.
    // TODO - also check hopperCap table!!!
    let hopperCap = 0;
    if (pitData != null) {
      if (pitData[teamNum] != null) {
        hopperCap = pitData[teamNum]["caphopper"];
      }
    }

    // Fuel ests only exist in pData if we have scouted match data for all 3 teams of the match's red/blue alliances. 
    let autonEstFuel = "-";    // default: use "-" when no fuel ests found from pData
    let teleopEstFuel = "-";
    let autonEstFuelTBA = "-"; 
    let teleopEstFuelTBA = "-";

    // First check if there's fuelD data for this team/match.
    if(pData[teamNum]["fuelD"] == null || pData[teamNum]["fuelD"][matchnum] == null) {
      if(pData[teamNum]["fuelD"] == null)
        console.log("    --> No fuelD found for team: "+teamNum);
      else console.log("    --> No fuelD basic fuel est found for team: "+teamNum+", matchnum = "+matchnum);
    } else {
      autonEstFuel = parseInt(pData[teamNum]["fuelD"][matchnum]["autonFE"]);
      teleopEstFuel = parseInt(pData[teamNum]["fuelD"][matchnum]["teleopFE"]);
    }

    const tdBody = "<td class='bg-body'>";
    let rowString = "<th class='fw-bold'>" + matchnum + "</th>";
    rowString += tdBody + "<a href='teamLookup.php?teamNum=" + teamNum + "'>" + teamNum + "</td>";

    // Insert column if the aliasList is not empty
    if (aliasList.length > 0) {
      rowString += tdBody + getAliasFromTeamNum(teamNum, aliasList) + "</td>";
    }

    // Use red text for the fuel estimate numbers when we don't know the true hopper capacity.
    colorTag = tdBody + "<span style='color:black;'>";
    if (hopperCap == 0) 
      colorTag = tdBody + "<span style='color:red;'>";

    // Get the TBA-based fuel estimates from the pData fuelD for this match.
    if(pData[teamNum]["fuelD"] == null || pData[teamNum]["fuelD"][matchnum] == null || pData[teamNum]["fuelD"][matchnum]["tbaAutonFE"] == null || pData[teamNum]["fuelD"][matchnum]["tbaTeleopFE"] == null ){
      if(pData[teamNum]["fuelD"] == null)
        console.log("    --> No fuelD found for team: "+teamNum);
      else console.log("   --> No fuelD TBA fuel est found for team: "+teamNum+", matchnum = "+matchnum);
    } else {
      autonEstFuelTBA = parseInt(pData[teamNum]["fuelD"][matchnum]["tbaAutonFE"]);
      teleopEstFuelTBA = parseInt(pData[teamNum]["fuelD"][matchnum]["tbaTeleopFE"]);
    }

    // Add fuel estimates to rowString with appropriate color.
    rowString += colorTag + autonEstFuel + "</span></td>";
    rowString += colorTag + autonEstFuelTBA + "</span></td>"; 
    rowString += colorTag + teleopEstFuel + "</span></td>";
    rowString += colorTag + teleopEstFuelTBA + "</span></td>";
     
    tbodyRef.insertRow().innerHTML = rowString;
  }  // end of matches loop

  sorttable.makeSortable(document.getElementById(tableId));
  const matchColumn = 0;
  sortTableByMatch(tableId, matchColumn);
};

/////////////////// Rebuilt calculations  //////////////
function calcAutonTotalFuel(hopperCap, preloadShot, hoppersShot, preloadAcc, hopperAcc)
{
  if (hopperCap == 0)
  {
    hopperCap = DEF_HOPPER_CAP;
  }
//HOLD  console.log(" --> For auton fuel est: hopperCap = " + hopperCap);

//HOLD  console.log("   --> preloadAcc (radio button data) = " + preloadAcc);
  // Convert the scouted preloadAcc data (radio button number) to the appropriate percentage.
  switch (preloadAcc) {
      case 0: preloadAcc = AUTON_NONE_ACC_RATE; break;     // N/A
      case 1: preloadAcc = AUTON_ALL_ACC_RATE; break;      // All
      case 2: preloadAcc = AUTON_MOST_ACC_RATE_PRE; break; // Most
      case 3: preloadAcc = AUTON_HALF_ACC_RATE; break;     // Half
      case 4: preloadAcc = AUTON_SOME_ACC_RATE_PRE; break; // Some
      case 5: preloadAcc = AUTON_NONE_ACC_RATE; break;     // None
      default: preloadAcc = AUTON_IDK_ACC_RATE; break;     // IDK
  }
//HOLD  console.log("   --> preloadAcc (converted to percentage) = " + preloadAcc);
  let autonPreloadTotal = preloadShot * preloadAcc * 8;
  autonPreloadTotal = Number(autonPreloadTotal).toFixed(2);

  // Convert the scouted hopperAcc data (radio button number) to the appropriate percentage.
//HOLD  console.log("   --> auto hopperAcc (radio button data) = " + hopperAcc);
  switch (hopperAcc) {
      case 0: hopperAcc = AUTON_NONE_ACC_RATE; break;        // N/A
      case 1: hopperAcc = AUTON_MOST_ACC_RATE_EXTRA; break;  // Most
      case 2: hopperAcc = AUTON_3_4_ACC_RATE; break;         // 3/4
      case 3: hopperAcc = AUTON_HALF_ACC_RATE; break;        // Half
      case 4: hopperAcc = AUTON_QUARTER_ACC_RATE; break;     // 1/4
      case 5: hopperAcc = AUTON_FEW_ACC_RATE; break;         // Few
      case 6: hopperAcc = AUTON_NONE_ACC_RATE; break;        // None
      default: hopperAcc = AUTON_IDK_ACC_RATE; break;        // IDK
  }
//HOLD  console.log("     --> auto hopperAcc (percentage) = " + hopperAcc);
//HOLD  console.log("     --> auto hoppersShot = " + hoppersShot);
  let autonExtraTotal = hoppersShot * hopperAcc * hopperCap;
  autonExtraTotal = Number(autonExtraTotal).toFixed(2);
//HOLD  console.log("       --> autonPreloadTotal = "+ autonPreloadTotal);
//HOLD  console.log("       --> autonExtraTotal = "+ autonExtraTotal);

  let totalEstFuel = parseFloat(autonPreloadTotal) + parseFloat(autonExtraTotal);
//HOLD  console.log("        -----> total Auton estFuel = " + totalEstFuel);
  return totalEstFuel;
};

function calcTeleopTotalFuel(hopperCap, hoppersShot, hopperAcc)
{
  if (hopperCap == 0)
    hopperCap = DEF_HOPPER_CAP;

//HOLD  console.log(" --> For teleop fuel est: hopperCap = " + hopperCap);
   
  // Convert the scouted teleop hopperAcc data (radio button) to the appropriate percentage.
//HOLD  console.log("   --> teleop hopperAcc (radio button) = " + hopperAcc);
  switch (hopperAcc) {
      case 0: hopperAcc = TELEOP_NONE_ACC_RATE; break;    // N/A
      case 1: hopperAcc = TELEOP_MOST_ACC_RATE; break;    // Most
      case 2: hopperAcc = TELEOP_3_4_ACC_RATE; break;     // 3/4
      case 3: hopperAcc = TELEOP_HALF_ACC_RATE; break;    // Half
      case 4: hopperAcc = TELEOP_QUARTER_ACC_RATE; break; // 1/4
      case 5: hopperAcc = TELEOP_FEW_ACC_RATE; break;     // Few
      case 6: hopperAcc = TELEOP_NONE_ACC_RATE; break;    // None
      default: hopperAcc = TELEOP_IDK_ACC_RATE; break;    // IDK
  }
//HOLD  console.log("   --> teleop hopperAcc (percentage) = " + hopperAcc);

  let teleopEstFuel = Number(hoppersShot * hopperAcc * hopperCap).toFixed(2);
//HOLD  console.log("     -----> teleopEstFuel = "+ teleopEstFuel);
  return teleopEstFuel;
};


