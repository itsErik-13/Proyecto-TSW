<?php
// file: model/GastoMapper.php
require_once(__DIR__ . "/../core/PDOConnection.php");

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/Payment.php");

/**
 * Class PaymentMapper
 *
 * Database interface for Payment entities
 *
 */
class PaymentMapper
{

	/**
	 * Reference to the PDO connection
	 * @var PDO
	 */
	private $db;

	public function __construct()
	{
		$this->db = PDOConnection::getInstance();
	}

	/**
	 * Returns all payments of a project with a list of debtors
	 * @param mixed $idProject id of the project
	 * @return array|null the list of payments of a project, null if there is no payments
	 */
	public function findByIdProjectWithDebtors($idProject)
	{
		// Obtenemos todos los pagos del proyecto
		$stmt = $this->db->prepare("SELECT * FROM payment WHERE idProject=?");
		$stmt->execute(array($idProject));
		$payment = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$payments = array();

		// Por cada pago obtenemos los deudores
		if ($payment != null) {
			foreach ($payment as $gasto) {
				$stmt = $this->db->prepare("SELECT debtorName FROM debt WHERE idProject=? and idPayment=?");
				$stmt->execute(array($idProject, $gasto["idPayment"]));
				$debtors_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$debtors = array();
				foreach ($debtors_db as $debtor)
					array_push($debtors, $debtor["debtorName"]);

				$toSave = new Payment();

				$toSave->setPayerName($gasto["payerName"]);
				$toSave->setIdProject($gasto["idProject"]);
				$toSave->setIdPayment($gasto["idPayment"]);
				$toSave->setSubject($gasto["subject"]);
				$toSave->setTotalAmount($gasto["totalAmount"]);
				$toSave->setDebtors($debtors);
				array_push($payments, $toSave);
			}
			return $payments;
		} else {
			return NULL;
		}
	}



	/**
	 * Saves a Payment into the database
	 * 
	 * @param Payment $payment The payment to be saved
	 * @throws PDOException if a database error occurs
	 * @return int The new payment id
	 */
	public function save(Payment $payment)
	{
		$stmt = $this->db->prepare("INSERT INTO payment (payerName, idProject, totalAmount, subject) values (?,?,?,?)");
		$stmt->execute(array($payment->getPayerName(), $payment->getIdProject(), $payment->getTotalAmount(), $payment->getSubject()));
		// Falta actualizar la tabla de deudas
		return $this->db->lastInsertId();
	}


	/**
	 * Updates a Payment in the database
	 *
	 * @param Payment $payment The payment to be updated
	 * @throws PDOException if a database error occurs
	 * @return void
	 */
	public function update(Payment $payment)
	{
		$stmt = $this->db->prepare("UPDATE payment set payerName=?, totalAmount=?, subject=? where idPayment=? and idProject=?");
		$stmt->execute(array($payment->getPayerName(), $payment->getTotalAmount(), $payment->getSubject(), $payment->getIdPayment(), $payment->getIdProject()));
	}

	/**
	 * Deletes a Payment in the database
	 *
	 * @param Payment $payment The payment to be deleted
	 * @throws PDOException if a database error occurs
	 * @return void
	 */
	public function delete(Payment $payment)
	{
		$stmt = $this->db->prepare("DELETE from payment WHERE idPayment=? and idProject=?");
		$stmt->execute(array($payment->getIdPayment(), $payment->getIdProject()));
	}

	public function findReceiverByPaymentId($idPayment, $idProject)
	{
		$stmt = $this->db->prepare("SELECT payerName FROM payment WHERE idPayment =? and idProject = ?");
		$stmt->execute(array($idPayment, $idProject));
		$payment = $stmt->fetch(PDO::FETCH_ASSOC);

		// Retornamos el nombre del pagador si el pago existe
		return $payment ? $payment["payerName"] : null;
	}

	/**
	 * Gives a Payment given its id's
	 * @param mixed $idPayment the id of the payment
	 * @param mixed $idProject the id of the project
	 * @return Payment|null the Payment with the id's given or null if there is none
	 */
	public function findByIdPayment($idPayment, $idProject)
	{
		$stmt = $this->db->query("SELECT * FROM payment where idPayment = $idPayment and idProject = $idProject");
		$payment = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($payment != null) {
			return new Payment($payment["idPayment"], $payment["idProject"], $payment["payerName"], $payment["totalAmount"], $payment["subject"]);
		}

		return NULL;
	}

	/**
	 * Find the amount payed by every user
	 * @param mixed $projectId
	 * @return array returns an array with de amount payed by every user in the project
	 */
	public function findTotalPaymentsByProjectId($projectId)
	{
		$stmt = $this->db->prepare("
			SELECT payerName, SUM(totalAmount) as totalPaid
			FROM payment
			WHERE idProject = ?
			GROUP BY payerName
		");
		$stmt->execute(array($projectId));
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$payments = [];
		foreach ($result as $row) {
			$payments[$row['payerName']] = $row['totalPaid'];
		}

		return $payments;
	}

	/**
	 * Returns a Payment with the debtors included
	 * @param mixed $idPayment
	 * @param mixed $idProject
	 * @return Payment|null
	 */
	public function findByIdWithDebtors($idPayment, $idProject)
	{
		$stmt = $this->db->prepare("SELECT * FROM payment WHERE idPayment=? and idProject=?");
		$stmt->execute(array($idPayment, $idProject));
		$payment = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($payment != null) {
			$stmt = $this->db->prepare("SELECT debtorName FROM debt WHERE idPayment=? and idProject=?");
			$stmt->execute(array($idPayment, $idProject));
			$debtors_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$debtors = array();
			foreach ($debtors_db as $debtor)
				array_push($debtors, $debtor["debtorName"]);


			$toSave = new Payment();

			$toSave->setPayerName($payment["payerName"]);
			$toSave->setIdProject($payment["idProject"]);
			$toSave->setIdPayment($payment["idPayment"]);
			$toSave->setSubject($payment["subject"]);
			$toSave->setTotalAmount(totalAmount: $payment["totalAmount"]);
			$toSave->setDebtors($debtors);

			return $toSave;
		} else {
			return NULL;
		}
	}

}
