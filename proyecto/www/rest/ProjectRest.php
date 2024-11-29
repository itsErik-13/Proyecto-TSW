<?php

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/UserMapper.php");

require_once(__DIR__ . "/../model/Project.php");
require_once(__DIR__ . "/../model/ProjectMapper.php");

require_once(__DIR__ . "/../model/Payment.php");
require_once(__DIR__ . "/../model/PaymentMapper.php");

require_once(__DIR__ . "/../model/Debt.php");
require_once(__DIR__ . "/../model/DebtMapper.php");

require_once(__DIR__ . "/BaseRest.php");

/**
 * Class PostRest
 *
 * It contains operations for creating, retrieving, updating, deleting and
 * listing posts, as well as to create comments to posts.
 *
 * Methods gives responses following Restful standards. Methods of this class
 * are intended to be mapped as callbacks using the URIDispatcher class.
 *
 */
class ProjectRest extends BaseRest
{
	private $projectMapper;
	private $paymentMapper;
	private $debtMapper;

	private $userMapper;

	public function __construct()
	{
		parent::__construct();

		$this->projectMapper = new ProjectMapper();
		$this->paymentMapper = new PaymentMapper();
		$this->debtMapper = new DebtMapper();
		$this->userMapper = new UserMapper();
	}

	public function getProjects()
	{
		$currentUser = parent::authenticateUser();
		$projects = $this->projectMapper->findProjectByMember(userName: $currentUser->getusername()); // Lo hacemos así o sacando el usuario del $data?

		$projects_array = array();
		foreach ($projects as $project) {
			array_push($projects_array, array(
				"idProject" => $project->getIdProject(),
				"projectName" => $project->getProjectName(),
				"theme" => $project->getTheme()
			));
		}

		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($projects_array));
	}

	public function createProject($data)
	{
		$currentUser = parent::authenticateUser();
		$project = new Project();

		if (isset($data->projectName) && isset($data->theme)) {
			$project->setProjectName($data->projectName);
			$project->setTheme($data->theme);
		}

		try {
			// validate Project object
			$project->checkIsValidForCreate(); // if it fails, ValidationException

			// save the Project object into the database            
			$idProject = $this->projectMapper->save($project, $currentUser);


			// response OK. Also send project in content
			header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
			header('Location: ' . $_SERVER['REQUEST_URI'] . "/" . $idProject); // Revisar esto, no tenemos que redirigir a dentro del proyecto
			header('Content-Type: application/json');
			echo (json_encode(array(
				"idProject" => $idProject,
				"projectName" => $project->getProjectName(),
				"theme" => $project->getTheme()
			)));

		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			header('Content-Type: application/json');
			echo (json_encode($e->getErrors()));
		}
	}

	public function deleteProject($idProject)
	{
		$currentUser = parent::authenticateUser();
		$project = $this->projectMapper->findByIdProject($idProject); // El id proyect se saca del $data o del $uri?

		if ($project == NULL) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request'); // Alomejor debería ser 404?
			echo ("Project with id " . $idProject . " not found");
			return;
		}

		$this->projectMapper->delete($project);

		header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content');
	}

	public function getPayments($idProject)
	{
		$currentUser = parent::authenticateUser();
		$payments = $this->paymentMapper->findByIdProjectWithDebtors($idProject); // El id proyect se saca del $data o del $uri?

		$payments_array = array();
		foreach ($payments as $payment) {
			array_push($payments_array, array(
				"idPayment" => $payment->getIdPayment(),
				"idProject" => $payment->getIdProject(),
				"payerName" => $payment->getPayerName(),
				"totalAmount" => $payment->getTotalAmount(),
				"subject" => $payment->getSubject(),
				"debtors" => $payment->getDebtors() // Revisar esto
			));
		}

		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($payments_array));
	}

	public function createPayment($idProject, $data)
	{
		$currentUser = parent::authenticateUser();
		$payment = new Payment();

		if (isset($data->payerName) && isset($data->totalAmount) && isset($data->subject) && isset($data->debtors)) {
			$payment->setIdProject(idProject: $idProject);
			$payment->setPayerName($data->payerName);
			$payment->setTotalAmount($data->totalAmount);
			$payment->setSubject($data->subject);
			$payment->setDebtors($data->debtors);
		}

		try {
			// validate Payment object
			$payment->checkIsValidForCreate(); // if it fails, ValidationException
			// save the Payment object into the database
			$idPayment = $this->paymentMapper->save($payment);

			foreach ($data->debtors as $debtor) {
				$debt = new Debt();
				$debt->setIdProject($idProject);
				$debt->setDebtorName($debtor);
				$debt->setIdPayment($idPayment);
				$this->debtMapper->save($debt);
			}

			// response OK. Also send payment in content
			header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
			header('Location: ' . $_SERVER['REQUEST_URI'] . "/" . $idPayment); // Revisar esto, no tenemos que redirigir a dentro del proyecto
			header('Content-Type: application/json');
			echo (json_encode(array(
				"idPayment" => $idPayment,
				"idProject" => $payment->getIdProject(),
				"payerName" => $payment->getPayerName(),
				"totalAmount" => $payment->getTotalAmount(),
				"subject" => $payment->getSubject(),
				"debtors" => $payment->getDebtors()
			)));
		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			header('Content-Type: application/json');
			echo (json_encode($e->getErrors()));
		}
	}

	public function updatePayment($idProject, $idPayment, $data)
	{
		$currentUser = parent::authenticateUser();
		$payment = $this->paymentMapper->findByIdPayment($idPayment, $idProject); //($idProject); // El id proyect se saca del $data o del $uri?

		if ($payment == NULL) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request'); // Alomejor debería ser 404?
			echo ("Payment with id " . $idPayment . " not found");
			return;
		}

		$payment->setIdPayment($idPayment);
		$payment->setIdProject($idProject);
		$payment->setPayerName($data->payerName);
		$payment->setTotalAmount($data->totalAmount);
		$payment->setSubject($data->subject);
		$payment->setDebtors($data->debtors);

		try {
			// validate Payment object
			$payment->checkIsValidForUpdate(); // if it fails, ValidationException
			$this->paymentMapper->update($payment);

			$this->debtMapper->deleteAll($payment);

			foreach ($data->debtors as $debtor) {
				$debt = new Debt();
				$debt->setIdProject($idProject);
				$debt->setDebtorName($debtor);
				$debt->setIdPayment($idPayment);
				$this->debtMapper->save($debt);
			}

			header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			header('Content-Type: application/json');
			echo (json_encode($e->getErrors()));
		}
	}

	public function deletePayment($idProject, $idPayment)
	{
		$currentUser = parent::authenticateUser();
		$payment = $this->paymentMapper->findByIdPayment($idPayment, $idProject); // El id proyect se saca del $data o del $uri?

		if ($payment == NULL) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request'); // Alomejor debería ser 404?
			echo ("Payment with id " . $idPayment . " not found");
			return;
		}

		$this->paymentMapper->delete($payment); // Ojo cuidado que alomejor payment no es de tipo payment si no de tipo array

		header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content');
	}

	public function getDebts($idProject)
	{

		$currentUser = parent::authenticateUser();
		// Hay que hacerlo aplicando el algoritmo de DebtController.php

		$payments = $this->paymentMapper->findTotalPaymentsByProjectId($idProject);
		$debts = $this->debtMapper->findTotalDebtsByProjectId($idProject);

		$amount = [];
		$users = array_unique(array_merge(array_keys($payments), array_keys($debts)));

		foreach ($users as $user) {
			$paid = isset($payments[$user]) ? $payments[$user] : 0;
			$debt = isset($debts[$user]) ? $debts[$user] : 0;
			$amount[$user] = $paid - $debt;
		}
		$users = array_keys($amount);
		$netAmounts = array_values($amount);

		$transactions = [];

		
		minCashFlowRec($netAmounts, $users, $transactions);

		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($transactions));
	}

	public function getMembers($idProject)
	{
		$currentUser = parent::authenticateUser();
		$members = $this->projectMapper->getMembers($this->projectMapper->findByIdProject($idProject)); // El id proyect se saca del $data o del $uri?

		$members_array = array();
		foreach ($members as $member) {
			array_push($members_array, array(
				"username" => $member["userName"]
			));
		}

		
		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($members_array));
	}

	public function addMember($idProject, $data)
	{
		$currentUser = parent::authenticateUser();
		$project = $this->projectMapper->findByIdProject($idProject); // El id proyect se saca del $data o del $uri?

		if ($project == NULL) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request'); // Alomejor debería ser 404?
			echo ("Project with id " . $idProject . " not found");
			return;
		}

		if ($this->projectMapper->canAddMember($project, $this->userMapper->getUserByEmail($data->email)) == false) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			echo ("Member is already in the project");
		}
		else if ($this->userMapper->getUserByEmail($data->email) != NULL) {
			$this->projectMapper->addMember($project, $this->userMapper->getUserByEmail($data->email));
			header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok'); // O 201 Created?
			echo("User added to the project");
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			echo ("User with email " . $data->email . " not found");
		}
	}

}

// URI-MAPPING for this Rest endpoint
$projectRest = new ProjectRest();
URIDispatcher::getInstance()
	->map("GET", "/project", array($projectRest, "getProjects")) 		//checked
	->map("POST", "/project", array($projectRest, "createProject"))        									//checked
	->map("DELETE", "/project/$1", array($projectRest, "deleteProject"))									//checked
	->map("GET", "/project/$1/payment", array($projectRest, "getPayments"))								    //checked
	->map("POST", "/project/$1/payment", array($projectRest, "createPayment"))								//checked
	->map("PUT", "/project/$1/payment/$2", array($projectRest, "updatePayment"))
	->map("DELETE", "/project/$1/payment/$2", array($projectRest, "deletePayment")) 						//checked
	->map("GET", "/project/$1/debt", array($projectRest, "getDebts"))										//checked
	->map("GET", "/project/$1/member", array($projectRest, "getMembers"))									//checked
	->map("POST", "/project/$1/member", array($projectRest, "addMember"));									//checked







	function minCashFlowRec(&$amount, $users, &$transactions) {
		// Encuentra el índice de los valores máximo y mínimo en el arreglo de saldos

		if (empty($amount)) {
			return;
		}
		
		$mxCredit = getMax($amount);
		$mxDebit = getMin($amount);
	
		// Si ambos valores son 0, todas las deudas están saldadas
		if ($amount[$mxCredit] == 0 && $amount[$mxDebit] == 0) {
			return;
		}
	
		// Encuentra la cantidad mínima que se puede transferir entre el deudor y el acreedor
		$min = minOf2(-$amount[$mxDebit], $amount[$mxCredit]);

		if ($min == 0) {
			return;
		}
		
		// Realiza el ajuste en los saldos
		$amount[$mxCredit] -= $min;
		$amount[$mxDebit] += $min;
	
		// Almacena la transacción en el arreglo de resultados en lugar de imprimirla
		$transactions[] = [
			"debtorName" => $users[$mxDebit],
			"amount" => $min,
			"receiverName" => $users[$mxCredit]
		];
	
		// Llamada recursiva para saldar el resto de las deudas
		minCashFlowRec($amount, $users, $transactions);
	}
	
	function getMax($arr) {
		$maxInd = 0;
		for ($i = 1; $i < count($arr); $i++) {
			if ($arr[$i] > $arr[$maxInd]) {
				$maxInd = $i;
			}
		}
		return $maxInd;
	}

	function getMin($arr) {
		$minInd = 0;
		for ($i = 1; $i < count($arr); $i++) {
			if ($arr[$i] < $arr[$minInd]) {
				$minInd = $i;
			}
		}
		return $minInd;
	}

	function minOf2($x, $y) {
		return ($x < $y) ? $x : $y;
	}