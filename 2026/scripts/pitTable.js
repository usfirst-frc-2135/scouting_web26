function insertPitTableHeader(tableId) {
    console.log("==> pitData: insertPitDataHeader()");
    let tbodyRef = document.getElementById(tableId).querySelector('thead');
    let headerRow = tbodyRef.insertRow();

    // Static columns
    let headers = [{
        text: "Team",
        class: "table-primary sorttable_numeric"
        
      },
      {
        text: "Hopper Capacity",
        class: "table-info"
      },
      {
        text: "Trench",
        class: "table-info"
      },
      {
        text: "Auton Climb",
        class: "table-info"
      },
      {
        text: "Climb Level",
        class: "table-info"
      },
      {
        text: "Swerve",
        class: "table-success"
      },
      {
        text: "Drive Motors",
        class: "table-success"
      },
      {
        text: "Spare Mechanism",
        class: "table-success"
      },
      {
        text: "Prog Language",
        class: "table-success"
      },
      {
        text: "Auto Align",
        class: "table-success"
      },
      {
        text: "Num Batteries",
        class: "table-success"
      },
      {
        text: "Pit Org",
        class: "table-warning"
      },
      {
        text: "Prep",
        class: "table-warning"
      },
      {
        text: "Scout",
        class: "table-warning"
      }
    ];

    // Create header cells
    for (let header of headers) {
      let th = document.createElement('th');
      th.scope = 'col';
      th.className = header.class;
      th.innerText = header.text;
      headerRow.appendChild(th);
    }
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

