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

$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] = array('UniversalPayment\Dca\DcaManagement', 'createReference');

/**
 * Palettes
 * tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['universalpayment'] = '{title_legend},type,headline;{payment_legend},universalPaymentMethod;{payment_setting_legend};{template_legend};{expert_legend:hide},guests,cssID,space';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['universalPaymentMethod'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['universalPaymentMethod'],
	'exclude'                 => true,
	'inputType'               => 'radio',
	'options_callback'		  => array('UniversalPayment\Factory','getPaymentModules'),
	'eval'                    => array('includeBlankOption'=>true,'submitOnChange'=>true),
	'sql'					  => "varchar(64) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['universalPaymentTemplate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['universalPaymentTemplate'],
	'default'				  => 'form_payment',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'		  => array('UniversalPayment\Dca\DcaManagement','getPaymentTemplates'),
	'eval'                    => array(),
	'sql'					  => "varchar(128) NOT NULL default ''",
);