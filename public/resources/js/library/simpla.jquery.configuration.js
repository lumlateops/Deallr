$(document).ready(function(){
	
	//Sidebar Accordion Menu:
		
		$("#main-filter li ul").hide(); // Hide all sub menus
		$("#main-filter li a.current").parent().find("ul").slideToggle("fast"); // Slide down the current menu item's sub menu
		
		$("#main-filter li a.filter-group").click( // When a top menu item is clicked...
			function () {
				$(this).next().slideToggle("fast"); // Slide down the clicked sub menu
				$(this).toggleClass("current");
				$(this).find(".expanded").toggleClass("hidden");
				$(this).find(".collapsed").toggleClass("hidden");
				$(this).prev().toggleClass("filter-hidden");
				return false;
			}
		);

    //Minimize Content Box
		
		$(".closed-box .content-box-content").hide(); // Hide the content of the header if it has the class "closed"
		$(".closed-box .content-box-tabs").hide(); // Hide the tabs in the header if it has the class "closed"

    // Content box tabs:
		
		$('.content-box .content-box-content div.tab-content').hide(); // Hide the content divs
		$('ul.content-box-tabs li a.default-tab').addClass('current'); // Add the class "current" to the default tab
		$('.content-box-content div.default-tab').show(); // Show the div with class "default-tab"
		
		$('.content-box ul.content-box-tabs li a').click( // When a tab is clicked...
			function() { 
				$(this).parent().siblings().find("a").removeClass('current'); // Remove "current" class from all tabs
				$(this).addClass('current'); // Add class "current" to clicked tab
				var currentTab = $(this).attr('href'); // Set variable "currentTab" to the value of href of clicked tab
				$(currentTab).siblings().hide(); // Hide all content divs
				$(currentTab).show(); // Show the content div with the id equal to the id of clicked tab
				return false; 
			}
		);

    //Close button:
		
		$(".close").click(
			function () {
				$(this).parent().fadeTo(400, 0, function () { // Links with the class "close" will close parent
					$(this).slideUp(400);
				});
				return false;
			}
		);

    // Initialise Facebox Modal window:
		
	//$('a[rel*=modal]').facebox(); // Applies modal window to any link with attribute rel="modal"
});