<?php
/*

--
-- Table structure for table `ps_application_status`
--
--

CREATE TABLE IF NOT EXISTS `ps_application_status` (
  `id_application_status` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_application_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=5 ;

-- Table structure for table `ps_application_status_lang`

CREATE TABLE IF NOT EXISTS `ps_application_status_lang` (
  `id_application_status` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id_application_status`,`id_lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;




*/
class ApplicationStatusCore extends ObjectModel
{
 	/** @var string Name */
	public $name;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'application_status',
		'primary' => 'id_application_status',
		'multilang' => true,
		'fields' => array(
			// Lang fields
			'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
		),
	);
	
	public function add($autodate = true, $null_values = true)
	{
		$success = parent::add($autodate, $null_values);
		return $success;
	}	
	
	
	/**
	* Get all available types
	*
	* @return array types
	*/
	public static function getApplicationStatuses($id_lang = null)
	{
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT aps.`id_application_status`, apsl.`name` AS name
		FROM `'._DB_PREFIX_.'application_status` aps
		LEFT JOIN `'._DB_PREFIX_.'application_status_lang` apsl ON (aps.`id_application_status` = apsl.`id_application_status` AND apsl.`id_lang` = '.(int)$id_lang.')
		ORDER BY aps.`id_application_status` ASC');
	}

	/**
	* Get the current type name
	*
	* @return string Type
	*/
	public static function getApplicationStatus($id_application_status, $id_lang = null)
	{
		if (!$id_lang)
			$id_lang = Configuration::get('PS_LANG_DEFAULT');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT apsl.`name`
			FROM `'._DB_PREFIX_.'application_status` aps
			LEFT JOIN `'._DB_PREFIX_.'id_application_status_lang` apsl ON (aps.`id_application_status` = apsl.`id_application_status`)
			WHERE aps.`id_application_status` = '.(int)$id_application_status.'
			AND apsl.`id_lang` = '.(int)$id_lang
		);
	}
	
	public static function getApplicationStatusIdByName($id_lang = null, $status_name)
	{
		if (!$id_lang)
			$id_lang = Configuration::get('PS_LANG_DEFAULT');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT aps.`id_application_status`
			FROM `'._DB_PREFIX_.'application_status` as
			LEFT JOIN `'._DB_PREFIX_.'id_application_status_lang` apsl ON (aps.`id_application_status` = apsl.`id_application_status`)
			WHERE apsl.`name` = "'.$status_name.'"
			AND apsl.`id_lang` = '.(int)$id_lang
		);
	}
}


