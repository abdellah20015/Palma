'use strict'; 
(function($, Drupal) {

   $(document).ready(function() {

   		$(".form-item-amount").append("<div id='errmsg' style='color: red;'></div>");
   		$( "#errmsg" ).insertAfter( "#edit-amount" );
   		$("#edit-amount").keypress(function(e) {
		    //if the letter is not digit then display error and don't type anything
		    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
		        //display error message
		        $("#errmsg").html("Ce champs n'accepte que des chiffres").show(2000);
		        return false;
		    }else{
        		// $("#errmsg").html("Ce champ accepté que des numéros").fadeOut(2000);
          	}
   		});
   		
   		$("#edit-submit").click(function(){
   			if ($.trim($("#edit-amount").val().length) ==0) {
				$("#errmsg").html("Ce champ est requis").show();
				setTimeout(function(){
					$("#errmsg").fadeOut(500);
				}, 7000);
   				return false;
   			}
   		});
  });

})(jQuery, Drupal);