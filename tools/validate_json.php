<?php
$s = file_get_contents(__DIR__ . '/../POSTMAN_COLLECTION.json');
json_decode($s);
$error = json_last_error_msg();
if ($error === 'No error') {
    echo "OK\n";
} else {
    echo "JSON_ERROR: " . $error . "\n";
}
