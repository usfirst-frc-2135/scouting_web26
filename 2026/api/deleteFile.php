<?php
$file = $_GET['file'] ?? null;
if (!is_string($file) || !is_file($file) || !unlink($file))
{
  http_response_code(500);
  echo "Unable to delete photo.";
  exit;
}

echo "success";
?>
