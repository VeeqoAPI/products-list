<?php

extract(require('config.php'));

function prepareProducts($response) {
  $products = $response;

  foreach ($products as $index => $product) {
    $products[$index] = array_merge([
      'buyUrl' => '#',
      'infoUrl' => '#'
    ], $product);
  }

  return $products;
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.veeqo.com/products?page_size=25&page=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "x-api-key: $apiKey"
));

$response = curl_exec($ch);

$responseSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
$time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

$err = curl_error($ch);

curl_close($ch);

$response = json_decode($response, true);

$results = [
  'products' => [],
  'error' => false,
  'time' => $time,
  'responseSize' => $responseSize
];

if ($err) {
  $results['error'] = "cURL Error #:" . $err ;
} elseif(isset($response['error_messages'])) {
  $results['error'] = "API error: " . $response['error_messages'];
} else {
  $results['products'] = prepareProducts($response);
}

return $results;
