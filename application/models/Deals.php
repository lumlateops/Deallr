<?php

class Application_Model_Deals
{
	const SORT_ORDER_ASC = 'ASC';
	const SORT_ORDER_DESC = 'DESC';
	
	const SORT_POST_DATE = 'postDate';
	const SORT_DISCOUNT_PERCENTAGE = 'discountPercentage';
	const SORT_EXPIRY_DATE = 'expiryDate';
	
	static $SORT_PARAMS = array(
		self::SORT_POST_DATE => 'Post Date',
		self::SORT_DISCOUNT_PERCENTAGE => 'Discount Percentage',
		self::SORT_EXPIRY_DATE => 'Expiration Date'
	);
	
	static $DEFAULT_SORT_ORDER = array(
		self::SORT_POST_DATE => self::SORT_ORDER_DESC,
		self::SORT_DISCOUNT_PERCENTAGE => self::SORT_ORDER_DESC,
		self::SORT_EXPIRY_DATE => self::SORT_ORDER_ASC
	);

	static function getDeals($page = 1, $sort = self::SORT_POST_DATE)
	{
		if (!$sort) {
			$sort = self::SORT_POST_DATE;
		}
		
		$service_params = array(
			'userId' => 4,//Application_Model_User::id(),
			'page' => $page,
			'sort' => $sort,
			'sortOrder' => self::$DEFAULT_SORT_ORDER[$sort]
		);
		$api_request = new Application_Model_APIRequest( array('deals'), $service_params );
		$api_response = $api_request->call();
		
		$deal_count = intval($api_response['numberOfResults'][0], 10);
		$max_pages = isset( $api_response['numberOfPages'] ) ? intval($api_response['numberOfPages'][0], 10) : 0;
		$deals = isset( $api_response['deals'] ) ? $api_response['deals'] : array();
		$formatted_deals = array();
		$count = 0;
		foreach( $deals as $deal ) {

			$deal_categories = array();
			if (isset($deal['dealCategories'])) {
				foreach( $deal['dealCategories'] as $deal_category ) {
					$deal_categories[] = array( $deal_category['id'], $deal_category['category'] );
				}
			}
						
			$formatted_deals[] = array(
				'deal_id' => $deal['id'],
			 	'deal_title' => $deal['title'],
			 	'deal_details' => $deal['description'],
				'deal_discount' => $deal['discountPercentage'],
			 	'deal_expiry_date' => $deal['expiryDate'],
				'deal_expiry_date_formatted' => $deal['expiryDate'],

				'deal_publisher_id' => $deal['retailer']['id'],
			 	'deal_publisher_logo' => $deal['retailer']['image'],
				'deal_publisher_url' => $deal['retailer']['domain'],
				'deal_publisher' => $deal['retailer']['name'],
				
				'deal_categories' => $deal_categories
			);
		}
		
		$ret_arr = array(
			'deal_count' => $deal_count, 
			'max_pages' => $max_pages, 
			'current_page' => $page,
			'current_sort' => $sort,
			'deals' =>  $formatted_deals
		);
		return $ret_arr;
	}
}

?>