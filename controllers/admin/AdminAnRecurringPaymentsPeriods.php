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

require_once _PS_MODULE_DIR_ . 'an_recurringpayments/classes/AnPeriod.php';

class AdminAnRecurringPaymentsPeriodsController extends ModuleAdminController
{
    protected $position_identifier = 'id_an_rps_period';

    public $module = null;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'an_rps_period';
        $this->identifier = 'id_an_rps_period';
        $this->className = 'anPeriod';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        $this->fields_list = array(
            'id_an_rps_period' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 30),
            'name' => array('title' => $this->l('Name'), 'width' => 300),
            'sort_order' => array('title' => $this->l('Sort Order'), 'width' => 300));

        $this->identifiersDnd = array('id_an_rps_period' => 'id_sslide_to_move');
    }
	
	
    public function renderList()
    {
		return $this->module->topNav() . parent::renderList();
    }

    public function renderForm()
    {
        $this->display = 'edit';
        $this->initToolbar();
        if (!$obj = $this->loadObject(true)) {
            return;
        }

        $weekdays = anPeriod::getWeekdays();
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('RECURRING PAYMENTS PERIOD'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'id' => 'name',
                    'lang' => true,
                    'required' => true,
                    'size' => 50,
                    'maxlength' => 50,
                    ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Customer Can Define Start Date:'),
                    'name' => 'can_define_start_date',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(array(
                        'id' => 'can_define_start_date_on',
                        'value' => 1), array(
                        'id' => 'can_define_start_date_off',
                        'value' => 0)
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Billing Frequency'),
                    'name' => 'billing_freq',
                    'id' => 'billing_freq',
                    'required' => true,
                    'size' => 4,
                    'maxlength' => 4,
                    ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Billing Period'),
                    'name' => 'billing_period',
                    'id' => 'billing_period',
                    'options' => array(
                        'query' => anPeriod::getDateType(),
                        'id' => 'name',
                        'name' => 'title',
                        )),
                array(
                    'type' => 'text',
                    'label' => $this->l('Maximum Billing Cycles'),
                    'name' => 'max_billing_cycles',
                    'id' => 'max_billing_cycles',
                    'required' => true,
                    'desc' => $this->l('0 - infinitely'),
                    'size' => 4,
                    'maxlength' => 4,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Allow Weekdays'),
                    'name' => 'allow_weekdays[]',
                    'id' => 'allow_weekdays',
                    'multiple' => true,
                    'required' => true,
                    'options' => array(
                        'query' => $weekdays,
                        'id' => 'name',
                        'name' => 'title',
                        )
                    ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Payment prior to Delivery'),
                    'name' => 'require_payment_before',
                    'id' => 'require_payment_before',
                    'required' => true,
                    'size' => 4,
                    'maxlength' => 4,
                    'desc' => $this->l('In days'),
                    ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Sort Order'),
                    'name' => 'sort_order',
                    'id' => 'sort_order',
                    'size' => 4,
                    'maxlength' => 4,
                    ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Apply to all products'),
                    'name' => 'apply_to_all_products',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(array(
                        'id' => 'apply_to_all_products_on',
                        'value' => 1),array(
                        'id' => 'apply_to_all_products_off',
                        'value' => 0)
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Recurring Payments Only to all products'),
                    'name' => 'onlyrecurring_to_all_products',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(array(
                        'id' => 'onlyrecurring_to_all_products_on',
                        'value' => 1), array(
                        'id' => 'onlyrecurring_to_all_products_off',
                        'value' => 0)
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Reduction (Percent)'),
                    'name' => 'reduction',
                    'id' => 'reduction',
                    'size' => 3,
                    'maxlength' => 3,
                    'desc' => $this->l('In the Reduction field, the discount for the product is set as a percentage, when choosing a rule that has a discount, a voucher for the product is created and applied'),
                ),
             ),
            'submit' => array('title' => $this->l('   Save   ')
        ));

        if (!is_null($obj->id)) {
            $this->fields_value['allow_weekdays[]'] = explode('-', $obj->allow_weekdays);
        }

        return $this->module->topNav() . parent::renderForm();
    }

    public function processSave()
    {
        if (Tools::getIsset('billing_freq')) {
            $billing_freq = (int)Tools::getValue('billing_freq');
            if ($billing_freq < 1) {
                $this->errors[] = $this->l('Billing Frequency should be more then 0');
            }
        }
        if (!Tools::getIsset('allow_weekdays')) {
                $this->errors[] = $this->l('Please select at least one weekday');
        }

        if (!Tools::getIsset('max_billing_cycles') || Tools::getValue('max_billing_cycles') == '') {
            $this->errors[] = $this->l('Please enter maximum billing cycles value');
        }

        if (Tools::getIsset('require_payment_before')) {
            $require_payment_before = (int)Tools::getValue('require_payment_before');
            if ($require_payment_before < 0) {
                $this->errors[] = $this->l('Please enter Payment prior to Delivery');
            }
        }

        if (!empty($this->errors)) {
            // if we have errors, we stay on the form instead of going back to the list
            $this->display = 'edit';
            return false;
        }

        parent::processSave();
        /*$prefix = an_recurringpayments::getPrefix();
        if (Tools::getValue('apply_to_all_products')) {
            $_products = Product::getProducts($this->context->language->id,0,0,  'id_product', 'ASC');
            $_data = array();
            $existing_products = Db::getInstance()->executeS('select * from ' . $prefix . 'recurringpayment_products');
            $existing_products = array_combine(array_column($existing_products, 'id_product'),$existing_products);
            foreach ($_products as $_product) {
                if (isset($existing_products[$_product['id_product']])){
                    $ids = explode('-',$existing_products[$_product['id_product']]['period_types']);
                    if (!isset(array_values($ids)[Tools::getValue('id_an_rps_period')])){
                       $ids[] = Tools::getValue('id_an_rps_period');
                    }
                    $_data[] = array(
                        'id_product' => (int)$_product['id_product'],
                        'id_shop' => $this->context->shop->id,
                        'period_types' => implode('-', $ids),
                        'status' => 1,
                        'only_recurringpayment' => Tools::getValue('onlyrecurring_to_all_products',false) ? 1 : 0,
                    );
                } else {
                    $_data[] = array(
                        'id_product' => (int)$_product['id_product'],
                        'id_shop' => $this->context->shop->id,
                        'period_types' => Tools::getValue('id_an_rps_period'),
                        'status' => 1,
                        'only_recurringpayment' => Tools::getValue('onlyrecurring_to_all_products',false) ? 1 : 0,
                    );
                }
            }
            Db::getInstance()->insert(
                an_recurringpayments::PREFIX . 'recurringpayment_products',
                $_data,
                true,
                false,
                Db::REPLACE
            );
        }*/
    }
}
