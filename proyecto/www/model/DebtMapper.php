<?php
// file: model/DebtMapper.php
require_once(__DIR__ . "/../core/PDOConnection.php");

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/Payment.php");
require_once(__DIR__ . "/../model/Debt.php");

/**
 * Class DebtMapper
 *
 * Database interface for Debt entities
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
	 * Returns a Debt given its id's
	 * @param string $idPayment
	 * @param string $idProject
	 * @return Debt|null
	 */
	public function findById(string $idPayment, string $idProject)
	{
		$stmt = $this->db->query("SELECT * FROM debt where idPayment = $idPayment and idProject = $idProject");
		$debt = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($debt != null) {
			return new Debt($debt["idProject"], $debt["idPayment"], $debt["debtorName"]);
		}
		
		return NULL;
	}

	/**
	 * Return the debts of an user
	 * @param string $debtorName
	 * @param string $idPayment
	 * @param string $idProject
	 * @return array|null
	 */
    public function findByDebtorName(string $debtorName, string $idPayment, string $idProject)
	{
		$stmt = $this->db->query("SELECT * FROM debt where idPayment = $idPayment and idProject = $idProject and debtorName = '$debtorName'");
		$debt_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $debts = array();
		if ($debt_db != null) {
            foreach ($debt_db as $debt) {
                array_push($debts, new Debt($debt["idProject"], $debt["idPayment"], $debt["debtorName"]));
            }
            return $debts;
		}
		
		return NULL;
	}

	/**
	 * Devuelve todos los Debts de un proyecto
	 * 
	 * 
	 * @throws PDOException si hay un error en la base de datos
	 * @return mixed Array de Debts
	 */
	public function findByIdProject($idProject)
	{
		$stmt = $this->db->prepare("SELECT * FROM payment WHERE idProject=?");
		$stmt->execute(array($idProject));
		$payment = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$payments = array();


		if ($payment != null) {
			foreach ($payment as $gasto) {
				$toSave =  new Payment();

				$toSave->setPayerName($gasto["payerName"]);
				$toSave->setIdProject($gasto["idProject"]);
				$toSave->setIdPayment($gasto["idPayment"]);
				$toSave->setSubject($gasto["subject"]);
				$toSave->setTotalAmount($gasto["totalAmount"]);
				array_push($payments, $gasto);
			}
			return $payments;
		} else {
			return NULL;
		}
	}


	/**
	 * Returns the amount that a user needs to pay in a payment
	 * @param mixed $idPayment
	 * @param mixed $idProject
	 * @return float
	 */
	public function getRelativeAmount($idPayment, $idProject) {
		//Calcular totalamount
		$stmt = $this->db->prepare("	
			SELECT totalAmount FROM payment WHERE idPayment = ? and idProject = ?	
		");
		$stmt->execute(array($idPayment, $idProject));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$totalAmount = $result['totalAmount'];
		//Calcular numero de deudores
		$stmt = $this->db->prepare("	
			SELECT COUNT(*) as numDebtors FROM debt WHERE idPayment = ? and idProject = ?	
		");
		$stmt->execute(array($idPayment, $idProject));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$numDebtors = $result['numDebtors'];
		//Calcular relativeAmount
		return $totalAmount / $numDebtors;
	}

	/**
	 * Saves a Debt into the database
	 * 
	 * @param Debt $debt The debt to be saved
	 * @throws PDOException if a database error occurs
	 * @return int The new debt id
	 */
	public function save(Debt $debt)
	{
		$stmt = $this->db->prepare("INSERT INTO debt (idProject, idPayment, debtorName) values (?,?,?)");
		$stmt->execute(array($debt->getIdProject(), $debt->getIdPayment(), $debt->getDebtorName()));
		// Falta actualizar la tabla de deudas
		return $this->db->lastInsertId();
    }

	
	/**
	 * Deletes all the debts of a payment
	 *
	 * @param Payment $payment The payment where whe need to delete the debts
	 * @throws PDOException if a database error occurs
	 * @return void
	 */
	public function deleteAll(Payment $payment)
	{
		$stmt = $this->db->prepare("DELETE from debt WHERE idPayment=? and idProject=?");
		$stmt->execute(array($payment->getIdPayment(), $payment->getIdProject()));
	}

	/**
	 * Returns an array with the debtor name, the receiver name and the amount for each payment of the project given
	 * @param mixed $projectId
	 * @return array
	 */
	public function findTotalDebtsByProjectId($projectId)
	{
		$stmt = $this->db->prepare("
			SELECT  debt.debtorName, SUM(p.totalAmount / 
				(SELECT COUNT(*) FROM debt AS d WHERE d.idPayment = debt.idPayment)
       		) AS totalDebt 
			FROM debt 
			JOIN payment p ON p.idPayment = debt.idPayment 
			WHERE debt.idProject = ?
			GROUP BY debt.debtorName;
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
