<?php
$title = 'Fuel Estimates';
require 'inc/header.php';
?>

<div class="container row-offcanvas row-offcanvas-left">
  <div id="content" class="column card-lg-12 col-sm-12 col-xs-12">

    <!-- Page Title -->
    <div class="row pt-3 mb-3">
      <h2 class="col-md-6 mb-3 me-3"><?php echo $title; ?> </h2>
      <div id="spinner" class="spinner-border ms-3 mb-3 me-3"></div>
    </div>

    <!-- Main row to hold the table -->
    <div class="row col-12 mb-3">

      <div id="freeze-table" class="freeze-table overflow-auto">
        <table id="estimatedFuelTable" class="table table-striped table-bordered table-hover table-sm border-secondary text-center">
          <thead class="z-3"> </thead>
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
  // Build the REBUILT Fuel Estimates data table
  //
  function loadFuelEstTable(tableId, aliasNames, matchTableData, pitData, tbaMatchData) 
  {
    if (aliasNames == [] || pitData == [] || matchTableData === null || tbaMatchData === null) {
      console.log("loadFuelEstTable: still waiting for get APIs to finish!");
      return;
    }   

    // All the args were obtained, so now create mdp and it will figure out all the calculations.
    console.log("==> loadFuelEstTable(): setting up mdp");
    let mdp = new matchDataProcessor(matchTableData,tbaMatchData,pitData);
    mdp.getSiteFilteredAverages(function(filteredMatchData, filteredAvgData) {
      if (filteredMatchData != undefined) {
        insertFuelEstimatesBody(tableId,filteredMatchData, filteredAvgData, aliasNames, [], pitData);
        document.getElementById('spinner').style.display = 'none';
        // script instructions say this is needed, but it breaks table header sorting
        // sorttable.makeSortable(document.getElementById(tableId));
        document.getElementById(tableId).click(); // This magic fixes the floating column bug
      } else {
        alert("loadFuelEstTable(): mdp not created!");
      }
    });
  }

  function buildFuelEstimatesTable(tableId) { 
    console.log("==> rebuiltFuelEstimates: starting buildFuelEstimatesTable()");
    let jAliasNames = null;
    let jPitData = null;
    let jMatchTableData = null;
    let tbaMatchData = null;

    // Load alias lookup table
    $.get("api/dbReadAPI.php", {
      getEventAliasNames: true
    }).done(function(eventAliasNames) {
      console.log("=> eventAliasNames");
      jAliasNames = JSON.parse(eventAliasNames);
      insertFuelEstimatesHeader(tableId, jAliasNames);
    });

    // Load the match table data
    $.get("api/dbReadAPI.php", {
      getAllMatchData: true
    }).done(function(matchTableData) {
       jMatchTableData = JSON.parse(matchTableData);
       loadFuelEstTable(tableId,jAliasNames,jMatchTableData,jPitData,tbaMatchData);
    });

    // In parallel, load the pitTable data
    $.get("api/dbReadAPI.php", {
      getAllPitData: true
    }).done(function(allPitData) {
      jPitData = JSON.parse(allPitData);
      loadFuelEstTable(tableId,jAliasNames,jMatchTableData,jPitData,tbaMatchData);
    });

    // In parallel, load the TBA matches data
    $.get("api/tbaAPI.php", {
      getEventMatches: true
    }).done(function(eventMatches) {
      tbaMatchData = JSON.parse(eventMatches)["response"];
      loadFuelEstTable(tableId,jAliasNames,jMatchTableData,jPitData,tbaMatchData);
    });
  }

  /////////////////////////////////////////////////////////////////////////////
  //
  // Process the generated html
  //    Calculate all fuel estimates data from our database
  //    When completed, display the web page
  //
  document.addEventListener("DOMContentLoaded", function() {

    const tableId = "estimatedFuelTable";
    const frozenTable = new FreezeTable('.freeze-table', {
      fixedNavbar: '.navbar'
    });

    buildFuelEstimatesTable(tableId);

    // Create frozen table panes and keep the panes updated
    document.getElementById(tableId).addEventListener('click', function() {
      if (frozenTable) {
        frozenTable.update();
      }
    });
  });
</script>

<script src="./scripts/aliasFunctions.js"></script>
<script src="./scripts/compareMatchNumbers.js"></script>
<script src="./scripts/compareTeamNumbers.js"></script>
<script src="./scripts/rebuiltFuelEstimates.js"></script>
<script src="./scripts/matchDataProcessor.js"></script>
<script src="./scripts/matchDataTable.js"></script>
<script src="./scripts/sortFrcTables.js"></script>
