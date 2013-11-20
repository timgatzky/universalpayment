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

/**
 * Class file
 */
class PaymentSelectMenu extends \Widget
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'form_widget';

	/**
	 * Options
	 * @var array
	 */
	protected $arrOptions = array();

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		// generate widget
		$objWidget = new \FormSelectMenu();
		// is radio
		if($this->universalPaymentSelectIsRadio) { $objWidget = new \FormRadioButton(); }
		$objWidget->name = $this->name;
		$objWidget->id = $this->id;
		$objWidget->class .= 'universalpayment_select';
		
		// get registered payment modules
		$arrPaymentModules = UniversalPayment::getInstance()->getPaymentModules();
		
		if(TL_MODE == 'BE')
		{
			if(count($arrPaymentModules) > 0)
			{
				foreach($arrPaymentModules as $value => $label)
				{
					$this->arrOptions[] = array('value'=>$value, 'label'=>trim($label));
				}
			}
		}
		elseif(TL_MODE == 'FE')
		{
			#$this->arrOptions =array( array('value'=>'','label'=>'-') );
		
			// fetch published payment fields
			$objDatabase = \Database::getInstance();
			
			$findInSet = $objDatabase->findInSet('universalPaymentMethod',array_flip($arrPaymentModules));
			$objFields = $objDatabase->execute("SELECT * FROM tl_form_field WHERE type='universalpayment' AND ".$findInSet.' AND invisible!=1');
			
			if($objFields->numRows < 1)
			{
				return '<span class="error">'.$GLOBALS['TL_LANG']['MSC']['noActivePaymentMethods'].'</span>';
			}
							
			while($objFields->next())
			{
				$label = trim($arrPaymentModules[$objFields->universalPaymentMethod]);
				$value = $objFields->universalPaymentMethod;
				$this->arrOptions[] = array('value'=>$value, 'label'=>$label);
			}
		}
		else{}

		$objWidget->options = $this->arrOptions;

		return $objWidget->generate();
	}
}