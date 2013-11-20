<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		universal_payment
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespaces
 */
namespace UniversalPayment\Core;

/**
 * Imports
 */
use UniversalPayment\Factory as UniversalPayment;
use UniversalPayment\Core\OrderFactory as PaymentOrder;
use UniversalPayment\Core\ModuleFactory as PaymentModule;
use UniversalPayment\Core\SessionFactory as PaymentSession;

/**
 * Class file
 */
class PostSale extends \Frontend
{
	/**
	 * Unique ID
	 * @var string
	 */
	protected $strUniqueId;
	
	/**
	 * Element type
	 * @var string
	 */
	protected $strType;
	
	/**
	 * Element source table
	 * @var string
	 */
	protected $strSource;
	
	/**
	 * Id of element
	 * @var string
	 */
	protected $intElement;

	/**
	 * Run
	 */
	public function run(\Database_Result $objPage, \Database_Result $objLayout, \PageRegular $objPageRegular)
	{
		// observe form submits or any other form action related to universalpayment
		$objInput = \Input::getInstance();
		
		// ignore when pre-sale actions
		if(strlen($objInput->get('fwd') > 0))
		{
			return;
		}
		
		$this->strUniqueId = $this->Input->get('uid');
		
		if(strlen($this->strUniqueId) < 1)
		{
			return;
		}
		
		$objDatabase = \Database::getInstance();
		$objPayment = UniversalPayment::getInstance();
		$objPaymentSession = PaymentSession::getInstance();
		
		// return check if an order already exists with the same unique id, e.g. reload the page
		$objOrderResult = $this->Database->prepare("SELECT * FROM tl_up_orders WHERE uniqueId=?")->limit(1)->execute($this->strUniqueId);
		if($objOrderResult->numRows > 0)
		{
			return;
		}
		
		// get session information form unique id
		$arrPayment = $objPaymentSession->findItemByUnique($this->strUniqueId);
		
		// return if the session was cleared or the transmitted unique ID does not match, don't accept orders
		if(empty($arrPayment) || $arrPayment['uniqueId'] != $this->strUniqueId)
		{
			return ;
		}
		
		$this->strType = $arrPayment['type'];
		$this->strSource = $objPayment->findTableByType($this->strType);
		$this->intElement = $arrPayment['id'];
		
		//-- generate the registered extension module class to run its personal processing methods
		$objResult =\Database::getInstance()->prepare("SELECT * FROM ".$this->strSource." WHERE id=?")->limit(1)->execute($this->intElement);
		
		// element does not exist in contao anymore
		if($objResult->numRows < 1)
		{
			return;
		}
		
		$strClass = $GLOBALS['UNIVERSALPAYMENT']['PAYMENT_'.strtoupper($arrPayment['type'])][$objResult->universalPaymentMethod];
		$objElement = new $strClass($objResult);
		$objElement->uniqueId = $this->strUniqueId;
		if(!isset($objElement->arrData) || empty($objElement->arrData))
		{
			$objElement->arrData = $objResult->row();
		}
		
		//-- create new order object
		$objOrder = PaymentOrder::getInstance();
		
		// make current order object accessible to the element
		$objElement->order = $objOrder;
		
		// run post sale methods in registered extension, allow other extensions to modify the order data
		if(method_exists($objElement,'processPostSale'))
		{
			$objElement = $objElement->processPostSale();
		}
		
		// trigger hook
		$objHooks = Hooks::getInstance();
		$objElement = $objHooks->callPostSaleHook($arrPayment,$objElement,$this);
		
		if(!$objElement)
		{
			// throw error here. User must return object
			trigger_error('POSTSALE HOOK EXPECTS ONE OBJECT ('.$strClass.') AS RETURN', E_USER_ERROR);
		}
		
		// build paymentData Array collection
		$arrPaymentData = array
		(
			'order'		=> $objElement->get('order'),
			'postsale'	=> $objElement->get('postsale'),
			'submitted'	=> $arrPayment['FORM_SUBMIT']['arrSubmitted'],
		);
		
		// set fix order values
		$objOrder->set('pid',PaymentModule::getInstance()->findItemByPid($this->intElement,$this->strType)->id);
		$objOrder->set('uniqueId',$this->strUniqueId);
		$objOrder->set('paymentData',serialize($arrPaymentData));
		$objOrder->set('method',$objResult->universalPaymentMethod);
		
		if(!$objOrder->get('orderId'))
		{
			$orderId = (isset($arrPayment['orderId']) ? $arrPayment['orderId']:'');
			$objOrder->set('orderId',$orderId);
		}
		
		// save order
		$bolSave = $objOrder->save();
		
		// clean the session after saving
		if($bolSave)
		{
			$objPaymentSession->removeItemByUnique($this->strUniqueId);
			// clean POST?
		}
		
		return true;
	}
}