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
 * Globals
 */
if(!is_array($GLOBALS['UNIVERSALPAYMENT']['PAYMENT_METHOD']))
{
	$GLOBALS['UNIVERSALPAYMENT']['PAYMENT_METHOD'] = array();
	$GLOBALS['UNIVERSALPAYMENT']['PAYMENT_MOD'] = array();
	$GLOBALS['UNIVERSALPAYMENT']['PAYMENT_FFD'] = array();
	$GLOBALS['UNIVERSALPAYMENT']['PAYMENT_CTE'] = array();
}

// Reference array for table selection by type of an contao payment element
$GLOBALS['UNIVERSALPAYMENT']['typeToTableReference'] = array
(
	'mod'	=> 'tl_module',
	'ffd'	=> 'tl_form_field',
	'cte'	=> 'tl_content'
);

// payment process status reference
$GLOBALS['UNIVERSALPAYMENT']['paymentProcessStatus'] = array
(
	0	=> 'CANCELED',
	1	=> 'IN_PROGRESS',
	2	=> 'COMPLETE'
);

// length of uniqueID
$GLOBALS['UNIVERSALPAYMENT']['uniqueIdLength']		= 28;	// number of characters


/**
 * Form fields
 */
$GLOBALS['TL_FFL']['universalpayment'] 				= 'UniversalPayment\Frontend\Formfield\PaymentField';
$GLOBALS['TL_FFL']['universalpayment_select'] 		= 'UniversalPayment\Frontend\Formfield\PaymentSelectMenu';
	
/**
 * Frontend modules
 */
$GLOBALS['FE_MOD']['universalpayment_node']['universalpayment'] = 'UniversalPayment\Frontend\Module\ModulePayment';

/**
 * Content element
 */
$GLOBALS['TL_CTE']['universalpayment_node']['universalpayment'] = 'UniversalPayment\Frontend\ContentElement\ContentPayment';

/**
 * Hooks
 */
#$GLOBALS['TL_HOOKS']['generatePage'][] 			= array('UniversalPayment\Core\SessionFactory','removeSession');
$GLOBALS['TL_HOOKS']['processFormData'][] 			= array('UniversalPayment\Core\PreSale','storeFormSubmit');
$GLOBALS['TL_HOOKS']['generatePage'][] 				= array('UniversalPayment\Core\PreSale','run');
$GLOBALS['TL_HOOKS']['generatePage'][] 				= array('UniversalPayment\Core\PostSale','run');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] 		= array('UniversalPayment\Core\InsertTags','replaceTags');


/**
 * Autoload namespace classes and deprecated classes
 */
if (version_compare(VERSION, '3.0', '<'))
{
	/**
	 * Feed contao with deprecated class files from a folder that is not the root folder of this extension
	 * @param string
	 */
	function universalpayment_load_deprecated($baseDir='')
	{
		if(strlen($baseDir) < 1)
		{
			$baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'deprecated';
		}
		
		// scan folder for files
		$arrScan = scan($baseDir);
		if(count($arrScan) < 1)
		{
			return null;
		}
		
		foreach($arrScan as $strFile)
		{
			if (substr($strFile, 0, 1) == '.')
			{
				continue;
			}
			
			$strClass = str_replace('.'.pathinfo($strFile, PATHINFO_EXTENSION),'',$strFile);
			$strFilePath = str_replace(TL_ROOT, '', $baseDir).'/'.$strFile;
			
			// check in contao filecache if the class is known
			if(FileCache::getInstance('autoload')->$strClass = $strFilePath && FileCache::getInstance('autoload')->$strClass && FileCache::getInstance('classes')->$strClass)
			{
				continue;
			}
			
			if(file_exists($baseDir.'/'.$strFile) && is_readable($baseDir.'/'.$strFile) )
			{
				require_once($baseDir.'/'.$strFile);
				if (class_exists('Cache'))
				{
					Cache::getInstance()->{'classFileExists-' . $strClass} = true;
				}
				FileCache::getInstance('classes')->$strClass = true;
				FileCache::getInstance('autoload')->$strClass = str_replace(TL_ROOT, '', $baseDir).'/'.$strFile;
			}
		}
	}
	universalpayment_load_deprecated();
	
	function universalpayment_autoloader($strClass)
	{
		$strClass = ltrim($strClass, '\\');
		$fileName  = '';
		
		if ($lastNsPos = strripos($strClass, '\\'))
		{
			$namespace = substr($strClass, 0, $lastNsPos);
			$strClass = substr($strClass, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= $strClass . '.php';
		
		foreach (array(
				dirname(__DIR__),
				dirname(__DIR__) . DIRECTORY_SEPARATOR . 'deprecated' ,
			) as $baseDir)
		{
			$file = $baseDir . DIRECTORY_SEPARATOR . $fileName;
			$filePath = str_replace(TL_ROOT, '', $baseDir).'/'.$fileName;
			
			if ( file_exists($file) && is_readable($file) )
			{
				require_once $baseDir . DIRECTORY_SEPARATOR . $fileName;
				
				if (class_exists('Cache'))
				{
					Cache::getInstance()->{'classFileExists-' . $strClass} = true;
				}
				FileCache::getInstance('classes')->$strClass = true;
				FileCache::getInstance('autoload')->$strClass = $filePath;
				
				return true;
			}
		}
	}
	
	spl_autoload_register('universalpayment_autoloader', true, true);
	spl_autoload_register('__autoload', true);
}
