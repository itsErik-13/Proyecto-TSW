<?php
// file: model/User.php

require_once(__DIR__ . "/../core/ValidationException.php");

/**
 * Class User
 *
 * Represents a User
 *
 * @author lipido <lipido@gmail.com>
 */
class User
{

	/**
	 * The user name of the user
	 * @var string
	 */
	private $userName;

	/**
	 * The password of the user
	 * @var string
	 */
	private $password;

	/**
	 * The email of the user
	 * @var string
	 */
	private $email;

	private $password2;

	/**
	 * The constructor
	 *
	 * @param string $username The name of the user
	 * @param string $passwd The password of the user
	 * @param string $email The email of the user
	 */
	public function __construct($userName = NULL, $password = NULL, $email = NULL, $password2 = NULL)
	{
		$this->userName = $userName;
		$this->password = $password;
		$this->email = $email;
		$this->password2 = $password2;
	}

	/**
	 * Gets the username of this user
	 *
	 * @return string The username of this user
	 */
	public function getUserName()
	{
		return $this->userName;
	}

	/**
	 * Sets the username of this user
	 *
	 * @param string $username The username of this user
	 * @return void
	 */
	public function setUserName($userName)
	{
		$this->userName = $userName;
	}

	/**
	 * Gets the password of this user
	 *
	 * @return string The password of this user
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Gets the password2 of this user
	 *
	 * @return string The password of this user
	 */
	public function getPassword2()
	{
		return $this->password2;
	}
	/**
	 * Sets the password of this user
	 *
	 * @param string $passwd The password of this user
	 * @return void
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * Sets the password of this user
	 *
	 * @param string $passwd The password of this user
	 * @return void
	 */
	public function setPassword2($password2)
	{
		$this->password2 = $password2;
	}

	/**
	 * Gets the email of this user
	 *
	 * @return string The email of this user
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Sets the email of this user
	 *
	 * @param string $email The email of this user
	 * @return void
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * Checks if the current user instance is valid
	 * for being registered in the database
	 *
	 * @throws ValidationException if the instance is
	 * not valid
	 *
	 * @return void
	 */
	public function checkIsValidForRegister()
	{
		$errors = array();
		if (strlen($this->userName) < 4) {
			$errors["userName"] = "Username must be at least 4 characters length";
		}
		if (strlen($this->password) < 4) {
			$errors["password"] = "Password must be at least 4 characters length";
		}if (filter_var($this->email, FILTER_VALIDATE_EMAIL) == false) {
			$errors["email"] = "Email must be valid";
		}if ($this->password2 != $this->password) {
			$errors["password2"] = "Passwords must match";
		}
		if (sizeof($errors) > 0) {
			throw new ValidationException($errors, "User is not valid");
		}
	}
}