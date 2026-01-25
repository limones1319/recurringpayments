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
return array("DROP TABLE `" . $prefix . "period`, 
    		    `" . $prefix . "period_lang`, 
                `" . $prefix . "recurringpayment_products`,
    		    `" . $prefix . "recurringpayment`,
    		    `" . $prefix . "recurringpayment_orders`;");
