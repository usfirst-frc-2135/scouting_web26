<?php
$title = 'Hopper Capacity Data';
require 'inc/header.php';
?>

<div class="container row-offcanvas row-offcanvas-left">
  <div id="content" class="column card-lg-12  col-sm-12 col-xs-12">

    <!-- Page Title -->
    <div class="row col-md-6 pt-3 mb-3">
      <h2 class="col-auto mb-3 me-3"><?php echo $title; ?> </h2>
      <a class="col-auto btn btn-primary mb-3 me-3" href="javascript:history.back()">Back</a>
    </div>

    <!-- Main row to hold the entry card -->
    <div class="row col-md-6 mb-3">
      <h5>Add New Hopper Capacity</h5>
      <div class="input-group mb-3">
        <input id="enterTeamNumber" class="form-control me-2" type="text" placeholder="Team number" aria-label="Team Number">
        <input id="enterHopperCap" class="form-control me-2" type="text" placeholder="Capacity number" aria-label="Capacity Number">
        <div class="input-group-append">
          <button id="addHopperCap" class="btn btn-primary me-2" type="button">Add Hopper Cap</button>
        </div>
      </div>
    </div>


    <!-- Main row to hold the table -->
    <div class="row col-md-6 mb-3">
      <style type="text/css" media="screen">
        thead {
          position: sticky;
          top: 56px;
          background: white;
        }
      </style>

      <table id="hopperCapTable" class="table table-striped table-bordered table-hover text-center sortable">
        <thead>
          <tr>
            <th scope="col" class="text-start sorttable_numeric">Team Number</th>
            <th scope="col" class="sorttable_numeric">Hopper Capacity</th>
            <th scope="col">Delete</th>
          </tr>
        </thead>
        <tbody class=" table-group-divider">
        </tbody>
      </table>
    </div>

  </div>
</div>

<?php include 'inc/footer.php'; ?>

<!-- Javascript page handlers -->

<script>
  //
  // Build the hopper capacity table
  //
  function loadHopperCapTable(tableId, hopperCapList) {
    console.log("==> hopperCapData: loadHopperCapTable()");
    if (hopperCapList === []) {
      // console.warn("loadHopperCapTable: hopperCapList is missing!");
      return;
    }

    // Go thru existing hopperCap table data and create a row string in HTML format, with the 
    // team number, hopper cap, and DELETE button that is inserted into the table body. 
    let tbodyRef = document.getElementById(tableId).querySelector('tbody');
    tbodyRef.innerHTML = "";
    for (let entry of hopperCapList) {
      let key = entry["teamnumber"].trim();
      let row = tbodyRef.insertRow();
      row.id = key + "_row";
      row.innerHTML = "";
      row.innerHTML += "<td class='text-start'>" + "<a href='teamLookup.php?teamNum=" + entry["teamnumber"] + "'>" + entry["teamnumber"] + "</a>" + "</td>";
      row.innerHTML += "<td>" + entry["hoppercap"] + "</td>";
      row.innerHTML += "<td> <button id='" + key + "_delete' value='" + key + "' class='btn btn-danger' type='button'>Delete</button></td>";

      // Add delete button
      document.getElementById(key + "_delete").addEventListener('click', function() {
        console.log("HopperCap Deleting " + this.value);
        deleteHopperCap(tableId, this.value);
      });
    }

    const teamColumn = 0;
    sortTableByTeam(tableId, teamColumn);
    // script instructions say this is needed, but it breaks table header sorting
    // sorttable.makeSortable(document.getElementById(tableId));
  }

  //
  // Attempt to save the team hopperCap to the hopperCap table
  //
  function addHopperCap(tableId, teamNum, hopperCap) {
    console.log("==> hopperCapData: addHopperCap()" + " " + teamNum + " " + hopperCap);
    $.post("api/dbWriteAPI.php", {
      writeSingleTeamHopperCap: JSON.stringify({
        "teamnumber": teamNum,
        "hoppercap": hopperCap
      })
    }, function(response) {
      if (response.indexOf('success') > -1) { // A loose compare, because success word may have a newline
        // alert("Success in submitting Hopper Cap data! Clearing Data.");
        document.getElementById("enterTeamNumber").value = "";
        document.getElementById("enterHopperCap").value = "";
        buildHopperCapTable(tableId);
      } else {
        alert("Failure in submitting Hopper Cap data! Please Check network connectivity.");
      }
    });
  }

  //
  // Attempt to remove the hopper cap from the hopperCap table
  //
  function deleteHopperCap(tableId, hopperCap) {
    console.log("==> starting deleteHopperCap() entry: " + hopperCap);
    $.post("api/dbWriteAPI.php", {
      deleteSingleTeamHopperCap: JSON.stringify({
        "hoppercap": hopperCap
      })
    }, function(response) {
      console.log("---==> deleteSingleTeamHopperCap response: " + response);
      if (response.indexOf('success') > -1) { // A loose compare, because success word may have a newline
        // alert("Success in submitting Hopper Cap data! Clearing Data.");
        document.getElementById("enterTeamNumber").value = "";
        document.getElementById("enterHopperCap").value = "";
        buildHopperCapTable(tableId);
      } else {
        alert("Failure in removing Hopper Cap data! Please Check network connectivity.");
      }
    });
  }

  //
  // Retrieve data and build team and hopperCap table
  //
  function buildHopperCapTable(tableId) {
    $.get("api/dbReadAPI.php", {
      getEventHopperCaps: true
    }).done(function(eventHopperCaps) {
      console.log("=> buildHopperCapTable(): done");
      let jHopperCaps = JSON.parse(eventHopperCaps);
      loadHopperCapTable(tableId, jHopperCaps);
    });
  }

  /////////////////////////////////////////////////////////////////////////////
  //
  // Process the generated html
  //    Get team list for this eventcode from TBA
  //    When all completed, generate the web page
  //
  document.addEventListener("DOMContentLoaded", function() {

    const tableId = "hopperCapTable";
    let hopperCapList = [];

    // Get the list of teams and add the team names 
    buildHopperCapTable(tableId);

    // Pressing enter in team number field attempts to save the hopperCap
    let teamInput = document.getElementById("enterTeamNumber");
    teamInput.addEventListener("keypress", function(event) {
      if (event.key === "Enter") {
        event.preventDefault();
        document.getElementById("addHopperCap").click();
      }
    });

    // Pressing enter in hopperCap number field attempts to save the hopperCap
    let hopperCapInput = document.getElementById("enterHopperCap");
    hopperCapInput.addEventListener("keypress", function(event) {
      if (event.key === "Enter") {
        event.preventDefault();
        document.getElementById("addHopperCap").click();
      }
    });

    // Save the hopper cap for the number entered
    document.getElementById("addHopperCap").addEventListener('click', function() {
      let teamNum = document.getElementById("enterTeamNumber").value.toUpperCase().trim();
      let hopperCap = document.getElementById("enterHopperCap").value.trim();
      if (validateTeamNumber(teamNum, null) > 0 && validateTeamNumber(hopperCap, null) > 0) {
        addHopperCap(tableId, teamNum, hopperCap);
      }
    });
  });
</script>

<script src="./scripts/compareMatchNumbers.js"></script>
<script src="./scripts/compareTeamNumbers.js"></script>
<script src="./scripts/sortFrcTables.js"></script>
<script src="./scripts/tableToJSON.js"></script>
<script src="./scripts/validateTeamNumber.js"></script>
