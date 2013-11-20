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

#$GLOBALS['TL_DCA']['tl_form_field']['config']['onload_callback'][] 	 = array('UniversalPayment\Backend\TableFormfield', 'loadDefaultPalettes');
$GLOBALS['TL_DCA']['tl_form_field']['config']['onsubmit_callback'][] = array('UniversalPayment\Dca\DcaManagement', 'createReference');

/**
 * Selectors
 */
$GLOBALS['TL_DCA']['tl_form_field']['palettes']['__selector__'][] = 'universalPaymentAddSettings';
$GLOBALS['TL_DCA']['tl_form_field']['palettes']['__selector__'][] = 'universalPaymentAddJumpTo';

/**
 * Palettes
 * universalpayment
 */
$GLOBALS['TL_DCA']['tl_form_field']['palettes']['universalpayment'] = '{type_legend},type,name;{payment_legend},universalPaymentMethod;{payment_setting_legend};{expert_legend:hide}';


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_form_field']['subpalettes']['universalPaymentAddSettings'] = '';
$GLOBALS['TL_DCA']['tl_form_field']['subpalettes']['universalPaymentAddJumpTo'] = 'universalPaymentJumpTo';


/**
 * Palettes
 * universalpayment_select
 */
$GLOBALS['TL_DCA']['tl_form_field']['palettes']['universalpayment_select'] = '{type_legend},type,name,label;{expert_legend:hide},universalPaymentSelectIsRadio';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form_field']['fields']['universalPaymentMethod'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['universalPaymentMethod'],
	'exclude'                 => true,
	'inputType'               => 'radio',
	'options_callback'		  => array('UniversalPayment\Factory','getPaymentModules'),
	'eval'                    => array('includeBlankOption'=>true,'submitOnChange'=>true),
	'sql'               	  => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_form_field']['fields']['universalPaymentSelectIsRadio'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['universalPaymentSelectIsRadio'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array(),
	'sql'               	  => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_form_field']['fields']['universalPaymentAddSettings'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['universalPaymentAddSettings'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'load_callback'			  => array
	(
		array('UniversalPayment\Backend\TableFormfield', 'toggleAddSettings'),
	),
	'sql'               	  => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_form_field']['fields']['universalPaymentAddJumpTo'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['universalPaymentAddJumpTo'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'load_callback'			  => array
	(
		array('UniversalPayment\Backend\TableFormfield', 'toggleAddJumpTo'),
	),
	'sql'               	  => "char(1) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_form_field']['fields']['universalPaymentJumpTo'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['universalPaymentJumpTo'],
	'exclude'                 => true,
	'inputType'               => 'pageTree',
	'eval'                    => array('fieldType'=>'radio', 'tl_class'=>'clr'),
	'sql'               	  => "int(10) unsigned NOT NULL default '0'"
);

