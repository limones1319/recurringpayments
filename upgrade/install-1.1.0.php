<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_0($object, $install = false)
{
    $result = true;

    $prefix = an_recurringpayments::getPrefix();
    $result &= Db::getInstance()->Execute('ALTER TABLE `'.$prefix.'period` ADD (
        `onlyrecurring_to_all_products` int(1) DEFAULT "0",
        `reduction` int(3) DEFAULT "0"
        )');

    return $result;
}
