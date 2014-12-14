<?php
/*

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
class DeadlineCore extends ObjectModel
{
	public $id;

	public $id_deadline;
	
	public $id_call;

 	public $deadline; //date

 	public $type;

 	public $name;
	
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'deadline',
		'primary' => 'id_deadline',
		'multilang' => true,
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

			'id_call' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),
			'deadline' => 	array('type' => self::TYPE_DATE),		
			'type' =>	array('type' => self::TYPE_BOOL),


			//Lang fields
			'name' => 				array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255),
			

		),
	);

	public static function getDeadlines($id_lang = 0, $id_deadline = null)
	{
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$sql = 'SELECT DISTINCT d.*, dl.`name`, cl.`title` 
		FROM `'._DB_PREFIX_.'deadline` d
		LEFT JOIN `'._DB_PREFIX_.'deadline_lang` AS dl ON (d.`id_deadline` = dl.`id_deadline` AND dl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'call` c ON d.`id_call` = c.`id_call`
		LEFT JOIN `'._DB_PREFIX_.'call_lang` cl ON (d.`id_call` = cl.`id_call` AND cl.`id_lang` = '.(int)$id_lang.')';		
		
		$sql.='WHERE 1 ';
		if ($id_call)
		{
			$sql .= ' AND d.`id_call` = ' . (int)$id_status;
		}
		$sql .= ' ORDER BY cl.`title` ASC';//psl.`name` ASC, 
		//echo $sql . '<br>'; 
		$calls = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		
		return $calls;
	}


	
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
	
	public static function getDeadlineById($id_deadline = null, $id_lang = 0)
	{
		if(!$id_call) $id_call=$this->id;
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = 'SELECT DISTINCT d.*, dl.`name`, cl.`title` AS call
		FROM `'._DB_PREFIX_.'deadline` d
		LEFT JOIN `'._DB_PREFIX_.'deadline_lang` AS dl ON (d.`id_deadline` = dl.`id_deadline` AND dl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'call` c ON d.`id_call` = c.`id_call`
		LEFT JOIN `'._DB_PREFIX_.'call_lang` cl ON (c.`id_call` = cl.`id_call` AND cl.`id_lang` = '.(int)$id_lang.')	
		
		WHERE d.`id_deadline` = '.$id_deadline;

		$deadline = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

		return $deadline;
	}
	
	

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
	
	
}
