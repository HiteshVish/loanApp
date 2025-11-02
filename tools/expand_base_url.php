<?php
$in = __DIR__ . '/../POSTMAN_COLLECTION_CLEAN.json';
$out = __DIR__ . '/../POSTMAN_COLLECTION_NO_VARS.json';
$data = json_decode(file_get_contents($in), true);
// Remove variables
unset($data['variable']);
// Walk and replace raw urls
function walk_replace(&$arr) {
    foreach ($arr as $k => &$v) {
        if (is_array($v)) {
            walk_replace($v);
        } elseif ($k === 'raw' && is_string($v)) {
            $v = str_replace('{{base_url}}', 'http://localhost:8000/api', $v);
        }
    }
}
walk_replace($data);
file_put_contents($out, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Wrote: $out\n";