<?php
$title = 'Pit Data';
require 'inc/header.php';
?>

<div class="container row-offcanvas row-offcanvas-left">
  <div id="content" class="column card-lg-12 col-sm-12 col-xs-12">

    <!-- Page Title -->
    <div class="row pt-3 mb-3">
      <h2 class="col-md-6 mb-3 me-3"><?php echo $title; ?> </h2>
    </div>

    <!-- Main row to hold the strategic table -->
    <div class=" row col-12 mb-3">

      <div id="freeze-table" class="freeze-table overflow-auto">
        <table id="pitDataTable" class="table table-striped table-bordered table-hover table-sm border-secondary text-center">
          <thead> </thead>
          <tbody class="table-group-divider"> </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php include 'inc/footer.php'; ?>

<!-- Javascript page handlers -->

<script>
  //
  // Insert the pit data table header
  //
   function loadPitTableBody(tableId, pitData) {
    console.log("=> loadPitTableBody");
    if (pitData === null)
      return;

    insertPitDataBody(tableId, pitData);
    // script instructions say this is needed, but it breaks table header sorting
    // sorttable.makeSortable(document.getElementById(tableId));
    document.getElementById(tableId).click(); // This magic fixes the floating column bug
  }

  //
  // Retrive strategic scouting data and load the table
  //
  function buildPitDataTable(tableId) {
    console.log("==> pitData: buildPitDataTable()");
    let jPitData = null;

    // Load the pit data
    $.get("api/dbReadAPI.php", {
      getAllPitData: true
    }).done(function(pitData) {
      console.log("=> getAllPitData");
      jPitData = JSON.parse(pitData);
      loadPitTableBody(tableId, jPitData);
    });
  }

  /////////////////////////////////////////////////////////////////////////////
  //
  // Process the generated html
  //    Get all pit data from our database
  //    When completed, display the web page
  //
  document.addEventListener("DOMContentLoaded", function() {
    console.log("==> pitData: DOMContentLoaded");

    const tableId = "pitDataTable";
    const frozenTable = new FreezeTable('.freeze-table', {
      fixedNavbar: '.navbar'
    });

    buildPitDataTable(tableId);

    // Create frozen table panes and keep the panes updated
    document.getElementById(tableId).addEventListener('click', function() {
      if (frozenTable) {
        frozenTable.update();
      }
    });
  });

  document.addEventListener("DOMContentLoaded", function() {

    const tableId = "pitDataTable";
    const frozenTable = new FreezeTable('.freeze-table', {
      fixedNavbar: '.navbar'
    });

    // Create frozen table panes and keep the panes updated
    document.getElementById(tableId).addEventListener('click', function() {
      if (frozenTable) {
        frozenTable.update();
      }
    });
  });
</script>

<script src="./scripts/compareMatchNumbers.js"></script>
<script src="./scripts/compareTeamNumbers.js"></script>
<script src="./scripts/pitTable.js"></script>
<!-- <script src="./scripts/pitDataTable.js"></script> -->
<script src="./scripts/sortFrcTables.js"></script>
