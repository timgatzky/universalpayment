<?php

/**
 * Deprecated files for contao2
 * Load namespace class file and set fallback class alias
 */
$strClass = 'UniversalPayment\Frontend\Formfield\PaymentField';
$strFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('\\', '/', $strClass) . '.php';
if(file_exists(TL_ROOT.'/'.$strFile) && is_readable(TL_ROOT.'/'.$strFile))
{
	require_once(TL_ROOT.'/'.$strFile);
	class_alias($strClass,'UniversalPaymentField');
	if(class_exists('Cache'))
	{
		Cache::getInstance()->{'classFileExists-' . $strClass} = true;			
	}
	FileCache::getInstance('classes')->$strClass = true;
	FileCache::getInstance('autoload')->$strClass = str_replace(TL_ROOT, '', $strFile);
}