<?php
/**
* 2022 Anvanto
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Anvanto <anvantoco@gmail.com>
*  @copyright 2022 Anvanto
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

require_once _PS_MODULE_DIR_ . 'an_recurringpayments/classes/AnRecurringPayments.php';

class AdminAnRecurringPaymentsController extends AdminController
{
    protected $position_identifier = 'id_an_rps_recurringpayment';

    public function __construct()
    {
        if (!Tools::getIsset('id_an_rps_recurringpayment')) {
            $this->bootstrap = true;
        }

        $this->table = 'an_rps_recurringpayment';
        $this->identifier = 'id_an_rps_recurringpayment';
        $this->className = 'AnRecurringPayments';
        $this->lang = false;
        $this->addRowAction('view');
        $this->addRowAction('delete');

        $this->_select = ' cu.*, p.name as product_name, pl.name as period ';

        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ .
            'cart` ca ON (ca.`id_cart` = a.`id_cart`)
            LEFT JOIN `' . _DB_PREFIX_ .
            'customer` cu ON (ca.`id_customer` = cu.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ .
            'product_lang` p ON (p.`id_product` = a.`id_product` 
                AND ca.`id_shop` = p.`id_shop` 
                AND p.`id_lang` = ' . (int)Context::getContext()->language->id . ')
            LEFT JOIN `' . Module::getInstanceByName('an_recurringpayments')->getPrefix() .
            'period_lang` pl ON (pl.`id_an_rps_period` = a.`id_period` 
            	AND pl.`id_lang` = ' . (int)Context::getContext()->language->id . ')
        ';

        parent::__construct();

        $this->fields_list = array(
            'id_an_rps_recurringpayment' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 30),
            'product_name' => array('title' => $this->l('Product Name'), 'width' => 70),
            'qty' => array('title' => $this->l('Qty'), 'width' => 70),
            'firstname' => array('title' => $this->l('First Name'), 'width' => 70),
            'lastname' => array('title' => $this->l('Last Name'), 'width' => 70),
            'period' => array('title' => $this->l('Period'), 'width' => 70),
            'start_date' => array('title' => $this->l('First Delivery'), 'width' => 50, 'type' => 'date'),
            'status' => array(
                'title' => $this->l('Status'),
                'width' => 25,
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false
            )
        );

        $this->bootstrap = true;
    }
	
    public function renderList()
    {
		return Module::getInstanceByName('an_recurringpayments')->topNav() . parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        if (array_key_exists('new', $this->toolbar_btn)) {
            unset($this->toolbar_btn['new']);
        }
    }

    public function renderView()
    {
        $this->initToolbar();
        if (!$obj = $this->loadObject(true)) {
            return;
        }

        $this->context->smarty->assign('an_obj', $obj);
        return parent::renderView()
            . Module::getInstanceByName('an_recurringpayments')
                ->display('an_recurringpayments', 'views/templates/admin/recurringpayment.tpl');
    }
}
