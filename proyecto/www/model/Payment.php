<?php
// file: model/Gasto.php

require_once(__DIR__ . "/../core/ValidationException.php");

/**
 * Class Payment
 *
 * Representa un gasto en un proyecto
 *
 * @author lipido <lipido@gmail.com>
 */
class Payment
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
	* The total subject of this payment
	* @var string
	*/
    private $subject;


    private $debtors;

    /**
     * The constructor
     *
     * @param string $payerName The id of this payer
     * @param string $idProject The id of this project
     * @param string $idPayment The id of this payment
     * @param string $totalAmount The total amount of this payment
     */
    public function __construct( $idPayment = NULL, $idProject = NULL, $payerName = NULL, $totalAmount = NULL, $subject = NULL)
    {
        $this->payerName = $payerName;
        $this->idProject = $idProject;
        $this->idPayment = $idPayment;
        $this->totalAmount = $totalAmount;
        $this->subject = $subject;
    }

    /**
     * Gets the id of this payer
     *
     * @return string The id of this payer
     */
    public function getPayerName()
    {
        return $this->payerName;
    }

    /**
     * Sets the id of this payer       
     */
    public function setPayerName($payerName)
    {
        $this->payerName = $payerName;
    }

    /**
     * Gets the id of this project  
     * 
     * @return string The id of this project
     * 
     */
    public function getIdProject()
    {
        return $this->idProject;
    }

    /**
     * Sets the id of this project       
     */
    public function setIdProject($idProject)
    {
        $this->idProject = $idProject;
    }

    /**
     * Gets the id of this payment  
     * 
     * @return string The id of this payment
     * 
     */
    public function getIdPayment()
    {
        return $this->idPayment;
    }

    /**
     * Sets the id of this payment       
     */
    public function setIdPayment($idPayment)
    {
        $this->idPayment = $idPayment;
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

       /**
     * Sets the id of this payment       
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * Sets the id of this payment       
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }

    /**
     * Sets the debtors of the payment    
     */
    public function setDebtors(array $debtors)
    {
        $this->debtors = $debtors;
    }

    /**
     * Get the debtors of this payment
     * @return array containing the debtors       
     */
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
            $errors["idProject"] = i18n("Project id is mandatory");
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
            $errors["idProject"] = i18n("Project id is mandatory");
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
