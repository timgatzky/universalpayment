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
use UniversalPayment\Core\ModuleFactory as PaymentModule;
use UniversalPayment\Core\SessionFactory as PaymentSession;
use UniversalPayment\Core\Hooks as PaymentHooks;

/**
 * Class file
 * Handle pre sale actions
 */
class PreSale extends \Frontend
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
	 * Do some actions after the user started the payment process
	 * @param object
	 * @param object
	 * @param object
	 * called from generatePage HOOK
	 */
	public function run(\Database_Result $objPage, \Database_Result $objLayout, \PageRegular $objPageRegular)
	{
		// observe form submits or any other form action related to universal_payment
		$objInput = \Input::getInstance();
		
		//check form submit, whether it is coming from a form gen or template form
		
		$this->strUniqueId = strlen($objInput->post('UNIQUE_ID')) ? $objInput->post('UNIQUE_ID') : $objInput->get('uid');
		
		if(strlen($this->strUniqueId) < 1 || strlen($objInput->get('fwd')) < 1)
		{
			return;
		}
			
		$objDatabase = \Database::getInstance();
		$objPayment = UniversalPayment::getInstance();
		$objPaymentSession = PaymentSession::getInstance();
		
		// get session information form unique id
		$arrPayment = $objPaymentSession->findItemByUnique($this->strUniqueId);
		
		// return if the session was cleared or the transmitted unique ID does not match, don't accept orders
		if(empty($arrPayment) || $arrPayment['uniqueId'] != $this->strUniqueId)
		{
			return ;
		}
		
		$this->strType = trim($arrPayment['type']);
		$this->strSource = $objPayment->findTableByType($this->strType);
		$this->intElement = $arrPayment['id'];
		
		//-- generate the registered extension module class to run its personal processing methods
		$objResult =\Database::getInstance()->prepare("SELECT * FROM ".$this->strSource." WHERE id=?")->limit(1)->execute($this->intElement);
		
		// element does not exist in contao anymore
		if($objResult->numRows < 1)
		{
			return;
		}
		
		$strClass = $GLOBALS['UNIVERSALPAYMENT']['PAYMENT_'.strtoupper($this->strType)][$objResult->universalPaymentMethod];
		$objElement = new $strClass($objResult);
		$objElement->uniqueId = $this->strUniqueId;
		$objElement->arrSubmitted = (count($_POST) > 0 ? $_POST : $_GET);
		if(!isset($objElement->arrData) || empty($objElement->arrData))
		{
			$objElement->arrData = $objResult->row();
		}
		
		// run pre-sale methods
		if(method_exists($objElement,'processPreSale'))
		{
			$objElement = $objElement->processPreSale();
		}
		
		// trigger hook
		$objHooks = Hooks::getInstance();
		$objElement = $objHooks->callPreSaleHook($arrPayment,$objElement,$this);
		
		// forward to payment site
		if( strlen($objInput->get('fwd')) )
		{
			$strURL = '';
			$objString = \String::getInstance();
			switch($this->strType)
			{
				case 'ffd':
					$strURL = $arrPayment['redirect'];
					break;
				case "mod":
				case 'cte':
					$arrParams = (count($_POST) > 0 ? $_POST : $_GET);
					$strParams = $objString->decodeEntities(http_build_query($arrParams));	
					$strURL = $objInput->get('fwd').'?'.$strParams;
					break;
				default:
				break;
			}
			
			// allow others extensions to redirect
			$arrParams = (count($_POST) > 0 ? $_POST : $_GET);
			$strParams = $objString->decodeEntities(http_build_query($arrParams));	
			$strURL = $objInput->get('fwd').'?'.$strParams;
			$strURL = Hooks::getInstance()->callPreSaleRedirectHook($strURL,$arrPayment,$objElement,$arrParams,$this);
			
			if(strlen($strURL) < 1)
			{
				return;
			}
			
			$this->redirect($strURL);
		}
		
	}
	
	/**
	 * Store the submitted from data in Session items for further use
	 * @param object
	 * @param object
	 * @param object
	 * called from processFormData HOOK
	 */
	public function  storeFormSubmit($arrPost, $arrForm, $arrFiles)
	{
		// observe form submits or any other form action related to universal_payment
		$objInput = \Input::getInstance();
		
		$objPayment = UniversalPayment::getInstance();
		$objSession = \Session::getInstance();
		$arrSession = $objSession->get('universalpayment_formsubmit');
		
		if(!$arrSession || !is_array($arrSession) )
		{
			$arrSession = array();
		}
		
		$arrSession['post'] = $arrPost;
		if(version_compare(VERSION, '2.11', '<=') )
		{
			$arrSession['form'] = $this->cleanFormData($arrForm);
		}
		else
		{
			$arrSession['form'] = $arrForm;
		}
		$arrSession['form'] = $this->cleanFormData($arrForm);
		$arrSession['files'] = $arrFiles;
		
		$objSession->set('universalpayment_formsubmit',$arrSession);
	}
	
	/**
	 * This method cleans the passed form data from object instances.
	 * This is necessary to prevent "dead" objects from being loaded.
	 *
	 * See https://github.com/contao/core/pull/6305
	 *
	 * Can be safely removed when above PR has been merged.
	 * 
	 * Thanks to xtra
	 * @param array $arrForm The form data to clean.
	 *
	 * @return array
	 */
	protected function cleanFormData($arrForm)
	{
		foreach ($arrForm as $strKey => $varValue)
		{
			if (is_object($varValue))
			{
				unset($arrForm[$strKey]);
			}
		}
		return $arrForm;
	}
	
	
}