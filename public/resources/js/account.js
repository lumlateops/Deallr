/*
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
*/

$(document).ready(function(){
	$("#providers li").click(function(){
		$("#providers li.selected").removeClass("selected");
		$(this).addClass("selected");
		$(this).find("input").focus();
	});
});