UserAuth = {};

UserAuth.onLogin = function() {
	console.log( "coming here" );
	FB.getLoginStatus(function(response) {
		if( response.status == "connected" 
			&& response.session && response.session.uid ) {
			$.ajax({
				type: "POST",
				url: "/signin/",
				data: "uid="+response.session.uid+"&format=json",
				dataType: "json",
				success: function(response) {
					if(response.url) {
						window.location.href = response.url;
						return;
					}
				}
			});
		}
	});
};

$(document).ready(function(){
	var roll_tag_lines = function() {
		//Customize tag lines container
		var hidden_tag_lines = $(".tag-line-container:hidden");
		var next_tag_line = $(".tag-line-container:visible").next();
		$(".tag-line-container:visible").fadeOut("fast", function(){
			if( next_tag_line.length ) {
				next_tag_line.fadeIn();
			} else if( hidden_tag_lines.length ) {
				$(hidden_tag_lines[0]).fadeIn();
			}		
		});
		setTimeout(roll_tag_lines, 10000);
	};
	setTimeout(roll_tag_lines, 10000);
});