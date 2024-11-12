<?php
// file: model/Debt.php

require_once(__DIR__ . "/../core/ValidationException.php");

/**
 * Class Debt
 *
 * Representa un gasto en la web, creado por un usuario.
 *
 * @author lipido <lipido@gmail.com>
 */
class Debt
{
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
     * The constructor
     *
     * @param string $debtorName The name of the debtor
     * @param string $idProject The id of the project
     * @param string $idPayment The id of the payment
     */
    public function __construct($debtorName = NULL, $idProject = NULL, $idPayment = NULL)
    {
        $this->debtorName = $debtorName;
        $this->idProject = $idProject;
        $this->idPayment = $idPayment;
    }

    /**
     * Gets the id of this payer
     *
     * @return string The id of this payer
     */
    public function getDebtorName()
    {
        return $this->debtorName;
    }

    /**
     * Sets the id of this payer       
     */
    public function setDebtorName($debtorName)
    {
        $this->debtorName = $debtorName;
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
}
