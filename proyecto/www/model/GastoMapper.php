<?php
// file: model/GastoMapper.php
require_once(__DIR__."/../core/PDOConnection.php");

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/Gasto.php");

/**
* Class GastoMapper
*
* Database interface for Gasto entities
*
*/
class GastoMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

	/**
	* Devuelve todos los Gastos
	*
	*
	* @throws PDOException si hay un error en la base de datos
	* @return mixed Array de Gastos
	*/
	public function findAll() {
		$stmt = $this->db->query("SELECT * FROM payments");
		$gastos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$gastos = array();

		foreach ($gastos_db as $gasto) {
			array_push($gastos, new Gasto($gasto["idPayer"], $gasto["idProject"], $gasto["idPayment"], $gasto["debt"], $gasto["totalAmount"]));
		}
		return $gastos;
	}

	/**
	 * Devuelve todos los Gastos de un proyecto
	 * 
	 * 
	 * @throws PDOException si hay un error en la base de datos
	 * @return mixed Array de Gastos
	 */
	public function findByProjectId($proyectoid) {
		$stmt = $this->db->prepare("SELECT * FROM payments WHERE idProject=?");
		$stmt->execute(array($proyectoid));
		$proyecto = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if($proyecto != null) {
			return new Gasto(
				$proyecto["idPayer"],
				$proyecto["idProject"],
				$proyecto["idPayment"],
				$proyecto["debt"],
				$proyecto["totalAmount"]);
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
	public function save(Gasto $gasto) {
		$stmt = $this->db->prepare("INSERT INTO payments (payerId, projectId, debt, totalAmount) values (?,?,?,?)");
		$stmt->execute(array($gasto->getPayerId(), $gasto->getProjectId(), $gasto->getDebt(), $gasto->getTotalAmount()));
		// Falta actualizar la tabla de deudas
		return $this->db->lastInsertId();
	}

	// Falta añadir los duedores a la tabla de deudas (addMember) (getMembers)

	/**
	* Updates a Post in the database
	*
	* @param Post $post The post to be updated
	* @throws PDOException if a database error occurs
	* @return void
	*/
	public function update(Post $post) {
		$stmt = $this->db->prepare("UPDATE posts set title=?, content=? where id=?");
		$stmt->execute(array($post->getTitle(), $post->getContent(), $post->getId()));
	}
	// Como es que hacemos nostros los update de proyectos?

	/*
	* Deletes a Project into the database
	*
	* @param Post $post The project to be deleted
	* @throws PDOException if a database error occurs
	* @return void
	*/
	public function delete(Proyecto $project) {
		$stmt = $this->db->prepare("DELETE from projects WHERE idProject=?");
		$stmt->execute(array($project->getId()));
	}

	/**
	* Deletes a Payment into the database
	*
	* @param Gasto $gasto The payment to be deleted
	* @throws PDOException if a database error occurs
	* @return void
	*/
	public function delete(Gasto $gasto) {
		$stmt = $this->db->prepare("DELETE from payments WHERE idPayment=?");
		$stmt->execute(array($gasto->getPaymentId()));
	}
	// Cuando se elimina un proyecto se eliminan todos los miembros de ese proyecto en cascada?
	// Y aquí con las deudas hay que hacer algo similar?

	}
