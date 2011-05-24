<?php
class Application_Data_Deals
{
	static function getDeals() {
		$company = array(
			array(
				1,
				'Macys',
				'http://www1.macys.com/img/nav/co_macysLogo3.gif',
				'http://www.macys.com'
			),
			array(
				2,
				'Pier One',
				'http://www.pier1.com/Portals/0/logo.gif',
				'http://www.pier1.com'
			),
			array(
				3,
				'Bed Bath & Beyond',
				'http://www.bedbathandbeyond.com/img/logo_bbb.gif',
				'http://www.bedbathandbeyond.com'
			),
			array(
				4,
				'A Pea in the Pod',
				'http://www.apeainthepod.com/images/Site/pea_logo_052610.gif',
				'http://www.apeainthepod.com'
			)
		);
		
		$categories = array(
			array(1, 'Electronics'),
			array(2, 'Apparel'),
			array(3, 'Shoes'),
			array(4, 'DVDs'),
			array(5, 'Kitchen & Dining'),
			array(6, 'Books')
		);
		
		$titles = array(
			'Friends & Family online today: extra 25% off + Free Shipping!',
		);

		$DEALS = array();
		$DEALS[] = array(
			'deal_id' => 11,
		 	'deal_title' => 'Friends & Family online today: extra 25% off + Free Shipping!',
		 	'deal_expiry_date' => 'EXPIRES May 2nd, 2011',
		 	'deal_details' => 'SHOP ONLINE TODAY & IN STORES TOMORROW<br/>get extra savings on <strong>the brands that rarely go on sale!</strong><br/><strong>PROMO Code: MACYSFF</strong>',
			'deal_publisher_id' => 1,
		 	'deal_publisher_logo' => 'http://www1.macys.com/img/nav/co_macysLogo3.gif',
			'deal_publisher_url' => 'http://www.apple.com',
			'deal_publisher' => 'Macys',
			'deal_discount' => '25',
			'deal_expiry_date' => '6/05/2011',
			'deal_expiry_date_formatted' => 'Jun 5th, 2011',
			'deal_categories' => array(
				array(1, 'Electronics'),
				array(2, 'Apparel'),
				array(3, 'Shoes')
			)
		);
		
		for($i = 0; $i < 10; $i++)
		{
			$company_rand_index = rand(0,3);
			$discount_per = rand(10,55);
			$deal_expiry_date = rand(6,12).'/'.rand(1,30).'/2011';
			$cat_rand_index1 = rand( 0, count($categories) - 1 );
			$cat_rand_index2 = rand( 0, count($categories) - 1 );
			
			$DEALS[] = array(
				'deal_id' => $i + 1,
				'deal_title' => $discount_per.'% off on Men Collection',
				'deal_expiry_date' => 'EXPIRES May 2nd, 2011',
				'deal_details' => 'Blah Blah Blah  	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah 	Blah Blah Blah',
				'deal_publisher_id' => $company[$company_rand_index][0],
				'deal_publisher' => $company[$company_rand_index][1],
				'deal_publisher_logo' => $company[$company_rand_index][2],
				'deal_publisher_url' => $company[$company_rand_index][3],
				'deal_discount' => $discount_per,
				'deal_expiry_date' => $deal_expiry_date,
				'deal_expiry_date_formatted' => date('M jS, Y', strtotime($deal_expiry_date)),
				'deal_categories' => array( $categories[$cat_rand_index1], $categories[$cat_rand_index1] )
			);
		}
		return $DEALS;
	}
}