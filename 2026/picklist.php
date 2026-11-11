<?php
$title = 'Picklist';
require 'inc/header.php';
?>

<div class="container-fluid">
  <style>
    #averagesTable tbody td:first-child {
      cursor: grab;
      touch-action: none;
    }

    #averagesTable tbody tr.sortable-chosen td:first-child {
      cursor: grabbing;
    }
  </style>

  <div class="row pt-3 mb-3 align-items-start">
    <h2 class="col-auto text-nowrap mb-3 me-3"><?php echo $title; ?></h2>
    <div class="col-auto mb-3">
      <div id="customMatch" class="accordion accordion-flush">
        <div class="accordion-item bg-secondary-subtle">
          <h2 class="accordion-header">
            <button class="accordion-button text-nowrap text-light bg-secondary" type="button" data-bs-toggle="collapse"
              data-bs-target="#filterEntry" aria-expanded="false" aria-controls="filterEntry">Match Range Filter</button>
          </h2>
          <div id="filterEntry" class="accordion-collapse collapse" data-bs-parent="#customMatch">
            <div class="input-group pt-3">
              <select id="startCompLevel" class="form-select w-auto flex-grow-0 ms-2 mb-3" aria-label="Start competition level">
                <option value="p">P</option>
                <option value="qm" selected>QM</option>
                <option value="sf">SF</option>
                <option value="f">F</option>
              </select>
              <input id="startMatchNum" class="form-control me-2 mb-3" type="text" placeholder="Start"
                aria-label="Start match number">
            </div>
            <div class="input-group">
              <select id="endCompLevel" class="form-select w-auto flex-grow-0 ms-2 mb-3" aria-label="End competition level">
                <option value="p">P</option>
                <option value="qm" selected>QM</option>
                <option value="sf">SF</option>
                <option value="f">F</option>
              </select>
              <input id="endMatchNum" class="form-control me-2 mb-3" type="text" placeholder="End"
                aria-label="End match number">
            </div>
            <button id="filterData" class="btn btn-primary btn-sm ms-2 mb-3" type="button" disabled>Filter Data</button>
          </div>
        </div>
      </div>
    </div>
    <div id="spinner" class="col-auto spinner-border ms-3 mb-3" role="status" aria-label="Loading event averages"></div>
    <p class="col-12 text-body-secondary">Drag a team number to set a temporary row order. Your order is kept when you apply a match-range filter.</p>
    <div id="loadError" class="col-12 alert alert-danger d-none" role="alert"></div>
  </div>

  <div class="row mb-3">
    <div class="overflow-auto">
      <table id="averagesTable" class="table table-striped table-bordered table-hover table-sm border-secondary text-center">
        <thead class="z-3"></thead>
        <tbody class="table-group-divider"></tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'inc/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"
  integrity="sha384-HZZ/fukV+9G8gwTNjN7zQDG0Sp7MsZy5DDN6VfY3Be7V9dvQpEpR2jF2HlyFUUjU" crossorigin="anonymous"></script>
<script src="./scripts/aliasFunctions.js"></script>
<script src="./scripts/compareMatchNumbers.js"></script>
<script src="./scripts/compareTeamNumbers.js"></script>
<script src="./scripts/eventAveragesTable.js"></script>
<script src="./scripts/matchDataProcessor.js"></script>
<script src="./scripts/rebuiltFuelEstimates.js"></script>
<script src="./scripts/sortFrcTables.js"></script>
<script>
  function parseJsonResponse(response)
  {
    return (typeof response === "string") ? JSON.parse(response) : response;
  }

  function getTeamOrder(tableId)
  {
    const tbody = document.getElementById(tableId).querySelector("tbody");
    return Array.from(tbody.rows, row => row.cells[0].textContent.trim());
  }

  function restoreTeamOrder(tableId, teamOrder)
  {
    const tbody = document.getElementById(tableId).querySelector("tbody");
    const rowsByTeam = new Map(Array.from(tbody.rows, row => [row.cells[0].textContent.trim(), row]));
    const orderedRows = [];

    for (const teamNum of teamOrder)
    {
      if (rowsByTeam.has(teamNum))
      {
        orderedRows.push(rowsByTeam.get(teamNum));
        rowsByTeam.delete(teamNum);
      }
    }

    orderedRows.push(...rowsByTeam.values());
    tbody.replaceChildren(...orderedRows);
  }

  function showLoadError(message)
  {
    const error = document.getElementById("loadError");
    error.textContent = message;
    error.classList.remove("d-none");
    document.getElementById("spinner").classList.add("d-none");
  }

  document.addEventListener("DOMContentLoaded", function()
  {
    const tableId = "averagesTable";
    const tbody = document.getElementById(tableId).querySelector("tbody");
    const filterButton = document.getElementById("filterData");
    let eventData;
    let latestRender = 0;

    new Sortable(tbody, {
      animation: 150,
      delay: 150,
      delayOnTouchOnly: true,
      handle: "td:first-child",
      touchStartThreshold: 5
    });

    Promise.all([
      $.get("api/dbReadAPI.php", { getEventAliasNames: true }).then(parseJsonResponse),
      $.get("api/tbaAPI.php", { getCOPRs: true }).then(parseJsonResponse),
      $.get("api/dbReadAPI.php", { getAllMatchData: true }).then(parseJsonResponse),
      $.get("api/dbReadAPI.php", { getAllPitData: true }).then(parseJsonResponse),
      $.get("api/dbReadAPI.php", { getEventHopperCaps: true }).then(parseJsonResponse),
      $.get("api/tbaAPI.php", { getEventMatches: true }).then(parseJsonResponse)
    ]).then(function([aliases, coprResponse, matchData, pitData, hopperCaps, eventMatches])
    {
      if (!coprResponse || !coprResponse.data || !eventMatches || !eventMatches.response)
      {
        throw new Error("The TBA event averages data is unavailable. Check the TBA configuration and try again.");
      }

      eventData = {
        aliases: aliases,
        coprs: coprResponse.data,
        matches: matchData,
        pit: pitData,
        hopperCaps: hopperCaps,
        tbaMatches: eventMatches.response
      };

      insertEventAveragesHeader(tableId, eventData.aliases);
      filterButton.disabled = false;
      renderAverages(null, null);
    }).catch(function(error)
    {
      console.error("Unable to load event averages:", error);
      showLoadError(error.message || "Unable to load event averages. Check the database and TBA connections, then reload.");
    });

    function renderAverages(startMatch, endMatch)
    {
      const renderId = ++latestRender;
      const previousOrder = getTeamOrder(tableId);
      document.getElementById("loadError").classList.add("d-none");
      document.getElementById("spinner").classList.remove("d-none");

      const processor = new matchDataProcessor(
        eventData.matches,
        eventData.tbaMatches,
        eventData.pit,
        eventData.hopperCaps
      );

      if (startMatch !== null && endMatch !== null)
      {
        processor.filterMatchRange(startMatch, endMatch);
      }

      processor.getSiteFilteredAverages(function(filteredMatchData, filteredAverages)
      {
        if (renderId !== latestRender)
        {
          return;
        }

        insertEventAveragesBody(tableId, filteredAverages, eventData.coprs, eventData.aliases, eventData.pit, [], false);
        restoreTeamOrder(tableId, previousOrder);
        document.getElementById("spinner").classList.add("d-none");
      }).fail(function(error)
      {
        if (renderId !== latestRender)
        {
          return;
        }

        console.error("Unable to load event match filters:", error);
        showLoadError("Unable to load the event match filters from the database. Check the connection and try again.");
      });
    }

    filterButton.addEventListener("click", function()
    {
      const startNumber = document.getElementById("startMatchNum").value.trim();
      const endNumber = document.getElementById("endMatchNum").value.trim();

      if (Boolean(startNumber) !== Boolean(endNumber))
      {
        latestRender++;
        showLoadError("Enter both a start and end match number to filter the table.");
        return;
      }

      const startMatch = startNumber
        ? document.getElementById("startCompLevel").value + startNumber
        : null;
      const endMatch = endNumber
        ? document.getElementById("endCompLevel").value + endNumber
        : null;

      renderAverages(startMatch, endMatch);
    });
  });
</script>
<?php include 'inc/end.php'; ?>
