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

class an_recurringpaymentsFrontModuleFrontController extends
 ModuleFrontController
{
    protected function hasrecurringpaymentAction()
    {
        $ids = Tools::getValue('ids', array());
        $allowed = array();
        foreach ($ids as $id) {
            $data = $this->module->getProductData((int)$id, $this->shop_id);
            if ($data['status'] == 1) {
                $allowed[] = array(
                    'id_product' => $data['id_product'],
                    'only_recurringpayment' => $data['only_recurringpayment']
                );
            }
        }

        $this->response = $allowed;
    }

    protected function addRecurringToCartAction()
    {
        $recurring = new AnRecurringPayments(Tools::getValue('id_an_rps_recurringpayment'), Tools::getValue('id_lang'));
        if($recurring->id_product == 0) {
            $cart = new Cart($recurring->id_cart);
            $cart->duplicate();
                Tools::redirect(
                    $this->context->link->getPageLink(
                    'cart',
                    null,
                    $this->context->language->id,
                    array(
                        'action' => 'show'
                    ),
                    false,
                    null,
                    true
                )
            );
            die;
        }
        $add_to_cart = Context::getContext()->link->getPageLink(
            'cart',
            false,
            Tools::getValue('id_lang'),
            array(
                'id_product' => $recurring->id_product,
                'id_product_attribute' => $recurring->id_product_attribute,
                'qty' => $recurring->qty,
                'id_an_rps_recurringpayment' => $recurring->id,
                'add' => 1,
                'paymentRecurring' => 1,
                'token' => Tools::getToken(false),
            )
        );
        $_period = new anPeriod($recurring->id_period);
        $this->module->createVoucher($recurring->id_product, $_period->reduction, Context::getContext()->cart->id);

        Tools::redirect($add_to_cart);
        die;
    }

    public function initContent()
    {
        parent::initContent();
        if (!is_null(Shop::getContextShopID())) {
            $this->shop_id = Shop::getContextShopID();
        }

        $this->response = '';
        if (Tools::isSubmit('action')) {
            $actionName = Tools::getValue('action') . 'Action';
            if (method_exists($this, $actionName)) {
                $this->$actionName();
            }
        }

        echo Tools::jsonEncode($this->response);
    }
}
