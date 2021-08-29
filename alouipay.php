<?php

/**
 * alouiPay - A Sample Payment Module for PrestaShop 1.7
 *
 * This file is the declaration of the module.
 */

use Symfony\Component\Config\ConfigCache;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AlouiPay extends PaymentModule
{
    private $_html = '';
    private $_postErrors = array();

    public $address;

    /**
     * alouiPay constructor.
     *
     * Set the information about this module
     */
    public function __construct()
    {
        $this->name                   = 'alouiPay';
        $this->tab                    = 'payments_gateways';
        $this->version                = '1.0';
        $this->author                 = 'ALOUI Mohamed HABIB';
        $this->controllers            = array('payment', 'validation');
        $this->currencies             = true;
        $this->currencies_mode        = 'checkbox';
        $this->bootstrap              = true;
        $this->displayName            = 'alouiPay';
        $this->description            = 'Sample Payment module developed for learning purposes.';
        $this->confirmUninstall       = 'Are you sure you want to uninstall this module?';
        $this->ps_versions_compliancy = array('min' => '1.7.0', 'max' => _PS_VERSION_);

        parent::__construct();
    }

    /**
     * Install this module and register the following Hooks:
     *
     * @return bool
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook('paymentOptions')
            && $this->registerHook('paymentReturn');
    }

    /**
     * Uninstall this module and remove it from all hooks
     *
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Returns a string containing the HTML necessary to
     * generate a configuration screen on the admin
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_html;
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        $formaction = $this->context->link->getModuleLink($this->name, 'validation', array(), true);
        $this->smarty->assign(['action' => $formaction]);
        $paymentForm = $this->fetch('module:alouipay/views/templates/hook/payment_options.tpl');
        $paymentOptions = new PrestaShop\PrestaShop\Core\Payment\PaymentOption;

        $paymentOptions->setModuleName($this->displayName)
            ->setCallToActionText($this->displayName)
            ->setAction($formaction)
            ->setForm($paymentForm);
        return [
            $paymentOptions
        ];
    }
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        Mail::Send(
            (int)(Configuration::get('PS_LANG_DEFAULT')), // defaut language id
            'contact', // email template file to be use
            ' custom order confirmation', // email subject
            array(
                '{email}' => Configuration::get('PS_SHOP_EMAIL'), // sender email address
                '{message}' => '<h1>Congratulation</h1>
                <p>You have just completed a payment with ALOUI Pay Module 👏👏👏</p>' // email content
            ),
            $this->context->customer->email, // receiver email address
            $this->context->customer->firstname .'  ' .  $this->context->customer->lastname, //receiver name
            NULL, //from email address
            NULL  //from name
        );

        return $this->fetch('module:alouipay/views/templates/hook/payment_return.tpl');
    }
}
