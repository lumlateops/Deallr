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

	private static function _getUser()
	{
		return 4;//Application_Model_User::id();
	}
	
	static function getWalletDeals($page = 1, $sort = self::SORT_POST_DATE)
	{
		return self::_getUserDeals(true, $page, $sort);
	}
	
	static function getDeals($page = 1, $sort = self::SORT_POST_DATE)
	{
		return self::_getUserDeals(false, $page, $sort);
	}
	
	private static function _getUserDeals($is_wallet, $page = 1, $sort = self::SORT_POST_DATE)
	{
		if (!$sort) {
			$sort = self::SORT_POST_DATE;
		}
		
		$service_params = array(
			'userId' => self::_getUser(),
			'page' => $page,
			'sort' => $sort,
			'sortOrder' => self::$DEFAULT_SORT_ORDER[$sort]
		);
		
		$service_request = $is_wallet ? array('deals', 'wallet') : array('deals');
		$api_request = new Application_Model_APIRequest( $service_request, $service_params );
		$api_response = $api_request->call();
		
		$deal_count = intval($api_response['numberOfResults'][0], 10);
		$max_pages = isset( $api_response['numberOfPages'] ) ? intval($api_response['numberOfPages'][0], 10) : 0;
		
		if ($is_wallet)
		{
			$deals = isset( $api_response['wallet'] ) ? $api_response['wallet'] : array();
		}
		else
		{
			$deals = isset( $api_response['deals'] ) ? $api_response['deals'] : array();
		}
		$formatted_deals = array();
		$count = 0;
		foreach( $deals as $deal ) {

			$deal_categories = array();
			if (isset($deal['categories'])) {
				foreach( $deal['categories'] as $deal_category ) {
					$deal_categories[] = array( $deal_category['id'], $deal_category['description'] );
				}
			}
			
			$tags = isset($deal['tags']) && $deal['tags'] ? $deal['tags'] : '';
			$tags = str_replace(',', ', ', $tags);
			$formatted_deals[] = array(
				'deal_id' => $is_wallet ? $deal['dealId'] : $deal['id'],
			 	'deal_title' => $deal['title'],
			 	'deal_details' => $deal['description'],
				'deal_discount' => $deal['discountPercentage'],
			 	'deal_expiry_date' => $deal['expiryDate'],
				'deal_expiry_date_formatted' => $deal['expiryDate'],
				'deal_post_date' => $deal['postDate'],
				'deal_free_shipping' => $deal['freeShipping'] ? '' : 'none',
				'deal_tags' => $tags,
				'deal_in_wallet' => $is_wallet ? 1 : ($deal['isInWallet'] ? 1 : 0),

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
	
	static function getDetails($deal_id)
	{
		$service_params = array(
			'dealId' => $deal_id
		);
		$api_request = new Application_Model_APIRequest( array('deal','detail'), $service_params );
		$api_response = $api_request->call();
		
		$deal_details = array(
			'deal_email_content' => $api_response['deal'][0]['dealEmail']['content'],
			'deal_email_subject' => $api_response['deal'][0]['dealEmail']['subject'],
			'deal_title' => $api_response['deal'][0]['title']
		);
		
		return $deal_details;
	}
	
	static function addToWallet($deal_id)
	{
		$service_params = array(
			'userId' => self::_getUser(),
			'dealId' => $deal_id
		);
		
		$api_request = new Application_Model_APIRequest( array('deals', 'wallet', 'add'), $service_params );
		$api_request->setMethod( Application_Model_APIRequest::METHOD_POST );
		$api_response = $api_request->call();
		
		return $api_response['status'][0] == 'ok';
	}
	
	static function removeFromWallet($deal_id)
	{
		$service_params = array(
			'userId' => self::_getUser(),
			'dealId' => $deal_id
		);
		
		$api_request = new Application_Model_APIRequest( array('deals', 'wallet', 'remove'), $service_params );
		$api_request->setMethod( Application_Model_APIRequest::METHOD_POST );
		$api_response = $api_request->call();
		
		return $api_response['status'][0] == 'ok';	
	}
	
	static function getDealsFromWallet()
	{
	
	}
}

?>