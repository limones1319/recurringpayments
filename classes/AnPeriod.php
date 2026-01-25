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

class AnPeriod extends ObjectModel
{
    public $id;
    public $id_an_rps_period;
    public $name;
    public $sort_order = 0;
    public $billing_freq;
    public $billing_period;
    public $max_billing_cycles = 0;
    public $allow_weekdays;
    public $require_payment_before;
    public $can_define_start_date;
    public $apply_to_all_products;
    public $onlyrecurring_to_all_products;
    public $reduction;

    public static $definition = array(
        'table' => 'an_rps_period',
        'primary' => 'id_an_rps_period',
        'multilang' => true,
        'fields' => array(
            'sort_order' => array('type' => self::TYPE_INT),
            'billing_freq' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'max_billing_cycles' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'billing_period' => array('type' => self::TYPE_STRING),
            'allow_weekdays' => array('type' => self::TYPE_HTML, 'required' => true),
            'require_payment_before' => array('type' => self::TYPE_INT),
            'can_define_start_date' => array('type' => self::TYPE_INT),
            'apply_to_all_products' => array('type' => self::TYPE_INT),
            'onlyrecurring_to_all_products' => array('type' => self::TYPE_INT),
            'reduction' => array('type' => self::TYPE_INT),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'size' => 128),
        ));

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang, null);
    }

    public static function getDateType()
    {
        return array(
            array('name' => 'day', 'title' => 'Day'),
            array('name' => 'week', 'title' => 'Week'),
            array('name' => 'month', 'title' => 'Month'),
            array('name' => 'year', 'title' => 'Year'),
        );
    }

    public static function getWeekdays()
    {
        return array(
            array('name' => 'sunday', 'title' => 'Sunday'),
            array('name' => 'monday', 'title' => 'Monday'),
            array('name' => 'tuesday', 'title' => 'Tuesday'),
            array('name' => 'wednesday', 'title' => 'Wednesday'),
            array('name' => 'thursday', 'title' => 'Thursday'),
            array('name' => 'friday', 'title' => 'Friday'),
            array('name' => 'saturday', 'title' => 'Saturday'),
        );
    }

    public static function getCollection()
    {
        return new Collection('anPeriod', Context::getContext()->language->id);
    }

    public function getMinDate()
    {
        return date('Y-m-d', strtotime('+' . $this->require_payment_before . ' day', time()));
    }

    public function getDaysIds()
    {
        $exclude = $this->allow_weekdays;
        $i = 0;
        foreach (self::getWeekdays() as $day) {
            $exclude = str_replace($day, (string )$i++, $exclude);
        }

        return str_replace('-', '', $exclude);
    }

    public static function getDayInfo($day)
    {
        $days = self::getWeekdays();
        if (array_key_exists($day, $days)) {
            return $days[$day]['name'];
        }

        return false;
    }

    protected function setValues()
    {
        if (Tools::getIsset('submitAdd' . self::$definition['table'])) {
            $weekdays = Tools::getValue('allow_weekdays', array());
            $this->allow_weekdays = implode('-', $weekdays);
            $this->billing_freq = abs($this->billing_freq);
            if ($this->billing_freq < 1) {
                $this->billing_freq = 1;
            }
            if ($this->reduction > 100) {
                $this->reduction = 100;
            }
            if ($this->reduction < 1 || !$this->reduction) {
                $this->reduction = 0;
            }

            $this->sort_order = abs($this->sort_order);
            $this->max_billing_cycles = abs($this->max_billing_cycles);
            $this->require_payment_before = abs($this->require_payment_before);
        }
    }

    public function update($NULL_values = false)
    {
        $this->setValues();
        return parent::update($NULL_values);
    }

    public function add($autodate = true, $NULL_values = false)
    {
        $this->setValues();
        return parent::add($autodate, $NULL_values);
    }
}
