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
            $period = $recurring->getPeriod();
            $paymentDate = $next['payment'];
            $deliveryDate = $next['delivery'];

            // Original Payment Due Date logic
            $sendEmail = false;
            $additionalMessage = '';

            // 1. Payment Warning (original)
            if ($today == $paymentDate && !$next['finished']) {
                $sendEmail = true;
                $additionalMessage = ''; 
            }

            // 2. Mid-period Reminder
            // Calculate halfway date between Payment Due Date and Expiry (Delivery) Date
            if ($period->require_payment_before > 0 && !$next['finished']) {
                 $daysDifference = (strtotime($deliveryDate) - strtotime($paymentDate)) / (60 * 60 * 24);
                 $halfDays = floor($daysDifference / 2);
                 if ($halfDays > 0) {
                     $midDate = date('Y-m-d', strtotime('+' . $halfDays . ' days', strtotime($paymentDate)));
                     if ($today == $midDate) {
                         $sendEmail = true;
                         $additionalMessage = 'Friendly reminder: your subscription renewal is coming up. To ensure uninterrupted service, please complete payment soon.';
                     }
                 }
            }

            // 3. Expiry Day Reminder
            if ($today == $deliveryDate && !$next['finished']) {
                $sendEmail = true;
                $additionalMessage = 'Your subscription expires today. Please finalize payment to avoid service interruption.';
            }

            if (!$sendEmail) {
                continue;
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
