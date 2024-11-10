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
class DeudasController extends BaseController
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

		$payments = $this->gastoMapper->findTotalPaymentsByProjectId($proyectoid);
		$debts = $this->debtMapper->findTotalDebtsByProjectId($proyectoid);

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
		$proyecto = $this->proyectoMapper->findById($proyectoid);
		$this->view->setVariable("gastos", $gastos);
		$this->view->setVariable("proyecto", value: $proyecto);

		// render the view (/view/gastos/index.php)
		$this->view->render("deudas", "index");
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