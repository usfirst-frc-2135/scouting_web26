<footer class="container">

</footer>

<div id="ajaxFailureAlert" class="container alert alert-danger alert-dismissible d-none" role="alert" aria-live="assertive">
  <span id="ajaxFailureMessage">A server request failed. Some information may be missing or unchanged; check the connection and retry.</span>
  <button id="dismissAjaxFailure" type="button" class="btn-close" aria-label="Dismiss request error"></button>
</div>

<!-- jquery: required by freeze-table and database access -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- freeze-table requires jquery -->
<script src="./external/freeze-table/freeze-table.js"></script>
<script src="./external/sorttable/sorttable.js"></script>

<!-- Bootstrap core JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<script>
  $(document).ajaxError(function(event, jqXHR, settings, errorThrown) {
    reportAsyncDataError(
      "A server request failed. Some information may be missing or unchanged; check the connection and retry.",
      errorThrown || jqXHR
    );
  });

  document.getElementById("dismissAjaxFailure").addEventListener("click", function() {
    document.getElementById("ajaxFailureAlert").classList.add("d-none");
  });
</script>
