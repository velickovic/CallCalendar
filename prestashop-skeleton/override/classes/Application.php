<?php
/*
CREATE TABLE IF NOT EXISTS `ps_project` (
  `id_project` int(11) NOT NULL AUTO_INCREMENT,
  `id_project_status` int(11) DEFAULT NULL,
  `id_project_type` int(11) DEFAULT NULL,
  `registry_number` varchar(128) COLLATE utf8_swedish_ci DEFAULT NULL,
  `url` varchar(128) COLLATE utf8_swedish_ci DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  PRIMARY KEY (`id_project`),
  KEY `FK_project_id_project_status` (`id_project_status`),
  KEY `FK_project_id_project_type` (`id_project_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `ps_project_lang` (
  `id_project` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_swedish_ci NULL,
  `acronym` varchar(32) COLLATE utf8_swedish_ci NULL,
  `keywords` text COLLATE utf8_swedish_ci,
  `partners` text COLLATE utf8_swedish_ci,
  `overview` text COLLATE utf8_swedish_ci,
  `results` text COLLATE utf8_swedish_ci,
  `future_work` text COLLATE utf8_swedish_ci,
  PRIMARY KEY (`id_project`,`id_lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

*/
class ApplicationCore extends ObjectModel
{
	public $id;
	
	public $id_application;

	public $id_call;

	public $id_application_status;

	public $id_project_type;

 	public $name;

 	public $money_requested;
	
 	public $mdhPartBudget;
	
	public $acronym;

	public $keywords;
	
	public $overview;

	public $url;

	/** @var string Object creation date */
	public $date_start;

	/** @var string Object last modification date */
	public $date_end;

	public $partnerBox;
	
	public $inputLeaders;
	public $inputMembers;
	public $inputAssociated;
	
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'application',
		'primary' => 'id_application',
		'multilang' => true,
		'fields' => array(
			'url' => 				array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isUrl', 'size' => 255),
			'id_call' => 	array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),
			'date_start' => 			array('type' => self::TYPE_DATE),
			'date_end' => 			array('type' => self::TYPE_DATE),
			'id_project_type' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),
			'id_application_status' => array('type' => self::TYPE_INT,  'required' => true,'validate' => 'isUnsignedId'),

			'money_requested' => array('type' => self::TYPE_INT,'validate' => 'isInt'),		
			'mdhPartBudget' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			// Lang fields
			'name' => 				array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255),
			'acronym' => 			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 32),
			'keywords' => 			array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isCleanHtml'),
			'overview' =>			array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isCleanHtml')
			
		),
	);

	public static function getApplications($id_lang = 0, $id_member = null, $id_status = null, $id_partner=null)
	{
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$sql = 'SELECT DISTINCT a.*, al.`name`, al.`acronym`, al.`keywords`, al.`overview`, ptl.`name` AS type, apsl.`name` AS status, cl.`title` AS call_title
		FROM `'._DB_PREFIX_.'application` a
		LEFT JOIN `'._DB_PREFIX_.'application_lang` AS al ON (a.`id_application` = al.`id_application` AND al.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'project_type` pt ON a.`id_project_type` = pt.`id_project_type`
		LEFT JOIN `'._DB_PREFIX_.'project_type_lang` ptl ON (ptl.`id_project_type` = pt.`id_project_type` AND ptl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'application_status` aps ON a.`id_application_status` = aps.`id_application_status`
		LEFT JOIN `'._DB_PREFIX_.'application_status_lang` apsl ON apsl.`id_application_status` = aps.`id_application_status`
		LEFT JOIN `'._DB_PREFIX_.'call` c ON a.`id_call` = c.`id_call`
		LEFT JOIN `'._DB_PREFIX_.'call_lang` cl ON (cl.`id_call` = c.`id_call` AND cl.`id_lang` = '.(int)$id_lang.')';		
		
		if($id_member)
		{
			$sql .= 'LEFT JOIN `'._DB_PREFIX_.'customer_application` AS ca ON (a.`id_application` = ca.`id_application`)';
		}
		
		if($id_partner)
		{
			$sql .='LEFT JOIN `'._DB_PREFIX_.'application_partner` ap ON ap.`id_application` = a.`id_application`';
		}
		
		$sql.='WHERE 1 ';
		
		if ($id_status)
		{
			$sql .= ' AND a.`id_application_status` = ' . (int)$id_status;
		}
		if ($id_member)
		{
			$sql .= ' AND ca.`id_customer` = "'. $id_member . '"';
		}
		
		if($id_partner)
		{
			$sql.=' AND ap.`id_partner`='.(int)$id_partner;
		}
		
		$sql .= ' ORDER BY ptl.`name` ASC';//psl.`name` ASC, 
		//echo $sql . '<br>'; 
		$applications = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		
		foreach ($applications as $k => $application)
			$applications[$k]['members'] = Application::getApplicationRelatedMembersById($application['id_application']); 
		
		return $applications;
	}
	
	// public static function getNumberOfProjects($id_lang = 0, $id_shop = null, $active = true)
	// {
	// 	if (!$id_lang)
	// 		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
	// 	$sql = 'SELECT DISTINCT p.*
	// 	FROM `'._DB_PREFIX_.'project` p
	// 	LEFT JOIN `'._DB_PREFIX_.'project_lang` AS pl ON (p.`id_project` = pl.`id_project` AND pl.`id_lang` = '.(int)$id_lang.')
	// 	LEFT JOIN `'._DB_PREFIX_.'project_status` ps ON p.`id_project_status` = ps.`id_project_status`
	// 	LEFT JOIN `'._DB_PREFIX_.'project_status_lang` psl ON psl.`id_project_status` = ps.`id_project_status`';		
		
	// 	if ($active)
	// 	{	
	// 		$sql.=' WHERE psl.name = ';
	// 		$sql.=' "active"';
	// 	}
		
	// 	if ($id_shop)
	// 	{
	// 		$sql .= ' AND p.id_project IN (
	// 		SELECT DISTINCT p.`id_project`
	// 		FROM `'._DB_PREFIX_.'project` p
	// 		LEFT OUTER JOIN `'._DB_PREFIX_.'project_initiative` AS pi ON (p.`id_project` = pi.`id_project`)
	// 		LEFT JOIN `'._DB_PREFIX_.'initiative` AS i ON (pi.`id_initiative` = i.`id_initiative`)
	// 		WHERE i.`id_shop` = '.(int)$id_shop.')';
	// 	}
	// 	//echo $sql . '<br>'; 
	// 	$projects = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		
	// 	return count($projects);
	// }
	
	public static function getApplicationById($id_application = null, $id_lang = 0)
	{
		if(!$id_application) $id_application=$this->id;
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$sql = '
		SELECT a.*, al.`name`, al.`acronym`, al.`keywords`, al.`overview`, ptl.`name` AS type, apsl.`name` AS status, cl.`title` AS call_name
		FROM `'._DB_PREFIX_.'application` a
		LEFT JOIN `'._DB_PREFIX_.'application_lang` AS al ON (a.`id_application` = al.`id_application` AND al.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'project_type` pt ON a.`id_project_type` = pt.`id_project_type`
		LEFT JOIN `'._DB_PREFIX_.'project_type_lang` ptl ON (ptl.`id_project_type` = pt.`id_project_type` AND ptl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'application_status` aps ON a.`id_application_status` = aps.`id_application_status`
		LEFT JOIN `'._DB_PREFIX_.'application_status_lang` apsl ON apsl.`id_application_status` = aps.`id_application_status`
		LEFT JOIN `'._DB_PREFIX_.'call` c ON a.`id_call` = c.`id_call`
		LEFT JOIN `'._DB_PREFIX_.'call_lang` cl ON (cl.`id_call` = c.`id_call` AND cl.`id_lang` = '.(int)$id_lang.')
		WHERE a.`id_application` = '.$id_application;

		$application = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

		$application['members'] = Application::getApplicationRelatedMembersById($id_application); 
		
		$application['partners'] = Application::getApplicationRelatedPartners($id_application); 

		return $application;
	}
	
	// public static function getProjectShop($id_project = null)
	// {
	// 	if(!$id_project) $id_project=$this->id;		
	// 	$sql = 'SELECT i.id_shop
	// 	FROM `'._DB_PREFIX_.'project` p
	// 	LEFT JOIN `'._DB_PREFIX_.'project_initiative` AS pi ON (p.`id_project` = pi.`id_project`)
	// 	LEFT JOIN `'._DB_PREFIX_.'initiative` AS i ON (pi.`id_initiative` = i.`id_initiative`)
	// 	WHERE i.`id_shop` > 0 and p.`id_project` = '.$id_project;
		
	// 	return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);	
	// }
	
	public static function getApplicationRelatedMembersById($id_application)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.*, rl.`name` AS role, ca.`id_application`
			FROM '._DB_PREFIX_.'customer c
			LEFT JOIN `'._DB_PREFIX_.'customer_application` AS ca ON c.`id_customer` = ca.`id_customer`
			LEFT JOIN `'._DB_PREFIX_.'role_lang` AS rl ON ca.`id_role` = rl.`id_role`
			WHERE ca.`id_application` = '.(int)$id_application.'
			ORDER BY c.`firstname`, c.`lastname`');
	}
	
	// public static function getProject($id_project=null, $id_lang = 0)
	// {
	// 	if(!$id_project) $id_project=$this->id;
	// 	if (!$id_lang)
	// 		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	// 	return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
	// 	SELECT DISTINCT p.*, pl.`name`, pl.`acronym`, pl.`keywords`, pl.`overview`, pl.`results`, pl.`future_work`, ptl.`name` AS type, psl.`name` AS status
	// 	FROM `'._DB_PREFIX_.'project` p
	// 	LEFT JOIN `'._DB_PREFIX_.'project_lang` AS pl ON (p.`id_project` = pl.`id_project` AND pl.`id_lang` = '.(int)$id_lang.')
	// 	LEFT JOIN `'._DB_PREFIX_.'project_type` pt ON p.`id_project_type` = pt.`id_project_type`
	// 	LEFT JOIN `'._DB_PREFIX_.'project_type_lang` ptl ON (ptl.`id_project_type` = pt.`id_project_type` AND ptl.`id_lang` = '.(int)$id_lang.')
	// 	LEFT JOIN `'._DB_PREFIX_.'project_status` ps ON p.`id_project_status` = ps.`id_project_status`
	// 	LEFT JOIN `'._DB_PREFIX_.'project_status_lang` psl ON psl.`id_project_status` = ps.`id_project_status`			
	// 	WHERE p.`id_project` = '.$id_project.'
	// 	ORDER BY ptl.`name`, p.`id_project` ASC');
	// }

	// public static function getProjectsExcludingSelfStatic($id_lang = 0, $id_project)
	// {
	// 	if (!$id_lang)
	// 		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	// 	return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
	// 	SELECT DISTINCT p.*, pl.`name`, pl.`acronym`, pl.`keywords`, pl.`overview`, pl.`results`, pl.`future_work`, ptl.`name` AS type, psl.`name` AS status
	// 	FROM `'._DB_PREFIX_.'project` p
	// 	LEFT JOIN `'._DB_PREFIX_.'project_lang` AS pl ON (p.`id_project` = pl.`id_project` AND pl.`id_lang` = '.(int)$id_lang.')
	// 	LEFT JOIN `'._DB_PREFIX_.'project_type` pt ON p.`id_project_type` = pt.`id_project_type`
	// 	LEFT JOIN `'._DB_PREFIX_.'project_type_lang` ptl ON (ptl.`id_project_type` = pt.`id_project_type` AND ptl.`id_lang` = '.(int)$id_lang.')
	// 	LEFT JOIN `'._DB_PREFIX_.'project_status` ps ON p.`id_project_status` = ps.`id_project_status`
	// 	LEFT JOIN `'._DB_PREFIX_.'project_status_lang` psl ON psl.`id_project_status` = ps.`id_project_status`			
	// 	WHERE p.`id_project` <> '.$id_project.'
	// 	ORDER BY ptl.`name`, p.`id_project` ASC');
	// }
	

	// public static function getProjectRelatedInitiativesById($id_project,$id_lang=null)
	// {
	// 	if (!$id_lang)
	// 		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
			
	// 		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
	// 		SELECT i.*, il.`name`, il.`acronym`, il.`focus`, il.`description`, itl.`name` AS initiative_type
	// 		FROM '._DB_PREFIX_.'project_initiative pi
	// 		LEFT JOIN `'._DB_PREFIX_.'initiative` AS i ON pi.`id_initiative` = i.`id_initiative`
	// 		LEFT JOIN `'._DB_PREFIX_.'initiative_lang` AS il ON (pi.`id_initiative` = il.`id_initiative` AND il.`id_lang` = '.(int)$id_lang.')
	// 		LEFT JOIN `'._DB_PREFIX_.'initiative_type_lang` AS itl ON (i.`id_initiative_type` = itl.`id_initiative_type` AND itl.`id_lang` = '.(int)$id_lang.')
	// 		WHERE pi.`id_project` = '.(int)$id_project);
	// }
		
	// public function getProjectRelatedInitiatives()
	// {
	// 	return Project::getProjectRelatedInitiativesById((int)$this->id,null);
	// }

	// public function getProjectRelatedFundingAgencies()
	// {
	// 	return FundingAgency::getProjectFundingAgencies((int)$this->id,null);
	// }
	
	public function getApplicationRelatedPartners($id_application = null, $id_lang = null)
	{
			if(!$id_application)
				$id_application=(int)$this->id;
			if (!$id_lang)
				$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT ap.`id_partner`, pl.`name`, pl.`acronym`, pl.`city`,  cl.`name` AS country, ptl.`name` AS type, p.url
			FROM `'._DB_PREFIX_.'application_partner` AS ap
			LEFT JOIN `'._DB_PREFIX_.'partner` p on ap.`id_partner` = p.`id_partner`
			LEFT JOIN `'._DB_PREFIX_.'partner_lang` AS pl ON (ap.`id_partner` = pl.`id_partner` AND pl.`id_lang` = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'partner_type_lang` AS ptl ON (p.`id_partner_type` = ptl.`id_partner_type` AND  ptl.`id_lang` = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'country_lang` AS cl ON (p.`id_country` = cl.`id_country` AND  cl.`id_lang` = '.(int)$id_lang.')
			WHERE ap.`id_application` = '.(int)$id_application.'
			ORDER BY ptl.`name` ASC, pl.`name` ASC');
	}
	

	// public static function getApplicationRelatedPartnersStatic($id_application , $id_lang = null)
	// {

	// }
	
	//TODO maybe implement later if needed
	// public static function getPartnerRelatedProjects($partner_id,$id_lang = 0)
	// {
	// 	if (!$id_lang)
	// 		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	// 	return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
	// 	SELECT DISTINCT p.*, pl.`name`, pl.`acronym`, pl.`keywords`, pl.`overview`, pl.`results`, pl.`future_work`, ptl.`name` AS type, psl.`name` AS status
	// 	FROM `'._DB_PREFIX_.'project` p
	// 	LEFT JOIN `'._DB_PREFIX_.'project_lang` AS pl ON (p.`id_project` = pl.`id_project` AND pl.`id_lang` = '.(int)$id_lang.')
	// 	LEFT JOIN `'._DB_PREFIX_.'project_type` pt ON p.`id_project_type` = pt.`id_project_type`
	// 	LEFT JOIN `'._DB_PREFIX_.'project_type_lang` ptl ON (ptl.`id_project_type` = pt.`id_project_type` AND ptl.`id_lang` = '.(int)$id_lang.')
	// 	LEFT JOIN `'._DB_PREFIX_.'project_status` ps ON p.`id_project_status` = ps.`id_project_status`
	// 	LEFT JOIN `'._DB_PREFIX_.'project_status_lang` psl ON psl.`id_project_status` = ps.`id_project_status`
	// 	LEFT JOIN `'._DB_PREFIX_.'project_partner` pp ON (pp.`id_project` = p.`id_project` AND pp.`id_partner`='.(int)$partner_id.')
	// 	ORDER BY ptl.`name`, p.`id_project` ASC');
	// }
	
	// public static function getInitiativeRelatedProjectsById($id_initiative, $id_lang = null)
	// {
	// 	if (!$id_lang)
	// 		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
			
	// 		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
	// 		SELECT p.*, pl.`name`, pl.`acronym`, pl.`keywords`, pl.`overview`, pl.`results`, pl.`future_work`, psl.`name` AS status
	// 		FROM '._DB_PREFIX_.'project_initiative pi
	// 		LEFT JOIN `'._DB_PREFIX_.'project` AS p ON pi.`id_project` = p.`id_project`
	// 		LEFT JOIN `'._DB_PREFIX_.'project_lang` AS pl ON (pi.`id_project` = pl.`id_project` AND pl.`id_lang` = '.(int)$id_lang.')
	// 		LEFT JOIN `'._DB_PREFIX_.'project_status` ps ON p.`id_project_status` = ps.`id_project_status`
	// 		LEFT JOIN `'._DB_PREFIX_.'project_status_lang` psl ON psl.`id_project_status` = ps.`id_project_status`
	// 		WHERE pi.`id_initiative` = '.(int)$id_initiative.'
	// 		ORDER BY psl.`name` ASC, pl.`name` ASC');
	// }
		
	// public function getInitiativeRelatedProjects($id_initiative)
	// {
	// 	return Project::getInitiativeRelatedProjectsById($id_initiative, $id_lang = null);
	// }
	
	// public static function getRelatedInitiativesByType($id_lang = null, $id_shop = null, $id_project, $type)
	// {
	// 	if (!$id_lang)
	// 		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	// 	$sql = '
	// 	SELECT DISTINCT i.*, il.`name`
	// 	FROM `'._DB_PREFIX_.'initiative` i
	// 	LEFT JOIN `'._DB_PREFIX_.'project_initiative` AS pi ON (i.`id_initiative` = pi.`id_initiative`)
	// 	LEFT JOIN `'._DB_PREFIX_.'initiative_lang` AS il ON (i.`id_initiative` = il.`id_initiative` AND il.`id_lang` = '.(int)$id_lang.')
	// 	LEFT JOIN `'._DB_PREFIX_.'initiative_type` it ON i.`id_initiative_type` = it.`id_initiative_type`
	// 	LEFT JOIN `'._DB_PREFIX_.'initiative_type_lang` itl ON (itl.`id_initiative_type` = it.`id_initiative_type` AND itl.`id_lang` = '.(int)$id_lang.')		
	// 	WHERE i.`active` = 1 
	// 	AND itl.`name` = "'.$type.'"'; 
		
	// 	if ($id_shop)
	// 	{
	// 		$sql .= ' AND i.id_initiative IN (
	// 		SELECT DISTINCT ir.`id_initiative_2`
	// 		FROM `'._DB_PREFIX_.'initiative_relationship` ir
	// 		LEFT JOIN `'._DB_PREFIX_.'initiative` AS i ON (ir.`id_initiative_1` = i.`id_initiative`)
	// 		WHERE i.`id_shop` = '.(int)$id_shop.')';
	// 	}
	// 	$sql .= ' AND pi.`id_project` = '.$id_project.' 
	// 	ORDER BY il.`name` ASC';
		
	// 	$result = Db::getInstance()->executeS($sql);
	// 	return $result;
	// }	

	
	public function add($autodate = true, $null_values = true)
	{
		$success = parent::add($autodate, $null_values);
		$this->updateApplicationRelationships($this->partnerBox);
		$this->updateStaff($this->inputLeaders, $this->inputMembers, $this->inputAssociated);
		
		return $success;
	}

	public function update($nullValues = false)
	{
		if (Context::getContext()->controller->controller_type == 'admin')
		{
			$this->updateApplicationRelationships($this->partnerBox);
			$this->updateStaff($this->inputLeaders, $this->inputMembers, $this->inputAssociated);
		}
		return parent::update(true);
	}
	public function delete()
	{
		if (parent::delete())
		{
			$this->cleanApplicationRelationships();
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customer_application` WHERE `id_application` = '.(int)$this->id);

			return true;
		}
		return false;
	}

	
	// public function cleanProjectInitiatives()
	// {
	// 	Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'project_initiative` WHERE `id_project` = '.(int)$this->id);
	// }
	
	// public function cleanProjectFundingAgencies()
	// {
	// 	Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'project_funding_agency` WHERE `id_project` = '.(int)$this->id);
	// }
	
	public function cleanApplicationPartners()
	{
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'application_partner` WHERE `id_application` = '.(int)$this->id);
	}
	
	//TODO usage line 394
	public function cleanApplicationRelationships()
	{
		
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'application_partner` WHERE `id_application` = '.(int)$this->id);


	}
	// public function updateProjectInitiatives($list)
	// {
	// 	$this->cleanProjectInitiatives();
	// 	if ($list && !empty($list))
	// 		$this->addProjectInitiatives($list);
	// }
	// public function updateProjectFundingAgencies($list)
	// {
	// 	$this->cleanProjectFundingAgencies();
	// 	if ($list && !empty($list))
	// 		$this->addProjectFundingAgencies($list);
	// }
	public function updateApplicationPartners($list)
	{
		$this->cleanApplicationPartners();
		if ($list && !empty($list))
			$this->addApplicationPartners($list);
	}

	//TODO change function parameters (see line 375)
	public function updateApplicationRelationships($partner_list)
	{
		$this->cleanApplicationRelationships();
		
		if ($partner_list && !empty($partner_list))
			$this->addApplicationPartners($partner_list);
	}
	
	// public function addProjectInitiatives($initiatives)
	// {
	// 	foreach ($initiatives as $initiative)
	// 	{
	// 		$row = array('id_project' => (int)$this->id, 'id_initiative' => (int)$initiative);
	// 		Db::getInstance()->insert('project_initiative', $row);
	// 	}
	// }
	
	// public function addProjectFundingAgencies($funding_agencies)
	// {
	// 	foreach ($funding_agencies as $funding_agency)
	// 	{
	// 		$row = array('id_project' => (int)$this->id, 'id_funding_agency' => (int)$funding_agency);
	// 		Db::getInstance()->insert('project_funding_agency', $row);
	// 	}
	// }
	
	public function addApplicationPartners($partners)
	{
		foreach ($partners as $partner)
		{
			$row = array('id_application' => (int)$this->id, 'id_partner' => (int)$partner);
			Db::getInstance()->insert('application_partner', $row);
		}
	}

	public static function getLeadersStatic($id_application)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.*, ca.`id_role`
			FROM '._DB_PREFIX_.'customer c
			LEFT JOIN `'._DB_PREFIX_.'customer_application` AS ca ON c.`id_customer` = ca.`id_customer`
			WHERE ca.`id_role` = 1 AND ca.`id_application` = '.(int)$id_application);
	}
	
	public function getLeaders()
	{
		return Application::getLeadersStatic((int)$this->id);
	}

	public static function getMembersStatic($id_application)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.*, ca.`id_role`
			FROM '._DB_PREFIX_.'customer c
			LEFT JOIN `'._DB_PREFIX_.'customer_application` AS ca ON c.`id_customer` = ca.`id_customer`
			WHERE ca.`id_role` = 2 AND ca.`id_application` = '.(int)$id_application);
	}
	
	public function getMembers()
	{
		return Application::getMembersStatic((int)$this->id);
	}

	public static function getAssociatedStatic($id_application)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.*, ca.`id_role`
			FROM '._DB_PREFIX_.'customer c
			LEFT JOIN `'._DB_PREFIX_.'customer_application` AS ca ON c.`id_customer` = ca.`id_customer`
			WHERE ca.`id_role` = 3 AND ca.`id_application` = '.(int)$id_application);
	}
	
	public function getAssociated()
	{
		return Application::getAssociatedStatic((int)$this->id);
	}	
	
	/**
	 * Update associated
	 */
	public function updateStaff($inputLeaders, $inputMembers, $inputAssociated)
	{
		$leaders[] =null;$members[] = null;$associated[] =null;
		if($inputLeaders){
		$exploded = explode('-', substr($inputLeaders, 0, -1));
		foreach ($exploded as $item)
			$leaders[] = $item;
		}
		if($inputMembers){
		$exploded = explode('-', substr($inputMembers, 0, -1));
		foreach ($exploded as $item)
			$members[] = $item;
		}
		if($inputAssociated){
		$exploded = explode('-', substr($inputAssociated, 0, -1));
		foreach ($exploded as $item)
			$associated[] = $item;			
		}		
		$this->cleanStaff();
		$this->addStaff($leaders, $members, $associated);
	}

	public function cleanStaff()
	{
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customer_application` WHERE `id_application` = '.(int)$this->id);
	}

	public function addStaff($leaders, $members, $associated)
	{
		if ($leaders && !empty($leaders)){
		foreach ($leaders as $leader)
		{
			if((int)$leader>0){
				$row = array('id_application' => (int)$this->id, 'id_customer' => (int)$leader, 'id_role' => 1);
				Db::getInstance()->insert('customer_application', $row);
				}
		}
		}
		if ($members && !empty($members)){
		foreach ($members as $member)
		{
		if((int)$member>0){
			$row = array('id_application' => (int)$this->id, 'id_customer' => (int)$member, 'id_role' => 2);
			Db::getInstance()->insert('customer_application', $row);
			}
		}
		}
		if ($associated && !empty($associated)){
		foreach ($associated as $person)
		{
		if((int)$person>0){
			$row = array('id_application' => (int)$this->id, 'id_customer' => (int)$person, 'id_role' => 3);
			Db::getInstance()->insert('customer_application', $row);
			}
		}
		}
	}

	public static function getFundingAgencyIdStatic($id_application) 
	{
		$sql = '
		SELECT c.`id_funding_agency`
		FROM `'._DB_PREFIX_.'application` a 
		LEFT JOIN `'._DB_PREFIX_.'call` c ON a.`id_call` = c.`id_call`
		WHERE a.`id_application` = '.$id_application.';'

		;

		return DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);//FIXME work only with applications with single funding agency which is ok for now

	}

	public static function getPartnersIdStatic($id_application) 
	{
		$sql = '
		SELECT ap.`id_partner`
		FROM `'._DB_PREFIX_.'application` a 
		LEFT JOIN `'._DB_PREFIX_.'application_partner` ap ON a.`id_application` = ap.`id_application`
		WHERE a.`id_application` = '.$id_application.';'

		;

		return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

	}

	public static function updateApplicationStatusToGrantedStatic($id_application) 
	{
		Db::getInstance()->update('application', array(
				'id_application_status' => 1
			), 'id_application = '.(int)$id_application);// id application status 1 has value 'Granted'
	}


	public static function createProject($id_application) 
	{

		$application = Application::getApplicationById($id_application);
		
		$project = new Project;
		
		$project->id_project_status = 2;//project status id 2 has value 'active'

		$project->id_project_type = $application['id_project_type'];

	 	$project->name = array((int)Configuration::get('PS_LANG_DEFAULT') => $application['name']);//TODO check if we must change ps_lang_default to context lang or smth like that ($this->context->language->id); same for all below

	 	$project->totalBudget = ((int)$application['money_requested'] + (int)$application['mdhPartBudget']);
		
	 	$project->mdhPartBudget = $application['mdhPartBudget'];
		
		$project->acronym = array((int)Configuration::get('PS_LANG_DEFAULT') => $application['acronym']);

		$project->keywords = array((int)Configuration::get('PS_LANG_DEFAULT') => $application['keywords']);
		
		$project->overview = array((int)Configuration::get('PS_LANG_DEFAULT') => $application['overview']);

		$project->url = $application['url'];
		

		/** @var string Object creation date */
		$project->date_start = $application['date_start'];

		/** @var string Object last modification date */
		$project->date_end = $application['date_end'];

		
		if(!$project->save()) {
			$project->add();	
		}

		$project->addStaffModified(Application::getLeadersStatic($id_application), Application::getMembersStatic($id_application), Application::getAssociatedStatic($id_application));
		
		$project->addProjectFundingAgencies(Application::getFundingAgencyIdStatic($id_application));

		// var_dump(Application::getPartnersIdStatic($id_application));
		// var_dump(Project::getLeadersStatic(336));

		$project->addProjectPartnersModified(Application::getPartnersIdStatic($id_application));


		Application::updateApplicationStatusToGrantedStatic($id_application);

		//create news
		$news = new NewsAndEvents;

		$news->title = array((int)Configuration::get('PS_LANG_DEFAULT') => $application['name']); //TODO probably change to some better title

		$news->id_news_and_events_type = 1; // id for news in news_and_events_type table

		$news->id_news_and_events_scope = 1; //od for public in news_and_events_scope table

		$news->content = 'automatically generated';

		$news->id_contact = 0; //TODO maybe change this



		if(!$news->save()) {
			$news->add();	
		}

		$news->addProjects(array('id_project' => $project->id ));


	}
	
	
}
