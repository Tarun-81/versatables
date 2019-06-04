<?php 
require_once 'config.php';

?>
   <script type="text/javascript" src="scripts/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="scripts/jquery.mockjax.js"></script>
    <script type="text/javascript" src="src/jquery.autocomplete.js"></script>
    <script type="text/javascript" src="scripts/countries.js"></script>
    <script type="text/javascript" src="scripts/demo.js"></script>
   
    <script src="http://code.jquery.com/jquery-migrate-1.1.0.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Versatable products</title>
    <link href="content/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Versatable products  Autocomplete</h1>

        <h2>Versatable</h2>
        
        <div style="position: relative; height: 80px;">
            <input type="text" name="country" id="autocomplete-ajax" style="position: absolute; z-index: 2; background: transparent;"/>
           <!--  <input type="text" name="country" id="autocomplete-ajax-x" disabled="disabled" style="color: #CCC; position: absolute; background: transparent; z-index: 1;"/> -->
        </div>
        <div id="selction-ajax"></div>
        </div>
     
            <div class="container">
        
        <!-- Modal -->
        <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Select options</h4>
            </div>
            <div class="modal-body ">
            
            
               <?php 
               //$suggestion_storage = "<script>document.write($('#selction-ajax').attr('selectedvalue'));</script>";
               
               ?>
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-default" >Add Item</button>
            </div>
            </div>
            
        </div>
        </div>

        </div>
      
        <script type="text/javascript">
        	var pdctarray = <?php echo json_encode($maparray); ?>;
    
			/*$("#myModal").on('shown.bs.modal', function() { 
				//$('#myModal').modal('show');
				var pdtid = $('#selction-ajax').attr("selectedvalue");

				    $.ajax({
						url: 'ajax.php',
						type: 'post',
						data: {pdtid: pdtid},
					done: function(response){ 
						console.log(response);
						alert("asdasd");
					// Add response in Modal body
					$('.modal-body').html(response);

					// Display Modal
					$('#myModal').modal('show'); 
					}
					});
					alert("run");
					var postData = {
					action:'test',
					pdtid:pdtid
					}
					$.ajax({
					  method:"POST",
					  url:"./ajax.php",
					  data:postData
					}).done(function( msg ) {
						alert("k");
						//var check = msg['check'];
					});
			});*/
    
    </script>
          
   
</body>
</html>
