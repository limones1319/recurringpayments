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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'an_recurringpayments/classes/AnPeriod.php';
require_once _PS_MODULE_DIR_ . 'an_recurringpayments/classes/AnRecurringPayments.php';

class an_recurringpayments extends Module
{
    const PREFIX = 'an_rps_';

    protected $hooks = array(
        'displayHeader',
        'displayProductButtons',
        'displayProductAdditionalInfo',
        'displayCustomerAccount',
        'actionCartSave',
    //    'displayCart',
        'displayAdminOrder',
        'displayAdminProductsExtra',
        'actionProductUpdate',
        'actionOrderStatusUpdate',
        'displayCartExtraProductActions',
        'displayOrderDetail',
        'displayAdminOrderContentOrder',
        'displayPDFInvoice',
    );

    protected $tabs = array(
        array(
            'class_name' =>     'AdminAnRecurringPaymentsPeriods',
            'parent' =>         'AdminParentOrders',
            'name' =>           'Recurring Payment Periods'
        ),
        array(
            'class_name' =>     'AdminAnRecurringPayments',
            'parent' =>         'AdminParentOrders',
            'name' =>           'Recurring Payment Subscribers'
        ),
    );

    public function __construct()
    {
        $this->name = 'an_recurringpayments';
        $this->tab = 'front_office_features';
        $this->version = '1.4.4';
        $this->author = 'Anvanto';
        $this->module_key = '171ca24823e4647293261989d7af604a';
		$this->addons_product_id = '31143';
		$this->url_rate = '';
		$this->url_contact_us = '';			
		
        parent::__construct();

        $this->displayName = $this->l('Recurring Payments and Subscriptions');
        $this->description =
            $this->l('Create subscription products using flexible settings and sell them on a recurring basis');
        $this->new = version_compare(_PS_VERSION_, '1.6.9.9', '>') ?  true : false;
        $this->firsttime = true;
    }

    public static function getPrefix()
    {
        return _DB_PREFIX_ . self::PREFIX;
    }

    public function install()
    {
        if (parent::install()) {
            $sql = include(_PS_MODULE_DIR_ . $this->name . '/sql/install.php');
            foreach ($sql as $_sql) {
                Db::getInstance()->Execute($_sql);
            }

            $languages = Language::getLanguages();
            foreach ($this->tabs as $tab) {
                $_tab = new Tab();
                $_tab->class_name = $tab['class_name'];
                $_tab->id_parent = Tab::getIdFromClassName($tab['parent']);
                $_tab->module = $this->name;
                foreach ($languages as $language) {
                    $_tab->name[$language['id_lang']] = $this->l($tab['name']);
                }

                $_tab->add();
            }

            foreach ($this->hooks as $hook) {
                $this->registerHook($hook);
            }

            
        }

        return false;
    }

    public function uninstall()
    {
        if (parent::uninstall()) {
            $sql = include(_PS_MODULE_DIR_ . $this->name . '/sql/uninstall.php');
            foreach ($sql as $_sql) {
                Db::getInstance()->Execute($_sql);
            }

            foreach ($this->tabs as $tab) {
                $idTab = Tab::getIdFromClassName($tab['class_name']);
                if ($idTab) {
                    $_tab = new Tab($idTab);
                    $_tab->delete();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Metodă care este apelată când un utilizator anulează subscrierea din contul său.
     * Această metodă poate fi apelată fie printr-un hook Prestashop,
     * fie dintr-un controller custom, în funcție de implementarea voastră.
     *
     * @param int $id_customer ID-ul clientului care a inițiat anularea
     * @param int $vmid        ID-ul VM-ului care trebuie oprit/șters
     * @param string $node     Numele nodului Proxmox
     * @return bool
     */
    public function processCancelSubscription($id_customer, $vmid, $node)
    {
        // 1) Apelăm microserviciul pentru a opri VM-ul
        // ---------------------------------------------------
        $stopResult = $this->callStopVMService($vmid, $node);

        // 2) Ștergem intrarea din orchestrateproxmox (DB)
        // ---------------------------------------------------
        // Puteți apela direct un query, sau puteți apela o metodă
        // din orchestrateproxmox.php dacă preferați să țineți logica acolo.
        // Exemplu direct (folosind Db::getInstance()):
        $sql = 'DELETE FROM `'._DB_PREFIX_.'orchestrateproxmox`
                WHERE `id_customer` = '.(int)$id_customer.'
                  AND `vmid` = '.(int)$vmid.'
                  AND `node` = "'.pSQL($node).'"';
        Db::getInstance()->execute($sql);

        // Returnăm true dacă totul a decurs cu succes
        return true;
    }

    /**
     * Apel către microserviciu (endpoint /stop_vm) pentru a opri VM-ul.
     *
     * @param int $vmid
     * @param string $node
     * @return array|false Răspuns de la microserviciu sau false dacă eroare
     */
    private function callStopVMService($vmid, $node)
    {
        // Exemplu: folosim librăria native cURL din PHP
        // (puteți folosi și file_get_contents cu context stream sau Guzzle etc.)
        $url = 'https://microserviciu.tld/stop_vm'; // Schimbați cu URL-ul real
        $payload = array(
            'node' => $node,
            'vmid' => (int)$vmid
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            // Dacă avem eroare cURL
            curl_close($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            // Răspuns OK => parsăm JSON
            return json_decode($response, true);
        }

        return false;
    }
        
    public function getShopID()
    {
        return (Shop::getContextShopID() ? Shop::getContextShopID() : 1);
    }

    public function getProductData($id_product, $id_shop)
    {
        $row = Db::getInstance()->getRow('
            SELECT * FROM `' . pSQL(self::getPrefix()) . 'recurringpayment_products`
            WHERE `id_product` = ' . (int)$id_product . ' 
            AND `id_shop` = ' . (int)$id_shop . '
            AND `status` = 1');

        if (!$row){
            $row['period_types'] = '';
        }

        $periodtypesarr = explode('-', $row['period_types']);
        foreach ($periodtypesarr as $key => $value) {
            $periodtypesarr[$key] = (int)$value;
        }
        $iapin = implode(',', $periodtypesarr);
        if (!empty($iapin)) {
            $periods = Db::getInstance()->executeS('
            SELECT * FROM `' . pSQL(self::getPrefix()) . 'period`
            WHERE `id_an_rps_period` IN (' . pSQL($iapin) . ') OR `apply_to_all_products` = 1
            ORDER BY `sort_order` ASC');
        } else {
            $periods = Db::getInstance()->executeS('
            SELECT * FROM `' . pSQL(self::getPrefix()) . 'period`
            WHERE `apply_to_all_products` = 1
            ORDER BY `sort_order` ASC');
        }
        /*echo('<pre>');
        var_dump($row);
        var_dump($periods);
        die();*/
        if (count($periods)) {
            if(!$row || !array_key_exists('only_recurringpayment', $row)) {
                $row['only_recurringpayment'] = 0;
            }
            foreach ($periods as $period) {
                $period = new anPeriod($period['id_an_rps_period'], $this->context->language->id);
                if ($period->id) {
                    $row['periods'][] = $period;
                    if (
                        $period->onlyrecurring_to_all_products
                        && (!array_key_exists('only_recurringpayment',$row) || $row['only_recurringpayment'] == 0)
                    ) {
                        $row['only_recurringpayment'] = $period->onlyrecurring_to_all_products;
                    }
                }
            }
        }
        return $row;
    }

    public function deleterecurringpayment($id_cart, $id_product, $ipa)
    {
        $_sub = AnRecurringPayments::getInstance($id_cart, $id_product, $ipa);
        if ($_sub->id) {
            return $_sub->delete();
        }

        return false;
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if (array_key_exists('id_product', $params)) {
            $id_product = (int)$params['id_product'];
        } else {
            $id_product = Tools::getValue('id_product');
        }


        if ($id_product) {
            $stores = Shop::getContextListShopID();
            if (count($stores) > 1) {
                return $this->l('Please select shop!');
            }

            $helper = new HelperForm();
            $helper->base_folder =
                realpath($this->context->smarty->getTemplateDir(0) . '../../default/template') . '/';
            $helper->base_tpl = 'helpers/form/form.tpl';

            $helper->module = $this;
            $helper->title = $this->displayName;
            $helper->first_call = false;

            $this->fields_form[0]['form'] = array(
                'legend' => array('title' => $this->displayName/*, 'image' => $this->_path . 'logo.gif'*/),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Recurring Payments:'),
                        'name' => 'an_recurringpayments[status]',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(array(
                            'id' => 'status_on',
                            'value' => 1), array(
                            'id' => 'status_off',
                            'value' => 0)
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Recurring Payments Only:'),
                        'name' => 'an_recurringpayments[only_recurringpayment]',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(array(
                            'id' => 'only_recurringpayment_on',
                            'value' => 1), array(
                            'id' => 'only_recurringpayment_off',
                            'value' => 0)
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allowed Billing Periods:'),
                        'name' => 'an_recurringpayments[period_types][]',
                        'multiple' => true,
                        'size' => 7,
                        'style' => 'width:100px',
                        'options' => array(
                            'query' => new Collection('anPeriod', $this->context->language->id),
                            'id' => 'id_an_rps_period',
                            'name' => 'name',
                        ),
                    ),
                ),
                'submit' => array(
                    'name' => 'submitAddproductAndStay',
                    'title' => $this->l('   Save And Stay  '),
                ),
            );

            $shop = $this->getShopID();
            $product_data = $this->getProductData($id_product, $shop);

            $helper->fields_value['an_recurringpayments[status]'] = $product_data['status'];
            $helper->fields_value['an_recurringpayments[only_recurringpayment]']
                = $product_data['only_recurringpayment'];
            $helper->fields_value['an_recurringpayments[period_types][]'] = explode('-', $product_data['period_types']);

            $html = $helper->generateForm($this->fields_form);

            $replasefrom = array('< form ', ' < /form > ', 'panel');
            $replaseto = array('< div ', ' < /div > ', 'paneldef');
            $replasefrom = $this->trimspasesarray($replasefrom);
            $replaseto = $this->trimspasesarray($replaseto);

            return str_replace($replasefrom, $replaseto, $html);
        }

        return $this->l('Please save the product');
    }

    public function trimspasesarray($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = str_replace(' ', '', $value);
            if ($value == '< form ' || $value == '< div ') {
                $array[$key] = $array[$key] . ' ';
            }
        }
        return $array;
    }

    public function hookActionProductUpdate($params)
    {
        if (Tools::isSubmit($this->name)) {
            $id_product = (int)Tools::getValue('id_product');
            $id_shop = $this->getShopID();

            if ($id_product) {
                $postData = Tools::getValue('an_recurringpayments');

                if (array_key_exists('period_types', $postData)) {
                    $perionTypes = pSQL(implode('-', (array)$postData['period_types']));
                } else {
                    $perionTypes = '';
                }

                $data = array(
                    'id_product' => (int)$id_product,
                    'id_shop' => (int)$id_shop,
                    'period_types' => $perionTypes,
                    'status' => (int)$postData['status'],
                    'only_recurringpayment' => (int)$postData['only_recurringpayment'],
                );
                $result = Db::getInstance()->execute('DELETE FROM `'. pSQL(self::getPrefix()) . 'recurringpayment_products`' . '
                    WHERE id_product = ' . (int)$id_product . ' AND id_shop = ' . (int)$id_shop);
                $result &= Db::getInstance()->insert(
                    self::PREFIX . 'recurringpayment_products',
                    $data,
                    true,
                    false,
                    version_compare(_PS_VERSION_, '1.6.0.9', '>') ? Db::ON_DUPLICATE_KEY : Db::INSERT_IGNORE
                );
                return $result;
            }
        }
    }

    public function hookActionOrderStatusUpdate($params)
    {        
        if ($params['newOrderStatus']->id == Configuration::get('an_recurringpayments_status_activator', null, null, null,2)) {
            $id_cart = OrderCore::getCartIdStatic($params['id_order']);
            
            // OLD $params['cart']->id
            Db::getInstance()->execute('UPDATE `'. pSQL(self::getPrefix()).'recurringpayment` SET `status` = 1 WHERE `id_cart` = '.(int) $id_cart);

        }
    }

    public function hookActionValidateOrder($params)
    {
        if ($params['orderStatus']->id == Configuration::get('an_recurringpayments_status_activator', null, null, null,2)) {
            $id_cart = OrderCore::getCartIdStatic($params['id_order']);
            Db::getInstance()->execute('UPDATE `'. pSQL(self::getPrefix()).'recurringpayment` SET `status` = 1 WHERE `id_cart` = '.(int) $id_cart);
        }
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCss($this->_path . 'views/css/front/front.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/front/front.js');

        $this->context->controller->addJqueryUI(array('ui.datepicker'));

        $recurringpayments = AnRecurringPayments::getrecurringpaymentsByCartId($this->context->cart->id);

        foreach ($recurringpayments as &$sub) {
            $sub['period'] = new anPeriod($sub['id_period'], $this->context->language->id);
            $sub['displayDate'] = Tools::displayDate($sub['start_date']);
        }

        $this->context->smarty->assign('recurringpayments', $recurringpayments);

        if ($this->context->controller instanceof ProductController || $this->context->controller instanceof CategoryController) {
            $this->context->controller->addJS($this->_path . 'views/js/front/product.js');
        }

        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->hookDisplayProductButtons($params);
    }

    public function hookDisplayProductButtons($params)
    {
        $id_product = (is_object($params['product']) ? (int) $params['product']->id : (int) $params['product']['id_product']);

        $data = $this->getProductData($id_product, $this->getShopID());
        //echo('<pre>');var_dump($data);die();
        if ($data) {
            if (Tools::getValue('action') == 'quickview') {
                $this->context->smarty->assign('anHide_display', 'block');
            } else {
                $this->context->smarty->assign('anHide_display', 'none');
            }
            $this->context->smarty->assign('recurringpayment', $data);
            return $this->display(__FILE__, 'product_buttons.tpl');
        }

        return '';
    }

    public function hookActionCartSave($params)
    {
        if (!$this->firsttime) {
            return null;
        }
    //    $this->firsttime = false;
        if (Tools::getIsset('saverecurringpayment') || Tools::getIsset('delete')) {
            if ($this->new) {
                $id_product_attribute = Tools::getValue('group', false) ? Product::getIdProductAttributesByIdAttributes(
                    (int)Tools::getValue('id_product'),
                    Tools::getValue('group')
                ) : Tools::getValue('id_customization');
            } else {
                $id_product_attribute = Tools::getValue('ipa');
            }
            if (!$id_product_attribute) {
                $product = new Product(Tools::getValue('id_product'));
                if ($product->cache_default_attribute) {
                    $id_product_attribute = $product->cache_default_attribute;
                }
            }
            if (isset($params['cart'])) {
                $cart = $params['cart'];
            } elseif (isset($params['object'])) {
                $cart = $params['object'];
            } elseif (isset(Context::getContext()->cart->id)) {
                $cart = Context::getContext()->cart;
            } else {
                $this->firsttime = true;
                return null;
            }

            $data = array(
                'id_cart' =>                (int)$cart->id,
                'id_product' =>             (int)Tools::getValue('id_product'),
                'id_product_attribute' =>   (int)$id_product_attribute,
                'qty' =>                    (int)Tools::getValue('qty', 1),
                'id_period' =>              (int)(Tools::getValue('recurringpayment_period', false) ? Tools::getValue('recurringpayment_period') : Tools::getValue('period')),
                'start_date' =>             (Tools::getValue('recurringpayment_start_date', false) ? pSQL(trim(Tools::getValue('recurringpayment_start_date'))) : pSQL(trim(Tools::getValue('start_date')))),
                'status' =>                 0,
            );

            $_period = new anPeriod($data['id_period']);

            if ($_period->id) {
                if (empty($data['start_date'])) {
                    $time = time();
                    if ($_period->require_payment_before > 0) {
                        $time = strtotime('+' . (int)$_period->require_payment_before . ' days', $time);
                    }
                    $data['start_date'] = pSQL(date('Y-m-d', $time));
                }

                if (Tools::getIsset('saverecurringpayment') && Tools::getValue('recurringpayment_period') != '-1') {
                    $this->deleterecurringpayment($data['id_cart'], $data['id_product'], $data['id_product_attribute']);
                    Db::getInstance()->insert(self::PREFIX . 'recurringpayment', $data, true, false, Db::INSERT);
                    if ($this->firsttime) {
                        $this->createVoucher(Tools::getValue('id_product'), $_period->reduction, $this->context->cart->id);
                    }
                    $this->firsttime = false;
                }
            }

            if (Tools::getIsset('delete')) {
                $this->deleterecurringpayment($data['id_cart'], $data['id_product'], $data['id_product_attribute']);
                $this->checkAndDeleteVoucher($data['id_product'], $data['id_cart']);
            }
        }

        if (Tools::getIsset('paymentRecurring')) {
            $id_an_rps_recurringpayment = (int)Tools::getValue('id_an_rps_recurringpayment');
            $id_cart = Context::getContext()->cart->id;

            $_sub = new AnRecurringPayments($id_an_rps_recurringpayment);
            if ($_sub->id && $id_cart) {
                $data = array('id_cart' => (int)$id_cart, 'id_an_rps_recurringpayment' => (int)$_sub->id);
                Db::getInstance()->insert(self::PREFIX . 'recurringpayment_orders', $data, true, false, Db::REPLACE);
            }
        }
        if (Tools::getIsset('op')) {
            $qty = Tools::getValue('qty', 1);
            $direction = Tools::getValue('op', 'up');
            $this->updateProductQty($qty, $direction);
        }
    }

    public function updateProductQty($qty, $direction)
    {
        if (isset($params['cart'])) {
            $cart = $params['cart'];
        } elseif (isset($params['object'])) {
            $cart = $params['object'];
        } elseif (isset(Context::getContext()->cart->id)) {
            $cart = Context::getContext()->cart;
        }
        if ($this->new) {
            $id_product_attribute = false;
            if ((bool)Tools::getValue('id_product_attribute', false)) {
                $id_product_attribute = Tools::getValue('id_product_attribute', null);
            }
            if ((bool)Tools::getValue('group', false)) {
                $id_product_attribute = (int)Product::getIdProductAttributesByIdAttributes(
                    Tools::getValue('id_product'),
                    Tools::getValue('group')
                );
            }
            if (!$id_product_attribute) {
                $product = new Product(Tools::getValue('id_product'));
                if ($product->cache_default_attribute) {
                    $id_product_attribute = $product->cache_default_attribute;
                }
            }
        } else {
            $id_product_attribute = (bool)Tools::getValue('ipa', false) ? Tools::getValue('ipa', null) : Tools::getValue('id_product_attribute', null);
        }

        $db = Db::getInstance();
        $sql = '
			SELECT * FROM `' . pSQL(self::getPrefix()) . 'recurringpayment`
			WHERE `id_cart` ='. (int)$cart->id .'
			AND `id_product` = '. (int)Tools::getValue('id_product') .'
			AND `id_product_attribute` = '. (int)$id_product_attribute;
        $row = $db->getRow($sql);
        if (!$row) {
            return false;
        }

        if ($direction == 'up') {
            $row['qty'] += $qty;
        } else {
            $row['qty'] -= $qty;
            if($row['qty'] == 0) {
                $this->checkAndDeleteVoucher(Tools::getValue('id_product'), (int)$cart->id);
            }
        }


        if ($db->update(self::PREFIX . 'recurringpayment', array('qty' => (int)$row['qty']), 'id_an_rps_recurringpayment = \'' . (int)$row['id_an_rps_recurringpayment'] . '\'')) {
            return true;
        }

        return false;
    }
    public function hookDisplayCartExtraProductActions($params)
    {
        $this->smarty->assign('id_product', $params['product']['id_product']);
        $this->smarty->assign('id_product_attribute', $params['product']['id_product_attribute']);
        return $this->display(__FILE__, 'views/templates/hook/cart_extra.tpl');
    }

    public function hookDisplayCustomerAccount($params)
    {
        $this->smarty->assign(array(
            'module_path'=> $this->_path,
            'an_new' => $this->new
        ));
        return $this->display(__FILE__, 'views/templates/hook/customer_account.tpl');
    }

    public function hookDisplayAdminOrder($params)
    {
        $_order = new Order($params['id_order']);
        $order_date_add = strtotime($_order->date_add);

        $newSub = AnRecurringPayments::getNewOrdersByCartId($params['cart']->id);
        if (count($newSub)) {
            $this->context->smarty->assign(array(
                'new_sub' => $newSub,
            ));
        }

        $subOrders = AnRecurringPayments::getSubOrdersByCartId($params['cart']->id);
        if (count($subOrders)) {
            $this->context->smarty->assign(array(
                'an_sub' => $subOrders,
                'order_date_add' => $order_date_add,
            ));
        }

        return $this->display(__FILE__, 'views/templates/hook/admin_order.tpl');
    }

    /**
     * @param $params
     * @return string
     */
    public function hookDisplayOrderDetail($params)
    {
        if (!$this->new) {
            return $this->displayOrderDetail($params);
        }
    }

    /**
     * @param $params
     * @return string
     */
    public function hookDisplayAdminOrderContentOrder($params)
    {
        return $this->displayOrderDetail($params);
    }

    public function hookDisplayPDFInvoice($params)
    {
        return $this->displayOrderDetail($params);
    }

    /**
     * @param $params
     * @return string
     */
    public function displayOrderDetail($params)
    {
        //$id_cart = $email ? $params['cart']->id : $params['order']->id_cart;

        $_order = $params['order'];
        $order_date_add = strtotime($_order->date_add);

        $newSub = AnRecurringPayments::getNewOrdersByCartId($params['cart']->id);
        if (count($newSub)) {
            $this->context->smarty->assign(array(
                'new_sub' => $newSub,
            ));
        }

        $subOrders = AnRecurringPayments::getSubOrdersByCartId($params['cart']->id);
        if (count($subOrders)) {
            $this->context->smarty->assign(array(
                'an_sub' => $subOrders,
                'order_date_add' => $order_date_add,
            ));
            return $this->display(
                __file__,
                'show_product_recurrings.tpl'
            );
        }
    }

    public function getContent()
    {
        $this->bootstrap = true;
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->title = $this->displayName;
        $url = $this->context->link->getModuleLink($this->name, 'cron');

        $query = array();
        foreach(OrderState::getOrderStates($this->context->language->id) as $orderstate) {
            $query[] = array('name' => $orderstate['name'], 'value' => $orderstate['id_order_state']);
        }
        $this->fields_form[0]['form'] = array(
            'legend' => array('title' => $this->displayName),
            'input' => array(
                array(
                    'label' => $this->l('Cron URL:'),
                    'name' => 'cron_url',
                    'readonly' => true,
                    'type' => 'text',
                    'desc' => array(
                        $this->l('You need add this url to cron job once a day. Example:'),
                        '0 0 * * * /usr/bin/wget --spider \"' . $url . '\" >/dev/null 2>&1',
                        'If cton on your server can\'t use adress as target you can use dedicated cron service',
                        ', for example https://www.easycron.com/'
                    )
                ),
                array(
                    'type' => 'select',
                    'name' => 'an_recurringpayments_status_activator',
                    'class' => 'an_recurringpayments_status_activator',
                    'label' => $this->l('Status for activate recurring'),
                    'options' => array(
                        'query' => $query,
                        'id' => 'value',
                        'name' => 'name'
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        if (Tools::isSubmit('submitAddconfiguration')) {
            Configuration::updateValue(
                'an_recurringpayments_status_activator',
                Tools::getValue('an_recurringpayments_status_activator')
            );
        }
        $helper->fields_value['an_recurringpayments_status_activator'] = Configuration::get(
            'an_recurringpayments_status_activator',
                null,
                null,
                null,
                2
        );
        $helper->fields_value['cron_url'] = $url;
        return $this->topNav() . $helper->generateForm($this->fields_form);
    }

    public function checkAndDeleteVoucher($id_product, $id_cart = false)
    {
        global $cookie;

        $sql = '
			SELECT * FROM `' . _DB_PREFIX_ . 'cart_rule` cr';
        $id_cart = (int)$id_cart ? $id_cart : $this->context->cart->id;

        $sql .= '
            LEFT JOIN ' . _DB_PREFIX_ . 'cart_cart_rule ccr ON ccr.`id_cart_rule` = cr.`id_cart_rule`';

        $sql .= '
            WHERE cr.`reduction_product` = ' . (int)$id_product;

        $sql .= '
                AND ccr.`id_cart` = ' . $id_cart;

        $rows = Db::getInstance()->ExecuteS($sql);

        if (!count($rows)) {
            return;
        }

        foreach ($rows as $row) {
            $cart_rule = new CartRule($row['id_cart_rule']);
            $cart_rule->delete();
        }
    }

    public function createVoucher($id_product, $reduction = false, $id_cart = false)
    {
        global $cookie;
        if (!$reduction || $reduction < 0) {
            return;
        }

        if ($reduction > 100) {
            $reduction = 100;
        }
        $sql = '
			SELECT * FROM `' . _DB_PREFIX_ . 'cart_rule` cr';


        $sql .= '
            LEFT JOIN ' . _DB_PREFIX_ . 'cart_cart_rule ccr ON ccr.`id_cart_rule` = cr.`id_cart_rule`';

        $id_cart = (int)$id_cart ? $id_cart : $this->context->cart->id;
        $sql .= '
			WHERE cr.`reduction_product` = ' . (int)$id_product . '
			AND cr.`reduction_percent` = ' . (int)$reduction;

        $sql .= '
                AND ccr.`id_cart` = ' . $id_cart;

        $rows = Db::getInstance()->ExecuteS($sql);

        if (count($rows)) {
            return;
        }
        $title = "Voucher for subscribe (recurring payment)";

        $cart_rule = new CartRule();
        $cart_rule->product_restriction = true;
        $cart_rule->id_product = $id_product;
        $cart_rule->reduction_product = $id_product;
        $cart_rule->minimum_amount = 0;
        $cart_rule->reduction_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $cart_rule->reduction = $reduction;
        $cart_rule->reduction_percent = $reduction;
        $cart_rule->reduction_type = 'percentage';
        $cart_rule->quantity = 1;
        $cart_rule->highlight = 1;
        $cart_rule->quantity_per_user = 1;
        $cart_rule->reduction_tax = true;
        $cart_rule->id_cart = $id_cart ? $id_cart : $this->context->cart->id;

        $voucher_code = Tools::passwdGen(16, "NO_NUMERIC");
        while (CartRule::cartRuleExists($voucher_code)) {
            $voucher_code = Tools::passwdGen(16, "NO_NUMERIC");
        }
        $cart_rule->code = $voucher_code;

        if (isset($cookie->id_customer)) {
            $cart_rule->id_customer = isset($cookie->id_customer) ? $cookie->id_customer : null;
        }

        $cart_rule->date_from = date('Y-m-d H:i:s');
        $cart_rule->date_to = date(
            'Y-m-d H:i:s',
            strtotime($cart_rule->date_from.' +10 day')
        );

        $cart_rule->active = true;

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $cart_rule->name[(int)$language['id_lang']] = $this->l($title);
        }

        $cart_rule->add();

        $this->context->cart->addCartRule($cart_rule->id);
    }

    public function translateEmail()
    {
        return array(
            'recurringpayment' => $this->l('Recurring Payment')
        );
    }
	
	
	
	
	
	
	public function topNav(){

		$this->context->smarty->assign([
			'subscribersUrl' => $this->context->link->getAdminLink('AdminAnRecurringPayments'),
			'periodsUrl' => $this->context->link->getAdminLink('AdminAnRecurringPaymentsPeriods'),
			'settingsUrl' => $this->context->link->getAdminLink('AdminModules').'&configure=an_recurringpayments',
		]);
		
		$this->context->smarty->assign('addons_product_id', $this->addons_product_id);
		$this->context->smarty->assign('theme', $this->getUrlsSuggestions());
		
		return $this->display(__FILE__, 'views/templates/admin/top.tpl');
	}	
	
	public function getUrlsSuggestions()
	{
		$urlContactUs = 'https://addons.prestashop.com/contact-form.php';

		if (isset($this->url_contact_us) && $this->url_contact_us != ''){
			$urlContactUs = $this->url_contact_us;
		} elseif (isset($this->addons_product_id) && $this->addons_product_id != ''){
			$urlContactUs .= '?id_product=' .$this->addons_product_id;
		}
		
		$urls['url_contact_us'] = $urlContactUs;
					
		$urlRate = 'https://addons.prestashop.com/ratings.php';

		if (isset($this->url_rate) && $this->url_rate != ''){
			$urlRate = $this->url_rate;
		} elseif (isset($this->addons_product_id) && $this->addons_product_id != ''){
			$urlRate .= '?id_product=' .$this->addons_product_id;
		}
		
		$urls['url_rate'] = $urlRate;
				
		return $urls;
	}
}
