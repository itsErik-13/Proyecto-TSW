<?php
// file: model/PostMapper.php
require_once(__DIR__."/../core/PDOConnection.php");

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/Project.php");

/**
* Class ProyectoMapper
*
* Database interface for Proyecto entities
*
*/
class ProjectMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}


	/**
	* Loads a Proyecto from the database given its id
	*
	* @throws PDOException if a database error occurs
	* @return Project The Proyect instances. NULL
	* if the Proyecto is not found
	*/
	public function findByIdProject($idProject){
		$stmt = $this->db->prepare("SELECT * FROM project WHERE idProject=?");
		$stmt->execute(array($idProject));
		$project = $stmt->fetch(PDO::FETCH_ASSOC);
		if($project != null) {
			return new Project(
			$project["idProject"],
			$project["projectName"],
			$project["theme"]);
		} else {
			return NULL;
		}
	}

	/**
	 * Gives the projects an user is involved in
	 * @param mixed $userName the name of the user
	 * @return array the projects the user can view
	 */
	public function findProjectByMember($userName) {
		$stmt = $this->db->prepare("SELECT * FROM project WHERE idProject IN (SELECT idProject FROM member WHERE userName=?)");
		$stmt->execute(array($userName));
		$projects_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$projects = array();

		foreach ($projects_db as $project) {
			array_push($projects, new Project($project["idProject"], $project["projectName"], $project["theme"]));
		}
		return $projects;
	}

		/**
		* Saves a Project into the database
		*
		* @param Post $post The project to be saved
		* @param User $user The author of the project
		* @throws PDOException if a database error occurs
		* @return int The mew project id
		*/
		public function save(Project $project, User $user) {
			$stmt = $this->db->prepare("INSERT INTO project (projectName, theme) values (?,?)");
			$stmt->execute(array($project->getProjectName(), $project->getTheme()));
            $id = $this->db->lastInsertId();
            $stmt = $this->db->prepare("INSERT INTO member (idProject, userName) values (?,?)");
            $stmt->execute(array($id, $user->getUserName()));
			return $id;
		}

		/**
		* Adds a member to a project
		*
		* @param Project $proyecto The project member will be added
		* @throws PDOException if a database error occurs
		* @return int The mew post id
		*/
		public function addMember(Project $project, User $user) {
			if($this->canAddMember($project, $user)) {
			$stmt = $this->db->prepare("INSERT INTO member (idProject, userName) values (?,?)");
			$stmt->execute(array($project->getIdProject(), $user->getUserName()));
			return $this->db->lastInsertId();
			}else{
				$errors = array();
				$errors["email"] = "The user is already on the project";
				throw New ValidationException($errors);
			}
		}


		/**
		 * Gives the members of a project
		 * @param Project $project the project to list members
		 * @return array the members of the project
		 */
		public function getMembers(Project $project) {
			$stmt = $this->db->prepare("SELECT * FROM member WHERE idProject=?");
			$stmt->execute(array($project->getIdProject()));
			$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $members;
		}

		/**
		* Deletes a Project into the database
		*
		* @param Post $post The project to be deleted
		* @throws PDOException if a database error occurs
		* @return void
		*/
		public function delete(Project $project) {
			$stmt = $this->db->prepare("DELETE from project WHERE idProject=?");
			$stmt->execute(array($project->getIdProject()));
		}


		/**
		 * Checks if a member can be added
		 * @param Project $project
		 * @param User $user
		 * @return bool true if the member can be added , false if not
		 */
		public function canAddMember(Project $project, User $user) {
			$stmt = $this->db->prepare("SELECT * FROM member WHERE idProject=? AND userName=?");
			$stmt->execute(array($project->getIdProject(), $user->getUsername()));
			$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return sizeof($members) == 0;
		}


		/**
		 * Checks if a member can manage a project
		 * @param Project $project
		 * @param mixed $userName
		 * @return bool true fi the member can manage the project, false otherwise
		 */
		public function canManageProject(Project $project,  $userName) {
			$stmt = $this->db->prepare("SELECT * FROM member WHERE idProject=? AND userName=?");
			$stmt->execute(array($project->getIdProject(), $userName));
			$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return sizeof($members) != 0;
		}

	}
