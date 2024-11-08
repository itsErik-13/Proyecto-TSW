<?php
// file: model/Gasto.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Gasto
*
* Representa un gasto en la web, creado por un usuario.
*
* @author lipido <lipido@gmail.com>
*/
class Gasto {

	/**
	* The id of this payer
	* @var string
	*/
	private $payerId;

	/**
	* The id of this project
	* @var string
	*/
	private $projectId;

	/**
	* The id of this payment
	* @var string
	*/
	private $paymentId;

    /**
	* The debt of this payment
	* @var string
	*/
    private $debt;

    /**
	* The total amount of this payment
	* @var string
	*/
    private $totalAmount;


	/**
	* The constructor
	*
	* @param string $payerId The id of this payer
	* @param string $projectId The id of this project
	* @param string $paymentId The id of this payment
    * @param string $debt The debt of this payment
	* @param string $totalAmount The total amount of this payment
	*/
	public function __construct($payerId=NULL, $projectId=NULL, $paymentId=NULL, $debt=NULL, $totalAmount=NULL) {
		$this->payerId = $payerId;
        $this->projectId = $projectId;
        $this->paymentId = $paymentId;
        $this->debt = $debt;
        $this->totalAmount = $totalAmount;
	}

    /**
	* Gets the id of this payer
	*
	* @return string The id of this payer
	*/
    public function getPayerId() {
        return $this->payerId;
    }

    /**
     * Sets the id of this payer       
     */
    public function setPayerId($payerId) {
        $this->payerId = $payerId;
    }

    /**
     * Gets the id of this project  
     * 
     * @return string The id of this project
     * 
     */
    public function getProjectId() {
        return $this->projectId;
    }

    /**
     * Sets the id of this project       
     */
    public function setProjectId($projectId) {
        $this->projectId = $projectId;
    }

    /**
     * Gets the id of this payment  
     * 
     * @return string The id of this payment
     * 
     */
    public function getPaymentId() {
        return $this->paymentId;
    }

    /**
     * Gets the debt of this payment  
     *
     * @return string The debt of this payment  
     * 
     */
    public function getDebt() {
        return $this->debt;
    }

    /**
     * Sets the id of this payment       
     */
    public function setDebt($debt) {
        $this->debt = $debt;
    }

    /**
     * Sets the id of this payment       
     */
    public function getTotalAmount() {
        return $this->totalAmount;
    }

    /**
     * Sets the id of this payment       
     */
    public function setTotalAmount($totalAmount) {
        $this->totalAmount = $totalAmount;
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
	public function checkIsValidForCreate() {
		$errors = array();
		if (strlen(trim($this->payerId)) == 0 ) {
			$errors["payerId"] = i18n("Payer id is mandatory");
		}
		if (strlen(trim($this->projectId)) == 0 ) {
            $errors["projectId"] = i18n("Project id is mandatory");
        }
        /*if (strlen(trim($this->paymentId)) == 0 ) {
            $errors["paymentId"] = i18n("Payment id is mandatory");
        }*/
        if (strlen(trim($this->debt)) == 0 ) {
            $errors["debt"] = i18n("Debt is mandatory");
        }
        if (strlen(trim($this->totalAmount)) == 0 ) {
            $errors["totalAmount"] = i18n("Total amount is mandatory");
        }

		if (sizeof($errors) > 0){
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
	public function checkIsValidForUpdate() {
		$errors = array();

        if (!isset($this->payerId)) {
            $errors["payerId"] = i18n("Payer id is mandatory");
        }
        if (!isset($this->projectId)) {
            $errors["projectId"] = i18n("Project id is mandatory");
        }
        if (!isset($this->paymentId)) {
            $errors["paymentId"] = i18n("Payment id is mandatory");
        }

		/*try{
			$this->checkIsValidForCreate();
		}catch(ValidationException $ex) {
			foreach ($ex->getErrors() as $key=>$error) {
				$errors[$key] = $error;
			}
		}*/
		if (sizeof($errors) > 0) {
			throw new ValidationException($errors, i18n("Payment is not valid"));
		}
	}
}
