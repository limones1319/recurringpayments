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

if (!class_exists('an_recurringpayments')) {
    return;
}

$prefix = an_recurringpayments::getPrefix();

return array(
    "
    CREATE TABLE IF NOT EXISTS `" . $prefix . "period` (
      `id_an_rps_period` int(11) NOT NULL AUTO_INCREMENT,
      `billing_freq` int(11) NOT NULL,
      `max_billing_cycles` int(11) NOT NULL,
      `billing_period` text NOT NULL,
      `allow_weekdays` text NOT NULL,
      `require_payment_before` int(11) NOT NULL,
      `sort_order` int(11) NOT NULL,
      `can_define_start_date` int(1) NOT NULL,
      `apply_to_all_products` int(1) NOT NULL,
      `onlyrecurring_to_all_products` int(1) NOT NULL,
      `reduction` int(3) NOT NULL,
      PRIMARY KEY (`id_an_rps_period`)
    ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;",
    "
    CREATE TABLE IF NOT EXISTS `" . $prefix . "period_lang` (
      `id_an_rps_period` int(11) NOT NULL AUTO_INCREMENT,
      `id_lang` int(10) unsigned NOT NULL,
      `name` varchar(255) NOT NULL,
      PRIMARY KEY (`id_an_rps_period`, `id_lang`)
    ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;",
    "
    CREATE TABLE IF NOT EXISTS `" . $prefix . "recurringpayment` (
      `id_an_rps_recurringpayment` int(11) NOT NULL AUTO_INCREMENT,
      `id_cart` int(11) NOT NULL,
      `id_product` int(11) NOT NULL,
      `id_product_attribute` int(11) NOT NULL,
      `qty` int(11) NOT NULL,
      `id_period` int(11) NOT NULL,
      `start_date` date NOT NULL,
      `status` int(11) NOT NULL,
      `add_date` date NOT NULL,
      PRIMARY KEY (`id_an_rps_recurringpayment`)
    ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;",
    "
    CREATE TABLE IF NOT EXISTS `" . $prefix . "recurringpayment_products` (
      `id_product` int(11) NOT NULL,
      `id_shop` int(11) NOT NULL,
      `period_types` text,
      `only_recurringpayment` int(1) NOT NULL,
      `status` int(11) NOT NULL,			  
      PRIMARY KEY (`id_product`, `id_shop`)
    ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;",
    "
    CREATE TABLE IF NOT EXISTS `" . $prefix . "recurringpayment_orders` (
      `id_cart` int(11) NOT NULL,
      `id_an_rps_recurringpayment` int(11) NOT NULL,
      PRIMARY KEY (`id_cart`, `id_an_rps_recurringpayment`)
    ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;");
