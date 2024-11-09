<?php
// file: model/Debt.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Debt
*
* Representa un gasto en la web, creado por un usuario.
*
* @author lipido <lipido@gmail.com>
*/
class Debt {

	/**
	* The name of the debtor
	* @var string
	*/
	private $debtorName;

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
    private $relativeAmount;

    /**
	* The name of the payment
	* @var string
	*/
    private $debt;




	/**
	* The constructor
	*
	* @param string $debtorName The name of the debtor
	* @param string $idProject The id of the project
	* @param string $idPayment The id of the payment
	* @param string $relativeAmount The relative amount of this debt
	*/
	public function __construct($debtorName=NULL, $idProject=NULL, $idPayment=NULL, $relativeAmount=NULL) {
		$this->debtorName = $debtorName;
        $this->idProject = $idProject;
        $this->idPayment = $idPayment;
        $this->relativeAmount = $relativeAmount;
	}



    /**
	* Gets the id of this payer
	*
	* @return string The id of this payer
	*/
    public function getDebtorName() {
        return $this->debtorName;
    }

    /**
     * Sets the id of this payer       
     */
    public function setDebtorName($debtorName) {
        $this->debtorName = $debtorName;
    }

    /**
     * Gets the id of this project  
     * 
     * @return string The id of this project
     * 
     */
    public function getProjectId() {
        return $this->idProject;
    }

    /**
     * Sets the id of this project       
     */
    public function setProjectId($projectId) {
        $this->idProject = $projectId;
    }

    /**
     * Gets the id of this payment  
     * 
     * @return string The id of this payment
     * 
     */
    public function getPaymentId() {
        return $this->idPayment;
    }


    public function setPaymentId($paymentId) {
        $this->idPayment = $paymentId;
    }

    /**
     * Sets the id of this payment       
     */
    public function getRelativeAmount() {
        return $this->relativeAmount;
    }

    /**
     * Sets the id of this payment       
     */
    public function setRelativeAmount($relativeAmount) {
        $this->relativeAmount = $relativeAmount;
    }


    /**
     * 
     * CHANGE |
     *        V
     */
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
		if (strlen(trim($this->payerName)) == 0 ) {
			$errors["payerId"] = i18n("Payer id is mandatory");
		}
		if (strlen(trim($this->idProject)) == 0 ) {
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

        if (!isset($this->payerName)) {
            $errors["payerId"] = i18n("Payer id is mandatory");
        }
        if (!isset($this->idProject)) {
            $errors["projectId"] = i18n("Project id is mandatory");
        }
        if (!isset($this->idPayment)) {
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
