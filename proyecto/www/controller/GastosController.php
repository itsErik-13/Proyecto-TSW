<?php
//file: controller/GastosController.php

require_once(__DIR__ . "/../model/Gasto.php");
require_once(__DIR__ . "/../model/GastoMapper.php");

require_once(__DIR__ . "/../model/Proyecto.php");
require_once(__DIR__ . "/../model/ProyectoMapper.php");

require_once(__DIR__ . "/../model/Debt.php");
require_once(__DIR__ . "/../model/DebtMapper.php");

require_once(__DIR__ . "/../model/User.php");

require_once(__DIR__ . "/../core/ViewManager.php");
require_once(__DIR__ . "/../controller/BaseController.php");

/**
 * Class GastosController
 *
 * Controller to make a CRUDL of Proyects entities
 *
 * @author lipido <lipido@gmail.com>
 */
class GastosController extends BaseController
{

	/**
	 * Reference to the GastoMapper to interact
	 * with the database
	 *
	 * @var GastoMapper
	 */
	private $gastoMapper;
	private $proyectoMapper;
	private $debtMapper;

	public function __construct()
	{
		parent::__construct();
		$this->view->setLayout("default");
		$this->gastoMapper = new GastoMapper();
		$this->proyectoMapper = new ProyectoMapper();
		$this->debtMapper = new DebtMapper();
	}

	/**
	 * Action to list payments
	 *
	 * Loads all the payments from the database.
	 * No HTTP parameters are needed.
	 *
	 * The views are:
	 * <ul>
	 * <li>payments/index (via include)</li>
	 * </ul>
	 */
	public function index()
	{

		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding payment requires login");
		}
		if (!isset($_GET["id"])) {
			throw new Exception("Project id is mandatory");
		}

		$proyectoid = $_GET["id"]; // Revisar si es correcto así? Tambien mirar cual es la clave concreta de un pago
		// obtain the data from the database
		$gastos = $this->gastoMapper->findByProjectIdWithDebtors($proyectoid);


		// put the array containing Gasto object to the view
		$proyecto = $this->proyectoMapper->findById($proyectoid);
		$this->view->setVariable("gastos", $gastos);
		$this->view->setVariable("proyecto", $proyecto);

		// render the view (/view/gastos/index.php)
		$this->view->render("gastos", "index");
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
	 * The views are:
	 * <ul>
	 * <li>proyectos/view: If proyect is successfully loaded (via include).	Includes these view variables:</li>
	 * <ul>
	 *	<li>post: The current Proyect retrieved</li>
	 *	<li>comment: The current Comment instance, empty or
	 *	being added (but not validated)</li>
	 * </ul>
	 * </ul>
	 *
	 * @throws Exception If no such post of the given id is found
	 * @return void
	 *
	 */
	public function view()
	{
		if (!isset($_GET["id"])) {
			throw new Exception("id is mandatory");
		}

		$proyectoid = $_GET["id"];

		// find the Proyect object in the database
		$proyecto = $this->proyectoMapper->findById($proyectoid);

		if ($proyecto == NULL) {
			throw new Exception("no such proyect with id: " . $proyectoid);
		}

		// put the Proyect object to the view
		$this->view->setVariable("proyecto", $proyecto);

		// // check if comment is already on the view (for example as flash variable)
		// // if not, put an empty Comment for the view
		// $comment = $this->view->getVariable("comment");
		// $this->view->setVariable("comment", ($comment==NULL)?new Comment():$comment);

		// render the pproyect (/view/proyectos/view.php)
		$this->view->render("gastos", "view");

	}

	/**
	 * Action to add a new Gasto
	 *
	 * When called via GET, it shows the add form
	 * When called via POST, it adds the proyect to the
	 * database
	 *
	 * The expected HTTP parameters are:
	 * <ul>
	 * <li>title: Title of the proyect (via HTTP POST)</li>
	 * <li>content: Content of the proyect (via HTTP POST)</li>
	 * </ul>
	 *
	 * The views are:
	 * <ul>
	 * <li>proyectos/add: If this action is reached via HTTP GET (via include)</li>
	 * <li>proyectos/index: If proyect was successfully added (via redirect)</li>
	 * <li>proyectos/add: If validation fails (via include). Includes these view variables:</li>
	 * <ul>
	 *	<li>proyect: The current Proyect instance, empty or
	 *	being added (but not validated)</li>
	 *	<li>errors: Array including per-field validation errors</li>
	 * </ul>
	 * </ul>
	 * @throws Exception if no user is in session
	 * @return void
	 */
	public function add()
	{
		$payment = new Gasto();
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding payments requires login");
		}
		if (isset($_POST["submit"])) { // reaching via HTTP Post...
			$payment = new Gasto();
			$proyecto = $this->proyectoMapper->findById($_POST["id"]);


			if ($this->proyectoMapper->canManageProject($proyecto, $this->currentUser->getUsername()) == false) {
				throw new Exception("You should be part of the project to view or edit it");
			}

			$payment->setProjectId($_POST["id"]);
			$payment->setPayerId($_POST["payerId"]);
			$payment->setTotalAmount($_POST["totalAmount"]);
			$payment->setDebt($_POST["paymentSubject"]);


			try {
				$payment->checkIsValidForCreate();

				$idPayment = $this->gastoMapper->save($payment);

				foreach ($_POST["selectedUsers"] as $user) {
					$debt = new Debt();
					$debt->setProjectId($_POST["id"]);
					$debt->setDebtorName($user);
					$debt->setPaymentId($idPayment);
					$debt->setRelativeAmount($payment->getTotalAmount() / count($_POST["selectedUsers"]));

					$this->debtMapper->save($debt);
				}

				$this->view->setFlash(sprintf(i18n("Payment \"%s\" successfully added."), $payment->getDebt()));


				// POST-REDIRECT-GET
				// Everything OK, we will redirect the user to the list of proyectos
				// We want to see a message after redirection, so we establish
				// a "flash" message (which is simply a Session variable) to be
				// get in the view after redirection.

				$this->view->setVariable("proyecto", $proyecto);
				$this->view->setVariable("members", $this->proyectoMapper->getMembers($proyecto));
				// perform the redirection. More or less:
				// header("Location: index.php?controller=proyectos&action=index")
				// die();
				$this->view->redirect("gastos", "index", "id=" . $proyecto->getId());



			} catch (ValidationException $ex) {
				// Get the errors array inside the exepction...
				$errors = $ex->getErrors();
				// And put it to the view as "errors" variable
				$this->view->setVariable("errors", $errors);
				if ($payment == NULL) {
					$payment = new Gasto($_POST["payerName"], $_POST["idProject"], $_POST["debt"], $_POST["totalAmount"]);
				}
				$payment->setDebtors($_POST["selectedUsers"]);
				$this->view->setVariable("payment", $payment);
			}
		} else {
			$proyecto = $this->proyectoMapper->findById($_GET["id"]);
			if ($this->proyectoMapper->canManageProject($proyecto, $this->currentUser->getUsername()) == false) {
				throw new Exception("You should be part of the project to view or edit it");
			}
		}
		// Put the Proyect object visible to the view
		$members = $this->proyectoMapper->getMembers($proyecto);
		$this->view->setVariable("proyecto", $proyecto);
		$this->view->setVariable("members", $members);
		$this->view->setVariable("payment", $payment);

		// render the view (/view/proyectos/add.php)
		$this->view->render("gastos", "addGasto", array("id" => $proyecto->getId()));
	}




	public function edit()
	{
		$payment = new Gasto();
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding payments requires login");
		}
		if (isset($_POST["submit"])) { // reaching via HTTP Post...
			$payment = new Gasto();
			$proyecto = $this->proyectoMapper->findById($_POST["id"]);
			$idPayment = $_POST["idPayment"];


			if ($this->proyectoMapper->canManageProject($proyecto, $this->currentUser->getUsername()) == false) {
				throw new Exception("You should be part of the project to view or edit it");
			}

			$payment->setPaymentId($idPayment);
			$payment->setProjectId($_POST["id"]);
			$payment->setPayerId($_POST["payerId"]);
			$payment->setTotalAmount($_POST["totalAmount"]);
			$payment->setDebt($_POST["paymentSubject"]);


			try {
				$payment->checkIsValidForUpdate();

				$this->gastoMapper->update($payment);
				$this->debtMapper->deleteAll($payment);
				foreach ($_POST["selectedUsers"] as $user) {
					$debt = new Debt();
					$debt->setProjectId($_POST["id"]);
					$debt->setDebtorName($user);
					$debt->setPaymentId($idPayment);
					$debt->setRelativeAmount($payment->getTotalAmount() / count($_POST["selectedUsers"]));

					$this->debtMapper->save(debt: $debt);
				}

				$this->view->setFlash(sprintf(i18n("Payment \"%s\" successfully edited."), $payment->getDebt()));


				// POST-REDIRECT-GET
				// Everything OK, we will redirect the user to the list of proyectos
				// We want to see a message after redirection, so we establish
				// a "flash" message (which is simply a Session variable) to be
				// get in the view after redirection.

				$this->view->setVariable("proyecto", $proyecto);
				$this->view->setVariable("members", $this->proyectoMapper->getMembers($proyecto));
				// perform the redirection. More or less:
				// header("Location: index.php?controller=proyectos&action=index")
				// die();
				$this->view->redirect("gastos", "index", "id=" . $proyecto->getId());



			} catch (ValidationException $ex) {
				$errors = $ex->getErrors();
				// And put it to the view as "errors" variable
				$this->view->setVariable("errors", $errors);
				if ($payment == NULL) {
					$payment = new Gasto($_POST["payerName"], $_POST["idProject"], $_POST["debt"], $_POST["totalAmount"]);
				}
				$payment->setDebtors($_POST["selectedUsers"]);
				$this->view->setVariable("payment", $payment);
			}
		} else {
			$proyecto = $this->proyectoMapper->findById($_GET["id"]);
			if ($this->proyectoMapper->canManageProject($proyecto, $this->currentUser->getUsername()) == false) {
				throw new Exception("You should be part of the project to view or edit it");
			}
			if (!isset($_GET["idPayment"])) {
				throw new Exception("Payment id is mandatory");
			}

			$payment = $this->gastoMapper->findByIdWithDebtors($_GET["idPayment"]);
		}
		// Put the Proyect object visible to the view
		$members = $this->proyectoMapper->getMembers($proyecto);
		$this->view->setVariable("proyecto", $proyecto);
		$this->view->setVariable("members", $members);
		$this->view->setVariable("payment", $payment);

		// render the view (/view/proyectos/add.php)
		$this->view->render("gastos", "editGasto", array("id" => $proyecto->getId()));
	}


	// EDIT

	/**
	 * Action to delete a proyect
	 *
	 * This action should only be called via HTTP POST
	 *
	 * The expected HTTP parameters are:
	 * <ul>
	 * <li>id: Id of the proyect (via HTTP POST)</li>
	 * </ul>
	 *
	 * The views are:
	 * <ul>
	 * <li>proyectos/index: If proyect was successfully deleted (via redirect)</li>
	 * </ul>
	 * @throws Exception if no id was provided
	 * @throws Exception if no user is in session
	 * @throws Exception if there is not any post with the provided id
	 * @throws Exception if the author of the post to be deleted is not the current user
	 * @return void
	 */
	public function delete()
	{
		if (!isset($_POST["id"])) {
			throw new Exception("id is mandatory");
		}
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Editing proyects requires login");
		}

		// Get the Proyect object from the database
		$idPayment = $_REQUEST["id"];
		$payment = $this->gastoMapper->findById($idPayment);

		// Does the post exist?
		if ($payment == NULL) {
			throw new Exception("no such payment with id: " . $idPayment);
		}

		$this->debtMapper->deleteAll($payment);
		$this->gastoMapper->delete($payment);

		// POST-REDIRECT-GET
		// Everything OK, we will redirect the user to the list of posts
		// We want to see a message after redirection, so we establish
		// a "flash" message (which is simply a Session variable) to be
		// get in the view after redirection.
		$this->view->setFlash(sprintf(i18n("Payment \"%s\" successfully deleted."), $payment->getDebt()));

		// perform the redirection. More or less:
		// header("Location: index.php?controller=posts&action=index")
		// die();
		$this->view->redirect("gastos", "index", "id=" . $payment->getProjectId());

	}
}