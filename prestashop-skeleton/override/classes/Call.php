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
class CallCore extends ObjectModel
{
	public $id;
	
	public $id_call;

	public $id_call_status;

	public $id_call_type;

 	public $title;

 	public $budget;

 	public $planed_project_start;
	
 	public $repeating;
	
	public $url_to_call;

	public $description;

	public $keywords;
	
	public $requirements;
	
	public $id_funding_agency;
	
	public $name;

	public $deadlines; //TODO implement that later

	public $inputLeaders; //this is filed for call's contacts

	
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'call',
		'primary' => 'id_call',
		'multilang' => true,
		'fields' => array(
			'url_to_call' => 			array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isUrl', 'size' => 255),
			'id_call_status' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),
			'id_call_type' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),
			'id_funding_agency' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),

			
			'budget' => 	array('type' => self::TYPE_INT,'validate' => 'isInt'),
			'planed_project_start' => 	array('type' => self::TYPE_DATE),		
			'repeating' =>	array('type' => self::TYPE_BOOL), //TODO Validation???


			//Lang fields
			'title' => 				array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255),
			'description' =>		array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isCleanHtml'),
			'keywords' =>		array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isCleanHtml'),
			'requirements' =>		array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isCleanHtml'),
			// 'name' => 				array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255),

		),
	);

	public static function getCalls($id_lang = 0, $id_status = null, $id_funding_agency = null, $id_type = null, $id_contact = null)
	{
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$sql = 'SELECT DISTINCT c.*, cl.`title`, cl.`description`, cl.`keywords`, cl.`requirements`, ctl.`name` AS type, csl.`name` AS status, fal.`name` AS agency
		FROM `'._DB_PREFIX_.'call` c
		LEFT JOIN `'._DB_PREFIX_.'call_lang` AS cl ON (c.`id_call` = cl.`id_call` AND cl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'call_type` ct ON c.`id_call_type` = ct.`id_call_type`
		LEFT JOIN `'._DB_PREFIX_.'call_type_lang` ctl ON (ctl.`id_call_type` = ct.`id_call_type` AND ctl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'call_status` cs ON c.`id_call_status` = cs.`id_call_status`
		LEFT JOIN `'._DB_PREFIX_.'call_status_lang` csl ON (csl.`id_call_status` = cs.`id_call_status` AND csl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'funding_agency` fa ON c.`id_funding_agency` = fa.`id_funding_agency`
		LEFT JOIN `'._DB_PREFIX_.'funding_agency_lang` fal ON (fal.`id_funding_agency` = fa.`id_funding_agency` AND fal.`id_lang` = '.(int)$id_lang.')';		
		
		if($id_contact)

		{
			$sql .= 'LEFT JOIN `'._DB_PREFIX_.'call_contact` AS cc ON (c.`id_call` = cc.`id_call`)';
		}

		$sql.='WHERE 1 ';
		if ($id_status)
		{
			$sql .= ' AND c.`id_call_status` = ' . (int)$id_status;
		}
		if ($id_type)
		{
			$sql .= ' AND c.`id_call_type` = ' . (int)$id_type;
		}
		if ($id_funding_agency)
		{
			$sql .= ' AND c.`id_funding_agency` = ' . (int)$id_funding_agency;
		}
		
		if ($id_contact)

		{
			$sql .= ' AND cc.`id_customer` = "'. (int)$id_contact . '"';
		}
		$sql .= ' ORDER BY cl.`title` ASC';//psl.`name` ASC, 
		//echo $sql . '<br>'; 
		$calls = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		
		return $calls;
	}
	
	public static function getCallById($id_call = null, $id_lang = 0)
	{
		if(!$id_call) $id_call=$this->id;
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = '
		SELECT c.*, cl.`title`, cl.`description`, cl.`keywords`, cl.`requirements`, ctl.`name` AS type, csl.`name` AS status, fal.`name` AS agency
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

		$call['inputLeaders'] = Call::getCallRelatedMembersById($id_call); 

		return $call;
	}
	
	//we have no idea why we have it here :O
	public static function getCall($id_call=null, $id_lang = 0)
	{
		if(!$id_call) $id_call=$this->id;
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT c.*, cl.`title`, cl.`description`, cl.`keywords`, cl.`requirements`, ctl.`name` AS type, csl.`name` AS status, fal.`name` AS agency
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

	public static function getCallRelatedMembersById($id_call)

	{

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('

			SELECT c.*, cc.`id_call`

			FROM '._DB_PREFIX_.'customer c

			LEFT JOIN `'._DB_PREFIX_.'call_contact` AS cc ON c.`id_customer` = cc.`id_customer`

			WHERE cc.`id_call` = '.(int)$id_call.'

			ORDER BY c.`firstname`, c.`lastname`');

	}





	public function add($autodate = true, $null_values = true)

	{
		$success = parent::add($autodate, $null_values);
		$this->updateStaff($this->inputLeaders);
		return $success;
	}



	public function update($nullValues = false)

	{
		if (Context::getContext()->controller->controller_type == 'admin')

		{
			$this->updateStaff($this->inputLeaders);
			var_dump($this->inputLeaders);
		}
		return parent::update(true);

	}

	public function delete()

	{
		if (parent::delete())

		{
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'call_contact` WHERE `id_call` = '.(int)$this->id);
			return true;
		}

		return false;

	}



	public static function getMembersStatic($id_call)

	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('

			SELECT c.*
			FROM '._DB_PREFIX_.'customer c
			LEFT JOIN `'._DB_PREFIX_.'call_contact` AS cc ON c.`id_customer` = cc.`id_customer`
			WHERE cc.`id_call` = '.(int)$id_call);

	}



	public function getMembers()

	{
		return Call::getMembersStatic((int)$this->id);
	}



	public function updateStaff( $contact )

	{
		$contacts[] =null;
		if($contact){
		$exploded = explode('-', substr($contact, 0, -1));
		foreach ($exploded as $item)
			$contacts[] = $item;
		}
		$this->cleanStaff();
		$this->addStaff($contacts);
	}



	public function cleanStaff()

	{		
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'call_contact` WHERE `id_call` = '.(int)$this->id);
	}



	public function addStaff($contacts)

	{
		if ($contacts && !empty($contacts)){
		foreach ($contacts as $contact)
		{

			if((int)$contact>0){
				$row = array('id_call' => (int)$this->id, 'id_customer' => (int)$contact['id_customer']);
				Db::getInstance()->insert('call_contact', $row);
				}
		}
		}
	}



	public static function getContactsStatic($id_call)

	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.*
			FROM '._DB_PREFIX_.'customer c
			LEFT JOIN `'._DB_PREFIX_.'call_contact` AS cc ON c.`id_customer` = cc.`id_customer`
			WHERE cc.`id_call` = '.(int)$id_call);

	}

	

	public function getContacts()

	{
		return Call::getContactsStatic((int)$this->id);
	}



	public static function getCallsContacts($id_call, $id_lang = 0) 

	{
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = 'SELECT c.`id_customer`, `email`,`url_private`, `firstname`, `lastname`, `phone`, `room`, GROUP_CONCAT(pl.`name` order by pl.name separator ",") as title
			FROM `'._DB_PREFIX_.'customer` c
			LEFT JOIN `'._DB_PREFIX_.'customer_position` AS pcp ON (pcp.id_customer=c.id_customer and (pcp.date_end is null or pcp.date_end=0 or pcp.date_end>curdate()))
			LEFT JOIN `'._DB_PREFIX_.'position_lang` AS pl ON (pcp.`id_position` = pl.`id_position` AND pl.`id_lang` = '.(int)$id_lang.')';
		if($id_call)
			$sql .= 'LEFT JOIN `'._DB_PREFIX_.'call_contact` AS cc ON (c.`id_customer` = cc.`id_customer`)';
		$sql.='WHERE c.deleted!=1 ';
		if ($id_call)
		{
			$sql .= ' AND cc.`id_call` = '.(int)$id_call;
		}
		$sql .= ' GROUP BY c.id_customer';
        $sql.=' ORDER BY c.`firstname`, c.`lastname` ASC';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

	}
}
