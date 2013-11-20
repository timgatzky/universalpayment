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
namespace UniversalPayment\Frontend\Formfield;


/**
 * Imports
 */
use UniversalPayment\Factory as UniversalPayment;
use UniversalPayment\Core\SessionFactory as PaymentSession;
use UniversalPayment\Core\ModuleFactory as PaymentModule;


/**
 * Class file
 * Handle Formfields
 */
class PaymentField extends \Widget
{
	/**
	 * Template
	 * @var
	 */
	protected $strTemplate = 'form_widget';
	
	/**
	 * Unique ID
	 * @var string
	 */
	protected $strUniqueId;
	
	/**
	 * Type of payment element
	 * @var string
	 */
	protected $strType = 'FFD';
	
	/**
	 * Data array
	 */
	protected $arrData = array();
	
	/**
	 * Display a wildcard
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$strMethodName = UniversalPayment::getInstance()->getPaymentModuleName($this->universalPaymentMethod);
						
			$objTemplate = new \BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### UNIVERSAL PAYMENT ###' . '<p class="tl_gray">'.$strMethodName.'</p>';
			$objTemplate->id = $this->id;
			
			return $objTemplate->parse();
		}
		
		return $this->compile();
	}
	
	/**
	 * Generate the widget and return it as string
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
		
		// if no class is specified, render the field as an hidden input field
		if(!class_exists($strClass))
		{
			$strClass = '\FormHidden';
		}
		
		$objDatabase = \Database::getInstance();
		$objResult = $objDatabase->prepare("SELECT * FROM tl_form_field WHERE id=?")->limit(1)->execute($this->id);
		
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
		
		$this->arrData = $objResult->row();
		
		$strBuffer = '';
		
		// generate the extension widget
		$objFormfield = new $strClass($objResult);
		$objFormfield->id = $this->id;
		$objFormfield->uniqueId = $this->strUniqueId;
		$objFormfield->arrData = $objResult->row();
		
		$strBuffer .= $objFormfield->generate();
		
		// insert a hidden field for the unique id
		$objUnique = new \FormHidden();
		$objUnique->name = 'UNIQUE_ID::'.$this->universalPaymentMethod; #'UNIQUE_ID';
		$objUnique->value = $this->strUniqueId;
		
		$strBuffer .= $objUnique->generate();
		
		// warp that whole thing in a container
		$arrClass = array('universalpayment_container','universalpayment_ffd_'.$this->id,$objResult->universalPaymentMethod);
		$strBuffer = sprintf('<div class="%s">%s</div>',implode(' ',$arrClass),$strBuffer);
		
		return $strBuffer;
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