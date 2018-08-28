<?php
class WsvcController extends CController
{
	public function action() {
		return array(
			'service'=>array(
				'class'=>'CWebServiceAction',
			),
		);
	}
	
	/**
	* @param string key
	* @return string session key
	* @soap
	*/
	public function auth($key) {
		if (Wservice::model()->exists('wsvc_key=?',array($key))) {
			$skey = sha1(mt_rand());
//			Yii::app()->cache->set('soap_client'.$key, $skey, 3600);
			return $skey;
		}
		return '';
	}
	
	/**
	* @param string key
	* @param string session
	* @param integer roster id 
	* @param string report code
	* @param string report name
	* @param string contact name
	* @param string contact phone
	* @param string city
	* @return integer result
	* @soap
	*/
	public function ucustomer($key, $session, $id, $code, $name, $cont_name, $cont_phone, $city) {
//		$value = Yii::app()->cache->get('soap_client'.$key);
		if ($value===false) return -1
		if ($value!==$session) return -2
		$customer=Customer::model()->find('roster_id=?',array($id));
		if ($customer===null) $customer = new Customer;
		$customer->code = $code;
		$customer->name = $name;
		$customer->cont_name = $cont_name;
		$customer->cont_phone = $cont_phone;
		$customer->city = $city;
		$customer->lcu = 'admin';
		$customer->luu = 'admin';
		if ($customer->save())
			return 0;
		else
			return -3;
	}
}
?>
