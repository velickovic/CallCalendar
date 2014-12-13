<?php
class AdminDeadlinesControllerCore extends AdminController
{
		
	protected $calls_array = array();

	public function __construct()
	{
		$this->table = 'deadline';
		$this->className = 'Deadline';
		$this->lang = true;
		//$this->addRowAction('view');
		$this->addRowAction('edit');
		$this->addRowAction('delete');
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
		$this->multishop_context = Shop::CONTEXT_ALL;

		$this->context = Context::getContext();

		$calls = Call::getCalls($this->context->language->id);

		if (!$calls)
			$this->errors[] = Tools::displayError('No calls');
		else
			{
			foreach ($calls as $call)
				$this->calls_array[$call['title']] = $call['title'];
			
			}
	
		$this->fields_list = array(
			'id_deadline' => array(
				'title' => $this->l('ID'),
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 'auto',
				'filter_key' => 'b!name'
			),	
			'call_title' => array(
				'title' => $this->l('Call'),
				'type'  => 'select',
				'list' => $this->calls_array,
				'filter_key' => 'cl!title',
				'width' => 'auto'
			) //TODO add types and / or dates
			
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
	 	$this->_select = 'b.name as name, cl.`title` AS call_title';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'call` c ON a.`id_call` = c.`id_call`
		LEFT JOIN `'._DB_PREFIX_.'call_lang` cl ON (cl.`id_call` = c.`id_call` AND cl.`id_lang` = '.(int)$this->context->language->id.')'; //TO DO add funding agency
		$this->_orderBy="name";
	 	return parent::renderList();
	}

	public function renderForm()
	{
		if (!($deadline = $this->loadObject(true)))
			return;
		
		$calls = Call::getCalls($this->context->language->id);
		
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('deadlines'),
				'image' => '../img/admin/date.png'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 100,
					'required' => true,
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),					
				array(
					'type' => 'select',
					'label' => $this->l('Call:'),
					'name' => 'id_call',
					'required' => true,
					'options' => array(
						'query' => $calls,
						'id' => 'id_call',
						'name' => 'title',
						'default' => array(
							'value' => '',
							'label' => $this->l('-- Choose --')
						)
					)
				),
				array(
					'type' => 'datetime',
					'label' => $this->l('Select deadline date:'),
					'name' => 'deadline',
					'size' => 20,
					'required' => true
				),	
				array(
					'type' => 'radio',
					'label' => $this->l('Type of deadline:'),
					'name' => 'type',
					'required' => true,
					'class' => 't',
					'is_bool' => false,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Internal')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('External')
						)
					)
				)
				
			)
		);

		// foreach ($this->_languages as $language)
		// {
		// 	$this->fields_value['description_'.$language['id_lang']] = htmlentities(stripslashes($this->getFieldValue(
		// 		$call,
		// 		'description',
		// 		$language['id_lang']
		// 	)), ENT_COMPAT, 'UTF-8');

		// 	$this->fields_value['requirements_'.$language['id_lang']] = htmlentities(stripslashes($this->getFieldValue(
		// 		$call,
		// 		'requirements',
		// 		$language['id_lang']
		// 	)), ENT_COMPAT, 'UTF-8');
		// }
		
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


