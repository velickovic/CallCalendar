<?php
/*

-- --------------------------------------------------------

--
-- Table structure for table `ps_project_type`
--
--

CREATE TABLE IF NOT EXISTS `ps_project_type` (
  `id_project_type` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_project_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=1 ;

-- Table structure for table `ps_project_type_lang`

CREATE TABLE IF NOT EXISTS `ps_project_type_lang` (
  `id_project_type` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id_project_type`,`id_lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;



*/
class CallTypeCore extends ObjectModel
{
 	/** @var string Name */
	public $name;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'call_type',
		'primary' => 'id_call_type',
		'multilang' => true,
		'fields' => array(
			// Lang fields
			'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
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
	public static function getCallTypes($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT ct.`id_call_type`, ctl.`name`
		FROM `'._DB_PREFIX_.'call_type` ct
		LEFT JOIN `'._DB_PREFIX_.'call_type_lang` ctl ON (ct.`id_call_type` = ctl.`id_call_type` AND ctl.`id_lang` = '.(int)$id_lang.')
		ORDER BY ct.`id_call_type` ASC');
	}

	/**
	* Get the current type name
	*
	* @return string Type
	*/
	public static function getCallType($id_call_type, $id_lang = null)
	{
		if (!$id_lang)
			$id_lang = Configuration::get('PS_LANG_DEFAULT');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT ctl.`name`
			FROM `'._DB_PREFIX_.'call_type` ct
			LEFT JOIN `'._DB_PREFIX_.'id_call_type_lang` ctl ON (ct.`id_call_type` = ctl.`id_call_type`)
			WHERE pt.`id_call_type` = '.(int)$id_call_type.'
			AND ctl.`id_lang` = '.(int)$id_lang
		);
	}
}


