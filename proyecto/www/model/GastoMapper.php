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
	* @param Post $post The post to be saved
	* @throws PDOException if a database error occurs
	* @return int The mew post id
	*/
	public function save(Proyecto $proyecto, User $user) {
		$stmt = $this->db->prepare("INSERT INTO projects (projectName, theme) values (?,?)");
		$stmt->execute(array($proyecto->getName(), $proyecto->getTheme()));
        $id = $this->db->lastInsertId();
        $stmt = $this->db->prepare("INSERT INTO members (idProject, username) values (?,?)");
        $stmt->execute(array($id, $user->getUsername()));
		return $this->db->lastInsertId();
	}

	public function save(Gasto $gasto) {
		$stmt = $this->db->prepare("INSERT INTO payments (payerId, projectId, paymentId, debt, totalAmount) values (?,?,?,?,?)");
		$stmt->execute(array($gasto->getPayerId(), $gasto->getProjectId(), $gasto->getPaymentId(), $gasto->getDebt(), $gasto->getTotalAmount()));
	}

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

	/**
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

	}
