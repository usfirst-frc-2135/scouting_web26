function insertPitTableHeader(tableId) {
    console.log("==> pitData: insertPitDataHeader()");
    let theadRef = document.getElementById(tableId).querySelector('thead');
    theadRef.innerHTML = "";   // clear table
    
    const thAuton = '<th scope="col" class="bg-success-subtle">';  // Auton color
    const thTeleop = '<th scope="col" class="bg-primary-subtle">';  // Teleop color
    const thEndgame = '<th scope="col" class="bg-warning-subtle">';  // Endgame color

    let rowString = '';
    rowString += '<th scope="col" class=bg-body sortable-numeric">' + 'Team' + '</th>';
    rowString += thAuton + 'Hopper Cap' + '</th>';
    rowString += thAuton + 'Trench' + '</th>';
    rowString += thAuton + 'Auton Climb' + '</th>';
    rowString += thAuton + 'Climb Level' + '</th>';
    rowString += thTeleop + 'Swerve' + '</th>';
    rowString += thTeleop + 'Drive Motors' + '</th>';
    rowString += thTeleop + 'Spare Mechanism' + '</th>';
    rowString += thTeleop + 'Prog Lang' + '</th>';
    rowString += thTeleop + 'Auto Align' + '</th>';
    rowString += thTeleop + 'Num Batteries' + '</th>';
    rowString += thEndgame + 'Pit Org' + '</th>';
    rowString += thEndgame + 'Prep' + '</th>';
    rowString += '<th scope="col"> Scout </th>';

    theadRef.insertRow().innerHTML = rowString;
  }

  //
  // Converts a given "1" to yes, "2" to no, anything else to a dash.
  //
  function toYesNo(value) {
    switch (String(value)) {
      case "0":
        return "No";
      case "1":
        return "Yes";
      default:
        return "-";
    }
  }

  //
  // Converts a given pit organization to a string
  //
  function toOrganization(value) {
    switch (String(value)) {
      case "1":
        return "Messy";
      case "2":
        return "Below Average";
      case "3":
        return "Organized!";
      case "4":
        return "Above Average";
      case "5":
        return "Pristine";
      default:
        return "-";
    }
  }

  //
  // Converts a given readiness to a string
  //
  function toPreparedness(value) {
    switch (String(value)) {
      case "1":
        return "Chaos";
      case "2":
        return "Below Average";
      case "3":
        return "Prepared!";
      case "4":
        return "Above Average";
      case "5":
        return "Proactive";
      default:
        return "-";
    }
  }


  //
  // Insert the pit data table body
  //
  function insertPitTableBody(tableId, pitData, teamFilter) {
    console.log("==> pitData: insertPitDataBody()");
    let tbodyRef = document.getElementById(tableId).querySelector('tbody');
    tbodyRef.innerHTML = "";   // clear table

    // Go thru each team and build the HTML string for that row.
    for (let teamNum in pitData) {
//      console.log("--->>> insertPitTableBody(): looking at teamNum = "+teamNum);
      if (teamFilter.length !== 0 && !teamFilter.includes(teamNum))
        continue;   // skip this team

      let rowString = "";
      rowString += "<td><a href='teamLookup.php?teamNum=" + teamNum + "'>" + teamNum + "</a>";
      
       //setting up the colors for the vertical rows 
      const tdBody = "<td class='bg-body'>";
      const tdBlue = "<td class='bg-primary-subtle'>";

      let teamPitData = pitData[teamNum];
      rowString += tdBody + teamPitData["caphopper"] + "</td>";
      rowString += tdBlue + toYesNo(teamPitData["trenchdrive"]) + "</td>";
      rowString += tdBody + toYesNo(teamPitData["climbable"]) + "</td>";
      rowString += tdBlue + teamPitData["climblevel"] + "</td>";
      rowString += tdBody + toYesNo(teamPitData["swerve"]) + "</td>";
      rowString += tdBlue + teamPitData["drivemotors"] + "</td>";
      rowString += tdBody + toYesNo(teamPitData["spareparts"]) + "</td>";
      rowString += tdBlue + teamPitData["proglanguage"] + "</td>";
      rowString += tdBody + toYesNo(teamPitData["computervision"]) + "</td>";
      rowString += tdBlue + teamPitData["numbatteries"] + "</td>";
      rowString += tdBody + toOrganization(teamPitData["pitorg"]) + "</td>";
      rowString += tdBlue + toPreparedness(teamPitData["preparedness"]) + "</td>";
      rowString += tdBody + teamPitData["scoutname"] + "</td>";

      tbodyRef.insertRow().innerHTML = rowString;
  }
}

