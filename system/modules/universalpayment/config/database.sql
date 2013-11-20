-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the Contao    *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_form_field`
-- 

CREATE TABLE `tl_form_field` (
	`universalPaymentMethod` varchar(64) NOT NULL default '',
	`universalPaymentSelectIsRadio` char(1) NOT NULL default '',
	`universalPaymentAddSettings` char(1) NOT NULL default '',
	`universalPaymentAddJumpTo` char(1) NOT NULL default '',
	`universalPaymentJumpTo` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_module`
--

CREATE TABLE `tl_module` (
	`universalPaymentMethod` varchar(64) NOT NULL default '',
	`universalPaymentTemplate` varchar(128) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_content`
--

CREATE TABLE `tl_content` (
	`universalPaymentMethod` varchar(64) NOT NULL default '',
	`universalPaymentTemplate` varchar(128) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_up_orders`
--

CREATE TABLE `tl_up_orders` (
  `id` int(10) unsigned NOT NULL auto_increment,
# tl_up_modules.id reference
  `pid` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `paymentData` blob NULL,
  `uniqueId` varchar(128) NOT NULL default '',
  `orderId` varchar(32) NOT NULL default '',
  `status` int(3) unsigned NOT NULL default '0',
  `method` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 
-- Table `tl_up_modules`
--

CREATE TABLE `tl_up_modules` (
  `id` int(10) unsigned NOT NULL auto_increment,
# id of referenced contao element
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `type` varchar(16) NOT NULL default '',
# table of referenced contao element
  `source` varchar(32) NOT NULL default '',
  `method` varchar(64) NOT NULL default '',
  `createTstamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

