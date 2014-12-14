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
class CallAttachmentCore extends ObjectModel
{
	//public $id;
	
	public $id_call;

	public $id_attachment;

	public $date_of_upload;
	
	public $id_call_attachment;

 	//public $title;

 	//public $description;
	
	//public $deadline; 

	// public $url;
	
	// public $registry_number;

	/** @var string Object creation date */
	// public $date_start;

	/** @var string Object last modification date */
	// public $date_end;

		/** @var string Friendly URL */
	// public $link_rewrite;
	
	// public $initiativeBox;
	// public $fundingAgencyBox;
	// public $partnerBox;
	
	// public $inputLeaders;
	// public $inputMembers;
	// public $inputAssociated;
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'call_attachment',
		'primary' => 'id_call_attachment',
		'multilang' => false,
		'fields' => array(
	/*		'url' => 			array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isUrl', 'size' => 255),
			'registry_number' => 	array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isString', 'size' => 255),
			'date_start' => 			array('type' => self::TYPE_DATE),
			'date_end' => 			array('type' => self::TYPE_DATE),
			'id_project_type' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),
			'id_project_status' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),

			'totalBudget' => array('type' => self::TYPE_INT,'validate' => 'isInt'),		
			'mdhPartBudget' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			// Lang fields
			'name' => 				array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255),
			'acronym' => 			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 32),
			'keywords' => 			array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isCleanHtml'),
			'overview' =>			array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isCleanHtml'),
			'results' =>			array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isCleanHtml'),
			'future_work' =>		array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isCleanHtml')*/


			//'url_to_call' => 			array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isUrl', 'size' => 255),
			'id_call' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),
			'id_attachment' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),
			'id_call_attachment' => array('type' => self::TYPE_INT,  'required' => false,'validate' => 'isUnsignedId'),

			
			'date_of_upload' => 	array('type' => self::TYPE_DATE,'validate' => 'isDate'),
			//'type' =>	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'), 


			//Lang fields
			//'title' => 				array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255),
			//'description' =>		array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isString'),
			//'name' =>		array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isString', 'required' => true, 'size' => 255),
			// 'name' => 				array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255),

		),
	);



	
/*	public static function getNumberOfProjects($id_lang = 0, $id_shop = null, $active = true)
	{
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$sql = 'SELECT DISTINCT p.*
		FROM `'._DB_PREFIX_.'project` p
		LEFT JOIN `'._DB_PREFIX_.'project_lang` AS pl ON (p.`id_project` = pl.`id_project` AND pl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'project_status` ps ON p.`id_project_status` = ps.`id_project_status`
		LEFT JOIN `'._DB_PREFIX_.'project_status_lang` psl ON psl.`id_project_status` = ps.`id_project_status`';		
		
		if ($active)
		{	
			$sql.=' WHERE psl.name = ';
			$sql.=' "active"';
		}
		
		if ($id_shop)
		{
			$sql .= ' AND p.id_project IN (
			SELECT DISTINCT p.`id_project`
			FROM `'._DB_PREFIX_.'project` p
			LEFT OUTER JOIN `'._DB_PREFIX_.'project_initiative` AS pi ON (p.`id_project` = pi.`id_project`)
			LEFT JOIN `'._DB_PREFIX_.'initiative` AS i ON (pi.`id_initiative` = i.`id_initiative`)
			WHERE i.`id_shop` = '.(int)$id_shop.')';
		}
		//echo $sql . '<br>'; 
		$projects = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		
		return count($projects);
	}
	*/
	/*
	public static function getCallById($id_call = null, $id_lang = 0)
	{
		if(!$id_call) $id_call=$this->id;
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = '
		SELECT c.*, cl.`title`, cl.`description`, cl.`requirements`, ctl.`name` AS type, csl.`name` AS status, fal.`name` AS agency
		FROM `'._DB_PREFIX_.'call` c
		LEFT JOIN `'._DB_PREFIX_.'call_lang` AS cl ON (c.`id_call` = cl.`id_call` AND cl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'call_type` ct ON c.`id_call_type` = ct.`id_call_type`
		LEFT JOIN `'._DB_PREFIX_.'call_type_lang` ctl ON (ctl.`id_call_type` = ct.`id_call_type` AND ctl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'call_status` cs ON c.`id_call_status` = cs.`id_call_status`
		LEFT JOIN `'._DB_PREFIX_.'call_status_lang` csl ON (csl.`id_call_status` = cs.`id_call_status` AND csl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'funding_agency` fa ON c.`id_funding_agency` = fa.`id_funding_agency`
		LEFT JOIN `'._DB_PREFIX_.'funding_agency_lang` fal ON (fal.`id_funding_agency` = fa.`id_funding_agency` AND fal.`id_lang` = '.(int)$id_lang.')	
		
		WHERE c.`id_call` = '.$id_call;

		$call = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

		return $call;
	}
	
	//we have no idea why we have it here :O
	public static function getCall($id_call=null, $id_lang = 0)
	{
		if(!$id_call) $id_call=$this->id;
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT c.*, cl.`title`, cl.`description`, cl.`requirements`, ctl.`name` AS type, csl.`name` AS status, fal.`name` AS agency
		FROM `'._DB_PREFIX_.'call` c
		LEFT JOIN `'._DB_PREFIX_.'call_lang` AS cl ON (c.`id_call` = cl.`id_call` AND cl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'call_type` ct ON c.`id_call_type` = ct.`id_call_type`
		LEFT JOIN `'._DB_PREFIX_.'call_type_lang` ctl ON (ctl.`id_call_type` = ct.`id_call_type` AND ctl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'call_status` cs ON c.`id_call_status` = cs.`id_call_status`
		LEFT JOIN `'._DB_PREFIX_.'call_status_lang` csl ON (csl.`id_call_status` = cs.`id_call_status` AND csl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'funding_agency` fa ON c.`id_funding_agency` = fa.`id_funding_agency`
		LEFT JOIN `'._DB_PREFIX_.'funding_agency_lang` fal ON (fal.`id_funding_agency` = fa.`id_funding_agency` AND fal.`id_lang` = '.(int)$id_lang.')			
		WHERE c.`id_call` = '.$id_call.'
		ORDER BY ctl.`name`, c.`id_call` ASC');
	}
*/
/* this will be modified later if we need it


	public static function getProjectsExcludingSelfStatic($id_lang = 0, $id_project)
	{
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT p.*, pl.`name`, pl.`acronym`, pl.`keywords`, pl.`overview`, pl.`results`, pl.`future_work`, ptl.`name` AS type, psl.`name` AS status
		FROM `'._DB_PREFIX_.'project` p
		LEFT JOIN `'._DB_PREFIX_.'project_lang` AS pl ON (p.`id_project` = pl.`id_project` AND pl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'project_type` pt ON p.`id_project_type` = pt.`id_project_type`
		LEFT JOIN `'._DB_PREFIX_.'project_type_lang` ptl ON (ptl.`id_project_type` = pt.`id_project_type` AND ptl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'project_status` ps ON p.`id_project_status` = ps.`id_project_status`
		LEFT JOIN `'._DB_PREFIX_.'project_status_lang` psl ON psl.`id_project_status` = ps.`id_project_status`			
		WHERE p.`id_project` <> '.$id_project.'
		ORDER BY ptl.`name`, p.`id_project` ASC');
	}
	*/
	

/*	
	we can use it if we change something in db

	public function add($autodate = true, $null_values = true)
	{
		$success = parent::add($autodate, $null_values);
		$this->updateProjectRelationships($this->initiativeBox,$this->fundingAgencyBox,$this->partnerBox);
		$this->updateStaff($this->inputLeaders, $this->inputMembers, $this->inputAssociated);
		
		return $success;
	}

	public function update($nullValues = false)
	{
		if (Context::getContext()->controller->controller_type == 'admin')
		{
			$this->updateProjectRelationships($this->initiativeBox,$this->fundingAgencyBox,$this->partnerBox);
			$this->updateStaff($this->inputLeaders, $this->inputMembers, $this->inputAssociated);
		}
		return parent::update(true);
	}
	public function delete()
	{
		if (parent::delete())
		{
			$this->cleanProjectRelationships();
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customer_project` WHERE `id_project` = '.(int)$this->id);

			return true;
		}
		return false;
	}
*/
		public static function getAttachments($id_lang, $id_call, $include = true)
	{
		return Db::getInstance()->executeS('
			SELECT *
			FROM '._DB_PREFIX_.'attachment a
			LEFT JOIN '._DB_PREFIX_.'attachment_lang al
				ON (a.id_attachment = al.id_attachment AND al.id_lang = '.(int)$id_lang.')
			WHERE a.id_attachment '.($include ? 'IN' : 'NOT IN').' (
				SELECT ca.id_attachment
				FROM '._DB_PREFIX_.'call_attachment ca
				WHERE id_call = '.(int)$id_call.'
			)'
		);
	}
	
}
