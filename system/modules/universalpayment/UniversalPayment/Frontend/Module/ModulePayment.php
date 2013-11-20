<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		universalpayment
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespaces
 */
namespace UniversalPayment\Frontend\Module;

/**
 * Imports
 */
use UniversalPayment\Factory as UniversalPayment;
use UniversalPayment\Core\SessionFactory as PaymentSession;
use UniversalPayment\Core\ModuleFactory as PaymentModule;

/**
 * Class file
 * Handle Frontend Modules
 */
class ModulePayment extends \Module
{
	/**
	 * Template
	 * @var
	 */
	protected $strTemplate = 'mod_payment';
	
	/**
	 * Unique ID
	 * @var string
	 */
	protected $strUniqueId;
	
	/**
	 * Type of payment element
	 * @var string
	 */
	protected $strType = 'MOD';
	
	/**
	 * Display wildcard
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$strMethodName = UniversalPayment::getInstance()->getPaymentModuleName($this->universalPaymentMethod);
			
			$this->Template = new \BackendTemplate('be_wildcard');
			$this->Template->wildcard = '### MODULE UNIVERSAL PAYMENT ###'. '<p class="tl_gray">'.$strMethodName.'</p>';
			return $this->Template->parse();
		}
		
		return parent::generate();
	}
	
	/**
	 * Generate the module
	 */
	protected function compile()
	{
		// get registered module class
		$strClass = $GLOBALS['UNIVERSALPAYMENT']['PAYMENT_'.strtoupper($this->strType)][$this->universalPaymentMethod];
		
		// if no class is specified, just render this module as default
		if(!class_exists($strClass))
		{
			return parent::generate();
		}
		
		$objDatabase = \Database::getInstance();
		$objResult = $objDatabase->prepare("SELECT * FROM tl_module WHERE id=?")->limit(1)->execute($this->id);
		
		// generate registered payment module
		$objModule = new $strClass($objResult);
		
		// generate a unique id for this payment instance
		$objPayment = UniversalPayment::getInstance();
		$objPaymentSession = PaymentSession::getInstance();
		
		// check if a unique id already exists in session
		$this->strUniqueId = $objPaymentSession->findUniqueByIdAndType($this->id,strtolower($this->strType));
		
		if(strlen($this->strUniqueId) < 1)
		{
			$this->strUniqueId = $objPayment->generateUnique($GLOBALS['UNIVERSALPAYMENT']['uniqueIdLength']);
			// store unique in current session
			$objPaymentSession->setUniqueById($this->strUniqueId,$this->id,strtolower($this->strType));
		} 
		$objModule->uniqueId = $this->strUniqueId;
		
		$this->Template->content = $objModule->generate();
	}
	
	/**
	 * Setter
	 */
	public function set($strKey,$strValue)
	{
		$this->arrData[$strKey] = $strValue;
	}
	
	/**
	 * Getter
	 */
	public function get($strKey)
	{
		return $this->arrData[$strKey];
	}
	
	/**
	 * Do pre-sale actions (default is empty)
	 */
	public function processPreSale() {}
	
	/**
	 * Do post-sale actions (default is empty)
	 */
	public function processPostSale() {}
	
	/**
	 * Get the unique Id
	 * @return string
	 */
	public function getUniqueID()
	{
		return $this->strUniqueId;
	}
}