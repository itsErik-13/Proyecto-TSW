<?php
// file: model/UserMapper.php

require_once(__DIR__."/../core/PDOConnection.php");

/**
* Class UserMapper
*
* Database interface for User entities
*
* @author lipido <lipido@gmail.com>
*/
class UserMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

	/**
	* Saves a User into the database
	*
	* @param User $user The user to be saved
	* @throws PDOException if a database error occurs
	* @return void
	*/
	public function save($user) {
		$stmt = $this->db->prepare("INSERT INTO user values (?,?,?)");
		$stmt->execute(array($user->getUserName(), $user->getEmail(), $user->getPassword()));
	}

	/**
	* Checks if a given username is already in the database
	*
	* @param string $username the username to check
	* @return boolean true if the username exists, false otherwise
	*/
	public function usernameExists($userName) {
		$stmt = $this->db->prepare("SELECT count(userName) FROM user where userName=?");
		$stmt->execute(array($userName));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}

	/**
	* Checks if a given pair of username/password exists in the database
	*
	* @param string $username the username
	* @param string $passwd the password
	* @return boolean true the username/passwrod exists, false otherwise.
	*/
	public function isValidUser($userName, $password) {
		$stmt = $this->db->prepare("SELECT count(userName) FROM user where userName=? and password=?");
		$stmt->execute(array($userName, $password));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}

	/**
	 * Gives the user by the email
	 * @param mixed $email
	 * @return User|null the user given its email, null if none exists.
	 */
	public function getUserByEmail($email) {
		$stmt = $this->db->prepare("SELECT * FROM user where email=?");
		$stmt->execute(array($email));

		if ($stmt->rowCount() == 0) {
			return NULL;
		}

		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		return new User($user["userName"], $user["password"], $user["email"] ,);
	}
}
