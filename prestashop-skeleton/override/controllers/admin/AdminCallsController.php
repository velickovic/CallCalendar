<?php
class AdminCallsControllerCore extends AdminController
{
	/** @var array call_types list */		
	protected $call_types_array = array();
	protected $call_statuses_array = array();
	protected $funding_agenices_array = array();

	public function __construct()
	{
		$this->table = 'call';
		$this->className = 'Call';
		$this->lang = true;
		//$this->addRowAction('view');
		$this->addRowAction('edit');
		$this->addRowAction('delete');
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
		$this->multishop_context = Shop::CONTEXT_ALL;

		$this->context = Context::getContext();

		$call_types = CallType::getCallTypes($this->context->language->id);
		$call_statuses = CallStatus::getCallStatuses($this->context->language->id);
		$funding_agencies = FundingAgency::getFundingAgencies($this->context->language->id);

		if (!$call_types)
			$this->errors[] = Tools::displayError('No call type');
		else if(!$call_statuses)
			$this->errors[] = Tools::displayError('No call status');
		else if(!$funding_agencies)
			$this->errors[] = Tools::displayError('No funding agencies');
		else
			{
			foreach ($call_types as $call_type)
				$this->call_types_array[$call_type['name']] = $call_type['name'];
			
			foreach ($call_statuses as $call_status)
				$this->call_statuses_array[$call_status['name']] = $call_status['name'];

			foreach ($funding_agencies as $funding_agency)
				$this->funding_agenices_array[$funding_agency['name']] = $funding_agency['name'];
			
			}
	
		$this->fields_list = array(
			'id_call' => array(
				'title' => $this->l('ID'),
				'width' => 25
			),
			'title' => array(
				'title' => $this->l('Title'),
				'width' => 'auto',
				'filter_key' => 'b!title'
			),	
			'call_type' => array(
				'title' => $this->l('Type'),
				'type'  => 'select',
				'list' => $this->call_types_array,
				'filter_key' => 'ctl!name',
				'width' => 'auto'
			),
			'call_status' => array(
				'title' => $this->l('Status'),
				'type'  => 'select',
				'list' => $this->call_statuses_array,
				'filter_key' => 'csl!name',
				'width' => 'auto'
			),
			'funding_agency' => array(
				'title' => $this->l('Funding Agency'),
				'type'  => 'select',
				'list' => $this->funding_agenices_array,
				'filter_key' => 'fal!name',
				'width' => 'auto'
			)
			
        );

		parent::__construct();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryPlugin('fancybox');
		$this->addJqueryUi('ui.sortable');
        if ($this->display == 'edit' || $this->display == 'add' || $this->display == 'list')
		{
			$this->addjQueryPlugin(array('autocomplete'));
            // $this->addJS(array(_PS_JS_DIR_.'admin-projects1.js')); //this is causing problems; probably should delete it 
		}
	}

	public function initToolbar()
	{
		if ($this->display == 'add' || $this->display == 'edit')
			$this->toolbar_btn['save-and-stay'] = array(
				'short' => 'SaveAndStay',
				'href' => '#',
				'desc' => $this->l('Save and stay'),
				'force_desc' => true,
			);
		parent::initToolbar();
	}
	
	public function initProcess()
	{
		$this->id_object = Tools::getValue('id_'.$this->table);

		parent::initProcess();
	}
	
	public function renderList()
	{
	 	$this->_select = 'b.title as call_title,ctl.`name` AS call_type, csl.`name` AS call_status, fal.`name` AS funding_agency';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'call_type` ct ON a.`id_call_type` = ct.`id_call_type`
		LEFT JOIN `'._DB_PREFIX_.'call_type_lang` ctl ON (ctl.`id_call_type` = ct.`id_call_type` AND ctl.`id_lang` = '.(int)$this->context->language->id.')
		LEFT JOIN `'._DB_PREFIX_.'call_status` cs ON a.`id_call_status` = cs.`id_call_status`
		LEFT JOIN `'._DB_PREFIX_.'call_status_lang` csl ON (csl.`id_call_status` = cs.`id_call_status` AND csl.`id_lang` = '.(int)$this->context->language->id.')
		LEFT JOIN `'._DB_PREFIX_.'funding_agency` fa ON a.`id_funding_agency` = fa.`id_funding_agency`
		LEFT JOIN `'._DB_PREFIX_.'funding_agency_lang` fal ON (fal.`id_funding_agency` = fa.`id_funding_agency` AND fal.`id_lang` = '.(int)$this->context->language->id.')'; //TO DO add funding agency
		$this->_orderBy="call_title";
	 	return parent::renderList();
	}

	public function renderForm()
	{
		if (!($call = $this->loadObject(true)))
			return;

		$call_types = CallType::getCallTypes($this->context->language->id);
		
		$call_statuses = CallStatus::getCallStatuses($this->context->language->id);
		
		$funding_agencies = FundingAgency::getFundingAgencies($this->context->language->id);
		
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('calls'),
				'image' => '../img/admin/date.png'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Title:'),
					'name' => 'title',
					'size' => 100,
					'required' => true,
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description:'),
					'name' => 'description',
					'lang' => true,
					'cols' => 100,
					'rows' => 10,
					'class' => 'rte',
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type'=>'textarea',
					'label' => $this->l('Reqirements:'),
					'name'=>'requirements',
					'lang' => true,
					'cols' => 100,
					'rows' => 10,
					'class' => 'rte',
					'required' => false,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),					
				array(
					'type' => 'select',
					'label' => $this->l('Call Type:'),
					'name' => 'id_call_type',
					'required' => true,
					'options' => array(
						'query' => $call_types,
						'id' => 'id_call_type',
						'name' => 'name',
						'default' => array(
							'value' => '',
							'label' => $this->l('-- Choose --')
						)
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Call Status:'),
					'name' => 'id_call_status',
					'required' => true,
					'options' => array(
						'query' => $call_statuses,
						'id' => 'id_call_status',
						'name' => 'name',
						'default' => array(
							'value' => '',
							'label' => $this->l('-- Choose --')
						)
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Funding Agency:'),
					'name' => 'id_funding_agency',
					'required' => true,
					'options' => array(
						'query' => $funding_agencies,
						'id' => 'id_funding_agency',
						'name' => 'name',
						'default' => array(
							'value' => '',
							'label' => $this->l('-- Choose --')
						)
					)
				),
				array(
					'type' => 'date',
					'label' => $this->l('Select planned project start date:'),
					'name' => 'planed_project_start',
					'size' => 20,
					'required' => false
				),
				array(
					'type' => 'text',
					'label' => $this->l('Budget of the project:'),
					'name' => 'budget',
					'size' => 20,
					'required' => false,
					'desc'=>"Input the budget of the call as an integer value (not displayed on the web)"
				),
				array(
					'type' => 'text',
					'label' => $this->l('URL:'),
					'name' => 'url_to_call',
					'size' => 100,
					'required' => false,
					'hint' => "Url should start with http:// or https:// (e.g. http://www.mdh.se or http://mdh.se)."

				),		
				array(
					'type' => 'radio',
					'label' => $this->l('Repeating:'),
					'name' => 'repeating',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('True')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('False')
						)
					)
				)
				
			)
		);

		foreach ($this->_languages as $language)
		{
			$this->fields_value['description_'.$language['id_lang']] = htmlentities(stripslashes($this->getFieldValue(
				$call,
				'description',
				$language['id_lang']
			)), ENT_COMPAT, 'UTF-8');

			$this->fields_value['requirements_'.$language['id_lang']] = htmlentities(stripslashes($this->getFieldValue(
				$call,
				'requirements',
				$language['id_lang']
			)), ENT_COMPAT, 'UTF-8');
		}
		
		$this->fields_form['submit'] = array(
			'title' => $this->l('   Save   '),
			'class' => 'button'
		);

				
		return parent::renderForm();
	}
	
	
	/*public function renderView()
	{
		$this->context = Context::getContext();
		if (!($project = $this->loadObject(true)))
			return;
			
		$this->tpl_view_vars = array(
			'project' => $project->getProject($project->id,$this->context->language->id),
			'language' => $this->context->language,
			'partnerList' => $this->renderProjectPartnersList($project),
			'fundingAgenciesList' => $this->renderProjectFundingList($project) ,
			'initiativesList' => $this->renderProjectInitiativeList($project)
		);

		return parent::renderView();
	}
		
	public function renderProjectPartnersList($project){
	
	$partner_types_array=array();
		$partner_types = PartnerType::getPartnerTypes($this->context->language->id);
		if (!$partner_types)
			$this->errors[] = Tools::displayError('No partner types');
		else
			foreach ($partner_types as $partner_type)
				$partner_types_array[$partner_type['name']] = $partner_type['name'];
		

		$partner_fields_display = (array(
			'id_partner' => array('title' => $this->l('ID'),'width' => 25),
			'name' => array('title' => $this->l('Name'),'width' => 'auto'),	
			'acronym' => array('title' => $this->l('Acronym'),'width' => 25),
			'type' => array('title' => $this->l('Type'),'type'  => 'select','list' => $partner_types_array,'filter_key' => 'ptl!name','width' => 'auto'),		
			'city' => array('title' => $this->l('City'),'width' => 'auto'),
			'country' => array('title' => $this->l('Country'),'width' => 'auto')
		));

		$partner_list = Project::getProjectRelatedPartners($project->id);
if($partner_list ){
		$helper = new HelperList();
		$helper->currentIndex = Context::getContext()->link->getAdminLink('AdminPartners', false);
		$helper->token = Tools::getAdminTokenLite('AdminPartners');
		$helper->shopLinkType = '';
		$helper->table = 'partner';
		$helper->identifier = 'id_partner';
		$helper->actions = array('edit', 'view');
		$helper->show_toolbar = false;

		return $helper->generateList($partner_list, $partner_fields_display);
		}
		return "No related partners ";
	}
	public function renderProjectFundingList($project){
	

		$funding_fields_display = (array(
			'id_funding_agency' => array('title' => $this->l('ID'),'width' => 25),
			'name' => array('title' => $this->l('Name'),'width' => 'auto'),	
			'acronym' => array('title' => $this->l('Acronym'),'width' => '25'),	
			'url' => array('title' => $this->l('Url'),'width' => 'auto')
			));

		$funding_list = FundingAgency::getProjectFundingAgencies($project->id);
		if($funding_list){
		$helper = new HelperList();
		$helper->currentIndex = Context::getContext()->link->getAdminLink('AdminFundingAgencies', false);
		$helper->token = Tools::getAdminTokenLite('AdminFundingAgencies');
		$helper->shopLinkType = '';
		$helper->table = 'funding_agency';
		$helper->identifier = 'id_funding_agency';
		$helper->actions = array('edit', 'view');
		$helper->show_toolbar = false;

		return $helper->generateList($funding_list, $funding_fields_display);
		}
		return "No related funding agencies";
	}

	public function renderProjectInitiativeList($project){
	
		$initiative_types_array= array();
		$initiative_types = InitiativeType::getInitiativeTypes($this->context->language->id);
		if (!$initiative_types)
			$this->errors[] = Tools::displayError('No initiative type');
		else
			foreach ($initiative_types as $initiative_type)
				$initiative_types_array[$initiative_type['name']] = $initiative_type['name'];

		$initiative_fields_display = array(
			'id_initiative' => array('title' => $this->l('ID'),	'width' => 25),
			'name' => array('title' => $this->l('Name'),'width' => 'auto'),	
			'acronym' => array(	'title' => $this->l('Acronym'),'width' => '25'),	
			'initiative_type' => array('title' => $this->l('Type'),'type'  => 'select','list' => $initiative_types_array,'filter_key' => 'itl!name','width' => 'auto'),	
			'date_add' => array('title' => $this->l('Creation date'),'width' => 150,'type' => 'date','align' => 'right'),
			'active' => array('title' => $this->l('Enabled'),'width' => 70,'active' => 'status','type' => 'bool','align' => 'center','orderby' => false)
		);

		$initiative_list = Project::getProjectRelatedInitiativesById($project->id);
		if($initiative_list){
		$helper = new HelperList();
		$helper->currentIndex = Context::getContext()->link->getAdminLink('AdminInitiatives', false);
		$helper->token = Tools::getAdminTokenLite('AdminInitiatives');
		$helper->shopLinkType = '';
		$helper->table = 'initiative';
		$helper->identifier = 'id_initiative';
		$helper->actions = array('edit', 'view');
		$helper->show_toolbar = false;

		return $helper->generateList($initiative_list, $initiative_fields_display);
		}
		return "No related initiatives ".$project->id;
	}
*/

}


