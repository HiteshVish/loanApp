<?php
$data = json_decode(file_get_contents(__DIR__ . '/../POSTMAN_COLLECTION.json'), true);

function cleanItem(&$item) {
    if (isset($item['request']['url']) && is_array($item['request']['url'])) {
        $url = $item['request']['url'];
        $new = [];
        if (isset($url['raw'])) $new['raw'] = $url['raw'];
        if (isset($url['query'])) $new['query'] = $url['query'];
        $item['request']['url'] = $new;
    }
    if (isset($item['item']) && is_array($item['item'])) {
        foreach ($item['item'] as &$sub) {
            cleanItem($sub);
        }
        unset($sub);
    }
}

if (isset($data['item']) && is_array($data['item'])) {
    foreach ($data['item'] as &$it) {
        cleanItem($it);
    }
    unset($it);
}

$file = __DIR__ . '/../POSTMAN_COLLECTION_CLEAN.json';
file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Wrote cleaned collection to: $file\n";
