<?php
/*

--
-- Table structure for table `ps_project_status`
--
--

CREATE TABLE IF NOT EXISTS `ps_project_status` (
  `id_project_status` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_project_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=5 ;

-- Table structure for table `ps_project_status_lang`

CREATE TABLE IF NOT EXISTS `ps_project_status_lang` (
  `id_project_status` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id_project_status`,`id_lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;




*/
class CallStatusCore extends ObjectModel
{
 	/** @var string Name */
	public $name;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'call_status',
		'primary' => 'id_call_status',
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
	public static function getCallStatuses($id_lang = null)
	{
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT cs.`id_call_status`, csl.`name` AS name
		FROM `'._DB_PREFIX_.'call_status` cs
		LEFT JOIN `'._DB_PREFIX_.'call_status_lang` csl ON (cs.`id_call_status` = csl.`id_call_status` AND csl.`id_lang` = '.(int)$id_lang.')
		ORDER BY cs.`id_call_status` ASC');
	}

	/**
	* Get the current type name
	*
	* @return string Type
	*/
	public static function getCallStatus($id_call_status, $id_lang = null)
	{
		if (!$id_lang)
			$id_lang = Configuration::get('PS_LANG_DEFAULT');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT csl.`name`
			FROM `'._DB_PREFIX_.'call_status` cs
			LEFT JOIN `'._DB_PREFIX_.'id_call_status_lang` csl ON (cs.`id_call_status` = csl.`id_call_status`)
			WHERE cs.`id_call_status` = '.(int)$id_call_status.'
			AND csl.`id_lang` = '.(int)$id_lang
		);
	}
	
	public static function getCallStatusIdByName($id_lang = null, $status_name)
	{
		if (!$id_lang)
			$id_lang = Configuration::get('PS_LANG_DEFAULT');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT cs.`id_call_status`
			FROM `'._DB_PREFIX_.'call_status` cs
			LEFT JOIN `'._DB_PREFIX_.'id_call_status_lang` csl ON (cs.`id_call_status` = csl.`id_call_status`)
			WHERE csl.`name` = "'.$status_name.'"
			AND csl.`id_lang` = '.(int)$id_lang
		);
	}
}


