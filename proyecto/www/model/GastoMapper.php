<?php
// file: model/GastoMapper.php
require_once(__DIR__ . "/../core/PDOConnection.php");

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/Gasto.php");

/**
 * Class GastoMapper
 *
 * Database interface for Gasto entities
 *
 */
class GastoMapper
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
	 * Devuelve todos los Gastos
	 *
	 *
	 * @throws PDOException si hay un error en la base de datos
	 * @return mixed Array de Gastos
	 */
	public function findAll()
	{
		$stmt = $this->db->query("SELECT * FROM payments");
		$gastos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$gastos = array();

		foreach ($gastos_db as $gasto) {
			array_push($gastos, new Gasto($gasto["idPayer"], $gasto["idProject"], $gasto["idPayment"], $gasto["debt"], $gasto["totalAmount"]));
		}
		return $gastos;
	}


	public function findById(string $idPayment)
	{
		$stmt = $this->db->query("SELECT * FROM payments where idPayment = $idPayment");
		$payment = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($payment != null) {
			return new Gasto($payment["payerName"], $payment["idProject"], $payment["idPayment"], $payment["debt"], $payment["totalAmount"]);
		}

		return NULL;
	}

	/**
	 * Devuelve todos los Gastos de un proyecto
	 * 
	 * 
	 * @throws PDOException si hay un error en la base de datos
	 * @return mixed Array de Gastos
	 */
	public function findByProjectId($proyectoid)
	{
		$stmt = $this->db->prepare("SELECT * FROM payments WHERE idProject=?");
		$stmt->execute(array($proyectoid));
		$payment = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$payments = array();


		if ($payment != null) {
			foreach ($payment as $gasto) {
				$toSave = new Gasto();

				$toSave->setPayerId($gasto["payerName"]);
				$toSave->setProjectId($gasto["idProject"]);
				$toSave->setPaymentId($gasto["idPayment"]);
				$toSave->setDebt($gasto["debt"]);
				$toSave->setTotalAmount($gasto["totalAmount"]);
				array_push($payments, $gasto);
			}
			return $payments;
		} else {
			return NULL;
		}
	}


	public function findByProjectIdWithDebtors($proyectoid)
	{
		$stmt = $this->db->prepare("SELECT * FROM payments WHERE idProject=?");
		$stmt->execute(array($proyectoid));
		$payment = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$payments = array();


		if ($payment != null) {
			foreach ($payment as $gasto) {
				$stmt = $this->db->prepare("SELECT debtorName FROM debts WHERE idProject=? and idPayment=?");
				$stmt->execute(array($proyectoid, $gasto["idPayment"]));
				$debtors_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$debtors = array();
				foreach ($debtors_db as $debtor)
					array_push($debtors, $debtor["debtorName"]);


				$toSave = new Gasto();

				$toSave->setPayerId($gasto["payerName"]);
				$toSave->setProjectId($gasto["idProject"]);
				$toSave->setPaymentId($gasto["idPayment"]);
				$toSave->setDebt($gasto["debt"]);
				$toSave->setTotalAmount($gasto["totalAmount"]);
				$toSave->setDebtors($debtors);
				array_push($payments, $toSave);
			}
			return $payments;
		} else {
			return NULL;
		}
	}


	public function findByIdWithDebtors($idPayment)
	{
		$stmt = $this->db->prepare("SELECT * FROM payments WHERE idPayment=?");
		$stmt->execute(array($idPayment));
		$payment = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($payment != null) {
			$stmt = $this->db->prepare("SELECT debtorName FROM debts WHERE idPayment=?");
			$stmt->execute(array($idPayment));
			$debtors_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$debtors = array();
			foreach ($debtors_db as $debtor)
				array_push($debtors, $debtor["debtorName"]);


			$toSave = new Gasto();

			$toSave->setPayerId($payment["payerName"]);
			$toSave->setProjectId($payment["idProject"]);
			$toSave->setPaymentId($payment["idPayment"]);
			$toSave->setDebt($payment["debt"]);
			$toSave->setTotalAmount(totalAmount: $payment["totalAmount"]);
			$toSave->setDebtors($debtors);

			return $toSave;
		} else {
			return NULL;
		}
	}

	/**
	 * Saves a Payment into the database
	 * 
	 * @param Gasto $gasto The payment to be saved
	 * @throws PDOException if a database error occurs
	 * @return int The new payment id
	 */
	public function save(Gasto $gasto)
	{
		$stmt = $this->db->prepare("INSERT INTO payments (payerName, idProject, debt, totalAmount) values (?,?,?,?)");
		$stmt->execute(array($gasto->getPayerId(), $gasto->getProjectId(), $gasto->getDebt(), $gasto->getTotalAmount()));
		// Falta actualizar la tabla de deudas
		return $this->db->lastInsertId();
	}


	/**
	 * Updates a Post in the database
	 *
	 * @param Post $post The post to be updated
	 * @throws PDOException if a database error occurs
	 * @return void
	 */
	public function update(Gasto $gasto)
	{
		$stmt = $this->db->prepare("UPDATE payments set payerName=?, totalAmount=?, debt=? where idPayment=? and idProject=?");
		$stmt->execute(array($gasto->getPayerId(), $gasto->getTotalAmount(), $gasto->getDebt(), $gasto->getPaymentId(), $gasto->getProjectId()));
	}

	/**
	 * Deletes a Payment into the database
	 *
	 * @param Gasto $gasto The payment to be deleted
	 * @throws PDOException if a database error occurs
	 * @return void
	 */
	public function delete(Gasto $gasto)
	{
		$stmt = $this->db->prepare("DELETE from payments WHERE idPayment=?");
		$stmt->execute(array($gasto->getPaymentId()));
	}
	// Cuando se elimina un proyecto se eliminan todos los miembros de ese proyecto en cascada?
	// Y aquí con las deudas hay que hacer algo similar?

	public function getDebtors($idPayment)
	{
		$stmt = $this->db->prepare("SELECT * FROM debts WHERE idPayment=?");
		$stmt->execute(array($idPayment));
		$debtors = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $debtors;
	}


	public function findTotalPaymentsByProjectId($projectId) {
		$stmt = $this->db->prepare("
			SELECT payerName, SUM(totalAmount) as totalPaid
			FROM payments
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

}
