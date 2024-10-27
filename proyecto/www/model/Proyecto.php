<?php
// file: model/Post.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Proyecto
*
* Representa un proyecto en la web, creado por un usuario.
*
* @author lipido <lipido@gmail.com>
*/
class Proyecto {

	/**
	* The id of this project
	* @var string
	*/
	private $id;

	/**
	* The name of this project
	* @var string
	*/
	private $name;

	/**
	* The theme of this post
	* @var string
	*/
	private $theme;

	/**
	* The constructor
	*
	* @param string $id The id of the project
	* @param string $name The name of the project
	* @param string $theme The theme of the project
	*/
	public function __construct($id=NULL, $name=NULL, $theme=NULL) {
		$this->id = $id;
		$this->name = $name;
		$this->theme = $theme;

	}

	/**
	* Gets the id of this project
	*
	* @return string The id of this project
	*/
	public function getId() {
		return $this->id;
	}

	/**
	* Gets the name of this project
	*
	* @return string The name of this project
	*/
	public function getName() {
		return $this->name;
	}

	/**
	* Sets the name of this project
	*
	* @param string $name the name of this project
	* @return void
	*/
	public function setName($name) {
		$this->name = $name;
	}

	/**
	* Gets the theme of this project
	*
	* @return string The theme of this project
	*/
	public function getTheme() {
		return $this->theme;
	}

	/**
	* Sets the theme of this project
	*
	* @param string $theme the theme of this project
	* @return void
	*/
	public function setTheme($theme) {
		$this->theme = $theme;
	}

	/**
	* Checks if the current instance is valid
	* for being updated in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForCreate() {
		$errors = array();
		if (strlen(trim($this->name)) == 0 ) {
			$errors["name"] = "Name is mandatory";
		}
		if (strlen(trim($this->theme)) == 0 ) {
			$errors["theme"] = "Theme is mandatory";
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "Project is not valid");
		}
	}

	/**
	* Checks if the current instance is valid
	* for being updated in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForUpdate() {
		$errors = array();

		if (!isset($this->id)) {
			$errors["id"] = "id is mandatory";
		}

		try{
			$this->checkIsValidForCreate();
		}catch(ValidationException $ex) {
			foreach ($ex->getErrors() as $key=>$error) {
				$errors[$key] = $error;
			}
		}
		if (sizeof($errors) > 0) {
			throw new ValidationException($errors, "Project is not valid");
		}
	}
}
