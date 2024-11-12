<?php
//file: controller/GastosController.php

require_once(__DIR__ . "/../model/Payment.php");
require_once(__DIR__ . "/../model/PaymentMapper.php");

require_once(__DIR__ . "/../model/Project.php");
require_once(__DIR__ . "/../model/ProjectMapper.php");

require_once(__DIR__ . "/../model/Debt.php");
require_once(__DIR__ . "/../model/DebtMapper.php");

require_once(__DIR__ . "/../model/User.php");

require_once(__DIR__ . "/../core/ViewManager.php");
require_once(__DIR__ . "/../controller/BaseController.php");

/**
 * Class PaymentController
 *
 * Controller to make a CRUDL of Proyects entities
 *
 * @author lipido <lipido@gmail.com>
 */
class PaymentController extends BaseController
{

	/**
	 * Reference to the GastoMapper to interact
	 * with the database
	 *
	 * @var GastoMapper
	 */
	private $paymentMapper;
	private $projectMapper;
	private $debtMapper;

	public function __construct()
	{
		parent::__construct();
		$this->view->setLayout("default");
		$this->paymentMapper = new PaymentMapper();
		$this->projectMapper = new ProjectMapper();
		$this->debtMapper = new DebtMapper();
	}

	/**
	 * Action to list payments
	 *
	 * Loads all the payments from the database.
	 * No HTTP parameters are needed.
	 *
	 */
	public function index()
	{

		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding payment requires login");
		}
		if (!isset($_GET["idProject"])) {
			throw new Exception("Project id is mandatory");
		}

		$idProject = $_GET["idProject"];
		// obtain the data from the database
		$payments = $this->paymentMapper->findByIdProjectWithDebtors($idProject);


		// put the array containing Gasto object to the view
		$project = $this->projectMapper->findByIdProject($idProject);
		$this->view->setVariable("payments", $payments);
		$this->view->setVariable("project", $project);

		// render the view (/view/gastos/index.php)
		$this->view->render("payment", "index");
	}


	// /**
	//  * Action to view a given proyect
	//  *
	//  * This action should only be called via GET
	//  *
	//  * The expected HTTP parameters are:
	//  * <ul>
	//  * <li>id: Id of the proyect (via HTTP GET)</li>
	//  * </ul>
	//  *
	//  *
	//  * @throws Exception If no such post of the given id is found
	//  * @return void
	//  *
	//  */
	// public function view()
	// {
	// 	if (!isset($_GET["idProject"])) {
	// 		throw new Exception("id is mandatory");
	// 	}

	// 	$idProject = $_GET["idProject"];

	// 	// find the Proyect object in the database
	// 	$project = $this->projectMapper->findByIdProject($idProject);

	// 	if ($project == NULL) {
	// 		throw new Exception("no such proyect with id: " . $idProject);
	// 	}

	// 	// put the Proyect object to the view
	// 	$this->view->setVariable("project", $project);

	// 	// render the pproyect (/view/proyectos/view.php)
	// 	$this->view->render("payment", "view");

	// }

	/**
	 * Action to add a new Payment
	 *
	 * When called via GET, it shows the add form
	 * When called via POST, it adds the payment to the
	 * database
	 * @throws Exception if no user is in session
	 * @throws Exception if the user cant manage the project
	 * @return void
	 */
	public function add()
	{
		$payment = new Payment();
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding payments requires login");
		}
		if (isset($_POST["submit"])) { // reaching via HTTP Post...
			$payment = new Payment();
			$project = $this->projectMapper->findByIdProject($_POST["idProject"]);


			if ($this->projectMapper->canManageProject($project, $this->currentUser->getUserName()) == false) {
				throw new Exception("You should be part of the project to view or edit it");
			}

			$payment->setIdProject($_POST["idProject"]);
			$payment->setPayerName($_POST["payerName"]);
			$payment->setTotalAmount($_POST["totalAmount"]);
			$payment->setSubject($_POST["subject"]);

			try {
				$payment->checkIsValidForCreate();

				$idPayment = $this->paymentMapper->save($payment);

				foreach ($_POST["selectedUsers"] as $user) {
					$debt = new Debt();
					$debt->setIdProject($_POST["idProject"]);
					$debt->setDebtorName($user);
					$debt->setIdPayment($idPayment);
					$this->debtMapper->save($debt);
				}

				$this->view->setFlash(sprintf(i18n("Payment \"%s\" successfully added."), $payment->getSubject()));


				// POST-REDIRECT-GET
				// Everything OK, we will redirect the user to the list of proyectos
				// We want to see a message after redirection, so we establish
				// a "flash" message (which is simply a Session variable) to be
				// get in the view after redirection.

				$this->view->setVariable("project", $project);
				$this->view->setVariable("members", $this->projectMapper->getMembers($project));
				// perform the redirection. More or less:
				// header("Location: index.php?controller=proyectos&action=index")
				// die();
				$this->view->redirect("payment", "index", "idProject=" . $project->getIdProject());



			} catch (ValidationException $ex) {
				// Get the errors array inside the exepction...
				$errors = $ex->getErrors();
				// And put it to the view as "errors" variable
				$this->view->setVariable("errors", $errors);
				if ($payment == NULL) {
					$payment = new Payment($_POST["payerName"], $_POST["idProject"], $_POST["totalAmount"], $_POST["subject"]);
				}
				$payment->setDebtors($_POST["selectedUsers"]);
				$this->view->setVariable("payment", $payment);
			}
		} else {
			$project = $this->projectMapper->findByIdProject($_GET["idProject"]);
			if ($this->projectMapper->canManageProject($project, $this->currentUser->getUsername()) == false) {
				throw new Exception("You should be part of the project to view or edit it");
			}
		}
		// Put the Proyect object visible to the view
		$members = $this->projectMapper->getMembers($project);
		$this->view->setVariable("project", $project);
		$this->view->setVariable("members", $members);
		$this->view->setVariable("payment", $payment);

		// render the view (/view/proyectos/add.php)
		$this->view->render("payment", "addPayment", array("idProject" => $project->getIdProject()));
	}



	/**
	 * Action to edit a Payment
	 *
	 * When called via GET, it shows the edit form
	 * When called via POST, it edits the payment 
	 * @throws Exception if no user is in session
	 * @throws Exception if the user cant manage the project
	 * @return void
	 */
	public function edit()
	{
		$payment = new Payment();
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding payments requires login");
		}
		if (isset($_POST["submit"])) { // reaching via HTTP Post...
			$payment = new Payment();
			$project = $this->projectMapper->findByIdProject($_POST["idProject"]);
			$idPayment = $_POST["idPayment"];


			if ($this->projectMapper->canManageProject($project, $this->currentUser->getUserName()) == false) {
				throw new Exception("You should be part of the project to view or edit it");
			}

			$payment->setIdPayment($idPayment);
			$payment->setIdProject($_POST["idProject"]);
			$payment->setPayerName($_POST["payerName"]);
			$payment->setTotalAmount($_POST["totalAmount"]);
			$payment->setSubject($_POST["subject"]);


			try {
				$payment->checkIsValidForUpdate();

				$this->paymentMapper->update($payment);
				$this->debtMapper->deleteAll($payment);
				foreach ($_POST["selectedUsers"] as $user) {
					$debt = new Debt();
					$debt->setIdProject($_POST["idProject"]);
					$debt->setDebtorName($user);
					$debt->setIdPayment($idPayment);
					$this->debtMapper->save(debt: $debt);
				}

				$this->view->setFlash(sprintf(i18n("Payment \"%s\" successfully updated."), $payment->getSubject()));

				// POST-REDIRECT-GET
				// Everything OK, we will redirect the user to the list of proyectos
				// We want to see a message after redirection, so we establish
				// a "flash" message (which is simply a Session variable) to be
				// get in the view after redirection.

				$this->view->setVariable("project", $project);
				$this->view->setVariable("members", $this->projectMapper->getMembers($project));
				// perform the redirection. More or less:
				// header("Location: index.php?controller=proyectos&action=index")
				// die();
				$this->view->redirect("payment", "index", "idProject=" . $project->getIdProject());



			} catch (ValidationException $ex) {
				$errors = $ex->getErrors();
				// And put it to the view as "errors" variable
				$this->view->setVariable("errors", $errors);
				if ($payment == NULL) {
					$payment = new Payment($_POST["payerName"], $_POST["idProject"], $_POST["totalAmount"], $_POST["subject"]);
				}
				$payment->setDebtors($_POST["selectedUsers"]);
				$this->view->setVariable("payment", $payment);
			}
		} else {
			$project = $this->projectMapper->findByIdProject($_GET["idProject"]);
			if ($this->projectMapper->canManageProject($project, $this->currentUser->getUsername()) == false) {
				throw new Exception("You should be part of the project to view or edit it");
			}
			if (!isset($_GET["idPayment"])) {
				throw new Exception("Payment id is mandatory");
			}

			$payment = $this->paymentMapper->findByIdWithDebtors($_GET["idPayment"], $_GET["idProject"]);
		}
		// Put the Proyect object visible to the view
		$members = $this->projectMapper->getMembers($project);
		$this->view->setVariable("project", $project);
		$this->view->setVariable("members", $members);
		$this->view->setVariable("payment", $payment);

		// render the view (/view/proyectos/add.php)
		$this->view->render("payment", "editPayment", array("idProject" => $project->getIdProject()));
	}


	// EDIT

	/**
	 * Action to delete a payment
	 *
	 * This action should only be called via HTTP POST
	 *
	 * @throws Exception if no id was provided
	 * @throws Exception if no user is in session
	 * @throws Exception if there is not any post with the provided id
	 * @throws Exception if the author of the post to be deleted is not the current user
	 * @return void
	 */
	public function delete()
	{
		if (!isset($_POST["idPayment"]) && !isset($_POST["idProject"])) {
			throw new Exception("id is mandatory");
		}
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Editing proyects requires login");
		}

		// Get the Proyect object from the database
		$idProject = $_REQUEST["idProject"];
		$idPayment = $_REQUEST["idPayment"];

		$payment = $this->paymentMapper->findByIdPayment($idPayment, $idProject);
		var_dump($payment);

		// Does the post exist?
		if ($payment == NULL) {
			throw new Exception("no such payment with id: " . $idPayment);
		}

		$this->debtMapper->deleteAll($payment);
		$this->paymentMapper->delete($payment);

		// POST-REDIRECT-GET
		// Everything OK, we will redirect the user to the list of posts
		// We want to see a message after redirection, so we establish
		// a "flash" message (which is simply a Session variable) to be
		// get in the view after redirection.
		$this->view->setFlash(sprintf(i18n("Payment \"%s\" successfully deleted."), $payment->getSubject()));

		// perform the redirection. More or less:
		// header("Location: index.php?controller=posts&action=index")
		// die();
		$this->view->redirect("payment", "index", "idProject=" . $payment->getIdProject());

	}
}