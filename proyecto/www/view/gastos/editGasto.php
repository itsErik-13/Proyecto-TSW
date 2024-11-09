<?php
//file: view/proyectos/index.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$proyecto = $view->getVariable("proyecto");

$members = $view->getVariable("members");

$payment = $view->getVariable("payment");

$currentuser = $view->getVariable("currentusername");

$errors = $view->getVariable("errors");

$view->setVariable("title", i18n("Edit payment"));

?>

<div class="bg-[#323231] rounded-lg p-8 shadow-lg max-w-sm w-full">
    <h2 class="text-3xl font-bold text-white text-center mb-6"><?= i18n("Edit payment") ?></h2>
    <form id="addPagoForm" class="text-white" method="POST" action="index.php?controller=gastos&amp;action=edit">
        <input type="hidden" name="id" value="<?= $proyecto->getId() ?>" />
        <input type="hidden" name="idPayment" value="<?= $payment->getPaymentId() ?>" />
        <div class="relative flex items-center mb-4">
            <input type="text" placeholder="Asunto" name="paymentSubject" value="<?= $payment->getDebt() ?>"
                class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all"
                required />
            <div class="absolute right-4 flex items-centerpointer-events-none">
                <i class="fa-solid fa-money-bill-1-wave text-gray-300"></i>
            </div>
        </div>
        <p class="text-red-600 inline"><?= isset($errors["debt"]) ? i18n($errors["debt"]) : "" ?></p>

        <div class="relative flex items-center mb-4">
            <select id="payerId" name="payerId"
                class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all">
                <option selected value="<?= $payment->getPayerId() ?>" name="<?= $currentuser ?>">
                    <?= $payment->getPayerId() ?>
                </option>
                <?php foreach ($members as $member): ?>
                    <?php if ($member["username"] == $payment->getPayerId())
                        continue; ?>
                    <option value="<?= $member["username"] ?>" name="<?= $member["username"] ?>"><?= $member["username"] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="absolute right-4 flex items-centerpointer-events-none">
                <i class="fa-solid fa-user text-gray-300"></i>
            </div>
        </div>

        <div class="relative flex items-center mb-4">
            <input type="text" placeholder="Cantidad" name="totalAmount" value="<?= $payment->getTotalAmount() ?>"
                class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all"
                required />
            <div class="absolute right-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-dollar-sign text-gray-300"></i>
            </div>
        </div>
        <p class="text-red-600 inline"><?= isset($errors["totalAmount"]) ? i18n($errors["totalAmount"]) : "" ?></p>

        <div>
            <button id="dropdownMiembrosPagoButton" data-dropdown-toggle="dropdownMiembrosPago"
                class="text-white bg-yellow-400 hover:bg-yellow-400 focus:ring-4 focus:outline-none focus:ring-[#323231] font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center"
                type="button">
                Usuarios
                <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 4 4 4-4" />
                </svg>
            </button>
        </div>


        <!-- Dropdown menu -->
        <div id="dropdownMiembrosPago" class="z-10 hidden w-48 bg-[#414140] rounded-lg shadow overflow-y-auto">
            <ul class="p-3 space-y-1 text-sm text-gray-700">
                <?php foreach ($members as $member): ?>
                    <li>
                        <div class="flex items-center p-2 rounded hover:bg-gray-600">
                            <input <?= in_array($member["username"], $payment->getDebtors()) ? "checked" : "" ?>
                                id="checkbox-item-1" type="checkbox" name="selectedUsers[]"
                                value="<?= $member["username"] ?>"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-600 ring-offset-gray-700 focus:ring-offset-gray-700 focus:ring-2 bg-gray-600 border-gray-500" />
                            <label for="checkbox-item-1"
                                class="w-full ms-2 text-sm font-medium rounded text-gray-300"><?= $member["username"] ?></label>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="flex items-center justify-between mt-2">
            <button type="submit" name="submit"
                class="text-white bg-[#edb705] hover:bg-[#ab8403] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                <i class="fa-solid fa-edit"></i>
            </button>
            <a href="index.php?controller=gastos&amp;action=index&amp;id=<?= $proyecto->getId() ?>"
            class="text-yellow-400 hover:text-yellow-500 text-sm underline"><?= i18n("Cancel") ?></a>
        </div>
    </form>
</div>