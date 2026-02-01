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

class AnRecurringPayments extends ObjectModel
{
    public $id;
    public $id_an_rps_recurringpayment;
    public $id_cart;
    public $id_product;
    public $id_product_attribute;
    public $qty = 1;
    public $id_period;
    public $start_date;
    public $status = 1;
    public $add_date;

    protected $product = null;
    protected $customer = null;
    protected $period = null;

    public static $definition = array(
        'table' => "an_rps_recurringpayment",
        'primary' => 'id_an_rps_recurringpayment',
        'multilang' => false,
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT),
            'id_product' => array('type' => self::TYPE_INT),
            'id_product_attribute' => array('type' => self::TYPE_INT),
            'qty' => array('type' => self::TYPE_INT),
            'id_period' => array('type' => self::TYPE_INT),
            'start_date' => array('type' => self::TYPE_DATE),
            'status' => array('type' => self::TYPE_INT),
            'add_date' => array('type' => self::TYPE_DATE),
            ),
        );

    protected $module = null;

    public function getModule()
    {
        if (is_null($this->module)) {
            $this->module = new an_recurringpayments;
        }

        return $this->module;
    }

    public function delete()
    {
        Db::getInstance()->Execute('
            DELETE FROM `' . an_recurringpayments::getPrefix() .
            'recurringpayment_orders` WHERE `id_an_rps_recurringpayment` = ' . $this->id);

        return parent::delete();
    }

    public static function getCollection()
    {
        return new Collection('AnRecurringPayments', Context::getContext()->language->id);
    }

    public static function getrecurringpaymentsByCartId($id_cart)
    {
        return Db::getInstance()->ExecuteS('
            SELECT * FROM `' . pSQL(an_recurringpayments::getPrefix()) .
            'recurringpayment` WHERE `id_cart` = ' . (int)$id_cart);
    }

    public static function getSubOrdersByCartId($id_cart)
    {
        $id_cart = (int)$id_cart;
        $orders = Db::getInstance()->ExecuteS('
            SELECT `id_an_rps_recurringpayment` FROM `' . pSQL(an_recurringpayments::getPrefix())
            . 'recurringpayment_orders` WHERE  `id_cart` = ' . (int)$id_cart);

        $_subs = array();
        if (count($orders)) {
            $_cart = new Cart($id_cart);
            foreach ($orders as $order) {
                $_sub = new self($order['id_an_rps_recurringpayment']);
                if ($_sub->id) {
                    foreach ($_cart->getProducts() as $_product) {
                        if ($_product['id_product'] == $_sub->id_product
                            and $_product['id_product_attribute'] == $_sub->id_product_attribute
                        ) {
                            $_subs[$_sub->id] = $_sub;
                            break;
                        }
                    }
                }
            }
        }

        return $_subs;
    }

    public static function getNewOrdersByCartId($id_cart)
    {
        $id_cart = (int)$id_cart;
        $orders = Db::getInstance()->ExecuteS('
            SELECT `id_an_rps_recurringpayment` FROM `' . pSQL(an_recurringpayments::getPrefix())
            . 'recurringpayment` WHERE  `id_cart` = ' . (int)$id_cart);

        $_subs = array();
        if (count($orders)) {
            $_cart = new Cart($id_cart);
            foreach ($orders as $order) {
                $_sub = new self($order['id_an_rps_recurringpayment']);
                if ($_sub->id) {
                    foreach ($_cart->getProducts() as $_product) {
                        if ($_product['id_product'] == $_sub->id_product
                            and $_product['id_product_attribute'] == $_sub->id_product_attribute
                        ) {
                            $_subs[$_sub->id] = $_sub;
                            break;
                        }
                    }
                }
            }
        }

        return $_subs;
    }

    public static function getInstance($id_cart, $id_product, $ipa)
    {
        $id_sub = (int)Db::getInstance()->getValue('
            SELECT `id_an_rps_recurringpayment` FROM `' . pSQL(an_recurringpayments::getPrefix())
            . 'recurringpayment` WHERE  `id_cart` = ' . (int)$id_cart .
            ' AND `id_product` = ' . (int)$id_product . ' AND `id_product_attribute` = ' . (int)$ipa);

        if ($id_sub) {
            return new self($id_sub);
        }

        return new self;
    }

    public function toggleStatus()
    {
        if ($this->status) {
            $this->status = 0;
        } else {
            $this->status = 1;
        }

        return $this->save();
    }

    public static function getCustomerrecurringpayments($id_customer)
    {
        $sql = '
            SELECT `id_an_rps_recurringpayment` FROM `' . an_recurringpayments::getPrefix() . 'recurringpayment` a
            LEFT JOIN `' . _DB_PREFIX_ .
            'cart` ca ON (ca.`id_cart` = a.`id_cart`)
            LEFT JOIN `' . _DB_PREFIX_ .
            'customer` cu ON (ca.`id_customer` = cu.`id_customer`)
            WHERE cu.`id_customer` = ' . (int)$id_customer . '
            ORDER BY a.`id_an_rps_recurringpayment` DESC';

        $subList = array();
        $result = Db::getInstance()->ExecuteS($sql);
        foreach ($result as $row) {
            $subList[] = new self($row['id_an_rps_recurringpayment']);
        }

        return $subList;
    }

    public function getProduct()
    {
        if (is_null($this->product)) {
            $id_lang = Context::getContext()->language->id;
            $this->product = new Product($this->id_product, true, $id_lang);
        }

        return $this->product;
    }

    public function getDesignation()
    {
        $params = Product::getAttributesParams($this->id_product, $this->id_product_attribute);

        $designation = '';
        foreach ($params as $param) {
            $designation .= $param['group'] . ' - ' . $param['name'] . ', ';
        }

        return rtrim($designation, ', ');
    }

    public function getCustomer()
    {
        if (is_null($this->customer)) {
            $cart = new Cart($this->id_cart);
            $this->customer = new Customer($cart->id_customer);
        }

        return $this->customer;
    }

    public function getPeriod()
    {
        if (is_null($this->period)) {
            $this->period = new anPeriod($this->id_period, Context::
                getContext()->language->id);
        }

        return $this->period;
    }

    public function getPaymentData()
    {
        $_period = $this->getPeriod();
        $first_delivery = strtotime($this->start_date);
        $last_dev = $first_delivery;
        $finished = false;

        $allow_days = explode('-', $_period->allow_weekdays);
        $today = strtotime(date('Y-m-d', time()));

        $total_cycles = 0;
        
        $check_time = time();
        while (true) {
            if ($last_dev >= $today && $first_delivery != $last_dev) {
                break;
            }

            $total_cycles++;
            $last_dev = strtotime('+' . $_period->billing_freq . ' ' . $_period->billing_period, $last_dev);

            if ((time() - $check_time) > 10) {
                break;
            }
        }

        $last_dev = strtotime('+' . self::addDays($last_dev, $allow_days) . ' day', $last_dev);

        $next_dev = $last_dev;
        $next_pay = strtotime('-' . $_period->require_payment_before . ' day', $next_dev);
        if ($next_pay < $today) {
            $next_dev = strtotime('+' . self::addDays($next_dev, $allow_days) . ' day', $next_dev);

            $next_pay = strtotime('+' . $_period->require_payment_before . ' day', $next_pay);
            $next_pay = strtotime('+' . $_period->billing_freq . ' ' . $_period->billing_period, $next_pay);
            $next_pay = strtotime('-' . $_period->require_payment_before . ' day', $next_pay);
        }

        if ($_period->max_billing_cycles > 0 && $total_cycles > $_period->max_billing_cycles) {
            $next_payment = '';
            $next_delivery = $this->getModule()->l('Recurring Payment is ended');
            $finished = true;
        } else {
            $next_delivery = date('Y-m-d', $next_dev);
            $next_payment = date('Y-m-d', strtotime('-' . $_period->require_payment_before . ' day', $next_dev));
        }

        $return = array();

        $return['payment'] = date('Y-m-d', $next_pay);
        $return['current_payment'] = $next_payment;
        $return['delivery'] = $next_delivery;
        $return['finished'] = $finished;

        return $return;
    }

    public static function addDays($time, $allow_days)
    {
        $nom_day = anPeriod::getDayInfo(date('w', $time));
        return self::addDaysForNextPayment($nom_day, $allow_days);
    }

    public static function addDaysForNextPayment($nom_day, $allow_days)
    {
        $days = anPeriod::getWeekdays();
        $start = false;
        $ids = array();
        foreach ($days as $day) {
            if ($nom_day == $day['name']) {
                $start = true;
            }

            if ($start) {
                $ids[] = $day['name'];
            }
        }

        foreach ($days as $day) {
            if ($nom_day == $day['name']) {
                break;
            }

            $ids[] = $day['name'];
        }

        $i = 0;
        foreach ($ids as $day) {
            if (in_array($day, $allow_days)) {
                return $i;
            }

            $i++;
        }

        return 0;
    }
}
