<?php
$title = 'Strategic Scouting Form';
require 'inc/header.php';
?>

<div class="container row-offcanvas row-offcanvas-left">
  <div id="content" class="column card-lg-12 col-sm-12 col-xs-12">

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

            <!-- Active Shift Actions -->
            <div class="card mb-3 bg-success-subtle">
              <div class="card-header fw-bold">
                Active Shift Actions
              </div>
              <div class="card-body">
                <div class="form-check form-check-inline">
                  <label for="activeShiftLoadedHopper" class="form-label">Loaded Hopper</label>
                  <input id="activeShiftLoadedHopper" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="activeShiftShotHopper" class="form-label">Shot Hopper</label>
                  <input id="activeShiftShotHopper" class="form-check-input" type="checkbox">
                </div>
                <div>
                  <span class="fw-bold">Active Shift - Passing:</span>
                </div>
                <div class="form-check form-check-inline">
                  <label for="activeShiftPassingFromAlliance" class="form-label">Passed fuel from other Alliance Zone</label>
                  <input id="activeShiftPassingFromAlliance" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="activeShiftPassingFromNeutral" class="form-label">Passed fuel from Neutral Zone</label>
                  <input id="activeShiftPassingFromNeutral" class="form-check-input" type="checkbox">
                </div>

                <!-- Auton - Committed fouls section -->
                <div>
                  <span class="fw-bold">Active Shift - Defense:</span>
                </div>
                <div class="form-check form-check-inline">
                  <label for="activeShiftDefenseAgainstShooter" class="form-label">Played defense against shooter</label>
                  <input id="activeShiftDefenseAgainstShooter" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="activeShiftDefenseAtBump" class="form-label">Played defense at bump</label>
                  <input id="activeShiftDefenseAtBump" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="activeShiftDefenseAtTrench" class="form-label">Played defense at trench</label>
                  <input id="activeShiftDefenseAtTrench" class="form-check-input" type="checkbox">
                </div>
              </div>
            </div>
            <!-- end Active Shift Mode -->

            <!-- Inactive Shift Mode -->
            <div class="card mb-3 bg-primary-subtle">
              <div class="card-header fw-bold">
                Inactive Shift Actions
              </div>
              <div class="card-body">
                <div class="form-check form-check-inline">
                  <label for="inactiveShiftLoadedHopper" class="form-label">Loaded Hopper</label>
                  <input id="inactiveShiftLoadedHopper" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="inactiveShiftShotHopper" class="form-label">Shot Hopper</label>
                  <input id="inactiveShiftShotHopper" class="form-check-input" type="checkbox">
                </div>
                <div>
                  <span class="fw-bold">Inactive Shift - Passing:</span>
                </div>
                <div class="form-check form-check-inline">
                  <label for="inactiveShiftPassingFromAlliance" class="form-label">Passed fuel from other Alliance Zone</label>
                  <input id="inactiveShiftPassingFromAlliance" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="inactiveShiftPassingFromNeutral" class="form-label">Passed fuel from Neutral Zone</label>
                  <input id="inactiveShiftPassingFromNeutral" class="form-check-input" type="checkbox">
                </div>

                <!-- Auton - Committed fouls section -->
                <div>
                  <span class="fw-bold">Active Shift - Defense:</span>
                </div>
                <div class="form-check form-check-inline">
                  <label for="inactiveShiftDefenseAgainstShooter" class="form-label">Played defense against shooter</label>
                  <input id="inactiveShiftDefenseAgainstShooter" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="inactiveShiftDefenseAtBump" class="form-label">Played defense at bump</label>
                  <input id="inactiveShiftDefenseAtBump" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="inactiveShiftDefenseAtTrench" class="form-label">Played defense at trench</label>
                  <input id="inactiveShiftDefenseAtTrench" class="form-check-input" type="checkbox">
                </div>
              </div>
            </div>
            <!-- end Inactive Shift Mode -->

            <!-- Playing Defense Section -->
            <div class="card mb-3 bg-warning-subtle">
              <div class="card-header fw-bold">
                Evading Defense
              </div>
              <div class="card-body">
                <!-- Defense tactics section -->
                <div class="mb-2">
                  <span class="fw-bold">Effectiveness:</span>
                </div>
                <div class="col-6">
                  <div class="input-group mb-3">
                    <select id="againstDefenseEffectiveness" class="form-select">
                      <option selected value="-1">Choose ...</option>
                      <option value="0">0-Low</option>
                      <option value="1">1-Med Low</option>
                      <option value="2">2-Avg</option>
                      <option value="3">3-Med High</option>
                      <option value="4">4-High</option>
                    </select>
                  </div>
                </div>
              </div>
              </div>

                <!-- Against defensive robot section -->

            <!-- end Playing Defense Section -->

           <!-- Bump Mode -->
            <div class="card mb-3 bg-success-subtle">
              <div class="card-header fw-bold">
                Bump
              </div>
              <div class="card-body">
                <div class="form-check form-check-inline">
                  <label for="bumpTippedOver" class="form-label">Tipped Over</label>
                  <input id="bumpTippedOver" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="bumpBottomedOut" class="form-label">Bottomed Out</label>
                  <input id="bumpBottomedOut" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="bumpAvoidedDefender" class="form-label">Avoided Defender</label>
                  <input id="bumpAvoidedDefender" class="form-check-input" type="checkbox">
                </div>
                <div class="form-check form-check-inline">
                  <label for="bumpGotStuckOnFuel" class="form-label">Got Stuck on Fuel</label>
                  <input id="bumpGotStuckOnFuel" class="form-check-input" type="checkbox">
                </div>
              </div>  
            </div>
            <!-- end bump -->

            <!-- Fouls Mode -->
            <div class="card mb-3 bg-primary-subtle">
              <div class="card-header fw-bold">
                Fouls
              </div>
              <div class="card-body">
                <div class="form-check form-check-inline">
                  <label for="fouls" class="form-label">Caused a foul</label>
                  <input id="fouls" class="form-check-input" type="checkbox">
                </div>
              </div>  
            </div>
            <!-- end fouls -->

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

    // Active Shift Scouting
    document.getElementById("activeShiftLoadedHopper").checked = false;
    document.getElementById("activeShiftShotHopper").checked = false;
    document.getElementById("activeShiftPassingFromAlliance").checked = false;
    document.getElementById("activeShiftPassingFromNeutral").checked = false;
    document.getElementById("activeShiftDefenseAgainstShooter").checked = false;
    document.getElementById("activeShiftDefenseAtBump").checked = false;
    document.getElementById("activeShiftDefenseAtTrench").checked = false;

    // Inactive Shift Scouting
    document.getElementById("inactiveShiftLoadedHopper").checked = false;
    document.getElementById("inactiveShiftShotHopper").checked = false;
    document.getElementById("inactiveShiftPassingFromAlliance").checked = false;
    document.getElementById("inactiveShiftPassingFromNeutral").checked = false;
    document.getElementById("inactiveShiftDefenseAgainstShooter").checked = false;
    document.getElementById("inactiveShiftDefenseAtBump").checked = false;
    document.getElementById("inactiveShiftDefenseAtTrench").checked = false;

    // Evading Defense Scouting
    document.getElementById("againstDefenseEffectiveness").value = "";

    // Bump Scouting
    document.getElementById("bumpTippedOver").checked = false;
    document.getElementById("bumpBottomedOut").checked = false;
    document.getElementById("bumpAvoidedDefender").checked = false;
    document.getElementById("bumpGotStuckOnFuel").checked = false;

    // Bump Scouting
    document.getElementById("fouls").checked = false;

    // Comment boxes
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

    // Active Shift scouting
    dataToSave["activeShiftLoadedHopper"] = (document.getElementById("activeShiftLoadedHopper").checked) ? 1 : 0;
    dataToSave["activeShiftShotHopper"] = (document.getElementById("activeShiftShotHopper").checked) ? 1 : 0;
    dataToSave["activeShiftPassingFromAlliance"] = (document.getElementById("activeShiftPassingFromAlliance").checked) ? 1 : 0;
    dataToSave["activeShiftPassingFromNeutral"] = (document.getElementById("activeShiftPassingFromNeutral").checked) ? 1 : 0;
    dataToSave["activeShiftDefenseAgainstShooter"] = (document.getElementById("activeShiftDefenseAgainstShooter").checked) ? 1 : 0;
    dataToSave["activeShiftDefenseAtBump"] = (document.getElementById("activeShiftDefenseAtBump").checked) ? 1 : 0;
    dataToSave["activeShiftDefenseAtTrench"] = (document.getElementById("activeShiftDefenseAtTrench").checked) ? 1 : 0;

    // Inactive Shift scouting
    dataToSave["inactiveShiftLoadedHopper"] = (document.getElementById("inactiveShiftLoadedHopper").checked) ? 1 : 0;
    dataToSave["inactiveShiftShotHopper"] = (document.getElementById("inactiveShiftShotHopper").checked) ? 1 : 0;
    dataToSave["inactiveShiftPassingFromAlliance"] = (document.getElementById("inactiveShiftPassingFromAlliance").checked) ? 1 : 0;
    dataToSave["inactiveShiftPassingFromNeutral"] = (document.getElementById("inactiveShiftPassingFromNeutral").checked) ? 1 : 0;
    dataToSave["inactiveShiftDefenseAgainstShooter"] = (document.getElementById("inactiveShiftDefenseAgainstShooter").checked) ? 1 : 0;
    dataToSave["inactiveShiftDefenseAtBump"] = (document.getElementById("inactiveShiftDefenseAtBump").checked) ? 1 : 0;
    dataToSave["inactiveShiftDefenseAtTrench"] = (document.getElementById("inactiveShiftDefenseAtTrench").checked) ? 1 : 0;

    // Evading Defense scouting
    dataToSave["againstDefenseEffectiveness"] = document.getElementById("againstDefenseEffectiveness").value;

    // Bump scouting
    dataToSave["bumpTippedOver"] = (document.getElementById("bumpTippedOver").checked) ? 1 : 0;
    dataToSave["bumpBottomedOut"] = (document.getElementById("bumpBottomedOut").checked) ? 1 : 0;
    dataToSave["bumpAvoidedDefender"] = (document.getElementById("bumpAvoidedDefender").checked) ? 1 : 0;
    dataToSave["bumpGotStuckOnFuel"] = (document.getElementById("bumpGotStuckOnFuel").checked) ? 1 : 0;

    // Fouls scouting
    dataToSave["fouls"] = (document.getElementById("fouls").checked) ? 1 : 0;

    // Comment boxes
    dataToSave["problem_comment"] = document.getElementById("problemComment").value;
    dataToSave["general_comment"] = document.getElementById("generalComment").value;

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
