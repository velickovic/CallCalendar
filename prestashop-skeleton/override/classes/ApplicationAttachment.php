<?php
/*

DROP TABLE IF EXISTS `ps_deadline`;

CREATE TABLE `ps_deadline` (
  `id_deadline` int(11) NOT NULL AUTO_INCREMENT,
  `id_call` int(11) NOT NULL, 
  `deadline` datetime NOT NULL,
  `type` tinyint(1) NOT NULL, 
  PRIMARY KEY (`id_deadline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ps_deadline_lang`;

CREATE TABLE `ps_deadline_lang` (
  `id_deadline` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id_deadline`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ps_call` (
  `id_call` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_funding_agency` int(11) unsigned NOT NULL,
  `id_call_status` int(11) unsigned NOT NULL,
  `id_call_type` int(11) unsigned NOT NULL,
  `planed_project_start` datetime NOT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `repeating` tinyint(1) DEFAULT '0',
  `url_to_call` text,
  PRIMARY KEY (`id_Call`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ps_call_lang` (
  `id_call` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) unsigned NOT NULL,
  `title` text,
  `description` text,
  `requirements` text,
  PRIMARY KEY (`id_call`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

*/
class ApplicationAttachmentCore extends ObjectModel
{
	public $id_application_attachment;
	
	public $id_application;

	public $id_attachment;

	public $description;

	public $date_of_upload;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'application_attachment',
		'primary' => 'id_application_attachment',
		'multilang' => false,
		'fields' => array(
			'id_application' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),
			'id_attachment' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),
			'id_application_attachment' => array('type' => self::TYPE_INT,  'required' => false,'validate' => 'isUnsignedId'),
			'description' => 				array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'date_of_upload' => 	array('type' => self::TYPE_DATE,'validate' => 'isDate'),
		),
	);

		public static function getAttachments($id_lang = 0, $id_application, $include = true)
	{

		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		return Db::getInstance()->executeS('
			SELECT a.`id_attachment`, a.`file_name`, a.`file`, a.`mime`, al.`name`, al.`description`
			FROM '._DB_PREFIX_.'attachment a
			LEFT JOIN '._DB_PREFIX_.'attachment_lang al ON (a.id_attachment = al.id_attachment AND al.id_lang = '.(int)$id_lang.')
			WHERE a.id_attachment '.($include ? 'IN' : 'NOT IN').' (
				SELECT aa.id_attachment
				FROM '._DB_PREFIX_.'application_attachment aa
				WHERE id_application = '.(int)$id_application.'
			)'
		);
	}
	
}
