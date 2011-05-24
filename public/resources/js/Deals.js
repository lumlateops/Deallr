Deals = function() {
	var _deals = [];
	var _deals_container = $("#deals-container");
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
	var _categories_list = [];
	var _discount_range_min = 100;
	var _discount_range_max = 0;
	var _filter_timeout_obj = '';
	var FILTER_INTERVAL = 400;
	var DEAL_ID_PREFIX = 'deal-';	
	var DEALS_TEMPLATE = '\
		<div class="deal-container" id="'+DEAL_ID_PREFIX+'{deal_id}">\
			<div class="deal-logo">\
				<img src="{deal_publisher_logo}" alt="{deal_publisher} Logo" />\
			</div>\
			<h3>{deal_title}</h3>\
			<div class="deal-content">\
				<div class="deal-expiry-date">\
					EXPIRES {deal_expiry_date_formatted}\
				</div>\
				<div class="deal-details">\
					{deal_details}\
				</div>\
				<div class="deal-facebook-like">\
					<iframe src="http://www.facebook.com/plugins/like.php?app_id=116558288428225&amp;href&amp;send=true&amp;layout=standard&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=lucida+grande&amp;height=80" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:30px;" allowTransparency="true"></iframe>\
				</div>\
			</div>\
		</div>\
	';

	var PUBLISHER_ID_PREFIX = 'publisher-';
	var PUBLISHER_TEMPLATE = '<li><input type="checkbox" checked="checked" name="deal-publisher" id="'+PUBLISHER_ID_PREFIX+'{id}" />{name}</li>';
	var CATEGORY_ID_PREFIX = 'category-';
	var CATEGORY_TEMPLATE = '<li><input type="checkbox" checked="checked" name="deal-category" id="'+CATEGORY_ID_PREFIX+'{id}" />{name}</li>';
	
	this.init = function(initial_deals) {
		if( initial_deals ) {
			_deals = initial_deals;
		}
		this.displayDeals();
		_buildFilters();
	};
	
	this.displayDeals = function(){
		var deals_by_id = {};
		$.each(_deals, function(key, value) {
			
			_displayDeal(value);
			
			_populateFilterValues(value);
			
			deals_by_id[value.deal_id] = value;
		});
		_deals = deals_by_id;
		delete deals_by_id;
		_loading_container.hide();
		_showDeals();
	};
	
	var _showDeals = function() {
		//$("#deals-container .deal-container:hidden").fadeIn();
		//setTimeout(_showDeals, 400);
	};
	
	var _displayDeal = function( deal_obj ) {
		var deal_html = DeallrUtil.replaceTokens(DEALS_TEMPLATE, deal_obj);
		_deals_container.append( deal_html );
	};
	
	var _populateFilterValues = function( deal_obj ) {
		var i = 0, imax = 0;
		
		if( _uniq_publishers_names.indexOf( deal_obj.deal_publisher ) === -1 ) {
			_publishers_list.push( {id: deal_obj.deal_publisher_id, name: deal_obj.deal_publisher} );
			_uniq_publishers_names.push( deal_obj.deal_publisher );
		}
		
		for( i = 0, imax = deal_obj.deal_categories.length; i < imax; i++ ) {
			if( _uniq_category_names.indexOf( deal_obj.deal_categories[i][1] ) === -1 ) {
				_categories_list.push( { id: deal_obj.deal_categories[i][0], name: deal_obj.deal_categories[i][1] } );
				_uniq_category_names.push( deal_obj.deal_categories[i][1] );
			}
		}
		
		deal_obj.deal_discount = parseInt( deal_obj.deal_discount, 10 );
		
		if( _discount_range_min > deal_obj.deal_discount ) {
			_discount_range_min = deal_obj.deal_discount;
		}

		if( _discount_range_max < deal_obj.deal_discount ) {
			_discount_range_max = deal_obj.deal_discount;
		}
	};
	
	var _buildFilters = function() {
		_buildPublishersFilter();
		_buildDiscountRateFilter();
		_buildCategoriesFilter();
		$("#main-filter input[type='checkbox']").change(_applyFilters);
	};
	
	_buildPublishersFilter = function() {
		if( _publishers_list.length ) {
			_publishers_list.sort(DeallrUtil.stringSortCallback);
						
			var publisher_html = '';
			$.each( _publishers_list, function(index, publisher) {
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
				if( 	selected_publisher_ids.indexOf( deal.deal_publisher_id ) !== -1 
					&& deal.deal_discount >= discount_min 
					&& deal.deal_discount <= discount_max
					&& $.grep(deal.deal_categories, function(category,index) { return selected_category_ids.indexOf(category[0]) !== -1; }).length
				) {
					$("#" + DEAL_ID_PREFIX + deal.deal_id).show();
				} else {
					$("#" + DEAL_ID_PREFIX + deal.deal_id).hide();
				}
			});
			
			if( $(".deal-container:visible").length === 0 ) {
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