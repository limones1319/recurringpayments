<?php
// Script to manually triggering a test email for subscription 265
// Simulating the "Mid-period Reminder" (5 days after notification/before expiry)

require_once dirname(__FILE__) . '/../../config/config.inc.php';
require_once dirname(__FILE__) . '/../../init.php';
require_once dirname(__FILE__) . '/an_recurringpayments.php';

$id_subscription = 265;
echo "Loading subscription ID: $id_subscription...\n";

$recurring = new AnRecurringPayments($id_subscription);

if (!Validate::isLoadedObject($recurring)) {
    die("Error: Subscription $id_subscription not found in database.\n");
}

echo "Subscription found. Preparing email...\n";

$module = new an_recurringpayments();
$customer = $recurring->getCustomer();
$product = $recurring->getProduct();
$next = $recurring->getPaymentData();

// Manually set the message for the "5 days reminder" test
$additionalMessage = 'Friendly reminder: your subscription renewal is coming up. To ensure uninterrupted service, please complete payment soon.';

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

$translationemail = $module->translateEmail(); // Public method in main class

echo "Sending email to: " . $customer->email . "\n";

$result = Mail::Send(
    $customer->id_lang,
    'sub_payment',
    $translationemail['recurringpayment'] . ' (TEST REMINDER)', // Added suffix to distinguish test
    $templateVars,
    $customer->email,
    $customer->firstname . ' ' . $customer->lastname,
    Configuration::get('PS_SHOP_EMAIL'),
    Configuration::get('PS_SHOP_NAME'),
    null,
    null,
    _PS_MODULE_DIR_ . 'an_recurringpayments/mails/'
);

if ($result) {
    echo "Email sent successfully.\n";
} else {
    echo "Error: Email failed to send.\n";
}
