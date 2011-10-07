Deals = function() {
	var _deals = {};
	var _current_page = 1;
	var _max_pages = 1;
	var _current_sort = '';
	var _deals_container = $("#deals-container");
	var _deals_sort_selector = $("#deals-sort-select-container select");
	var _load_more_deals_container = $("#load-more-deals");
	var _loading_container = $("#deals-loading");
	var _publishers_filter_container = $("#publisher-filter");
	var _categories_filter_container = $("#category-filter");
	var _discount_slider_container = $( "#discount-per-slider" );
	var _discount_slider_values_container = $( "#discount-slider-values" );
	var _filter_overlay_container = $('#deals-filter-overlay');
	var _no_filter_container = $("#filter-no-results");
	var _publishers_list = [];
	var _uniq_category_names = [];
	var _uniq_publishers_names = [];
	var _publisher_counts = [];
	var _categories_list = [];
	var _categories_count = [];
	var _discount_range_min = 100;
	var _discount_range_max = 0;
	var _is_wallet = false;
	var _filter_timeout_obj = '';
	var FILTER_INTERVAL = 400;
	var DEAL_ID_PREFIX = 'deal-';	
	var DEALS_TEMPLATE = '\
		<div class="deal-container" id="'+DEAL_ID_PREFIX+'{deal_id}">\
			<div class="deal-logo">\
				<img src="{deal_publisher_logo}" alt="{deal_publisher} Logo" />\
			</div>\
			<h3><a class="deal-title" name="{deal_id}" title="Click to see the original email">{deal_title}</a></h3>\
			<div class="deal-content">\
				<div class="add-to-wallet"></div>\
				<div class="add-to-wallet-button-container"><a class="button white add-to-wallet-button" name="{deal_id}" title="Add">Add</a></div>\
				<div class="deal-details">{deal_details}</div>\
				<div class="deal-tags">{deal_tags}</div>\
				<div class="deal-facebook-like">\
					<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fdev.deallr.com%2Fdeals%2F{deal_id}&amp;send=false&amp;layout=standard&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=lucida+grande&amp;height=80" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:30px;" allowTransparency="true"></iframe>\
				</div>\
				<div class="deal-shipping" style="display:{deal_free_shipping};">FREE SHIPPING</div>\
				<div class="deal-post-date">Posted: {deal_post_date}</div>\
			</div>\
		</div>\
	';

	var SORT_PARAM_ID_PREFIX = 'sort-param-';
	var PUBLISHER_ID_PREFIX = 'publisher-';
	var PUBLISHER_TEMPLATE = '<li><input type="checkbox" checked="checked" name="deal-publisher" id="'+PUBLISHER_ID_PREFIX+'{id}" /><span class="filter-name" title="{name}">{name}</span> ({count})</li>';
	var CATEGORY_ID_PREFIX = 'category-';
	var CATEGORY_TEMPLATE = '<li><input type="checkbox" checked="checked" name="deal-category" id="'+CATEGORY_ID_PREFIX+'{id}" /><span class="filter-name" title="{name}">{name}</span> ({count})</li>';
	
	this.init = function(initial_deals, is_wallet) {
		_is_wallet = is_wallet;
		_loadDeals(initial_deals);
		_load_more_deals_container.click(function() {
			_no_filter_container.hide();
			_getMoreDeals(true);
		});
		_deals_sort_selector.change(_organizeDeals);
		_buildFilters();
		if( !_is_wallet ) {
			_initWalletHandlers();
		}
		_initDealEmailTriggerHandler();
	};
	
	var _initDealEmailTriggerHandler = function() {
		$('.deal-title').live('click', function(){
			$.colorbox({
				iframe:true, 
				scrolling: false,
				innerWidth:800, 
				innerHeight:800, 
				href:"http://dev.deallr.com/deals/details/deal_id/" + $(this).attr('name')
			});
		});
	};
	
	var _initWalletHandlers = function() {
		// Add to Wallet
		$('.add-to-wallet-button').live('click', function(){
			$(this).html('Adding..').attr('disabled', 'disabled').addClass('wallet-adding-in-progress').removeClass('add-to-wallet-button');
			$.ajax({
				type: "POST",
				url: "/wallet/add",
				data: "format=json&deal_id=" + $(this).attr('name'),
				dataType: "json",
				success: function(response) {
					if(response.status) {
						$(this).removeClass('wallet-adding-in-progress').addClass('remove-from-wallet-button').removeAttr('disabled').html('Remove').attr('title','Remove deal from Wallet');
						$(this).parents('.deal-content').addClass('deal-in-wallet');
					} else {
						$(this).removeClass('wallet-adding-in-progress').addClass('add-to-wallet-button').removeAttr('disabled').html('Add').attr('title','Add deal to Wallet');
						$(this).parents('.deal-content').removeClass('deal-in-wallet');
					}
				}.bind(this)
			});
		});

		// Remove from Wallet
		$('.remove-from-wallet-button').live('click', function(){
			$(this).html('Removing..').attr('disabled', 'disabled').addClass('wallet-removing-in-progress').removeClass('remove-from-wallet-button');
			$.ajax({
				type: "POST",
				url: "/wallet/remove",
				data: "format=json&deal_id=" + $(this).attr('name'),
				dataType: "json",
				success: function(response) {
					if(response.status) {
						$(this).removeClass('wallet-removing-in-progress').addClass('add-to-wallet-button').removeAttr('disabled').html('Add').attr('title','Add deal to Wallet');
						$(this).parents('.deal-content').removeClass('deal-in-wallet');
					} else {
						$(this).removeClass('wallet-removing-in-progress').addClass('remove-from-wallet-button').removeAttr('disabled').html('Remove').attr('title','Remove deal from Wallet');
						$(this).parents('.deal-content').addClass('deal-in-wallet');
					}
				}.bind(this)
			});
		});
	};
	
	var _displayDeals = function(deals){
		$.each(deals, function(key, value) {
			
			if( !_deals[value.deal_id] ) {
				_displayDeal(value);
				_populateFilterValues(value);
				_deals[value.deal_id] = value;
			}
		});
		
		_loading_container.hide();
		_showDeals();
	};
	
	var _loadDeals = function(deals_obj) {
		var tdeals = {};
		if( tdeals ) {
			tdeals = deals_obj.deals;
			_current_page = parseInt(deals_obj.current_page, 10);
			_max_pages = parseInt(deals_obj.max_pages, 10);
			_current_sort = deals_obj.current_sort;
		}
		_showHideLoadMore();
		_displayDeals(tdeals);
	};
	
	var _organizeDeals = function() {
		var sort_param_value = _deals_sort_selector[0].value;
		sort_param_value = sort_param_value.replace(SORT_PARAM_ID_PREFIX, '');
		_current_sort = sort_param_value;
		_current_page = 1;
		_deals = {};
		_publishers_list = [];
		_uniq_category_names = [];
		_uniq_publishers_names = [];
		_publisher_counts = [];
		_categories_list = [];
		_categories_count = [];		
		_loading_container.show();
		_load_more_deals_container.hide();
		$("#deals-container .deal-container").remove();
		_getMoreDeals(false);
	};
	
	var _getMoreDeals = function(increment_current_page) {
		
		if (increment_current_page && ((_current_page + 1) > _max_pages)) {
			_load_more_deals_container.hide();
			return;
		}
				
		$.ajax({
			type: "GET",
			url: (_is_wallet ? "/wallet" : "/deals") + "/index/page/" + (_current_page + (increment_current_page ? 1 : 0)) + "/sort/" + _current_sort,
			data: "format=json",
			dataType: "json",
			success: function(response) {
				_loadDeals(response.user_deals);
				_rebuildFilters();
				$("#deals-container .deal-container:hidden").show();
			}
		});
	};
	
	var _showHideLoadMore = function() {
		if (_max_pages > _current_page) {
			_load_more_deals_container.show();
		} else {
			_load_more_deals_container.hide();
		}
	};
	
	var _showDeals = function() {
		//$("#deals-container .deal-container:hidden").fadeIn();
		//setTimeout(_showDeals, 400);
	};
	
	var _displayDeal = function( deal_obj ) {
		var deal_html = DeallrUtil.replaceTokens(DEALS_TEMPLATE, deal_obj);
		_deals_container.append( deal_html );
		if(!_is_wallet && deal_obj.deal_in_wallet === 1) {
			var deal_container = $('#' + DEAL_ID_PREFIX + deal_obj.deal_id);
			deal_container.children('.deal-content').addClass('deal-in-wallet');
			deal_container.find('.add-to-wallet-button')
						  .html('Remove')
						  .addClass('remove-from-wallet-button')
						  .removeClass('add-to-wallet-button')
						  .attr('title','Remove deal from Wallet');
		}
	};
	
	var _populateFilterValues = function( deal_obj ) {
		var i = 0, imax = 0;
		
		if( _uniq_publishers_names.indexOf( deal_obj.deal_publisher ) === -1 ) {
			_publisher_counts[deal_obj.deal_publisher_id] = 1;
			_publishers_list.push( {id: deal_obj.deal_publisher_id, name: deal_obj.deal_publisher} );
			_uniq_publishers_names.push( deal_obj.deal_publisher );
		} else {
			_publisher_counts[deal_obj.deal_publisher_id]++;
		}
		
		for (i = 0, imax = deal_obj.deal_categories.length; i < imax; i++) {
			if( _uniq_category_names.indexOf( deal_obj.deal_categories[i][1] ) === -1 ) {
				_categories_list.push( { id: deal_obj.deal_categories[i][0], name: deal_obj.deal_categories[i][1] } );
				_uniq_category_names.push( deal_obj.deal_categories[i][1] );
				_categories_count[deal_obj.deal_categories[i][0]] = 1;
			} else {
				_categories_count[deal_obj.deal_categories[i][0]]++;
			}
		}		
	};
	
	var _buildFilters = function() {
		_buildPublishersFilter();
		_buildCategoriesFilter();
		$("#main-filter input[type='checkbox']").change(_applyFilters);
	};
	
	var _rebuildFilters = function() {
		_publishers_filter_container.html('');
		_categories_filter_container.html('');
		_buildFilters();
	};
	
	_buildPublishersFilter = function() {
		if( _publishers_list.length ) {
			_publishers_list.sort(DeallrUtil.stringSortCallback);
						
			var publisher_html = '';
			$.each( _publishers_list, function(index, publisher) {
				publisher.count = _publisher_counts[publisher.id];
				publisher_html = DeallrUtil.replaceTokens(PUBLISHER_TEMPLATE, publisher);
				_publishers_filter_container.append(publisher_html);
			});
		}
	};

	_buildCategoriesFilter = function() {
		if( _categories_list.length ) {
			_categories_list.sort(DeallrUtil.stringSortCallback);
						
			var category_html = '';
			$.each( _categories_list, function(index, category) {
				category.count = _categories_count[category.id];
				category_html = DeallrUtil.replaceTokens(CATEGORY_TEMPLATE, category);
				_categories_filter_container.append(category_html);
			});
		}
	};
	
	_buildDiscountRateFilter = function() {
		$(function() {
			_discount_slider_container.slider({
				range: true,
				min: _discount_range_min,
				max: _discount_range_max,
				values: [_discount_range_min, _discount_range_max],
				slide: function( event, ui ) {
					_discount_slider_values_container.html( "Discount - " + ui.values[0] + "%  -  " + ui.values[1] + "%");
					_applyFilters();
				}
			});
			_discount_slider_values_container.html( "Discount - " + _discount_slider_container.slider( "values", 0) + "%  -  " + _discount_slider_container.slider( "values", 1) + "%");
		});
	};
	
	_applyFilters = function() {
		if( _filter_timeout_obj ) {
			clearTimeout( _filter_timeout_obj );
		}
		
		_filter_timeout_obj = setTimeout( function() {
			_no_filter_container.hide();			
			_filter_overlay_container.show();
			var selected_category_ids = [];
			var selected_publisher_ids = [];
			var discount_min = _discount_slider_container.slider( "values", 0);
			var discount_max = _discount_slider_container.slider( "values", 1);
			$("input[name='deal-publisher']:checked").each(function() {
				selected_publisher_ids.push( parseInt( this.id.replace(PUBLISHER_ID_PREFIX,""), 10 ) );
			});
			$("input[name='deal-category']:checked").each(function() {
				selected_category_ids.push( parseInt( this.id.replace(CATEGORY_ID_PREFIX,""), 10 ) );
			});
			
			$.each(_deals,function(index, deal) {
				
				if(	   selected_publisher_ids.indexOf( deal.deal_publisher_id ) !== -1
					&& $.grep(deal.deal_categories, function(category,index) { 
				   		return selected_category_ids.indexOf(category[0]) !== -1; 
				   }).length ) {
					$("#" + DEAL_ID_PREFIX + deal.deal_id).show();
				} else {
					$("#" + DEAL_ID_PREFIX + deal.deal_id).hide();
				}
			});
			
			if($(".deal-container:visible").length === 0) {
				_no_filter_container.show();
			} else {
				_no_filter_container.hide();
			}
			
			setTimeout(function() {
				_filter_overlay_container.fadeOut("slow");
			},500);
		}, FILTER_INTERVAL);
	};
};