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
namespace UniversalPayment\Dca;

/**
 * Imports
 */
use \UniversalPayment\Factory as UniversalPayment;
use \UniversalPayment\Core\ModuleFactory as PaymentModule;

/**
 * Class file
 * Handle methods related to DataContainers
 */
class DcaManagement extends \Backend
{
	/**
	 * Return all form payment templates
	 * @param DataContainer
	 * @return array
	 */
	public function getPaymentTemplates(\DataContainer $objDC)
	{
		$intPid = $objDC->activeRecord->pid;
		
		$objInput = \Input::getInstance();

		if ($objInput->get('act') == 'overrideAll')
		{
			$intPid = $objInput->get('id');
		}

		return $this->getTemplateGroup('form_payment', $intPid);
	}
	
	
	/**
	 * Create a reference entry in tl_up_modules
	 */
	public function createReference(\DataContainer $objDC)
	{
		$objActiveRecord = \Database::getInstance()->prepare("SELECT * FROM ".$objDC->table." WHERE id=?")->limit(1)->execute($objDC->id);
		
		$objPaymentModule = PaymentModule::getInstance();
		
		// get the element type by the current dca table
		$strType = UniversalPayment::getInstance()->findTypeByTable($objDC->table);
		
		// check if an payment module entry already exists
		$intEntry = $objPaymentModule->findItemByPid($objDC->id,$strType)->id;
		
		$tstamp = time();
		$arrSet = array
		(
			'pid'			 => $objDC->id,
			'tstamp' 		 => $tstamp,
			'source' 		 => $objDC->table,
			'type'	 		 => $strType,
			'method'	 	 => $objActiveRecord->universalPaymentMethod,
		);
		
		$objPaymentModule->setData($arrSet);
		
		// update entry
		if(isset($intEntry) && $intEntry > 0)
		{
			$objPaymentModule->update($intEntry);
			return;
		}
		// create new entry
		if($objActiveRecord->universalPaymentMethod)
		{
			$objPaymentModule->save();
			return;
		}
		
	}
	
}