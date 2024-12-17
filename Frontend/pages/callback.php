<?php
$secret_key = 'sk_test_912d5e0bd9c238a9aec4fee4cd7471445b121a0b';

$reference = $_GET['reference'];
if (!$reference) {
    die('No reference supplied');
}

$url = "https://api.paystack.co/transaction/verify/$reference";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secret_key",
    "Cache-Control: no-cache"
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($result['status']) {
    echo 'Payment was successful!';

} else {
    echo 'Payment verification failed: ' . $result['message'];
}
?>
