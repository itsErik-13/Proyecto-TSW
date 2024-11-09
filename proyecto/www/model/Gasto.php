<?php
// file: model/Gasto.php

require_once(__DIR__ . "/../core/ValidationException.php");

/**
 * Class Gasto
 *
 * Representa un gasto en la web, creado por un usuario.
 *
 * @author lipido <lipido@gmail.com>
 */
class Gasto
{

    /**
     * The id of this payer
     * @var string
     */
    private $payerName;

    /**
     * The id of this project
     * @var string
     */
    private $idProject;

    /**
     * The id of this payment
     * @var string
     */
    private $idPayment;

    /**
     * The total amount of this payment
     * @var string
     */
    private $totalAmount;

    /**
     * The name of the payment
     * @var string
     */
    private $debt;

    private $debtors;




    /**
     * The constructor
     *
     * @param string $payerName The id of this payer
     * @param string $idProject The id of this project
     * @param string $idPayment The id of this payment
     * @param string $debt The debt of this payment
     * @param string $totalAmount The total amount of this payment
     */
    public function __construct($payerName = NULL, $idProject = NULL, $idPayment = NULL, $debt = NULL, $totalAmount = NULL, $debtors = NULL)
    {
        $this->payerName = $payerName;
        $this->idProject = $idProject;
        $this->idPayment = $idPayment;
        $this->debt = $debt;
        $this->totalAmount = $totalAmount;
        $this->debtors = $debtors;
    }

    /**
     * Gets the id of this payer
     *
     * @return string The id of this payer
     */
    public function getPayerId()
    {
        return $this->payerName;
    }

    /**
     * Sets the id of this payer       
     */
    public function setPayerId($payerId)
    {
        $this->payerName = $payerId;
    }

    /**
     * Gets the id of this project  
     * 
     * @return string The id of this project
     * 
     */
    public function getProjectId()
    {
        return $this->idProject;
    }

    /**
     * Sets the id of this project       
     */
    public function setProjectId($projectId)
    {
        $this->idProject = $projectId;
    }

    /**
     * Gets the id of this payment  
     * 
     * @return string The id of this payment
     * 
     */
    public function getPaymentId()
    {
        return $this->idPayment;
    }

    /**
     * Gets the debt of this payment  
     *
     * @return string The debt of this payment  
     * 
     */
    public function getDebt()
    {
        return $this->debt;
    }

    public function setPaymentId($paymentId)
    {
        $this->idPayment = $paymentId;
    }

    /**
     * Sets the id of this payment       
     */
    public function setDebt($debt)
    {
        $this->debt = $debt;
    }

    /**
     * Sets the id of this payment       
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Sets the id of this payment       
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    public function setDebtors(array $debtors)
    {
        $this->debtors = $debtors;
    }

    public function getDebtors()
    {
        return $this->debtors;
    }

    /**
     * Checks if the current instance is valid
     * for being updated in the database.
     *
     * @throws ValidationException if the instance is
     * not valid
     *
     * @return void
     */
    public function checkIsValidForCreate()
    {
        $errors = array();
        if (strlen(trim($this->payerName)) == 0) {
            $errors["payerName"] = i18n("Payer id is mandatory");
        }
        if (strlen(trim($this->idProject)) == 0) {
            $errors["projectId"] = i18n("Project id is mandatory");
        }
        if (strlen(trim($this->debt)) < 4 | strlen(trim($this->debt)) > 30) {
            $errors["debt"] = i18n("Debt should be between 4 and 30 characters");
        }
        if (!is_numeric(trim($this->totalAmount))) {
            $errors["totalAmount"] = i18n("Total amount should be a number");
        }

        if (strlen(trim($this->totalAmount)) == 0 || strlen(trim($this->totalAmount)) > 10) {
            $errors["totalAmount"] = i18n("Between 1-10 characters");
        }

        if (sizeof($errors) > 0) {
            throw new ValidationException($errors, i18n("Payment is not valid"));
        }
    }

    /**
     * Checks if the current instance is valid
     * for being updated in the database.
     *
     * @throws ValidationException if the instance is
     * not valid
     *
     * @return void
     */
    public function checkIsValidForUpdate()
    {
        $errors = array();
        if (strlen(trim($this->payerName)) == 0) {
            $errors["payerName"] = i18n("Payer id is mandatory");
        }
        if (strlen(trim($this->idProject)) == 0) {
            $errors["projectId"] = i18n("Project id is mandatory");
        }
        if (strlen(trim($this->debt)) < 4 | strlen(trim($this->debt)) > 30) {
            $errors["debt"] = i18n("Debt should be between 4 and 30 characters");
        }
        if (!is_numeric(trim($this->totalAmount))) {
            $errors["totalAmount"] = i18n("Total amount should be a number");
        }

        if (strlen(trim($this->totalAmount)) == 0 | strlen(trim($this->totalAmount)) > 10) {
            $errors["totalAmount"] = i18n("Between 1-10 characters");
        }

        if (sizeof($errors) > 0) {
            throw new ValidationException($errors, i18n("Payment is not valid"));
        }
    }
}
