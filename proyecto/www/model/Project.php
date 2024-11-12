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
class Project {

	/**
	* The id of this project
	* @var string
	*/
	private $idProject;

	/**
	* The name of this project
	* @var string
	*/
	private $projectName;

	/**
	* The theme of this post
	* @var string
	*/
	private $theme;

	/**
	* The constructor
	*
	* @param string $id The id of the project
	* @param string $projectName The projectName of the project
	* @param string $theme The theme of the project
	*/
	public function __construct($idProject=NULL, $projectName=NULL, $theme=NULL) {
		$this->idProject = $idProject;
		$this->projectName = $projectName;
		$this->theme = $theme;

	}

	/**
	* Gets the id of this project
	*
	* @return string The id of this project
	*/
	public function getIdProject() {
		return $this->idProject;
	}

	/**
	* Gets the name of this project
	*
	* @return string The name of this project
	*/
	public function getProjectName() {
		return $this->projectName;
	}

	/**
	* Sets the name of this project
	*
	* @param string $name the name of this project
	* @return void
	*/
	public function setProjectName($projectName) {
		$this->projectName = $projectName;
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
		if (strlen(trim($this->projectName)) == 0 ) {
			$errors["projectName"] = "Name is mandatory";
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

		if (!isset($this->projectName)) {
			$errors["projectName"] = "Name is mandatory";
		}
		if (sizeof($errors) > 0) {
			throw new ValidationException($errors, "Project is not valid");
		}
	}
}
