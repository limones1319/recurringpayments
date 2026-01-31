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

class an_recurringpaymentsCronModuleFrontController extends
 ModuleFrontController
{
    public function initContent()
    {
        $today = date('Y-m-d');
        $recurrings = AnRecurringPayments::getCollection()->where('status', '=', 1);

        foreach ($recurrings as $recurring) {
            $next = $recurring->getPaymentData();
            if ($next['finished']) {
                continue;
            }

            $paymentDate = $next['payment'];
            $deliveryDate = $next['delivery'];

            $deliveryDateObj = DateTime::createFromFormat('Y-m-d', $deliveryDate);
            if (!$deliveryDateObj) {
                continue;
            }

            $sendEmail = false;

            $deliveryMinus5 = (clone $deliveryDateObj)->modify('-5 days')->format('Y-m-d');
            $deliveryMinus1 = (clone $deliveryDateObj)->modify('-1 day')->format('Y-m-d');

            if ($today == $deliveryMinus5 || $today == $deliveryMinus1 || $today == $paymentDate) {
                $sendEmail = true;
            }

            if (!$sendEmail) {
                continue;
            }

            $daysUntilExpiry = (int)(new DateTime($today))->diff($deliveryDateObj)->format('%r%a');
            if ($daysUntilExpiry < 0) {
                continue;
            }

            if ($daysUntilExpiry === 0) {
                $additionalMessage = $this->module->l(
                    'Just a friendly reminder: to keep your service active, please complete the payment today. If payment is not completed today, the service will be suspended.'
                );
            } else {
                $dayLabel = $daysUntilExpiry === 1 ? $this->module->l('day') : $this->module->l('days');
                $additionalMessage = sprintf(
                    $this->module->l(
                        'Just a friendly reminder: to keep your service active, please complete the payment. If payment is not completed within the next %d %s, the service will be suspended.'
                    ),
                    $daysUntilExpiry,
                    $dayLabel
                );
            }

            $customer = $recurring->getCustomer();
            $product = $recurring->getProduct();

            $templateVars = array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{product_name}' => $product->name,
                '{attributes}' => $recurring->getDesignation(),
                '{qty}' => $recurring->qty,
                '{payment}' => Tools::displayDate($next['payment']),
                '{delivery}' => Tools::displayDate($next['delivery']),
                '{additional_message}' => $additionalMessage,
                '{my_subscription}' => Context::getContext()->link->getModuleLink(
                    'an_recurringpayments',
                    'account'
                ),
                '{payment_link}' => Context::getContext()->link->getModuleLink(
                    'an_recurringpayments',
                    'front',
                    array(
                        'action' => 'addRecurringToCart',
                        'id_an_rps_recurringpayment' => $recurring->id,
                    ),
                    null,
                    $customer->id_lang
                ),
            );

            $translationemail = $this->module->translateEmail();

            Mail::Send(
                $customer->id_lang,
                'sub_payment',
                $translationemail['recurringpayment'],
                $templateVars,
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname,
                Configuration::get('PS_SHOP_EMAIL'),
                Configuration::get('PS_SHOP_NAME'),
                null,
                null,
                _PS_MODULE_DIR_ . 'an_recurringpayments/mails/'
            );

            echo 'Sent to ' . $customer->email . '
            ';
        }

        die('Completed');
    }
}
