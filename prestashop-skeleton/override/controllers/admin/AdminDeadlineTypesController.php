<?php
class AdminDeadlineTypesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'type_of_deadline';
		$this->className = 'DeadlineType';
		$this->multishop_context = Shop::CONTEXT_ALL;
		$this->lang = true;
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowActionSkipList('delete', array(1));

		$this->bulk_actions = array(
			'delete' => array('text' => $this->l('Delete selected'), 
			'confirm' => $this->l('Delete selected items?'))
			);
	
		$this->fields_list = array(
			'id_type_of_deadline' => array(
						'title' => $this->l('ID'), 
						'align' => 'center', 
						'width' => 25
						),
			'name' => array('title' => $this->l('Name'))
			);
			
		$this->identifier = 'id_type_of_deadline';

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Deadline Types'),
				'image' => '../img/admin/table.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 33,
					'required' => true,
					'lang' => true,
				)
			),
			'submit' => array(
				'title' => $this->l('Save   '),
				'class' => 'button'
			)
		);

		$list_type_of_deadline = array();
		foreach (DeadlineType::getDeadlineTypes($this->context->language->id) as $type_of_deadline)
			$list_type_of_deadline[] = array('value' => $type_of_deadline['id_type_of_deadline'], 'name' => $type_of_deadline['name']);

		parent::__construct();
	}

}


