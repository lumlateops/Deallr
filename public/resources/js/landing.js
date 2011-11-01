$(document).ready(function(){	
	$("#fb-login").click(function(){
		window.location.href = '/signup/index/btoken/' + $("#beta-invite-input").val();
	});
});