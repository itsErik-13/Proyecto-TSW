<?php
// file: model/DebtMapper.php
require_once(__DIR__ . "/../core/PDOConnection.php");

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/Gasto.php");
require_once(__DIR__ . "/../model/Debt.php");

/**
 * Class DebtMapper
 *
 * Database interface for Gasto entities
 *
 */
class DebtMapper
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


	public function findByIdPayment(string $idPayment)
	{
		$stmt = $this->db->query("SELECT * FROM debts where idPayment = $idPayment");
		$debt = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($debt != null) {
			return new Debt($debt["debtorName"], $debt["idProject"], $debt["idPayment"], $debt["relativeAmount"]);
		}
		
		return NULL;
	}

    public function findByDebtorName(string $debtorName, string $idPayment)
	{
		$stmt = $this->db->query("SELECT * FROM debts where idPayment = $idPayment and debtorName = '$debtorName'");
		$debt_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $debts = array();
		if ($debt_db != null) {
            foreach ($debt_db as $debt) {
                array_push($debts, new Debt($debt["debtorName"], $debt["idProject"], $debt["idPayment"], $debt["relativeAmount"]));
            }
            return $debts;
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
				$toSave =  new Gasto();

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

	/**
	 * Saves a Payment into the database
	 * 
	 * @param Gasto $gasto The payment to be saved
	 * @throws PDOException if a database error occurs
	 * @return int The new payment id
	 */
	public function save(Debt $debt)
	{
		$stmt = $this->db->prepare("INSERT INTO debts (idProject, idPayment, debtorName, relativeAmount) values (?,?,?,?)");
		$stmt->execute(array($debt->getProjectId(), $debt->getPaymentId(), $debt->getDebtorName(), $debt->getRelativeAmount()));
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
	public function update(Post $post)
	{
		$stmt = $this->db->prepare("UPDATE posts set title=?, content=? where id=?");
		$stmt->execute(array($post->getTitle(), $post->getContent(), $post->getId()));
	}

	/**
	 * Deletes a Payment into the database
	 *
	 * @param Gasto $gasto The payment to be deleted
	 * @throws PDOException if a database error occurs
	 * @return void
	 */
	public function deleteAll(Gasto $payment)
	{
		$stmt = $this->db->prepare("DELETE from debts WHERE idPayment=?");
		$stmt->execute(array($payment->getPaymentId()));

	}

	public function findTotalDebtsByProjectId($projectId) {
		$stmt = $this->db->prepare("
			SELECT debtorName, SUM(relativeAmount) as totalDebt
			FROM debts
			WHERE idProject = ?
			GROUP BY debtorName
		");
		$stmt->execute(array($projectId));
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		$debts = [];
		foreach ($result as $row) {
			$debts[$row['debtorName']] = $row['totalDebt'];
		}
	
		return $debts;
	}




}
