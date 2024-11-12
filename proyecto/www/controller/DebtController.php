<?php
//file: controller/DebtController.php

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
 * Class DebtController
 *
 * Controller to make a CRUDL of Proyects entities
 *
 * @author lipido <lipido@gmail.com>
 */
class DebtController extends BaseController
{

	/**
	 * Reference to the PaymentMapper to interact
	 * with the database
	 *
	 * @var PaymentMapper
	 */
	private $paymentMapper;
	private $projectMapper;
	private $debtMapper;
	private $n;

	public function __construct()
	{
		parent::__construct();
		$this->view->setLayout("default");
		$this->paymentMapper = new PaymentMapper();
		$this->projectMapper = new ProjectMapper();
		$this->debtMapper = new DebtMapper();
	}

	/**
	 * Action to list debts
	 *
	 * Loads all the debts from the database.
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
		$gastos = $this->paymentMapper->findByIdProjectWithDebtors($idProject);

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

		
		$this->minCashFlowRec($netAmounts, $users, $transactions);

		$this->view->setVariable("transactions", $transactions);


		// put the array containing Gasto object to the view
		$project = $this->projectMapper->findByIdProject($idProject);
		$this->view->setVariable("payments", $gastos);
		$this->view->setVariable("project", value: $project);

		// render the view (/view/gastos/index.php)
		$this->view->render("debt", "index");
	}

	function minCashFlowRec(&$amount, $users, &$transactions) {
		// Encuentra el índice de los valores máximo y mínimo en el arreglo de saldos

		if (empty($amount)) {
			return;
		}
		
		$mxCredit = $this->getMax($amount);
		$mxDebit = $this->getMin($amount);
	
		// Si ambos valores son 0, todas las deudas están saldadas
		if ($amount[$mxCredit] == 0 && $amount[$mxDebit] == 0) {
			return;
		}
	
		// Encuentra la cantidad mínima que se puede transferir entre el deudor y el acreedor
		$min = $this->minOf2(-$amount[$mxDebit], $amount[$mxCredit]);

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
		$this->minCashFlowRec($amount, $users, $transactions);
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
}