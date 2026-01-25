<?php
$zip = new ZipArchive;
$res = $zip->open('/tmp/an_recurringpayments.zip');
if ($res === TRUE) {
  $extractPath = '/var/www/html/modules/an_recurringpayments/';
  if (!file_exists($extractPath)) {
    mkdir($extractPath, 0777, true);
  }
  $zip->extractTo($extractPath);
  $zip->close();
  echo "Extraction successful\n";
} else {
  echo "Failed to open zip. Code: $res\n";
  exit(1);
}
