<?php
require_once('vendor/autoload.php');
\Stripe\Stripe::setApiKey("sk_test_Aw5qhoJPvrGjUCdnlymAAUEL00pc4JbRYS");
try {
$token = \Stripe\Token::create([
  'card' => [
    'number' => 4242424242424242,
    'exp_month' => 06,
    'exp_year' => 2025,
    'cvc' => 966
  ]
]);
 echo "<pre>";
 print_r($token);
  }catch (Exception $e) {
    $error = $e->getMessage();
    print_r($error);
  }

?>