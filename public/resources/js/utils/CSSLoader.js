var CSSLoader = function(css_files) {
	var css_obj = '';
	$.each(css_files,function(){ 
		$("head").append("<link>");
    	css_obj = $("head").children(":last");
		css_obj.attr({
			rel:  "stylesheet",
			type: "text/css",
			href: this + ".css"
		});
	});
};