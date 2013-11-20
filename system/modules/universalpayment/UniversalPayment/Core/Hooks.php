<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		
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

/**
 * Class file
 * Manage Hooks
 */
class Hooks extends \System
{
	/**
	 * Current object instance (Singleton)
	 * @var Database
	 */
	protected static $objInstance;
	
	/**
	 * Instantiate this class and return it (Factory)
	 * @return FormPayment
	 * @throws Exception
	 */
	public static function getInstance()
	{
		if (!is_object(self::$objInstance))
		{
			self::$objInstance = new self();
		}

		return self::$objInstance;
	}
	
	
	/**
	 * Pre-sale hook
	 * Triggered before any payment module is processed e.g. when a form is submitted or a redirect is fired
	 * @param array		Payment information
	 * @param object	The module preparing a payment process
	 * @param object	The caller class
	 */
	public function callPreSaleHook($arrPayment,$objPaymentElement,$objCaller)
	{
		$strClass = get_class($objPaymentElement);
		
		if (isset($GLOBALS['TL_HOOKS']['UNIVERSALPAYMENT']['PRESALE']) && count($GLOBALS['TL_HOOKS']['UNIVERSALPAYMENT']['PRESALE']) > 0)
		{
			foreach($GLOBALS['TL_HOOKS']['UNIVERSALPAYMENT']['PRESALE'] as $callback)
			{
				$this->import($callback[0]);
				$objPaymentElement = $this->$callback[0]->$callback[1]($arrPayment,$objPaymentElement,$objCaller,$this);
			}
		}
		
		if(!$objPaymentElement)
		{
			// throw error here. User must return object
			trigger_error('PRESALE HOOK EXPECTS ONE OBJECT ('.$strClass.') AS RETURN', E_USER_ERROR);
		}
		
		return $objPaymentElement;
	}
	
	
	/**
	 * Post-sale hook
	 * Triggered after any payment module processed
	 * @param array		Payment information
	 * @param object	The module finishing a payment process
	 * @param object	The caller class
	 */
	public function callPostSaleHook($arrPayment,$objPaymentElement,$objCaller)
	{
		$strClass = get_class($objPaymentElement);
		
		if (isset($GLOBALS['TL_HOOKS']['UNIVERSALPAYMENT']['POSTSALE']) && count($GLOBALS['TL_HOOKS']['UNIVERSALPAYMENT']['POSTSALE']) > 0)
		{
			foreach($GLOBALS['TL_HOOKS']['UNIVERSALPAYMENT']['POSTSALE'] as $callback)
			{
				$this->import($callback[0]);
				$this->$callback[0]->$callback[1]($arrPayment,$objPaymentElement,$objCaller,$this);
			}
		}
		
		if(!$objPaymentElement)
		{
			// throw error here. User must return object
			trigger_error('POSTSALE HOOK EXPECTS ONE OBJECT ('.$strClass.') AS RETURN', E_USER_ERROR);
		}
		
		return $objPaymentElement;
	}
	
	
	/**
	 * Pre-sale redirection hook
	 * Triggered before any payment module is processed and redirected to payment site
	 * Expects the redirection url as return
	 * @param array		Payment information
	 * @param object	The module preparing a payment process
	 * @param object	The caller class
	 * @return string	Redirect URL
	 */
	public function callPreSaleRedirectHook($arrPayment,$objPaymentElement,$arrParams,$objCaller)
	{
		$strReturn = '';
		
		if (isset($GLOBALS['TL_HOOKS']['UNIVERSALPAYMENT']['PRESALE_REDIRECT']) && count($GLOBALS['TL_HOOKS']['UNIVERSALPAYMENT']['PRESALE_REDIRECT']) > 0)
		{
			foreach($GLOBALS['TL_HOOKS']['UNIVERSALPAYMENT']['PRESALE_REDIRECT'] as $callback)
			{
				$this->import($callback[0]);
				$strReturn = $this->$callback[0]->$callback[1]($arrPayment,$objPaymentElement,$arrParams,$objCaller,$this);
			}
		}
		
		return $strReturn;
	}
	

}