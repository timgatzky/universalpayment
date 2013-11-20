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

/*
Inserttags are:

Explanation:
SELECTOR	"up" or "universalpayment" (without quotation marks)
TYPE		Type of Payment element e.g. MOD,FFD,CTE (is a module, formfield or contentelement)
(*)			The Id of the Element

// returns current unique ID or generates a new one
// @return string 	
{{up::unique_id::TYPE::*}}
{{universalpayment::unique_id::TYPE::*}}

// return order information as array
// @return array
{{SELECTOR::order::ID-OR-UNIQUE_ID}}

// return field value for the given fieldname (tries POST then GET)
{{form::FIELDNAME}}

// return POST value for the given fieldname
{{post::FIELDNAME}}

// return POST value for the given fieldname
{{get::FIELDNAME}}

*/

/**
 * Namespaces
 */
namespace UniversalPayment\Core;

/**
 * Imports
 */
use UniversalPayment\Factory as UniversalPayment;
use UniversalPayment\Core\SessionFactory as PaymentSession;

/**
 * Class file
 * Inserttags
 */
class InsertTags extends \Controller
{
	/**
	 * Replace Inserttags
	 */
	public function replaceTags($strTag)
	{
		$arrElements = explode('::', $strTag);
		
		switch(strtolower($arrElements[0]))
		{
			// universalpayment inserttags
			case 'up': 
			case 'universalpayment':
				switch($arrElements[1])
				{
					// return unique id or generate new one
					case 'unique_id':
						$objPayment = UniversalPayment::getInstance();
						$objPaymentSession = PaymentSession::getInstance();
						
						$strUniqueId = '';
						
						// just generate a unique id if no more parameters were given
						if(!$arrElements[2])
						{
							return $objPayment->generateUnique($GLOBALS['UNIVERSALPAYMENT']['uniqueIdLength']);
						}
						
						$strType = $arrElements[2];
						$intId = $arrElements[3];
						
						// check if a unique id already exists in session
						$strUniqueId = $objPaymentSession->findUniqueByIdAndType($intId,strtolower($strType));
						
						return $strUniqueId; 
						break;
					// return order information as array
					case 'order':
						$objDatabase = \Database::getInstance();
						$value = $arrElements[2];
						
						//check for id
						$objResult = $objDatabase->prepare("SELECT * FROM tl_up_orders WHERE id=?")->limit(1)->execute($value);
						if($objResult->numRows > 0)
						{
							return $objResult->row();
						}
						// check for unique id
						$objResult = $objDatabase->prepare("SELECT * FROM tl_up_orders WHERE uniqueId=?")->limit(1)->execute($value);
						if($objResult->numRows > 0)
						{
							return $objResult->row();
						}
						
						return false;
						
						break;
					default:
						return false;
						break;	
				}
				break;
			
			// return POST variable
			case 'post':
				$objInput = \Input::getInstance();
				$value = $arrElements[1];
				
				if($objInput->post($value))
				{
					return $objInput->post($value);
				}
				if($_POST[$value])
				{
					return $_POST[$value];
				}
				return false;
				
				break;
			// return GET variable
			case 'get':
				$objInput = \Input::getInstance();
				$value = $arrElements[1];
				
				if($objInput->get($value))
				{
					return $objInput->get($value);
				}
				if($_GET[$value])
				{
					return $_GET[$value];
				}
				return false;
				
				break;
			// return either POST or GET variable
			case 'form':
				$objInput = \Input::getInstance();
				$value = $arrElements[1];
				
				if($this->replaceInsertTags('{{post::'.$value.'}}'))
				{
					return $this->replaceInsertTags('{{post::'.$value.'}}');
				}
				if($this->replaceInsertTags('{{get::'.$value.'}}'))
				{
					return $this->replaceInsertTags('{{get::'.$value.'}}');
				}
				
				return false;
				
			default:
				return false;
				break;
		}
	}

}