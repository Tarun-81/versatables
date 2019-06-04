<?php
require_once('config.php');
  
    $idd = $_REQUEST['pdtid'];
    $recresults = $obj-> get_rows ("", "id = ".$idd , "crm_products");
    $productdetail = array();
    foreach($recresults as $key =>$rec)
    {
        $productdetail[$key] = $rec;
        
    }
    echo json_encode($productdetail);
    die();
   
     /*   //print_r($_GET['data1']);
        $suggestion_storage = $_GET['data1'];
        $alloptions = $obj-> get_rows ("", "id = ".$suggestion_storage , "crm_products") ; 
        print_r($alloptions); */
?>