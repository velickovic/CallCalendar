<?php
class CallControllerCore extends FrontController
{
	public $php_self = 'call';
 	protected $call;

	public function setMedia()
	{
		parent::setMedia();

		$this->addjqueryPlugin('metadata');
		$this->addjqueryPlugin('tablesorter');
	}

	public function canonicalRedirection($canonicalURL = '')
	{
	}
	
	public function initContent()
	{
		parent::initContent();

		$id = Tools::getValue('id');
		
        
		$object = Call::getCallById($id);
		
        
		$this->context->smarty->assign(array(
			'id' => Context::getContext()->shop->id,
			'call' => $object,
			'base_url' => __PS_BASE_URI__,
		));

		$this->setTemplate(_PS_THEME_DIR_.'call.tpl');
		
	}
}


