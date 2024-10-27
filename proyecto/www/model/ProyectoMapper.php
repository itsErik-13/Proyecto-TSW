<?php
// file: model/PostMapper.php
require_once(__DIR__."/../core/PDOConnection.php");

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/Proyecto.php");

/**
* Class ProyectoMapper
*
* Database interface for Proyecto entities
*
*/
class ProyectoMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

	/**
	* Devuelve todos los Proyectos
	*
	*
	* @throws PDOException si hay un error en la base de datos
	* @return mixed Array de Proyectos
	*/
	public function findAll() {
		$stmt = $this->db->query("SELECT * FROM projects");
		$proyectos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$proyectos = array();

		foreach ($proyectos_db as $proyecto) {
			array_push($proyectos, new Proyecto($proyecto["idProject"], $proyecto["projectName"], $proyecto["theme"]));
		}
		return $proyectos;
	}

	/**
	* Loads a Proyecto from the database given its id
	*
	* @throws PDOException if a database error occurs
	* @return Proyecto The Proyect instances. NULL
	* if the Proyecto is not found
	*/
	public function findById($proyectoid){
		$stmt = $this->db->prepare("SELECT * FROM projects WHERE idProject=?");
		$stmt->execute(array($proyectoid));
		$proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

		if($proyecto != null) {
			return new Proyecto(
			$proyecto["idProject"],
			$proyecto["projectName"],
			$proyecto["theme"]);
		} else {
			return NULL;
		}
	}

	public function findProyectsByMember($username) {
		$stmt = $this->db->prepare("SELECT * FROM projects WHERE idProject IN (SELECT idProject FROM members WHERE username=?)");
		$stmt->execute(array($username));
		$proyectos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$proyectos = array();

		foreach ($proyectos_db as $proyecto) {
			array_push($proyectos, new Proyecto($proyecto["idProject"], $proyecto["projectName"], $proyecto["theme"]));
		}
		return $proyectos;
	}

	/**
	* Loads a Post from the database given its id
	*
	* It includes all the comments
	*
	* @throws PDOException if a database error occurs
	* @return Post The Post instances (without comments). NULL
	* if the Post is not found
	*/
	public function findByIdWithComments($postid){
		$stmt = $this->db->prepare("SELECT
			P.id as 'post.id',
			P.title as 'post.title',
			P.content as 'post.content',
			P.author as 'post.author',
			C.id as 'comment.id',
			C.content as 'comment.content',
			C.post as 'comment.post',
			C.author as 'comment.author'

			FROM posts P LEFT OUTER JOIN comments C
			ON P.id = C.post
			WHERE
			P.id=? ");

			$stmt->execute(array($postid));
			$post_wt_comments= $stmt->fetchAll(PDO::FETCH_ASSOC);

			if (sizeof($post_wt_comments) > 0) {
				$post = new Post($post_wt_comments[0]["post.id"],
				$post_wt_comments[0]["post.title"],
				$post_wt_comments[0]["post.content"],
				new User($post_wt_comments[0]["post.author"]));
				$comments_array = array();
				if ($post_wt_comments[0]["comment.id"]!=null) {
					foreach ($post_wt_comments as $comment){
						$comment = new Comment( $comment["comment.id"],
						$comment["comment.content"],
						new User($comment["comment.author"]),
						$post);
						array_push($comments_array, $comment);
					}
				}
				$post->setComments($comments_array);

				return $post;
			}else {
				return NULL;
			}
		}

		/**
		* Saves a Proyect into the database
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
		* Deletes a Post into the database
		*
		* @param Post $post The post to be deleted
		* @throws PDOException if a database error occurs
		* @return void
		*/
		public function delete(Post $post) {
			$stmt = $this->db->prepare("DELETE from posts WHERE id=?");
			$stmt->execute(array($post->getId()));
		}

	}
