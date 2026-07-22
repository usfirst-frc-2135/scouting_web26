<?php
$title = 'QR Scanner';
require 'inc/header.php';
?>

<div class="container row-offcanvas row-offcanvas-left">
  <div id="content" class="column card-lg-12 col-sm-12 col-xs-12">

    <!-- Page Title -->
    <div class="row pt-3 mb-3">
      <div class="row justify-content-md-center">
        <h2 class="col-md-6 mb-3 me-3"><?php echo $title; ?> </h2>
      </div>

      <!-- Main card to hold the QR scanner -->
      <div class="card col-md-6 mx-auto">
        <div class="card-body">
          <div id="interactive" class="viewport mt-3">
            <video id="camera" class="col-12" autoplay="true"></video>
          </div>
          <select id="cameraSelector" class="form-select mb-3" aria-label="Camera Select">
          </select>
          <div class="d-grid gap-2 col-6 mx-auto">
            <button id="submitData" class="btn btn-success mb-3" type="button"></button>
          </div>
        </div>
      </div>

    </div>

    <table id="qrScanTable" class="table">
      <thead>
        <tr>
          <th scope="col">Event Code</th>
          <th scope="col">Match Number</th>
          <th scope="col">Team Number</th>
          <th scope="col">Scout</th>
          <th scope="col">Delete</th>
        </tr>
      </thead>
      <tbody class="table-group-divider"> </tbody>
    </table>

  </div>
</div>

<?php include 'inc/footer.php'; ?>

<!-- Javascript page handlers -->

<script>
  const qrValidLength = 32; // This is determined by game requirements and adjusted each year

  // Validate the scanned QR string as an associative array
  function validateQrObject(qrObject) {
    if (!qrObject || typeof qrObject !== 'object' || Array.isArray(qrObject)) {
      console.warn("validateQrObject: expected an object as an associative array");
      return false;
    }

    const requiredFields = [
      "appVersion",
      "eventcode",
      "matchnumber",
      "teamnumber",
      "teamalias",
      "scoutname",

      "died",

      // Autonomous
      "autonShootPreload",
      "autonPreloadAccuracy",
      "autonHoppersShot",
      "autonHopperAccuracy",
      "autonAllianceZone",
      "autonDepot",
      "autonOutpost",
      "autonNeutralZone",
      "autonClimb",

      // Teleop
      "teleopHoppersUsed",
      "teleopHopperAccuracy",
      "teleopIntakeAndShoot",
      "teleopPassingRate",
      "teleopDefenseLevel",
      "driverAbility",
      "teleopAllianceToAlliance",
      "teleopNeutralToAlliance",


      // Endgame
      "endgameStartClimb",
      "endgameClimbLevel",
      "endgameClimbPosition",

      // Overall
      "comment",

      // Future expansion fields
      "other1", // used for shovelFuel
      "other2",
      "other3",
      "other4"
    ];

    for (let field of requiredFields) {
      const value = String(qrObject[field] ?? "").trim();
      if (!value) {
        console.warn("validateQrObject: missing required value for " + field);
        return false;
      }
    }

    return true;
  }

  // Normalize the scanned QR string as an associative array
  function normalizeQrObject(qrObject) {
    const normalized = {
      appVersion: String(qrObject.appVersion ?? "").trim(),
      eventcode: String(qrObject.eventcode ?? "").trim(),
      matchnumber: String(qrObject.matchnumber ?? "").trim(),
      teamnumber: String(qrObject.teamnumber ?? "").trim(),
      teamalias: String(qrObject.teamalias ?? "").trim(),
      scoutname: String(qrObject.scoutname ?? "").trim(),
      died: String(qrObject.died ?? "").trim(),
      autonShootPreload: String(qrObject.autonShootPreload ?? "").trim(),
      autonPreloadAccuracy: String(qrObject.autonPreloadAccuracy ?? "").trim(),
      autonHoppersShot: String(qrObject.autonHoppersShot ?? "").trim(),
      autonHopperAccuracy: String(qrObject.autonHopperAccuracy ?? "").trim(),
      autonAllianceZone: String(qrObject.autonAllianceZone ?? "").trim(),
      autonDepot: String(qrObject.autonDepot ?? "").trim(),
      autonOutpost: String(qrObject.autonOutpost ?? "").trim(),
      autonNeutralZone: String(qrObject.autonNeutralZone ?? "").trim(),
      autonClimb: String(qrObject.autonClimb ?? "").trim(),
      teleopHoppersUsed: String(qrObject.teleopHoppersUsed ?? "").trim(),
      teleopHopperAccuracy: String(qrObject.teleopHopperAccuracy ?? "").trim(),
      teleopIntakeAndShoot: String(qrObject.teleopIntakeAndShoot ?? "").trim(),
      teleopPassingRate: String(qrObject.teleopPassingRate ?? "").trim(),
      teleopDefenseLevel: String(qrObject.teleopDefenseLevel ?? "").trim(),
      driverAbility: String(qrObject.driverAbility ?? "").trim(),
      teleopAllianceToAlliance: String(qrObject.teleopAllianceToAlliance ?? "").trim(),
      teleopNeutralToAlliance: String(qrObject.teleopNeutralToAlliance ?? "").trim(),
      endgameStartClimb: String(qrObject.endgameStartClimb ?? "").trim(),
      endgameClimbLevel: String(qrObject.endgameClimbLevel ?? "").trim(),
      endgameClimbPosition: String(qrObject.endgameClimbPosition ?? "").trim(),
      comment: String(qrObject.comment ?? "").trim(),
      other1: String(qrObject.other1 ?? "").trim(),
      other2: String(qrObject.other2 ?? "").trim(),
      other3: String(qrObject.other3 ?? "").trim(),
      other4: String(qrObject.other4 ?? "").trim()
    };

    return normalized;
  }

  // Validate the scanned QR string
  function validateQrList(qrList) {
    console.log("==> validateQrList: qrList.length = " + qrList.length + " (valid " + qrValidLength + ")");
    if (qrList.length != qrValidLength) {
      console.warn("===> validateQrList: returning false! ");
      return false;
    }
    console.log("===> validateQrList: returning true! ");
    return true;
  }

  //
  // Convert the scanned QR string to a list
  //
  function qrStringToList(dataString) {
    let out = [];
    if (dataString.includes('\t')) {
      out = dataString.trim().split("\t");
    } else if (dataString.includes(',')) {
      out = dataString.trim().split(",");
    }

    for (let i = 0; i < out.length; ++i) {
      out[i] = out[i].trim();
    }
    return out;
  }

  //
  // IMPORTANT! also need to adjust data list size in "validateQrList" and "padList"!!!
  function qrListToMatchData(qrList) {
    let matchData = {};

    // TODO: Fix these for 2027!
    // TODO: Make case names consistent. Database fields are historically all lowercase, but QR code fields are camelCase. This is confusing and should be fixed.

    // Perennial fields that always occur
    matchData["appVersion"] = qrList[0]; // TODO: This is a QR code version number, NOT the app version number. It is used to determine how to parse the QR code data.
    matchData["eventcode"] = qrList[1];
    matchData["matchnumber"] = qrList[2];
    matchData["teamnumber"] = qrList[3];
    matchData["teamalias"] = qrList[4];
    matchData["scoutname"] = qrList[5];

    // Recurring (and overall) data
    matchData["died"] = qrList[6];

    // Match or year-specific fields below here!

    // Autonomous
    matchData["autonShootPreload"] = qrList[7];
    matchData["autonPreloadAccuracy"] = qrList[8];
    matchData["autonHoppersShot"] = qrList[9];
    matchData["autonHopperAccuracy"] = qrList[10];
    matchData["autonAllianceZone"] = qrList[11];
    matchData["autonDepot"] = qrList[12];
    matchData["autonOutpost"] = qrList[13];
    matchData["autonNeutralZone"] = qrList[14];
    matchData["autonClimb"] = qrList[15];

    // Teleop
    matchData["teleopHoppersUsed"] = qrList[16];
    matchData["teleopHopperAccuracy"] = qrList[17];
    matchData["teleopIntakeAndShoot"] = qrList[18];
    matchData["teleopPassingRate"] = qrList[19];
    matchData["teleopDefenseLevel"] = qrList[20];
    matchData["driverAbility"] = qrList[21];
    matchData["teleopAllianceToAlliance"] = qrList[22];
    matchData["teleopNeutralToAlliance"] = qrList[23];

    // Endgame
    matchData["endgameStartClimb"] = qrList[24];
    matchData["endgameClimbLevel"] = qrList[25];
    matchData["endgameClimbPosition"] = qrList[26];

    // Overall
    matchData["comment"] = qrList[27];

    // Extra spots
    matchData["other1"] = qrList[28]; // used for shovelFuel
    matchData["other2"] = qrList[29];
    matchData["other3"] = qrList[30];
    matchData["other4"] = qrList[31];
    return matchData;
  }

  //
  // Creates the key used to store the QR scan in the database
  //
  function getKeyForMatchData(matchData) {
    return matchData["eventcode"] + "_" + matchData["matchnumber"] + "_" + matchData["teamnumber"];
  }

  //
  // Adds a QR scan to the table of scans
  //
  function addMatchDataToTable(tableId, matchData, scannedMatches) {
    let key = getKeyForMatchData(matchData);

    console.log("addMatchDataToTable: Checking for key in scannedMatches: " + key + " " + scannedMatches);

    if (!Object.prototype.hasOwnProperty.call(scannedMatches, key)) {
      // Modify global variables
      if (matchData["eventcode"] !== frcEventCode) {
        console.warn("Event code does not match the one in db_config! - " + frcEventCode + "/" + matchData["eventcode"]);
        alert("QR event code does not match the one in db_config! - " + frcEventCode + "/" + matchData["eventcode"]); // this is a passive notification - return if we want to prevent this
      }
      scannedMatches[key] = matchData;
      updateScannedMatchCount(scannedMatches);
      let tbodyRef = document.getElementById(tableId).querySelector('tbody');
      let row = tbodyRef.insertRow();
      row.id = key + "_row";
      row.innerHTML = "";
      row.innerHTML += "<td>" + matchData["eventcode"] + "</td>";
      row.innerHTML += "<td>" + matchData["matchnumber"] + "</td>";
      row.innerHTML += "<td>" + matchData["teamnumber"] + "</td>";
      row.innerHTML += "<td>" + matchData["scoutname"] + "</td>";
      row.innerHTML += "<td> <button id='" + key + "_delete' value='" + key + "' class='btn btn-danger' type='button'>Delete</button></td>";

      // Add delete button
      document.getElementById(key + "_delete").addEventListener('click', function() {
        removeQrScanEntry(this.value, scannedMatches);
      });
    } else {
      console.log("addMatchDataToTable: scannedMatches already has that key!");
    }
  }

  //
  // Removes a QR scan row and cleans up
  //
  function removeQrScanEntry(dataKey, scannedMatches) {
    if (Object.prototype.hasOwnProperty.call(scannedMatches, dataKey)) {
      // Remove the match data, update the count, remove the row
      delete scannedMatches[dataKey];
      updateScannedMatchCount(scannedMatches);
      document.getElementById(dataKey + "_row").remove();
    } else {
      console.log("removeQrScanEntry: scannedMatches does not have that key!");
    }
  }

  //
  // Alerts user of a successful QR scan
  //
  function indicateScanSuccess() {
    const canVibrate = window.navigator.vibrate;
    if (canVibrate) { // iOS does not support vibrate and crashes, so test if it's available
      try {
        window.navigator.vibrate(200); // MacOS Chrome throws an "intervention" if window is not clicked first!
      } catch (e) {
        console.warn("indicateScanSuccess: Vibrate notification request failed! - " + e);
        alert("Vibrate notification request failed!");
      }
    }

    document.getElementById("content").classList.add("bg-success");
    setTimeout(function() {
      document.getElementById("content").classList.remove("bg-success");
    }, 500);
  }

  //
  //  Saves default camera ID to localStorage for on reload camera config persistence
  //
  function setDefaultDeviceID(id) {
    localStorage.setItem("cameraDefaultID", id);
  }

  //
  //  Reads default camera ID from localStorage, or returns original ID
  //
  function getDefaultDeviceID(id) {
    let defaultId = localStorage.getItem("cameraDefaultID");
    return (defaultId !== null) ? defaultId : id;
  }

  //
  // Responsible for handling actions that occur when camera is scanning
  //
  function addCameraScanner(camId, scanner, tableId, scannedMatches) {
    scanner.decodeFromInputVideoDeviceContinuously(camId, 'camera', function(result, err) {
      if (result) {
        console.log("addCameraScanner: qrList = " + result.text);
        try {
          let qrObject = JSON.parse(result.text);
          console.log("addCameraScanner: qrObject = ", qrObject);
          if (validateQrObject(qrObject)) {
            indicateScanSuccess();
            addMatchDataToTable(tableId, normalizeQrObject(qrObject), scannedMatches);
          } else {
            console.warn("addCameraScanner: QR scan content failed validation as associative array!");
            alert("QR scan content failed validation as associative array!");
          }
        } catch (e) {
          console.warn("addCameraScanner: QR scan content is not valid JSON! - " + e);
          let qrList = qrStringToList(result.text);
          console.log("addCameraScanner: qrList = " + qrList);
          if (validateQrList(qrList)) {
            indicateScanSuccess();
            addMatchDataToTable(tableId, qrListToMatchData(qrList), scannedMatches);
          } else {
            console.warn("addCameraScanner: QR scan content failed validation!");
            alert("QR scan content failed validation!");
          }
        }
      }
    });
  }

  //
  // Build the camera selection dropdown and connect the scanner passed in
  //
  function createCameraSelector(camTagId, scanner, tableId, scannedMatches) {
    // Look for cameras, enumerate them, and connect the scanner
    scanner.getVideoInputDevices().then(function(videoInputDevices) {
      let camDeviceId = null;
      let camSelector = document.getElementById(camTagId);
      console.log("createCameraSelector: Camera count: " + videoInputDevices.length);
      if (videoInputDevices.length >= 1) {
        videoInputDevices.forEach(function(element) {
          if (camDeviceId === null) {
            camDeviceId = element.deviceId;
          }

          let option = document.createElement('option');
          option.value = element.deviceId;
          option.innerHTML = element.label;
          camSelector.appendChild(option);
        });
      }

      // Creates scanner on default camera based on saved data
      addCameraScanner(getDefaultDeviceID(camDeviceId), scanner, tableId, scannedMatches);

      // Handle drop down changes to select another camera when necessary
      document.getElementById(camTagId).addEventListener('change', function() {
        let newCamId = document.getElementById(camTagId).value;
        addCameraScanner(newCamId, scanner, tableId, scannedMatches);
        setDefaultDeviceID(newCamId);
      });
    });
  }

  //
  // Update the scanned match counter
  //
  function updateScannedMatchCount(scannedMatches) {
    scanCount = Object.keys(scannedMatches).length;
    document.getElementById("submitData").innerText = "Click to Submit Data: " + scanCount;
  }

  //
  // Clear the scanned data to reset for more scans
  //
  function clearScannedMatches(tableId, scannedMatches) {
    for (let entry in scannedMatches) {
      delete scannedMatches[entry];
    }
    document.getElementById(tableId).querySelector('tbody').innerHTML = "";
    updateScannedMatchCount(scannedMatches);
  }

  // Send scanned match data to the database
  function submitScannedMatches(tableId, scannedMatches) {
    let indexedMatches = [];
    for (const [key, value] of Object.entries(scannedMatches)) {
      indexedMatches.push(value);
    }
    if (indexedMatches.length == 0) {
      console.warn("submitScannedMatches: No scanned match entries found! - Data NOT Submitted");
      alert("No scanned match entries found! - Data NOT Submitted");
    } else {
      $.post("api/dbWriteAPI.php", {
        writeTeamMatch: JSON.stringify(indexedMatches)
      }, function(response) {
        console.log("=> writeTeamMatch: " + JSON.stringify(response));
        if (response.indexOf('success') > -1) { // A loose compare, because success word may have a newline
          clearScannedMatches(tableId, scannedMatches);
          alert("Data Successfully Submitted! Clearing Data.");
        } else {
          console.warn("submitScannedMatches: Write to DB failed! - Data NOT Submitted (is this a duplicate?)");
          alert("Write to DB failed! - Data NOT Submitted (is this a duplicate?)");
        }
      });
    }
  }

  /////////////////////////////////////////////////////////////////////////////
  //
  // Process the generated html
  //
  document.addEventListener("DOMContentLoaded", function() {

    // All successfully scanned matches
    const tableId = "qrScanTable";
    const scannedMatches = {};

    // Initialze the page
    updateScannedMatchCount(scannedMatches);

    // Attach the ZXing QR scanner/decoder to the camera and load camera choices
    const scanner = new ZXing.BrowserQRCodeReader();
    createCameraSelector("cameraSelector", scanner, tableId, scannedMatches);

    // Submit the scanned data
    document.getElementById("submitData").addEventListener('click', function() {
      submitScannedMatches(tableId, scannedMatches);
    });
  });
</script>

<script src="./external/zxing/zxing.min.js"></script>
