$(document).ready(function(){
	
	var email_add_button = $("#add-email-button-container a");
	var providers_form_err_obj = $("#providers-form-errors");
	$("#providers li").click(function(){
		$("#providers li.selected").removeClass("selected");
		$(this).addClass("selected");
		$(this).find("input").focus();
	});
	
	$("#providers li input").focus(function(){
		var all_inputs = $("#providers li input"), i = 0, imax = all_inputs.length;
		for( i = 0; i < imax; i++ ) {
			if( !$(all_inputs[i]).parents('li').hasClass("selected") ) {
				$(all_inputs[i]).val("");
			}
		}
	});
	
	email_add_button.click(function() {
		var email = $("#providers li.selected input[name='email']").val();
		var provider = $("#providers li.selected").attr('name');
		if( email.trim != undefined && !email.trim() ) {
			providers_form_err_obj.html("Email is mandatory");
			providers_form_err_obj.fadeIn();
		} else if( provider.trim != undefined && !provider.trim() ) {
			providers_form_err_obj.html("Provider is mandatory");
			providers_form_err_obj.fadeIn();		
		} else {
			providers_form_err_obj.hide();
			providers_form_err_obj.html("");
			$.ajax({
				type: "POST",
				url: "/account/add/",
				data: "email="+email+"&provider="+provider+"&format=json",
				dataType: "json",
				success: function(response) {
					if( response.err ) {
						providers_form_err_obj.html("This email address has already been added. Please enter a different address.");
						providers_form_err_obj.fadeIn();
					} else {					
						alert(response.provider_url);
						if(response.provider_url) {
							window.location.href = response.provider_url;
							return;
						}
					}
				}
			});
		}
	});
});