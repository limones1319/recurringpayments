<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0($object, $install = false)
{
    $result = true;

    $prefix = an_recurringpayments::getPrefix();

    $result &= Db::getInstance()->Execute('
        ALTER TABLE `'.$prefix.'period`
        CHANGE `id_an_period` id_an_rps_period int(11)
    ');
    $result &= Db::getInstance()->Execute('
        ALTER TABLE `'.$prefix.'period_lang`
        CHANGE `id_an_period` id_an_rps_period int(11)
    ');
    $result &= Db::getInstance()->Execute('
        ALTER TABLE `'.$prefix.'recurringpayment`
        CHANGE `id_recurringpayment` id_an_rps_recurringpayment int(11)
    ');
    return $result;
}
