<?php
$title = 'Strategic Scouting Form';
require 'inc/header.php';
?>

<div class="container-fluid row-offcanvas row-offcanvas-left">
  <div id="content" class="column col-lg-12 col-sm-12 col-xs-12">

    <!-- Page Title -->
    <div class="row pt-3 mb-3">
      <div class="row justify-content-md-center">
        <h2 class="col-md-6 mb-3 me-3"><?php echo $title; ?> </h2>
      </div>

      <!-- Main card to hold the strategic form -->
      <div class="card col-md-6 mx-auto">

        <div id="strategicScoutingMessage" class="alert alert-dismissible fade show" style="display: none" role="alert">
          <div id="uploadMessageText"></div>
          <button id="closeMessage" class="btn-close" type="button" aria-label="Strategic Form Close"></button>
        </div>

        <!-- Strategic Entry Form -->
        <div class="card-body mb-3">
          <form id="strategicForm" method="post" enctype="multipart/form-data" name="strategicForm">
            <div>
              <h4>Match Info</h4>
            </div>
            <div class="row  col-9 col-md-7 mb-3">
              <span>Match Number</span>
              <div class="input-group">
                <div class="input-group-prepend">
                  <select id="enterCompLevel" class="form-select" aria-label="Comp Level Select">
                    <option id="compLevelP" value="p">P</option>
                    <option id="compLevelQM" value="qm" selected>QM</option>
                    <option id="compLevelSF" value="sf">SF</option>
                    <option id="compLevelF" value="f">F</option>
                  </select>
                </div>
                <input id="enterMatchNumber" class="form-control" type="text" placeholder="Match number">
              </div>
            </div>

            <div class="col-7 col-md-5 mb-3">
              <label for="enterTeamNumber" class="form-label">Team Number</label>
              <input id="enterTeamNumber" class="form-control" type="text" placeholder="FRC team number">
            </div>
            <div id="aliasNumber" class="ms-3 mb-3 text-success"></div>

            <div class="col-7 col-md-6 mb-3">
              <label for="selectScoutName" class="form-label">Scout Name</label>
              <select id="selectScoutName" class="form-select mb-3" onchange="showScoutInputBox(this.value)"
                aria-label="selectScoutName">
                <option selected>Choose ...</option>
              </select>
              <div id="otherDiv" class="mb-3" style="display:none;">
                <input id="otherScoutName" class="form-control" type="text" placeholder="First name, last initial">
              </div>
            </div>

            <!-- Autonomous -->
            <div class="card mb-3 bg-success-subtle">
              <div class="card-body">
                <div>
                  <span class="fw-bold">Autonomous Neutral Zone Action</span>
                </div>
                <div class="form-check form-check-inline">
                  <label for="autonFuelDisrupt" class="form-label">Significantly disrupted other side's Neutral Zone fuel</label>
                  <input id="autonFuelDisrupt" class="form-check-input" type="checkbox">
                </div>
              </div>
            </div>
            <!-- end Autonomous -->

            <!-- Played Defense -->
            <div class="card mb-3 bg-primary-subtle">
              <div class="card-body">
                <div>
                  <span class="fw-bold">Played Defense</span>
                </div>
                <div class="form-check form-check-inline">
                  <label for="defenseAgainstShooter" class="form-label">Against shooter</label>
                  <input id="defenseAgainstShooter" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="defenseAtBump" class="form-label">At bump</label>
                  <input id="defenseAtBump" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="defenseAtTrench" class="form-label">At trench</label>
                  <input id="defenseAtTrench" class="form-check-input" type="checkbox">
                </div>
              </div>
            </div>
            <!-- end Played Defense -->

            <!-- Evaded Defense Section -->
            <div class="card mb-3 bg-warning-subtle">
              <div class="card-body">
                <div>
                  <span class="fw-bold">Evaded Defense Effectiveness</span>
                </div>
                <div class="col-6">
                  <div class="input-group mb-3">
                    <select id="againstDefenseEffectiveness" class="form-select">
                      <option selected value="-1">Choose ...</option>
                      <option value="0">0 - N/A</option>
                      <option value="1">1 - Low</option>
                      <option value="2">2 - Med Low</option>
                      <option value="3">3 - Avg</option>
                      <option value="4">4 - Med High</option>
                      <option value="5">5 - High</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <!-- end Evaded Defense section -->

            <!-- Bump Issues-->
            <div class="card mb-3 bg-danger-subtle">
              <div class="card-body">
                <div>
                  <span class="fw-bold">Bump Issues </span>
                </div>
                <div class="form-check form-check-inline">
                  <label for="bumpBottomedOut" class="form-label">Bottomed out</label>
                  <input id="bumpBottomedOut" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="bumpTippedOver" class="form-label">Tipped over</label>
                  <input id="bumpTippedOver" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="bumpGotStuckOnFuel" class="form-label">Stuck on fuel > 5s</label>
                  <input id="bumpGotStuckOnFuel" class="form-check-input" type="checkbox">
                </div>
              </div>
            </div>
            <!-- end bump -->

            <!-- Stealing Fuel -->
            <div class="card mb-3 bg-success-subtle">
              <div class="card-body">
                <div>
                  <span class="fw-bold">Stealing Fuel from Alliance Zone</span>
                </div>
                <div class="form-check form-check-inline">
                  <label for="stealingFuel" class="form-label">Got a significant amount of fuel by passing, herding or outtaking</label>
                  <input id="stealingFuel" class="form-check-input" type="checkbox">
                </div>
              </div>
            </div>
            <!-- end Stealing Fuel -->

            <!-- Comments section -->
            <div class="card bg-body-subtle mb-3">
              <div class="card-header fw-bold">
                Comments
              </div>
              <div class="card-body">
                <div>
                  <label for="problemComment" class="form-label">Problems robot had on the field:</label>
                  <input id="problemComment" class="form-control" type="text">
                </div>

                <div>
                  <label for="generalComment" class="form-label">General comment:</label>
                  <input id="generalComment" class="form-control" type="text">
                </div>
              </div>
            </div>
            <!-- End Comments section -->
          </form>

          <!-- Submit button -->
          <div class="d-grid gap-2 col-6 mx-auto">
            <button id="submitButton" class="btn btn-primary" type="button">Submit</button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'inc/footer.php'; ?>

<!-- Javascript page handlers -->

<script>
  //
  // Check if our URL directs to a specific team
  //
  function checkURLForTeamSpec() {
    console.log("=> strategicForm: checkURLForTeamSpec()");
    let sp = new URLSearchParams(window.location.search);
    if (sp.has('teamNum')) {
      return sp.get('teamNum');
    }
    return "";
  }

  //
  // Show scout name text entry box
  //
  function showScoutInputBox(value) {
    document.getElementById('otherDiv').style.display = value === 'Other' ? 'block' : 'none';
  }

  // Get scout name - return empty string if not a valid selection or empty text box
  function getScoutName() {
    let scoutName = document.getElementById("selectScoutName").value.trim();

    if (scoutName === "Choose ...")
      scoutName = "";
    else if (scoutName === "Other") {
      scoutName = document.getElementById("otherScoutName").value.trim();
      scoutName.replace(' ', '_');
    }
    return scoutName;
  }

  //
  // Validate strategic form entries
  //
  function validateStrategicForm() {
    console.log("==> strategicForm.php: clearStrategicForm()");
    let isError = false;
    let errMsg = "Please enter values for these fields:";
    let matchNumber = document.getElementById("enterMatchNumber").value.trim();
    let teamNum = document.getElementById("enterTeamNumber").value.toUpperCase().trim();
    let scoutName = getScoutName();

    // Make sure there is a team number, scoutname and matchnum.
    if ((matchNumber === "") || isNaN(parseInt(matchNumber))) {
      if (isError)
        errMsg += ",";
      errMsg += " Match Number";
      isError = true;
    }
    if (validateTeamNumber(teamNum, null) <= 0) {
      if (isError)
        errMsg += ",";
      errMsg += " Team Number";
      isError = true;
    }
    if (scoutName === "") {
      if (isError)
        errMsg += ",";
      errMsg += " Scout Name";
      isError = true;
    }
    if (isError) {
      alert(errMsg);
    }
    return isError;
  }

  //
  // Clear strategic form entries
  //
  function clearStrategicForm() {
    console.log("==> strategicForm.php: clearStrategicForm()");
    document.getElementById("compLevelQM").selected = true;
    document.getElementById("enterMatchNumber").value = "";
    document.getElementById("enterTeamNumber").value = "";
    document.getElementById("aliasNumber").innerText = "";
    document.getElementById("selectScoutName").value = "Choose ...";
    document.getElementById("otherScoutName").value = "";

    console.log("  ==> clearing autonFuelDistrup checkbox");
    // Autonomous Neutral Zone fuel disruption 
    document.getElementById("autonFuelDisrupt").checked = false;

    // Defense Scouting
    console.log("  ==> clearing defense checkboxes");
    document.getElementById("defenseAgainstShooter").checked = false;
    document.getElementById("defenseAtBump").checked = false;
    document.getElementById("defenseAtTrench").checked = false;

    // Evading Defense Scouting
    console.log("  ==> clearing against-defense list");
    document.getElementById("againstDefenseEffectiveness").value = "";

    // Bump Scouting
    console.log("  ==> clearing bump checkboxes");
    document.getElementById("bumpTippedOver").checked = false;
    document.getElementById("bumpBottomedOut").checked = false;
    document.getElementById("bumpGotStuckOnFuel").checked = false;

    // Stealing Fuel 
    console.log("  ==> clearing stealing checkbox");
    document.getElementById("stealingFuel").checked = false;

    // Comment boxes
    console.log("  ==> clearing commenst ");
    document.getElementById("problemComment").value = "";
    document.getElementById("generalComment").value = "";
  }

  //
  // Write strategic form data to DB table
  //
  function getStrategicFormData() {
    console.log("==> strategicForm.php: getStrategicFormData()");
    let dataToSave = {};

    // Create match number before writing to table.
    let compLevel = document.getElementById("enterCompLevel").value;
    let matchNumber = document.getElementById("enterMatchNumber").value.trim();
    dataToSave["matchnumber"] = compLevel + matchNumber;
    dataToSave["teamnumber"] = document.getElementById("enterTeamNumber").value.toUpperCase().trim();
    dataToSave["scoutname"] = getScoutName();

    // Autonomous scouting (stored under "activeShiftLoadedHopper" keyword)
    dataToSave["activeShiftLoadedHopper"] = (document.getElementById("autonFuelDisrupt").checked) ? 1 : 0;

    // Defense scouting (storing data with old "activeShift.." keywords)
    dataToSave["activeShiftDefenseAgainstShooter"] = (document.getElementById("defenseAgainstShooter").checked) ? 1 : 0;
    dataToSave["activeShiftDefenseAtBump"] = (document.getElementById("defenseAtBump").checked) ? 1 : 0;
    dataToSave["activeShiftDefenseAtTrench"] = (document.getElementById("defenseAtTrench").checked) ? 1 : 0;

    // Evading Defense scouting (note value may not be set)
    let ade = document.getElementById("againstDefenseEffectiveness").value;
    if (ade == -1)
      ade = 0;
    dataToSave["againstDefenseEffectiveness"] = ade;

    // Bump scouting
    dataToSave["bumpTippedOver"] = (document.getElementById("bumpTippedOver").checked) ? 1 : 0;
    dataToSave["bumpBottomedOut"] = (document.getElementById("bumpBottomedOut").checked) ? 1 : 0;
    dataToSave["bumpGotStuckOnFuel"] = (document.getElementById("bumpGotStuckOnFuel").checked) ? 1 : 0;

    // Stealing fuel scouting (saved under old "fouls" keyword)
    dataToSave["fouls"] = (document.getElementById("stealingFuel").checked) ? 1 : 0;

    // Comment boxes
    dataToSave["problem_comment"] = document.getElementById("problemComment").value;
    dataToSave["general_comment"] = document.getElementById("generalComment").value;

    // Save dummy data to unused data spots.
    dataToSave["activeShiftShotHopper"] = 0;
    dataToSave["activeShiftPassingFromAlliance"] = 0;
    dataToSave["activeShiftPassingFromNeutral"] = 0;
    dataToSave["activeShiftShoveledFuel"] = 0;
    dataToSave["inactiveShiftLoadedHopper"] = 0;
    dataToSave["inactiveShiftShotHopper"] = 0;
    dataToSave["inactiveShiftPassingFromAlliance"] = 0;
    dataToSave["inactiveShiftPassingFromNeutral"] = 0;
    dataToSave["inactiveShiftShoveledFuel"] = 0;
    dataToSave["inactiveShiftDefenseAgainstShooter"] = 0;
    dataToSave["inactiveShiftDefenseAtBump"] = 0;
    dataToSave["inactiveShiftDefenseAtTrench"] = 0;
    dataToSave["bumpAvoidedDefender"] = 0;

    return dataToSave;
  }

  //
  // Send the pit form data to the server
  //
  function submitStrategicFormData(strategicFormData) {
    console.log("==> strategicForm: submitStrategicFormData()");
    $.post("api/dbWriteAPI.php", {
      writeStrategicData: JSON.stringify(strategicFormData)
    }).done(function(response) {
      console.log("=> writeStrategicData");
      if (response.indexOf('success') > -1) { // A loose compare, because success word may have a newline
        clearStrategicForm();
        alert("Success in submitting Strategic Form data!");
      } else {
        alert("Failure in submitting Strategic Form!");
      }
    });
  }

  /////////////////////////////////////////////////////////////////////////////
  //
  // Process the generated html
  //
  document.addEventListener("DOMContentLoaded", function() {

    let jAliasNames = null;

    // Read the alias table
    $.get("api/dbReadAPI.php", {
      getEventAliasNames: true
    }).done(function(eventAliasNames) {
      console.log("=> eventAliasNames");
      jAliasNames = JSON.parse(eventAliasNames);
    });

    // Check URL for source team to load
    let initTeamNumber = checkURLForTeamSpec().toUpperCase();
    if (initTeamNumber !== "") {
      document.getElementById("enterTeamNumber").value = initTeamNumber;
    }

    // Read scout names from database for this event
    $.get("api/dbReadAPI.php", {
      getEventScoutNames: true
    }).done(function(eventScoutNames) {
      console.log("=> getEventScoutNames");
      let scoutSelect = document.getElementById("selectScoutName");
      let jsonNames = JSON.parse(eventScoutNames);
      for (let name of jsonNames) {
        let option = document.createElement('option');
        option.value = name["scoutname"];
        option.innerHTML = name["scoutname"];
        scoutSelect.appendChild(option);
      };
      let other = document.createElement('option');
      other.value = "Other";
      other.innerHTML = "Other";
      scoutSelect.appendChild(other);
    });

    // Submit the strategic form data
    document.getElementById("submitButton").addEventListener('click', function() {
      if (!validateStrategicForm()) {
        let strategicFormData = getStrategicFormData();
        submitStrategicFormData(strategicFormData);
      }
    });

    // Attach enterTeamNumber listener when losing focus to check for alias numbers
    document.getElementById('enterTeamNumber').addEventListener('focusout', function() {
      console.log("enterTeamNumber: focus out");
      let enteredNum = event.target.value.toUpperCase().trim();
      if (isAliasNumber(enteredNum)) {
        let teamNum = getTeamNumFromAlias(enteredNum, jAliasNames);
        if (teamNum === "")
          document.getElementById("aliasNumber").innerText = "Alias number " + enteredNum + " is NOT valid!";
        else
          document.getElementById("aliasNumber").innerText = "Alias number " + enteredNum + " is Team " + teamNum;
        document.getElementById("enterTeamNumber").value = teamNum;
      } else
        document.getElementById("aliasNumber").innerText = "";
    });
  });
</script>

<script src="./scripts/aliasFunctions.js"></script>
<script src="./scripts/validateTeamNumber.js"></script>
