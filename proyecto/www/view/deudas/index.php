<?php
//file: view/proyectos/index.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$proyecto = $view->getVariable("proyecto");

$currentuser = $view->getVariable("currentusername");

$transacciones = $view->getVariable("transactions");

$errors = $view->getVariable("errors");

$view->setVariable("title", i18n("Debts"));

?>


<div class="bg-[#323231] rounded-lg p-4 shadow-lg max-w-4xl w-full text-center">
  <h2 class="text-3xl font-bold text-white mb-6"><?= $proyecto->getName() ?></h2>

  <nav>
    <ul class="flex justify-center space-x-10 text-white text-lg font-bold">
      <li>
        <a href="index.php?controller=gastos&amp;action=index&amp;id=<?= $proyecto->getId() ?>"
          class="hover:text-<?= $proyecto->getTheme() ?>"><?= i18n("Payments") ?></a>
      </li>
      <li>
        <a class="text-<?= $proyecto->getTheme() ?> underline"><?= i18n("Debts") ?></a>
      </li>
      <li>
        <a href="index.php?controller=proyectos&amp;action=viewMembers&amp;id=<?= $proyecto->getId() ?>"
          class="hover:text-<?= $proyecto->getTheme() ?>"><?= i18n("Members") ?></a>
      </li>
    </ul>
  </nav>
</div>
<div class="bg-[#323231] rounded-lg p-8 shadow-lg max-w-4xl w-full text-center mt-5">
  <!-- Lista de deudas -->
  <div
    class="space-y-4 overflow-y-auto max-h-[320px] pr-2 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-thumb]:rounded-full">
    <?php if ($transacciones != null): ?>
      <?php foreach ($transacciones as $deuda): ?>
        <div class="flex flex-col justify-between bg-[#3d3d3d] p-4 rounded text-white space-y-2">
          <!-- Información de Usuario y Beneficiario -->
          <div class="relative flex justify-between items-center text-sm">
            <p><?= $deuda["debtorName"] ?></p>
            <i class="fa-solid fa-arrow-right text-white absolute left-1/2 transform -translate-x-1/2"></i>
            <p><?= $deuda["receiverName"] ?></p>
          </div>
          <!-- Información de Pago Principal -->
          <div class="flex justify-center items-center">
            <b>$<?= $deuda["amount"] ?></b>
          </div>
        </div>

      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-white text-lg font-bold"><?= i18n("There are no debts") ?></p>
    <?php endif; ?>
  </div>
</div>