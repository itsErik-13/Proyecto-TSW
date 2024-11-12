<?php
//file: controller/PostController.php

require_once(__DIR__ . "/../model/Project.php");
require_once(__DIR__ . "/../model/ProjectMapper.php");
require_once(__DIR__ . "/../model/PaymentMapper.php");
require_once(__DIR__ . "/../model/UserMapper.php");
require_once(__DIR__ . "/../model/User.php");

require_once(__DIR__ . "/../core/ViewManager.php");
require_once(__DIR__ . "/../controller/BaseController.php");

/**
 * Class ProjectController
 *
 * Controller to make a CRUDL of Project entities
 *
 * @author lipido <lipido@gmail.com>
 */
class ProjectController extends BaseController
{

	/**
	 * Reference to the ProyectoMapper to interact
	 * with the database
	 *
	 * @var ProyectoMapper
	 */
	private $projectMapper;
	private $userMapper;
	private $paymentMapper;

	public function __construct()
	{
		parent::__construct();
		$this->view->setLayout("default");
		$this->projectMapper = new ProjectMapper();
		$this->userMapper = new UserMapper();
		$this->paymentMapper = new PaymentMapper();
	}

	/**
	 * Action to list proyects
	 *
	 * Loads all the proyects from the database.
	 * No HTTP parameters are needed.
	 */
	public function index()
	{

		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding posts requires login");
		}
		// obtain the data from the database
		$projects = $this->projectMapper->findProjectByMember($this->currentUser->getUserName());

		// put the array containing Proyect object to the view
		$this->view->setVariable("projects", $projects);

		// render the view (/view/proyectos/index.php)
		$this->view->render("project", "index");
	}

	/**
	 * Action to view a given proyect
	 *
	 * This action should only be called via GET
	 *
	 * The expected HTTP parameters are:
	 * <ul>
	 * <li>id: Id of the proyect (via HTTP GET)</li>
	 * </ul>
	 *
	 * @throws Exception if the user is not logged in
	 * @throws Exception if no idProject is given
	 * @return void
	 *
	 */
	public function view()
	{

		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding posts requires login");
		}

		if (!isset($_GET["idProject"])) {
			throw new Exception("id is mandatory");
		}

		$idProject = $_GET["idProject"];


		$project = $this->projectMapper->findByIdProject($idProject);
		
		if ($this->projectMapper->canManageProject($project, $this->currentUser->getUserName()) == false) {
			throw new Exception("You should be part of the project to view or edit it");
		}


		if ($project == NULL) {
			throw new Exception("no such proyect with id: " . $idProject);
		}

		$this->view->setVariable("project", $project);
		$payments = $this->paymentMapper->findByIdProjectWithDebtors($idProject);
		$this->view->setVariable("payments", $payments);

		$this->view->render("payment", "index");
	}

	/**
	 * Action to add a new proyect
	 *
	 * When called via GET, it shows the add form
	 * When called via POST, it adds the project to the
	 * database
	 *
	 * @throws Exception if no user is in session
	 * @return void
	 */
	public function add()
	{
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding proyects requires login");
		}

		$project = new Project();

		if (isset($_POST["submit"])) { // reaching via HTTP Post...

			// populate the Proyect object with data form the form
			$project->setProjectName($_POST["projectName"]);
			$project->setTheme($_POST["theme"]);


			try {
				// validate Proyecto object
				$project->checkIsValidForCreate(); 

				$this->projectMapper->save($project, $this->currentUser);

				// POST-REDIRECT-GET
				// Everything OK, we will redirect the user to the list of proyectos
				// We want to see a message after redirection, so we establish
				// a "flash" message (which is simply a Session variable) to be
				// get in the view after redirection.
				$this->view->setFlash(sprintf(i18n("Project \"%s\" successfully added."), $project->getProjectName()));

				$this->view->redirect("project", "index");

			} catch (ValidationException $ex) {
				// Get the errors array inside the exepction...
				$errors = $ex->getErrors();
				// And put it to the view as "errors" variable
				$this->view->setVariable("errors", $errors);
			}
		}

		// Put the Proyect object visible to the view
		$this->view->setVariable("project", $project);

		// render the view (/view/proyectos/add.php)
		$this->view->render("project", "index");

	}


	/**
	 * Action to delete a project
	 *
	 * This action should only be called via HTTP POST
	 *
	 * @throws Exception if no idProject was provided
	 * @throws Exception if no user is in session
	 * @throws Exception if there is not any project with the provided id
	 * @return void
	 */
	public function delete()
	{
		if (!isset($_POST["idProject"])) {
			throw new Exception("id is mandatory");
		}
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Editing proyects requires login");
		}

		// Get the Proyect object from the database
		$idProject = $_REQUEST["idProject"];
		$project = $this->projectMapper->findByIdProject($idProject);

		if ($project == NULL) {
			throw new Exception("no such proyect with id: " . $idProject);
		}

		$this->projectMapper->delete($project);

		// POST-REDIRECT-GET
		// Everything OK, we will redirect the user to the list of posts
		// We want to see a message after redirection, so we establish
		// a "flash" message (which is simply a Session variable) to be
		// get in the view after redirection.
		$this->view->setFlash(sprintf(i18n("Project \"%s\" successfully deleted."), $project->getProjectName()));


		$this->view->redirect("project", "index");

	}

	/**
	 * Action to add a member to a project
	 *
	 * When called via GET, it shows the add form
	 * When called via POST, it adds the project to the
	 * database
	 *
	 * @throws Exception if no user is in session
	 * @throws Exception if the user cannot manage the project
	 * @return void
	 */
	public function addMember()
	{
		$member = new User();
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding members requires login");
		}
		if (isset($_POST["submit"])) { // reaching via HTTP Post...
			$project = $this->projectMapper->findByIdProject($_POST["idProject"]);
			if ($this->projectMapper->canManageProject($project, $this->currentUser->getUserName()) == false) {
				throw new Exception("You should be part of the project to view or edit it");
			}
			try {
				$member = $this->userMapper->getUserByEmail($_POST["email"]);
				if ($member == NULL) {
					$errors = array();
					$errors["email"] = "User not found";
					throw new ValidationException($errors, "User not found");
				}
				// validate Proyecto object
				$this->projectMapper->addMember($project, $member); // if it fails, ValidationException

				$this->view->setFlash(sprintf(i18n("Member \"%s\" successfully added."), $member->getUserName()));


				// POST-REDIRECT-GET
				// Everything OK, we will redirect the user to the list of proyectos
				// We want to see a message after redirection, so we establish
				// a "flash" message (which is simply a Session variable) to be
				// get in the view after redirection.

				$this->view->setVariable("project", $project);
				$this->view->setVariable("members", $this->projectMapper->getMembers($project));

				$this->view->redirect("project", "viewMembers", "idProject=" . $project->getIdProject());

			} catch (ValidationException $ex) {
				$errors = $ex->getErrors();
				$this->view->setVariable("errors", $errors);
				if ($member == NULL) {
					$member = new User(email: $_POST["email"]);
				}
				$this->view->setVariable("member", $member);
			}
		} else {
			$project = $this->projectMapper->findByIdProject($_GET["idProject"]);
			if ($this->projectMapper->canManageProject($project, $this->currentUser->getUserName()) == false) {
				throw new Exception("You should be part of the project to view or edit it");
			}
		}
		$this->view->setVariable("project", $project);
		$this->view->setVariable("member", $member);

		$this->view->render("project", "addMember", array("idProject" => $project->getIdProject()));
	}


	/**
	 * List the members of a project
	 * @throws Exception if the user is not in session
	 * @throws Exception if no idProject is given
	 * @throws Exception if the user cannot manage the project
	 */
	public function viewMembers()
	{

		if (!isset($_GET["idProject"])) {
			throw new Exception("id is mandatory");
		}
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Viewing members requires login");
		}
		$project = $this->projectMapper->findByIdProject($_GET["idProject"]);
		if ($this->projectMapper->canManageProject($project, $this->currentUser->getUsername()) == false) {
			throw new Exception("You should be part of the project to view or edit it");
		}
		$members = $this->projectMapper->getMembers($project);
		$this->view->setVariable("members", $members);
		$this->view->setVariable("project", $project);
		$this->view->render("project", "viewmembers");
	}


}