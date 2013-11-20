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
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'UniversalPayment'
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'UniversalPayment\Factory'			   							=> 'system/modules/universal_payment/UniversalPayment/Factory.php',
	'UniversalPayment\Core\Hooks'				  				    => 'system/modules/universalpayment/UniversalPayment/Core/Hooks.php',
	'UniversalPayment\Core\InsertTags'				  			    => 'system/modules/universalpayment/UniversalPayment/Core/InsertTags.php',
	'UniversalPayment\Core\ModuleFactory'				  		    => 'system/modules/universalpayment/UniversalPayment/Core/ModuleFactory.php',
	'UniversalPayment\Core\OrderFactory'				  		    => 'system/modules/universalpayment/UniversalPayment/Core/OrderFactory.php',
	'UniversalPayment\Core\PostSale' 					  		    => 'system/modules/universalpayment/UniversalPayment/Core/PostSale.php',
	'UniversalPayment\Core\PreSale' 					  		    => 'system/modules/universalpayment/UniversalPayment/Core/PreSale.php',
	
	'UniversalPayment\Dca\DcaManagement' 					  	    => 'system/modules/universalpayment/UniversalPayment/Dca/DcaManagement.php',
	
	'UniversalPayment\Backend\TableModule' 					  	    => 'system/modules/universalpayment/UniversalPayment/Backend/TableModule.php',
	
	'UniversalPayment\Frontend\Formfield\PaymentField'   		    => 'system/modules/universalpayment/UniversalPayment/Frontend/Formfield/PaymentField.php',
	'UniversalPayment\Frontend\Formfield\PaymentSelectMenu'   	    => 'system/modules/universalpayment/UniversalPayment/Frontend/Formfield/PaymentSelectMenu.php',
	
	'UniversalPayment\Frontend\Module\ModulePayment'   	 		    => 'system/modules/universalpayment/UniversalPayment/Frontend/Module/ModulePayment.php',
	
	'UniversalPayment\Frontend\ContentElement\contentPayment'   	=> 'system/modules/universalpayment/UniversalPayment/Frontend/ContentElement/ContentPayment.php',
	
	// Backwards compatibility from here
	'ContentUniversalPayment'   				 					=> 'system/modules/universalpayment/deprecated/ContentUniversalPayment.php',
	'ModuleUniversalPayment'   				 						=> 'system/modules/universalpayment/deprecated/ModuleUniversalPayment.php',
	'TableModuleUniversalPayment'   							    => 'system/modules/universalpayment/deprecated/TableModuleUniversalPayment.php',
	'UniversalPaymentDcaManagement'   							    => 'system/modules/universalpayment/deprecated/UniversalPaymentDcaManagement.php',
	'UniversalPaymentFactory'   								 	=> 'system/modules/universalpayment/deprecated/UniversalPaymentFactory.php',
	'UniversalPaymentField'   				 					    => 'system/modules/universalpayment/deprecated/UniversalPaymentField.php',
	'UniversalPaymentFieldSelectMenu'   						    => 'system/modules/universalpayment/deprecated/UniversalPaymentFieldSelectMenu.php',
	'UniversalPaymentHooks'   				 					    => 'system/modules/universalpayment/deprecated/UniversalPaymentHooks.php',
	'UniversalPaymentInsertTags'   								 	=> 'system/modules/universalpayment/deprecated/UniversalPaymentInsertTags.php',
	'UniversalPaymentModule'   				 					    => 'system/modules/universalpayment/deprecated/UniversalPaymentModule.php',
	'UniversalPaymentOrders'   				 					    => 'system/modules/universalpayment/deprecated/UniversalPaymentOrders.php',
	'UniversalPaymentPostSale'   								 	=> 'system/modules/universalpayment/deprecated/UniversalPaymentPostSale.php',
	'UniversalPaymentPreSale'   								 	=> 'system/modules/universalpayment/deprecated/UniversalPaymentPreSale.php',
	'UniversalPaymentInsertTags'   								 	=> 'system/modules/universalpayment/deprecated/UniversalPaymentInsertTags.php',
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'form_payment'         											=> 'system/modules/universalpayment/templates',
	'mod_payment'         											=> 'system/modules/universalpayment/templates',
));