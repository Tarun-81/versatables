<?php
require_once('vendor/autoload.php');
if ($_POST) {
    \Stripe\Stripe::setApiKey("sk_test_Aw5qhoJPvrGjUCdnlymAAUEL00pc4JbRYS");
  $error = '';
  $success = '';
  try {
    if (!isset($_POST['stripeToken']))
      throw new Exception("The Stripe Token was not generated correctly");
      \Stripe\Charge::create(["amount" => 1000,
                                "currency" => "usd",
                                "card" => $_POST['stripeToken'],
                                "description" => "Charge for jenny.rosen@example.com"
                               ]);
    $success = 'Your payment was successful.';
  }
  catch (Exception $e) {
    $error = $e->getMessage();
  }
}
?>
