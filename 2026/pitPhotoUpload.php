<?php
$title = 'Photo Upload';
require 'inc/header.php';
?>

<div class="container-fluid row-offcanvas row-offcanvas-left">
  <div id="content" class="column col-lg-12 col-md-12 col-sm-12 col-12">

    <!-- Page Title -->
    <div class="row pt-3 mb-3">
      <div class="row justify-content-md-center">
        <h2 class="col-md-6 mb-3 me-3"><?php echo $title; ?> </h2>
      </div>

      <!-- Main photo upload card -->
      <div class="card col-md-6 mx-auto">

        <div class="card-body">
          <form id="uploadForm" method="post" enctype="multipart/form-data">
            <div class="col-7 col-md-5 mb-3">
              <label for="enterTeamNumber" class="form-label">Team Number</label>
              <input id="enterTeamNumber" class="form-control" type="text" placeholder="FRC team number" name="enterTeamNumber">
            </div>
            <div id="aliasNumber" class="ms-3 mb-3 text-success"></div>

            <div class="mb-3">
              <label for="robotPic" class="form-label">Robot Photo</label>
              <input id="robotPic" class="form-control mb-3" type="file">
              <div class="row text-center">
                <output id="photoPreview"></output>
              </div>
            </div>

            <div class="d-grid gap-2 col-6 mx-auto mb-3">
              <button id="upload" class="btn btn-primary" type="button">Upload</button>
            </div>
          </form>

          <div class="text-center mt-3">
            <label for="replacePic" class="form-label">Replace Existing Photos</label>
            <input id="replacePic" class="form-check-input" type="checkbox">
          </div>

        </div>

        <button id="loadingSpinner" class="btn btn-primary mb-3" type="button" disabled>
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
          <span role="status">Loading...</span>
        </button>
        <div id="uploadMessage" class="alert alert-dismissible fade show" style="display: none" role="alert">
          <div id="uploadMessageText"></div>
          <button id="closeMessage" class="btn-close" type="button" aria-label="Photo Upload Close"></button>
        </div>

        <!-- Existing photos card -->
        <div class="card">
          <div class="card-header">
            <h3>Existing Photos</h3>
          </div>
          <div class="card-body">
            <div id="photoPrevious"></div>
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
    console.log("=> pitPhotoUpload: checkURLForTeamSpec()");
    let sp = new URLSearchParams(window.location.search);
    if (sp.has('teamNum')) {
      return sp.get('teamNum');
    }
    return "";
  }

  //
  // Display success to user
  //
  function showSuccessMessage(message) {
    console.log("==> pitPhotoUpload: showSuccessMessage()" + message);
    document.getElementById("robotPic").value = "";
    document.getElementById("enterTeamNumber").value = "";
    document.getElementById("aliasNumber").innerText = "";

    document.getElementById("uploadMessageText").innerHTML = message;
    document.getElementById("uploadMessage").classList.add("alert-success");
    document.getElementById("uploadMessage").classList.remove("alert-danger");
    document.getElementById("uploadMessage").style.display = "block";
  }

  //
  // Display error to user
  //
  function showErrorMessage(message) {
    console.log("==> pitPhotoUpload: showErrorMessage: " + message);

    document.getElementById("uploadMessageText").innerHTML = message;
    document.getElementById("uploadMessage").classList.add("alert-danger");
    document.getElementById("uploadMessage").classList.remove("alert-success");
    document.getElementById("uploadMessage").style.display = "block";
  }

  //
  // Display success to user
  //
  function uploadSuccess(msg, _textStatus, _jqXHR) {
    console.log("==> pitPhotoUpload: uploadSuccess: " + msg);
    document.getElementById("loadingSpinner").style.visibility = 'hidden';
    document.getElementById("upload").disabled = false;
    let jMsg;
    try {
      jMsg = typeof msg === "string" ? JSON.parse(msg) : msg;
    } catch (error) {
      console.error("Unable to parse photo upload response:", error);
      showErrorMessage("The server returned an invalid response. Check whether the photo uploaded before retrying.");
      return;
    }

    if (jMsg === null || typeof jMsg !== "object") {
      showErrorMessage("The server returned an invalid response. Check whether the photo uploaded before retrying.");
      return;
    }

    if (jMsg["success"]) {
      showSuccessMessage("Upload successful, clearing form!");
    } else {
      showErrorMessage(jMsg["message"] || "The photo upload failed.");
    }
  }

  function logDeletedPhoto(imageFile, _response, _textStatus, _jqXHR) {
    console.log("==> Deleted existing photo: " + imageFile);
  }

  function logPhotoDeletionFailure(imageFile, jqXHR, textStatus, errorThrown) {
    console.error("Could NOT delete existing photo: " + imageFile, textStatus, errorThrown);
  }

  function deleteTeamPhoto(imageFile, _previousResponse, _previousTextStatus, _previousJqXHR) {
    return $.ajax({
      url: "api/deleteFile.php",
      data: {
        file: "<?php echo dirname(__FILE__) . '/' ?>" + imageFile
      }
    }).done(function(response, textStatus, jqXHR) {
      logDeletedPhoto(imageFile, response, textStatus, jqXHR);
    }).fail(function(jqXHR, textStatus, errorThrown) {
      logPhotoDeletionFailure(imageFile, jqXHR, textStatus, errorThrown);
    });
  }

  function deleteExistingPhotos(teamImages, _textStatus, _jqXHR) {
    const imageFiles = typeof teamImages === "string" ? JSON.parse(teamImages) : teamImages;
    if (!Array.isArray(imageFiles)) {
      throw new Error("The existing photo list is invalid.");
    }

    let deleteRequest = $.Deferred().resolve().promise();
    for (const imageFile of imageFiles) {
      deleteRequest = deleteRequest.then(function(previousResponse, previousTextStatus, previousJqXHR) {
        return deleteTeamPhoto(imageFile, previousResponse, previousTextStatus, previousJqXHR);
      });
    }
    return deleteRequest;
  }

  function uploadReplacementPhoto(teamNum, _response, _textStatus, _jqXHR) {
    document.getElementById("replacePic").checked = false;
    uploadSelectedPhoto(teamNum);
  }

  function showPhotoUploadFailure(message, jqXHR, textStatus, errorThrown) {
    console.error(message, textStatus, errorThrown, jqXHR);
    document.getElementById("loadingSpinner").style.visibility = 'hidden';
    document.getElementById("upload").disabled = false;
    showErrorMessage(message);
  }

  function uploadSelectedPhoto(teamNum) {
    let uploadPost = new FormData();
    uploadPost.append("teamPic", document.getElementById("robotPic").files[0]);
    uploadPost.append("teamNum", teamNum);

    $.ajax({
      type: "POST",
      url: "api/dbWriteAPI.php",
      data: uploadPost,
      cache: false,
      contentType: false,
      processData: false
    }).done(uploadSuccess).fail(function(jqXHR, textStatus, errorThrown) {
      showPhotoUploadFailure(
        "Unable to upload the photo. Check the connection and try again. If replacing photos, review the existing photos before retrying.",
        jqXHR,
        textStatus,
        errorThrown
      );
    });
  }

  //
  // Upload the selected image file to the server
  //
  function handlePhotoUpload(aliasList) {
    console.log("=> pitPhotoUpload: handlePhotoUpload");
    let teamNum = document.getElementById("enterTeamNumber").value.toUpperCase().trim();
    if (document.getElementById("robotPic").value != "") {
      if (isAliasNumber(teamNum)) {
        if (!Array.isArray(aliasList)) {
          showErrorMessage("Alias lookup is still loading. Please try again.");
          return;
        }

        teamNum = getTeamNumFromAlias(teamNum, aliasList);
        if (teamNum === "") {
          showErrorMessage("The alias number is not valid.");
          return;
        }
        document.getElementById("enterTeamNumber").value = teamNum;
      }

      if (validateTeamNumber(teamNum, null) > 0) {
        document.getElementById("loadingSpinner").style.visibility = 'visible';
        document.getElementById("upload").disabled = true;

        if (document.getElementById("replacePic").checked) {
          console.log("==> Going to remove existing photos for team #" + teamNum);

          $.get("api/dbReadAPI.php", {
            getImagesForTeam: teamNum
          }).then(deleteExistingPhotos)
            .done(function(response, textStatus, jqXHR) {
              uploadReplacementPhoto(teamNum, response, textStatus, jqXHR);
            }).fail(function(jqXHR, textStatus, errorThrown) {
              showPhotoUploadFailure(
                "Could not replace existing photos. No new photo was uploaded; some existing photos may already have been removed. Check the photo list before retrying.",
                jqXHR,
                textStatus,
                errorThrown
              );
            });
        } else {
          uploadSelectedPhoto(teamNum);
        }
      } else {
        document.getElementById("loadingSpinner").style.visibility = 'hidden';
        alert("Team Number is invalid - must be integer with optional last alpha!");
      }
    } else {
      document.getElementById("loadingSpinner").style.visibility = 'hidden';
      alert("Please fill in robot photo!");
    }

    document.getElementById("closeMessage").addEventListener('click', function() {
      document.getElementById("uploadMessage").style.display = "none";
      document.getElementById("replacePic").checked = false;
      document.getElementById('photoPreview').innerHTML = ""; // clear photoPreview image
      document.getElementById('photoPrevious').innerHTML = ""; // clear previous photos
    });
  }

  //
  // Handles FileReader events when an image file has been selected
  //
  function handleFileSelect(evt) {
    let files = evt.target.files;
    let f = files[0];
    let reader = new FileReader();
    reader.onload = (
      function(theFile) {
        return function(e) {
          document.getElementById('photoPreview').innerHTML = [
            '<img src="', e.target.result, '" title="', theFile.name, '" width="300">'
          ].join('');
        };
      }
    )(f);
    reader.readAsDataURL(f);
  }

  //
  // Load existing photos for this team
  //
  function loadPreviousTeamPhotos(teamNum, requestId, requestState) {
    console.log("==> Load existing photos for team #" + teamNum);

    // First get list of robot-pic files for this team.
    $.get("api/dbReadAPI.php", {
      getImagesForTeam: teamNum
    }).done(function(teamImages) {
      if (requestId !== requestState.latestRequestId) {
        return;
      }
      console.log("=> getImagesForTeam\n" + teamImages);
      let jTeamImages = parseAjaxJson(teamImages, "team photo list", Array.isArray);

      for (let imageFile of jTeamImages) {
        document.getElementById('photoPrevious').innerHTML +=
          '<div class="text-center">File:  ' + imageFile + '</div>' +
          '<img class="mx-auto d-block mb-3" src="' + imageFile + '" title="' + teamNum + '" width="150" >';
      }
    });
  }

  /////////////////////////////////////////////////////////////////////////////
  //
  // Process the generated html
  //    When upload button is pressed
  //      If replace all images button is checked
  //      Load a list of previous images for that team
  //      Delete all previous images
  //    Post the new image to the database
  //    Wait for existing-photo deletion before uploading a replacement
  //
  document.addEventListener("DOMContentLoaded", function() {

    const loadingSpinner = document.getElementById("loadingSpinner");
    loadingSpinner.style.visibility = 'hidden';
    let jAliasNames = null;
    const photoRequestState = { latestRequestId: 0 };

    // Read the alias table
    $.get("api/dbReadAPI.php", {
      getEventAliasNames: true
    }).done(function(eventAliasNames) {
      console.log("=> eventAliasNames");
      jAliasNames = parseAjaxJson(eventAliasNames, "event aliases", Array.isArray);
    });

    // Check URL for source team to load
    let initTeamNumber = checkURLForTeamSpec().toUpperCase();
    if (initTeamNumber !== "") {
      document.getElementById("enterTeamNumber").value = initTeamNumber;
    }

    // Attach enterTeamNumber listener when losing focus to check for alias numbers
    document.getElementById('enterTeamNumber').addEventListener('focusout', function(evt) {
      handleTeamNumberFocusOut(evt, jAliasNames, photoRequestState);
    });

    // Confirm the file selction selection is supported
    if (!window.FileReader) {
      console.warn("This browser does not support 'FileReader'");
      return alert("This browser does not support 'FileReader'");
    }

    // Attach image upload processor
    document.getElementById('robotPic').addEventListener('change', handleFileSelect, false);

    // Attach image file reader selection from disk
    document.getElementById("upload").addEventListener('click', function() {
      handlePhotoUpload(jAliasNames);
    });
  });

  function handleTeamNumberFocusOut(evt, aliasList, requestState) {
    console.log("enterTeamNumber: focus out");
    const requestId = ++requestState.latestRequestId;
    document.getElementById('photoPrevious').innerHTML = "";
    let enteredNum = (evt.target.value || '').toUpperCase().trim();
    if (isAliasNumber(enteredNum)) {
      if (!Array.isArray(aliasList)) {
        document.getElementById("aliasNumber").innerText = "Alias lookup is still loading. Please try again.";
        return;
      }

      let teamNum = getTeamNumFromAlias(enteredNum, aliasList);
      if (teamNum === "")
        document.getElementById("aliasNumber").innerText = "Alias number " + enteredNum + " is NOT valid!";
      else
        document.getElementById("aliasNumber").innerText = "Alias number " + enteredNum + " is Team " + teamNum;
      document.getElementById("enterTeamNumber").value = teamNum;
    } else
      document.getElementById("aliasNumber").innerText = "";
    loadPreviousTeamPhotos(document.getElementById("enterTeamNumber").value, requestId, requestState);
  }
</script>

<script src="./scripts/aliasFunctions.js"></script>
<script src="./scripts/validateTeamNumber.js"></script>
<?php include 'inc/end.php'; ?>
