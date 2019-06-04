/*jslint  browser: true, white: true, plusplus: true */
/*global $, countries */

$(function () {
    'use strict';
    var countriesArray = $.map(pdctarray, function (value, key) { return { value: value, data: key }; });
    
    // Initialize ajax autocomplete:
    $('#autocomplete-ajax').autocomplete({
        // serviceUrl: '/autosuggest/service/url',
        lookup: countriesArray,
        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
            return re.test(suggestion.value);
        },
        onSelect: function(suggestion) {
            $('#selction-ajax').attr("selectedvalue",suggestion.data);                    
			var postData = {
			action:'test',
			pdtid:suggestion.data
			}
			$.ajax({
			  method:"POST",
              url:"./ajax.php",
              dataType: 'json',
			  data:postData
			}).done(function( response ) {
                var data = response[0];
                console.log(data);
                var html = '<div>';
                if( typeof data.Frame_Color != 'undefined' || data.Frame_Color != null )
                {
                    if(data.Frame_Color !="")
                    {
                      html += '<div class="framelable">Frame Color</div>'; 
                    }
                    var fcolor = data.Frame_Color;
                    if (fcolor.indexOf(',') != -1) {
                        var segments = fcolor.split(',');
                        $.each(segments, function( index, value ) {
                            html +='<div class="fullform"><input type="radio" name="fcolor" value="'+value+'"><lable class="leftlable">' + value  + '</lable></div>'; 
                          });                     
                        
                    }
                    else{
                        html +='<div class="fullform"><input type="radio" name="fcolor" value="'+fcolor+'"></div>'; 
                    }
                   
                    
                }
                if( typeof data.Color != 'undefined' || (data.Color && data.Color.length) )
                {
                    if(data.Color!=""){
                        html += '<div class="framelable">Color</div>';
                    }
                    
                    var Color = data.Color;
                    if (Color.indexOf(',') != -1) {
                        var segments = Color.split(',');
                        $.each(segments, function( index, value ) {
                            html +='<div class="fullform"><input type="radio" name="Color" value="'+value+'"><lable class="leftlable">' + value  + '</lable></div>'; 
                          });                     
                        
                    }
                    else{
                        html +='<div class="fullform"><input type="radio" name="Color" value="'+Color+'"></div>'; 
                    }
                    
                   
                }
                if( typeof data.Control_Switch != 'undefined' || data.Control_Switch != null )
                {
                    if(data.Control_Switch !="")
                    {
                        html += '<div class="framelable">Control Switch</div>';
                    }
                    
                    var Control_Switch = data.Control_Switch;
                    if (Control_Switch.indexOf(',') != -1) {
                        var segments = Control_Switch.split(',');
                        $.each(segments, function( index, value ) {
                            html +='<div class="fullform"><input type="radio" name="Control_Switch" value="'+value+'"><lable class="leftlable">' + value + '</lable></div>'; 
                          });                     
                        
                    }
                    else{
                        html +='<div class="fullform"><input type="radio" name="Control_Switch" value="'+Control_Switch+'"></div>';
                    }
                    
                   
                }
                if( typeof data.Depth != 'undefined' || data.Depth != null )
                {
                    if(data.Depth !="")
                    {
                       html += '<div class="framelable">Depth</div>';
                    }
                    var Depth = data.Depth;
                    if (Depth.indexOf(',') != -1) {
                        var segments = Depth.split(',');
                        $.each(segments, function( index, value ) {
                            html +='<div class="fullform"><input type="radio" name="Depth" value="'+value+'"><lable class="leftlable">' + value  + '</lable></div>'; 
                          });                     
                        
                    }
                    else{
                        html +='<div class="fullform"><input type="radio" name="Depth" value="'+Depth+'"></div>';
                    }
                   
                   
                }
                if( typeof data.Width != 'undefined' || data.Width != null )
                {
                    if(data.Width !="")
                    {
                      html += '<div class="framelable">Width</div>';
                    }
                    var Width = data.Width;
                    if (Width.indexOf(',') != -1) {
                        var segments = Width.split(',');
                        $.each(segments, function( index, value ) {
                            html +='<div class="fullform"><input type="radio" name="Width" value="'+value+'"><lable class="leftlable">' + value  + '</lable></div>'; 
                          });                     
                        
                    }
                    else{
                        html +='<div class="fullform"><input type="radio" name="Width" value="'+Width+'"></div>';
                    }
                   
                }
                if( typeof data.Select_Users != 'undefined' || data.Select_Users != null )
                {
                    if(data.Select_Users !="")
                    {
                     html += '<div class="framelable">Select Users</div>';
                    }
                    var Select_Users = data.Select_Users;
                    if (Select_Users.indexOf(',') != -1) {
                        var segments = Select_Users.split(',');
                        $.each(segments, function( index, value ) {
                            html +='<div class="fullform"><input type="radio" name="Select_Users" value="'+value+'"><lable class="leftlable">' + value  + '</lable></div>'; 
                          });                     
                        
                    }
					
                    else{
                        html +='<div class="fullform"><input type="radio" name="Select_Users" value="'+Select_Users+'"></div>';
                    }
                    
                   
                }
                if( typeof data.Surface_Color != 'undefined' || data.Surface_Color != null )
                {
                    if(data.Surface_Color !="")
                    {
                      html += '<div class="framelable">Surface Color</div>';
                    }
                    var Surface_Color = data.Surface_Color;
                   
                    if (Surface_Color.indexOf(',') != -1) {
                        var segments = Surface_Color.split(',');
                        $.each(segments, function( index, value ) {
                            html +='<div class="fullform"><input type="radio" name="Surface_Color" value="'+value+'"><lable class="leftlable">' + value  + '</lable></div>'; 
                          });                     
                        
                    }
                    else{
                        html +='<div class="fullform"><input type="radio" name="Surface_Color" value="'+Surface_Color+'"></div>';
                    }
                    
                } 
                var pdtName = data.Product_Name;
                html +='<div class="fullform"><input type="hidden" name="pname" value="'+pdtName+'"></div>';
                html+='</div>';
                $('#myModal').modal('show');
                $("#myModal").find('.modal-body').html(html);
                              
                
              
			});
            
        },       
        onInvalidateSelection: function() {
            $('#selction-ajax').removeAttr("selectedvalue");
              
        },select: function(event, ui) {
            $(this).val("");
            return false;
        }
    });
    $(document).ready(function() {
       // alert( $.cookie("example") );
      $("#myModal").modal('hide');
      
      
    });
    $("#myModal").on("hidden.bs.modal", function () {
        $('#autocomplete-ajax').autocomplete('close').val('');
        
    });
    $('#add_item').on('click',function (event) {
        var selected_product = $("input[name='pname']").val();
        
        $('.selected_product').append('<p>'+selected_product+'</p>');
        $("#myModal").modal('hide');
      });


});