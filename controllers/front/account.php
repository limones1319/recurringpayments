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

class an_recurringpaymentsAccountModuleFrontController extends
 ModuleFrontController
{
    public $auth = true;

    public $display_column_left = false;
    public $display_column_right = false;

    private function saveQtyAction($_sub)
    {
        $qty = Tools::getValue('qty');
        if (Validate::isInt($qty)) {
            $_sub->qty = $qty;
            return $_sub->save();
        }

        return false;
    }

    private function changeStatusAction($_sub)
    {
        return $_sub->toggleStatus();
    }

    private function deleteAction($_sub)
    {
        return $_sub->delete();
    }

    public function postProcess()
    {
        if (Tools::getIsset('action')) {
            $method = Tools::getValue('action') . 'Action';
            $id = Tools::getValue('id');
            $_sub = new AnRecurringPayments($id);
            if ($_sub->id and $_sub->getCustomer()->id == Context::getContext()->customer->id) {
                if (method_exists($this, $method)) {
                    if ($this->$method($_sub)) {
                        Tools::redirectLink(
                            Context::getContext()->link->getModuleLink(
                                'an_recurringpayments',
                                'account'
                            )
                        );
                    }
                }
            }

            $this->context->smarty->assign('an_error', true);
        }
    }

    public function initContent()
    {
        parent::initContent();

        if (!Context::getContext()->customer->isLogged()) {
            Tools::redirect(
                'index.php?controller=authentication&redirect=module&module=an_recurringpayments&action=account'
            );
        }

        if (Context::getContext()->customer->id) {
            $this->context->smarty->assign(
                'myrecurringpayments',
                AnRecurringPayments::getCustomerRecurringPayments(Context::getContext()->customer->id)
            );
            if ($this->module->new) {
                $this->setTemplate('module:an_recurringpayments/views/templates/front/account.tpl');
            } else {
                $this->setTemplate('account-old.tpl');
            }
        }
    }
}
