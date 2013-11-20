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
namespace UniversalPayment\Backend;

/**
 * Imports
 */
use \UniversalPayment\Core\ModuleFactory as PaymentModule;

/**
 * Class file
 * Handle methods related to tl_form_field
 */
class TableFormfield extends \Backend
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
	 * Load the default palettes view for tl_form_field
	 */
	public function loadDefaultPalettes(\DataContainer $objDC)
	{
		$GLOBALS['TL_DCA']['tl_form_field']['palettes']['universalpayment'] = '{type_legend},type,name;{payment_legend},universalPaymentMethod;{payment_setting_legend},universalPaymentAddJumpTo;universalPaymentAddSettings;{expert_legend:hide}';
	}
	
	public function modifyPalette(\DataContainer $objDC)
	{
		
	}
	
	
	public function toggleAddSettings($varValue, \DataContainer $objDC)
	{
		if($varValue)
		{
			$this->strToggler = 'addSettings';
			$this->modifyPalette($objDC);
		}
		
		return $varValue;
	}
	
	public function toggleAddJumpTo($varValue, \DataContainer $objDC)
	{
		if($varValue)
		{
			$this->strToggler = 'addJumpTo';
			$this->modifyPalette($objDC);
			}
		
		return $varValue;
	}
}