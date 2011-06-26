UserAuth = {};

UserAuth.onLogin = function(response) {
	if( response.status == "connected" ) {
		alert("Need to sign in the user UID = " + response.session.uid );
	}
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