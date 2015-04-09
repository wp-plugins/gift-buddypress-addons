jQuery(document).ready(function($) {
	
	$(".form_submit").on("click", function() {
		return confirm("Are you Sure? Do you want to send Gift?");
	});
	
	$('#SendGiftForm').validate({
	
		invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();

			if (errors) {
				$("#error-message").show().text("You missed " + errors + " field(s)");
			} else {
				$("#error-message").hide();
			}
		},
		rules: {
			post_id: {
				required: true,
			},
			
			reciever: {
				required: true,
				
			},
		},
		
		messages: {
			post_id: "Please Choose Gift.",
			reciever: "Please select gift reciever.",
			
		}
	});
});