<?php
$title = 'Team Lookup';
require 'inc/header.php';
?>

<div class="container row-offcanvas row-offcanvas-left">
  <div id="content" class="column card-lg-12 col-sm-12 col-xs-12">

    <!-- Page Title -->
    <div class="row pt-3 mb-3">
      <h2 class="col-md-6 mb-3 me-3"><?php echo $title; ?> </h2>
    </div>

    <!-- Main row to hold the team lookup form -->
    <div class="row col-md-6 mb-3">
      <div class="input-group mb-3">
        <input id="enterTeamNumber" class="form-control" type="text" placeholder="FRC team number" aria-label="Team Number">
        <div class="input-group-append">
          <button id="loadTeamButton" class="btn btn-primary" type="button">Load Team</button>
        </div>
      </div>
    </div>
    <div id="aliasNumber" class="ms-3 mb-3 text-success"></div>

    <!-- First column of data starts here -->
    <div class="row">
      <div class="col-lg-6 col-sm-6 col-xs-6 gx-3">
        <div class="card mb-3">
          <div class="card-body">
            <h5 id="teamTitle" class="card-title">Team # </h5>

            <!-- Robot photo carousel section -->
            <div id="robotPicsCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
              <div id="robotPics" class="carousel-inner">

              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#robotPicsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#robotPicsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>

            <!-- Auton collapsible graph -->
            <div class="card mb-3 bg-success-subtle">
              <div class="card-header">
                <h5 class="text-center">
                  <a href="#collapseAutonCoralGraph" data-bs-toggle="collapse" aria-expanded="false">Auton Scoring</a>
                </h5>
              </div>
              <div id="collapseAutonCoralGraph" class="card-body collapse">
                <canvas id="autoChart" width="400" height="360"></canvas>
              </div>
            </div>

            <!-- Teleop collapsible graph -->
            <div class="card mb-3 bg-primary-subtle">
              <div class="card-header">
                <h5 class="text-center">
                  <a href="#collapseTeleopCoralGraph" data-bs-toggle="collapse" aria-expanded="false">Teleop Scoring</a>
                </h5>
              </div>
              <div id="collapseTeleopCoralGraph" class="card-body collapse">
                <canvas id="teleopChart" width="400" height="360"></canvas>
              </div>
            </div>

            <!-- Endgame collapsible graph -->
            <div class="card mb-3 bg-warning-subtle">
              <div class="card-header">
                <h5 class="text-center">
                  <a href="#collapseEndgameGraph" data-bs-toggle="collapse" aria-expanded="false">Endgame Scoring</a>
                </h5>
              </div>
              <div id="collapseEndgameGraph" class="card-body collapse">
                <canvas id="endgameChart" width="400" height="360"></canvas>
              </div>
            </div>

          </div>
        </div>

      </div>

      <!-- Second Column of Data starts here -->
      <div class="col-lg-6 col-sm-6 col-xs-6 gx-3">
        <div class="card mb-3">
          <div class="card-body">

            <!-- Match Total Points section -->
            <div class="card mb-3">
              <div class="card-header">
                <h5 class="text-center">Match Totals</h5>
              </div>
              <div class="card-body">
                <table id="matchSheetTable"
                  class="table table-striped table-bordered table-hover table-sm border-secondary text-center ">
                  <thead>
                    <tr>
                      <th scope="col" class="text-start">Totals</th>
                      <th scope="col">AVG</th>
                      <th scope="col">MAX</th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                    <tr>
                      <th scope="row" class="text-start">Match Points</th>
                      <td> </td>
                      <td> </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Auton Points section -->
            <div class="card mb-3 bg-success-subtle">
              <div class="card-header">
                <h5 class="text-center"><a href="#collapseAuton" data-bs-toggle="collapse" aria-expanded="false">Auton</a></h5>
              </div>
              <div id="collapseAuton" class="card-body collapse">
                <table id="autonTable" class="table table-striped table-bordered table-hover table-sm border-secondary text-center">
                  <thead>
                    <tr>
                      <th scope="col" class="text-start"></th>
                      <th scope="col">AVG</th>
                      <th scope="col">MAX</th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                    <tr>
                      <th scope="row" class="text-start">Auton Points</th>
                      <td> </td>
                      <td> </td>
                    </tr>
                    <tr>
                      <th scope="row" class="text-start">Fuel Est</th>
                      <td> </td>
                      <td> </td>
                    </tr>
                  </tbody>
                  <table id="autonClimbTable"
                  class="table table-striped table-bordered table-hover table-sm border-secondary text-center ">
                  <thead>
                    <tr>
                      <th scope="col" class="text-start"></th>
                      <th scope="col" style="width:12%">NA</th>
                      <th scope="col" style="width:12%">B</th>
                      <th scope="col" style="width:12%">L</th>
                      <th scope="col" style="width:12%">F</th>
                      <th scope="col" style="width:12%">R</th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                    <tr>
                      <th scope="row" class="text-start">Climb %</th>
                      <td> </td>
                      <td> </td>
                      <td> </td>
                      <td> </td>
                      <td> </td>
                    </tr>
                  </tbody>
                </table>
                </table>
              </div>
            </div>

            <!-- Teleop Points section -->
            <div class="card mb-3 bg-primary-subtle">
              <div class="card-header">
                <h5 class="text-center"> <a href="#collapseTeleop" data-bs-toggle="collapse" aria-expanded="false">Teleop </a>
                </h5>
              </div>
              <div id="collapseTeleop" class="card-body collapse">
                <table id="teleopTable"
                  class="table table-striped table-bordered table-hover table-sm border-secondary text-center ">
                  <thead>
                    <tr>
                      <th scope="col" class="text-start"></th>
                      <th scope="col">AVG</th>
                      <th scope="col">MAX</th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                    <tr>
                      <th scope="row" class="text-start">Fuel Est</th>
                      <td> </td>
                      <td> </td>
                    </tr>
                    <tr>
                      <th scope="row" class="text-start">Defense</th>
                      <td> </td>
                      <td> </td>
                    </tr>
                  </tbody>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Endgame Points section -->
            <div class="card mb-3 bg-warning-subtle">
              <div class="card-header">
                <h5 class="text-center"> <a href="#collapseEndgame" data-bs-toggle="collapse" aria-expanded="false">Endgame
                  </a>
                </h5>
              </div>
              <div id="collapseEndgame" class="card-body collapse">
                <table id="endgameTotalPtsTable"
                  class="table table-striped table-bordered table-hover table-sm border-secondary text-center ">
                  <thead>
                    <tr>
                      <th scope="col" class="text-start"></th>
                      <th scope="col">AVG</th>
                      <th scope="col">MAX</th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                    <tr>
                      <th scope="row" class="text-start">Endgame Points</th>
                      <td> </td>
                      <td> </td>
                    </tr>
                  </tbody>
                </table>
                <table id="endgameClimbTable"
                  class="table table-striped table-bordered table-hover table-sm border-secondary text-center ">
                  <thead>
                    <tr>
                      <th scope="col" class="text-start"></th>
                      <th scope="col" style="width:12%">NA</th>
                      <th scope="col" style="width:12%">L1</th>
                      <th scope="col" style="width:12%">L2</th>
                      <th scope="col" style="width:12%">L3</th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                    <tr>
                      <th scope="row" class="text-start">Climb %</th>
                      <td> </td>
                      <td> </td>
                      <td> </td>
                      <td> </td>
                      <td> </td>
                    </tr>
                  </tbody>
                </table>
                <table id="endgameStartClimbTable"
                  class="table table-striped table-bordered table-hover table-sm border-secondary text-center ">
                  <thead>
                    <tr>
                      <th scope="col" class="text-start"></th>
                      <th scope="col" style="width:12%">NA</th>
                      <th scope="col" style="width:12%">B4</th>
                      <th scope="col" style="width:12%">Bell</th>
                      <th scope="col" style="width:12%">10s</th>
                      <th scope="col" style="width:12%">lt10s</th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                    <tr>
                      <th scope="row" class="text-start">Start Climb %</th>
                      <td> </td>
                      <td> </td>
                      <td> </td>
                      <td> </td>
                    </tr>
                  </tbody>
                </table>
                <table id="endgameClimbPosTable"
                  class="table table-striped table-bordered table-hover table-sm border-secondary text-center ">
                  <thead>
                    <tr>
                      <th scope="col" class="text-start"></th>
                      <th scope="col" style="width:12%">NA</th>
                      <th scope="col" style="width:12%">B</th>
                      <th scope="col" style="width:12%">L</th>
                      <th scope="col" style="width:12%">F</th>
                      <th scope="col" style="width:12%">R</th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                    <tr>
                      <th scope="row" class="text-start">Climb Pos %</th>
                      <td> </td>
                      <td> </td>
                      <td> </td>
                      <td> </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Strategic Data collapsible table -->
        <div class="card mb-3">
          <div class="card-header">
            <h5 class="text-center">
              <a href="#collapseStrategicData" data-bs-toggle="collapse" aria-expanded="false">Strategic Scouting</a>
            </h5>
          </div>
          <div id="collapseStrategicData" class="card-body collapse">

            <!-- <div id="freeze-table-strat" class="freeze-table overflow-auto"> -->
            <div class="overflow-auto">
              <table id="strategicDataTable"
                class="table table-striped table-bordered table-hover table-sm border-secondary text-center">
                <thead> </thead>
                <tbody class="table-group-divider"> </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Pit Data collapsible table -->
        <div class="card mb-3">
          <div class="card-header">
            <h5 class="text-center">
              <a href="#collapsePitData" data-bs-toggle="collapse" aria-expanded="false">Pit Scouting</a>
            </h5>
          </div>
          <div id="collapsePitData" class="card-body collapse">

            <!-- <div id="freeze-table-strat" class="freeze-table overflow-auto"> -->
            <div class="overflow-auto">
              <table id="pitDataTable"
                class="table table-striped table-bordered table-hover table-sm border-secondary text-center">
                <thead> </thead>
                <tbody class="table-group-divider"> </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Match scouting data collapsible table -->
        <div class="card mb-3">
          <div class="card-header">
            <h5 class="text-center">
              <a href="#collapseAllMatches" data-bs-toggle="collapse" aria-expanded="false">Match Scouting</a>
            </h5>
          </div>
          <div id="collapseAllMatches" class="card-body collapse">

            <!-- <div id="freeze-table-match" class="freeze-table overflow-auto"> -->
            <div class="overflow-auto">
              <table id="matchDataTable"
                class="table table-striped table-bordered table-hover table-sm border-secondary text-center">
                <thead class="z-3"> </thead>
                <tbody class="table-group-divider"> </tbody>
              </table>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php include 'inc/footer.php'; ?>

<!-- Javascript page handlers -->


<script>
  let autoChart;
  let teleopChart;
  let endgameChart;

  //
  // Round data to no more than two decimal digits
  //
  function roundTwoPlaces(val) {
    return Math.round((val + Number.EPSILON) * 100) / 100;
  }

  //
  ///// AUTON GRAPH STARTS HERE /////
  //
  function loadAutonGraph(teamNum, matchData, avgsData) {
    console.log("==> teamLookup: loadAutonGraph()");

    // Set up the datasets for this graph.
    let datasets = []; // Each entry is a dict with a label and data attribute
    datasets.push({
      label: "Climb",
      data: [],
      backgroundColor: '#DE8E2C'
    }); // Yellow
    datasets.push({
      label: "Fuel Est",
      data: [],
      backgroundColor: '#2779F5'
    }); // Blue 
    let mydata = [];

    // Note: matchData contains scouted data for all teams, so find each match for this team.
    for (let i=0; i<matchData.length; i++) {
      let matchItem = matchData[i];
      let matchnum = matchItem["matchnumber"];
      let teamnumber = matchItem["teamnumber"];
      if(teamnumber == teamNum) {
//        console.log("    ==> teamLookup: match "+matchnum+": found team "+teamNum);

        // Get the auton fuel estimate for this team / match from the avgsData
        let autonFuelEst = 0; // default
        let pDataTeamItem = avgsData[teamNum];
        if(pDataTeamItem !== undefined) {
          if(pDataTeamItem["fuelD"][matchnum]["autonFE"] !== undefined) {
            autonFuelEst = pDataTeamItem["fuelD"][matchnum]["autonFE"];
//            console.log("        ==> basic autonFE = "+autonFuelEst);
          }
          if(pDataTeamItem["fuelD"][matchnum]["tbaAutonFE"] !== undefined) {
            autonFuelEst = pDataTeamItem["fuelD"][matchnum]["tbaAutonFE"];
//            console.log("        ==> tba autonFE = "+autonFuelEst);
          }
        }

        let autonClimb = 0;
        if (matchItem["autonClimb"] == 0) {
          autonClimb = 0;
        }; 
        if (matchItem["autonClimb"] == 1 || matchItem["autonClimb"] == 2 || matchItem["autonClimb"] == 3 || matchItem["autonClimb"] == 4) {
          autonClimb = 15;   
        };

        mydata.push({
          matchnum: matchnum,
          fuel: autonFuelEst,
          climb: autonClimb,
        });
      }
    }  // done with matchData for loop

    mydata.sort(function(rowA, rowB) {
      return (compareMatchNumbers(rowA["matchnum"], rowB["matchnum"]));
    });

    // Build data sets; go thru each mydata row and populate the graph datasets.
    let matchList = []; // List of matches to use as x labels
    let autonFuelTips = []; // holds custom tooltips for auton fuel estimate dat
    let autonClimbTips = []; // holds custom tooltips for auton climb data      

    for (let i = 0; i < mydata.length; i++) {
      let matchnum = mydata[i]["matchnum"];
      matchList.push(matchnum);

      function storeAndGetTip(value, tipPrefix, dataset, yesNo) {
        dataset.push(value);
        if (yesNo) {
          value = (value) ? "Yes" : "No";
        }
        return tipPrefix + value;
      }

      autonClimbTips.push({
        xlabel: matchnum,
        tip: storeAndGetTip(mydata[i]["climb"], "Climb=", datasets[0]["data"], true)
      });
      autonFuelTips.push({
        xlabel: matchnum,
        tip: storeAndGetTip(mydata[i]["fuel"], "Fuel Est=", datasets[1]["data"], false)
      });
    }

    // Define the graph as a line chart:
    if (autoChart !== undefined) {
      autoChart.destroy();
    }

    // Create the Auton graph
    const ctx = document.getElementById('autoChart').getContext('2d');
    autoChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: matchList,
        datasets: datasets
      },
      options: {
        scales: {
          x: {
            stacked: true
          },
          y: {
            stacked: true,
            min: 0,
            ticks: {
              precision: 0
            },
            max:160
          } 
        },
        plugins: 
        {
          tooltip: {
            callbacks: 
            { // Special tooltip handling
              label: function(tooltipItem, ddata) {

                function getTip(matchno, tipList) {
                  for (let i = 0; i < tipList.length; i++)
                    if (tipList[i].xlabel === matchno)
                      return tipList[i].tip;
                }

                let matchnum = tooltipItem.label;
                let tipStr = datasets[tooltipItem.datasetIndex].label;
                switch (tooltipItem.datasetIndex) {
                  case 0:
                    return getTip(matchnum, autonClimbTips);
                  case 1:
                    return getTip(matchnum, autonFuelTips);
                  default:
                    return "missing tip string!"
                }
                return tipStr;
              }
            }
          }
        }
      }
    });
  } 
  ///// AUTON GRAPH ENDS HERE /////

  //
  ///// TELEOP GRAPH STARTS HERE /////
  //
  function loadTeleopGraph(teamNum, matchData, avgsData) {
    console.log("==> teamLookup: loadTeleopGraph()");

    // Declare variables
    let datasets = []; // Each entry is a dict with a label and data attribute

    datasets.push({
      label: "Defense",
      data: [],
      backgroundColor: '#097513'
    }); // Green
    datasets.push({
      label: "Fuel Est",
      data: [],
      backgroundColor: '#2779F5'
    }); // Blue

    // Go thru each matchData QR code string and build up a table of the data for this team, 
    // so we can later sort it so the matches are listed in the right order. 
    let mydata = [];
    for (let i = 0; i < matchData.length; i++) {
      let matchItem = matchData[i];
      let matchnum = matchItem["matchnumber"];
      let teamnumber = matchItem["teamnumber"];
      if(teamnumber == teamNum) {
//        console.log("    ==> loadTeleopGraph: match "+matchnum+": found team "+teamNum);

        // Get the teleop fuel estimate for this team / match from the avgsData
        let teleopFuelEst = 0; // default
        let pDataTeamItem = avgsData[teamNum];
        if(pDataTeamItem !== undefined) {
          if(pDataTeamItem["fuelD"][matchnum]["teleopFE"] !== undefined) {
            teleopFuelEst = pDataTeamItem["fuelD"][matchnum]["teleopFE"];
//            console.log("        ==> basic teleopFE = "+teleopFuelEst);
          }
          if(pDataTeamItem["fuelD"][matchnum]["tbaTeleopFE"] !== undefined) {
            teleopFuelEst = pDataTeamItem["fuelD"][matchnum]["tbaTeleopFE"];
//            console.log("        ==> tba teleopFE = "+teleopFuelEst);
          }
        }
 
        // Note: defense level is 0-5, so multiply by 10 to get a value that will show on the graph.
        // IF THIS MULT value changes from 10, you must update the toDefenseLevel() function.
        mydata.push({
          matchnum: matchItem["matchnumber"],
          defense: matchItem["teleopDefenseLevel"] * 10, 
          fuel: teleopFuelEst,
        });
      }
    }

    mydata.sort(function(rowA, rowB) {
      return (compareMatchNumbers(rowA["matchnum"], rowB["matchnum"]));
    });

    // Build data sets; go thru each mydata row and populate the graph datasets.
    let matchList = [];          // List of matches to use as x lables
    let teleopDefenseTips = [];  // holds custom tooltips for teleop defense level
    let teleopFuelTips = [];     //holds custom tooltips for teleop fuel estimate

    for (let i = 0; i < mydata.length; i++) {
      let mNum = mydata[i]["matchnum"];
      matchList.push(mNum);

      function storeAndGetTip(value, tipPrefix, dataset, defLevel) {
        dataset.push(value);
        if (defLevel) {
          value = toDefenseLevel(value);
        }
        return tipPrefix + value;
      }

      teleopDefenseTips.push({
        xlabel: mNum,
        tip: storeAndGetTip(mydata[i]["defense"], "Defense=", datasets[0]["data"], true)
      });
      teleopFuelTips.push({
        xlabel: mNum,
        tip: storeAndGetTip(mydata[i]["fuel"], "Fuel=", datasets[1]["data"],false)
      });
    }

    // Define the graph as a line chart:
    if (teleopChart !== undefined) {
      teleopChart.destroy();
    }

    // Create the Teleop graph
    const ctx = document.getElementById('teleopChart').getContext('2d');
    teleopChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: matchList,
        datasets: datasets
      },
      options: {
        scales: {
          x: {
            stacked: true
          },
          y: {
            stacked: true,
            min: 0,
            ticks: {
              precision: 0
            },
            max: 200
          } // Set Y axis maximum value - 16 coral + algae in teleop
        },
        plugins: {
          tooltip: {
            callbacks: { // Special tooltip handling
              label: function(tooltipItem, ddata) {

                function getTip(matchno, tipList) {
                  for (let i = 0; i < tipList.length; i++)
                    if (tipList[i].xlabel === matchno)
                      return tipList[i].tip;
                }

                let xNum = tooltipItem.label;
                let tipStr = datasets[tooltipItem.datasetIndex].label;
                switch (tooltipItem.datasetIndex) {
                  case 0:
                    return getTip(xNum, teleopDefenseTips);
                  case 1:
                    return getTip(xNum, teleopFuelTips);
                  default:
                    return "missing tip string!"
                }
                return tipStr;
              }
            }
          }
        }
      }
    });
  }
  ///// TELEOP GRAPH ENDS HERE /////

  //
  ///// ENDGAME GRAPH STARTS HERE /////
  //
  function loadEndgameGraph(teamNum,matchData) {
    console.log("==> teamLookup: loadEndgameGraph()");

    // Retrieve the data for each match
    let datasets = [];

    datasets.push({
      label: "Climb Level",
      data: [],
      backgroundColor: '#2CA9DE'
    }); 

    // Go thru each matchData QR code string and build up a table of the data, so we can
    // later sort it so the matches are listed in the right order. 
    let mydata = [];
    for (let i = 0; i < matchData.length; i++) {
      let matchItem = matchData[i];
      let matchnum = matchItem["matchnumber"];
      let teamnumber = matchItem["teamnumber"];
      if(teamnumber == teamNum) {
//        console.log("    ==> loadEndgameGraph: match "+matchnum+": found team "+teamNum);
        mydata.push({
          matchnum: matchItem["matchnumber"],
          climblevel: matchItem["endgameClimbLevel"],
        });
      }
    }

    mydata.sort(function(rowA, rowB) {
      return (compareMatchNumbers(rowA["matchnum"], rowB["matchnum"]));
    });


    // Build data sets; go thru each mydata row and populate the graph datasets.
    let matchList = [];
    let climbLevelTips = [];

    for (let i = 0; i < mydata.length; i++) {
      let matchnum = mydata[i]["matchnum"];
      matchList.push(matchnum);

      value = {
        0: "N/A",
        1: "L1",
        2: "L2",
        3: "L3"
      };

      // Get endgame climb level
      let endgameClimbLevel = mydata[i]["climblevel"];
      datasets[0]["data"].push(endgameClimbLevel);
      climbLevelTips.push({
        xlabel: matchnum,
        tip: "Climb Level =" + value[endgameClimbLevel]
      });
    }

    if (endgameChart !== undefined) {
      endgameChart.destroy();
    }

    // Create the Endgame graph
    const ctx = document.getElementById('endgameChart').getContext('2d');
    endgameChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: matchList,
        datasets: datasets
      },
      options: {
        scales: {
          x: {
            stacked: true
          },
          y: {
            stacked: true,
            min: 0,
            ticks: {
              precision: 0
            },
            max: 4
          } // Set Y axis maximum value - deep climb
        },
        plugins: {
          tooltip: {
            callbacks: { // Special tooltip handling
              label: function(tooltipItem, ddata) {

                function getTip(matchno, tipList) {
                  for (let i = 0; i < tipList.length; i++)
                    if (tipList[i].xlabel === matchno)
                      return tipList[i].tip;
                }

                let matchnum = tooltipItem.label;
                let tipStr = datasets[tooltipItem.datasetIndex].label;
                switch (tooltipItem.datasetIndex) {
                  case 0:
                    return getTip(matchnum, climbLevelTips);
                  default:
                    return "missing tip string!"
                }
                return tipStr;
              }
            }
          }
        }
      }
    });
  }

  ///// ENDGAME GRAPH END HERE /////

  //
  // Create an html table row with tr and td cells
  //
  function writeAverageTableRow(tableID, values, length) {
    let tbodyRef = document.getElementById(tableID).querySelector('tbody');
    let row = "<th  class='text-start'>" + values[0] + "</th>";
    for (let i = 1; i < length; i++) {
      row += (i < values.length) ? "<td>" + values[i] + "</td>" : "<td> </td>";
    }
    tbodyRef.insertRow().innerHTML = row;
  }

  //
  // Generate all of the table data and fill them
  //
  function loadAverageTables(avgs) {
    console.log("==> teamLookup: loadAverageTables()");

    /////// Match Totals Table  
    writeAverageTableRow("matchSheetTable", ["Total Match Points", avgs["totalMatchPoints"].avg, avgs["totalMatchPoints"].max], 3);

    //Auton Table  
    writeAverageTableRow("autonTable", ["Auton Points", avgs["autonTotalPoints"].avg, avgs["autonTotalPoints"].max], 3);
    writeAverageTableRow("autonTable", ["Fuel Est", avgs["autonFinalFuelEst"].avg, avgs["autonFinalFuelEst"].max], 3);
    writeAverageTableRow("autonClimbTable", ["Climb %", avgs["autonClimb"].arr[0].avg, avgs["autonClimb"].arr[1].avg, avgs["autonClimb"].arr[2].avg, avgs["autonClimb"].arr[3].avg, avgs["autonClimb"].arr[4].avg], 6);

    // Teleop Table
    writeAverageTableRow("teleopTable", ["Fuel Est", avgs["teleopTotalPoints"].avg, avgs["teleopTotalPoints"].max], 3);
    writeAverageTableRow("teleopTable", ["Defense", avgs["teleopDefenseLevel"].avg, avgs["teleopDefenseLevel"].max], 3);

    /////// Endgame Table
    writeAverageTableRow("endgameTotalPtsTable", ["Endgame Points", avgs["endgamePoints"].avg, avgs["endgamePoints"].max], 3);
    writeAverageTableRow("endgameClimbTable", ["Climb %", avgs["endgameClimbLevel"].arr[0].avg, avgs["endgameClimbLevel"].arr[1].avg, avgs["endgameClimbLevel"].arr[2].avg, avgs["endgameClimbLevel"].arr[3].avg], 5);
    writeAverageTableRow("endgameStartClimbTable", ["Start Climb %", avgs["endgameStartClimb"].arr[0].avg, avgs["endgameStartClimb"].arr[1].avg, avgs["endgameStartClimb"].arr[2].avg, avgs["endgameStartClimb"].arr[3].avg, avgs["endgameStartClimb"].arr[4].avg], 6);
    writeAverageTableRow("endgameClimbPosTable", ["Climb Pos %", avgs["endgameClimbPosition"].arr[0].avg, avgs["endgameClimbPosition"].arr[1].avg, avgs["endgameClimbPosition"].arr[2].avg, avgs["endgameClimbPosition"].arr[3].avg, avgs["endgameClimbPosition"].arr[4].avg], 6);
  }

  // MAIN PAGE PROCESSORS HERE

  //
  // Check if our URL directs to a specific team
  //
  function checkURLForTeamSpec() {
    console.log("=> teamLookup: checkURLForTeamSpec()");
    let sp = new URLSearchParams(window.location.search);
    if (sp.has('teamNum')) {
      return sp.get('teamNum');
    }
    return "";
  }

  //
  // Takes list of Team photo paths and loads them.
  //
  function loadTeamPhotos(teamPhotos) {
    console.log("==> teamLookup: loadTeamPhotos()");
    let first = true;
    for (let uri of teamPhotos) {
      let tags = "<div class='carousel-item";
      if (first) {
        tags += " active";
      }
      first = false;
      tags += "'> <img src='./" + uri + "' class='d-block w-100'> </div>";
      document.getElementById("robotPics").innerHTML += tags;
    }
  }

  // Converts a given defenseLevel value to approprate word.
  function toDefenseLevel(value) {
    switch (String(value)) {
      case "10":
        return "Low";
      case "20":
        return "Med Low";
      case "30":
        return "Medium";
      case "40":
        return "Med High";
      case "50":
        return "High";
      default:
        return "-";
    }
  }

  // Converts a given "1" to yes, "0" to no, anything else to empty string.
  function toYesNo(value) {
    switch (String(value)) {
      case "1":
        return "Yes";
      case "2":
        return "No";
      default:
        return "-";
    }
  }

  //
  // Load the pit data table for this team

  //
  // Load the match data table
  //
  function loadMatchData(team, matchData, aliasList, pitData, tbaMatchData, hopperCapData) {
    console.log("==> teamLookup: loadMatchData()");
    let mdp = new matchDataProcessor(matchData, tbaMatchData, pitData, hopperCapData);
    mdp.getSiteFilteredAverages(function(filteredMatches, filteredAvgData) {
      if (filteredMatches != undefined && filteredAvgData != undefined) {
        console.log("   ==> loadMatchData: got MDP");
        loadAutonGraph(team, filteredMatches, filteredAvgData);
        loadTeleopGraph(team, filteredMatches, filteredAvgData);
        loadEndgameGraph(team, filteredMatches);
        insertMatchDataBody("matchDataTable", filteredMatches, aliasList, [team]);

        let teamAverages = filteredAvgData[team];
        if (teamAverages !== undefined) {
          loadAverageTables(teamAverages);
        } else {
          alert("No averages data for this team at this event!");
        }
      }
    });
  }

  //
  // Clear existing data
  //
  function clearTeamLookupPage() {
    console.log("==> teamLookup: clearTeamLookupPage()");
    document.getElementById("aliasNumber").innerText = "";
    document.getElementById("teamTitle").innerText = "";
    document.getElementById("robotPics").innerText = "";
    document.getElementById("matchSheetTable").querySelector('tbody').innerHTML = "";
    document.getElementById("autonTable").querySelector('tbody').innerHTML = "";
    document.getElementById("autonClimbTable").querySelector('tbody').innerHTML = "";
    document.getElementById("teleopTable").querySelector('tbody').innerHTML = "";
    document.getElementById("endgameTotalPtsTable").querySelector('tbody').innerHTML = "";
    document.getElementById("endgameClimbTable").querySelector('tbody').innerHTML = "";
    document.getElementById("endgameStartClimbTable").querySelector('tbody').innerHTML = "";
    document.getElementById("endgameClimbPosTable").querySelector('tbody').innerHTML = "";
    document.getElementById("pitDataTable").querySelector('tbody').innerHTML = "";
    document.getElementById("strategicDataTable").querySelector('tbody').innerHTML = "";
    document.getElementById("matchDataTable").querySelector('tbody').innerHTML = "";
  }

  ///////////////////////////////////////////////////////////////////
  // Main function that runs when we want to load a team.
  //    teamName will be set to the alias for BCD teamnums
  function buildTeamLookupPage(teamNum, aliasList) {
    console.log("==> teamLookup: buildTeamLookupPage() teamNum " + teamNum);
    clearTeamLookupPage();

    // Get alias numbers if they exist for this team number
    let evtTeam = teamNum;
    if (aliasList !== null) {
      let aliasNum = getAliasFromTeamNum(teamNum, aliasList);
      if (aliasNum !== "")
        evtTeam = getAliasFromTeamNum(teamNum, aliasList);
    }

    // Retrieve team info to get team names
    $.get("api/tbaAPI.php", {
      getTeamInfo: evtTeam
    }).done(function(teamInfo) {
//      console.log("=> getTeamInfo:\n" + teamInfo);
      if (teamInfo === null) {
        return alert("Can't load teamName from TBA; check if TBA Key was set in db_config");
      }
      // Form the team string with number and name
      let teamStr = teamNum + " - " + JSON.parse(teamInfo)["response"]["nickname"];
      if (isAliasNumber(evtTeam)) {
        teamStr = teamNum + " - " + evtTeam;
      }
      document.getElementById("teamTitle").innerText = teamStr;
    });

    // Add images for the team
    $.get("api/dbReadAPI.php", {
      getImagesForTeam: teamNum
    }).done(function(teamImages) {
      console.log("=> getImagesForTeam:\n" + teamImages);
      loadTeamPhotos(JSON.parse(teamImages));
    });

    // Add Match Scouting Data and pit data
    // Going to need all the match data and all the pit data (to calc all the other teams fuel est)
    $.get("api/dbReadAPI.php", {
      getAllMatchData: true
    }).done(function(allMatches) {
        let matchData = JSON.parse(allMatches);
//        console.log("=> got all Match data");
        $.get("api/dbReadAPI.php", {
          getAllPitData: true
        }).done(function(allPitData) {
          let pitData = JSON.parse(allPitData);
          $.get("api/tbaAPI.php", {
            getEventMatches: true
          }).done(function(eventMatches) {
            tbaMatchData = JSON.parse(eventMatches)["response"];
//            console.log("=> got TBA event matches");
            $.get("api/dbReadAPI.php", {
              getEventHopperCaps: true
            }).done(function(allHopperCaps) {
              hopperCapData = JSON.parse(allHopperCaps);
//              console.log("=> got Hopper Cap data ");
              loadMatchData(teamNum, matchData, aliasList, pitData, tbaMatchData, hopperCapData);
          });
        });
      });
    });

    // Do the Strategic Data Table.
    $.get("api/dbReadAPI.php", {
      getTeamStrategicData: teamNum
    }).done(function(teamStratData) {
//      console.log("=> getTeamStrategicData");
      insertStrategicDataBody("strategicDataTable", JSON.parse(teamStratData), aliasList, [teamNum]);
    });

    // Do the Pit Data Table.
    $.get("api/dbReadAPI.php", {
      getAllPitData: teamNum
    }).done(function(teamPitData) {
//      console.log("=> getTeamPitData");
      console.log("==> teamPitData: "+ teamPitData);
      insertPitTableBody("pitDataTable", JSON.parse(teamPitData));
    });
  }


  
  //
  // Autocorrects alias number in team number entry field
  //
  function validateEnteredTeamNumber(event, aliasList) {
//    console.log("enterTeamNumber: focus out");
    let enteredNum = event.target.value.toUpperCase().trim();
    if (isAliasNumber(enteredNum) && aliasList !== null) {
      let teamNum = getTeamNumFromAlias(enteredNum, aliasList);
      if (teamNum === "")
        document.getElementById("aliasNumber").innerText = "Alias number " + enteredNum + " is NOT valid!";
      else
        document.getElementById("aliasNumber").innerText = "Alias number " + enteredNum + " is Team " + teamNum;
      document.getElementById("enterTeamNumber").value = teamNum;
    } else
      document.getElementById("aliasNumber").innerText = "";
  }

  /////////////////////////////////////////////////////////////////////////////
  //
  // Process the generated html
  //    When the team lookup page load button is pressed
  //      In parallel, start retrieving each of these for the selected team:
  //        - Team info (name) from TBA
  //        - Images of the robot from database
  //        - Match scouting data from database
  //        - Pit scouting data from database
  //        - Strategic scouting data from database
  //
  document.addEventListener("DOMContentLoaded", function() {
    let jAliasNames = null;

    // Read the alias table
    $.get("api/dbReadAPI.php", {
      getEventAliasNames: true
    }).done(function(eventAliasNames) {
      jAliasNames = JSON.parse(eventAliasNames);
      insertStrategicDataHeader("strategicDataTable", jAliasNames);
      insertPitTableHeader("pitDataTable", jAliasNames);
      insertMatchDataHeader("matchDataTable", jAliasNames);

      // Check URL for team# to use (we may have gotten here by clicking on a team number link from another page)
      // Note: for aliases: this could only be the BCDnum, never the 99#.
      let urlTeamNum = checkURLForTeamSpec().toUpperCase();
      if (validateTeamNumber(urlTeamNum, null) > 0) {
        console.log("urlTeamNum = " + urlTeamNum);
        document.getElementById("enterTeamNumber").value = urlTeamNum;
        buildTeamLookupPage(urlTeamNum, jAliasNames);
      }
    });

    // Attach enterTeamNumber listener so that pressing enter in team number field loads the page
    document.getElementById("enterTeamNumber").addEventListener("keypress", function(event) {
      if (event.key === "Enter") {
        validateEnteredTeamNumber(event, jAliasNames);
        event.preventDefault();
        document.getElementById("loadTeamButton").click();
      }
    });

    // Attach enterTeamNumber listener when losing focus to check for alias numbers
    document.getElementById('enterTeamNumber').addEventListener('focusout', function() {
//      console.log("enterTeamNumber: focus out");
      validateEnteredTeamNumber(event, jAliasNames);
    });

    // Load team data for the number entered
    document.getElementById("loadTeamButton").addEventListener('click', function() {
      let teamNum = document.getElementById("enterTeamNumber").value.toUpperCase().trim();
      if (validateTeamNumber(teamNum, null) > 0 && jAliasNames !== null) {
        buildTeamLookupPage(teamNum, jAliasNames);
      }
    });
  });
</script>

<script src="./scripts/aliasFunctions.js"></script>
<script src="./scripts/compareMatchNumbers.js"></script>
<script src="./scripts/compareTeamNumbers.js"></script>
<script src="./scripts/sortFrcTables.js"></script>
<script src="./scripts/matchDataProcessor.js"></script>
<script src="./scripts/matchDataTable.js"></script>
<script src="./scripts/strategicDataTable.js"></script>
<script src="./scripts/validateTeamNumber.js"></script>
<script src="./scripts/rebuiltFuelEstimates.js"></script>
<script src="./scripts/pitTable.js"></script>

<script src="./external/charts/chart.umd.js"></script>

