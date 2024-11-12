<?php
//file: view/proyectos/index.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$project = $view->getVariable("project");

$members = $view->getVariable("members");

$payment = $view->getVariable("payment");

$currentuser = $view->getVariable("currentusername");

$errors = $view->getVariable("errors");

$view->setVariable("title", i18n("Add payment"));

?>


<div class="bg-[#323231] rounded-lg p-8 shadow-lg max-w-sm w-full">
    <h2 class="text-3xl font-bold text-white text-center mb-6"><?= i18n("Add payment") ?></h2>
    <form id="addPaymentForm" class="text-white" method="POST" action="index.php?controller=payment&amp;action=add">
        <input type="hidden" name="idProject" value="<?= $project->getIdProject() ?>" />
        <div class="relative flex items-center mb-4">
            <input type="text" placeholder="<?=i18n("Subject") ?>" name="subject" value="<?= $payment->getSubject() ?>"
                class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all"
                required />
            <div class="absolute right-4 flex items-centerpointer-events-none">
                <i class="fa-solid fa-money-bill-1-wave text-gray-300"></i>
            </div>
        </div>
        <p class="text-red-600 inlinse"><?= isset($errors["debt"]) ? i18n($errors["debt"]) : "" ?></p>

        <div class="relative flex items-center mb-4">
            <select id="payerName" name="payerName"
                class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all">
                <option selected value="<?= $payment->getPayerName() == "" ? $currentuser : $payment->getPayerName() ?>"
                    name="<?= $payment->getPayerName() == "" ? $currentuser : $payment->getPayerName() ?>">
                    <?= $payment->getPayerName() == "" ? $currentuser : $payment->getPayerName() ?>
                </option>
                <?php foreach ($members as $member): ?>
                    <?php if ($member["userName"] == $currentuser)
                        continue; ?>
                    <option value="<?= $member["userName"] ?>" name="<?= $member["userName"] ?>"><?= $member["userName"] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="absolute right-4 flex items-centerpointer-events-none">
                <i class="fa-solid fa-user text-gray-300"></i>
            </div>
        </div>

        <div class="relative flex items-center mb-4">
            <input type="text" placeholder="<?=i18n("Amount") ?>" name="totalAmount" value="<?= $payment->getTotalAmount() ?>"
                class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all"
                required />
            <div class="absolute right-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-dollar-sign text-gray-300"></i>
            </div>
        </div>
        <p class="text-red-600 inline"><?= isset($errors["totalAmount"]) ? i18n($errors["totalAmount"]) : "" ?></p>
        <div>
            <button id="dropdownMembersPaymentButton" data-dropdown-toggle="dropdownMembersPayment"
                class="text-white bg-yellow-400 hover:bg-yellow-400 focus:ring-4 focus:outline-none focus:ring-[#323231] font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center"
                type="button">
                <?=i18n("Users") ?>
                <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 4 4 4-4" />
                </svg>
            </button>
        </div>


        <!-- Dropdown menu -->
        <div id="dropdownMembersPayment" class="z-10 hidden w-48 bg-[#414140] rounded-lg shadow overflow-y-auto">
            <ul class="p-3 space-y-1 text-sm text-gray-700">
                <?php foreach ($members as $member): ?>
                    <li>
                        <div class="flex items-center p-2 rounded hover:bg-gray-600">
                            <input <?= $payment->getDebtors() == null ? "checked" : (in_array($member["userName"], $payment->getDebtors()) ? "checked" : "") ?> id="checkbox-item-1" type="checkbox"
                                name="selectedUsers[]" value="<?= $member["userName"] ?>"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-600 ring-offset-gray-700 focus:ring-offset-gray-700 focus:ring-2 bg-gray-600 border-gray-500" />
                            <label for="checkbox-item-1"
                                class="w-full ms-2 text-sm font-medium rounded text-gray-300"><?= $member["userName"] ?></label>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="flex items-center justify-between mt-2">
            <button type="submit" name="submit"
                class="text-white bg-[#edb705] hover:bg-[#ab8403] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                <i class="fa-solid fa-money-bill-1-wave"></i>
            </button>
            <a href="index.php?controller=payment&amp;action=index&amp;idProject=<?= $project->getIdProject() ?>"
            class="text-yellow-400 hover:text-yellow-500 text-sm underline"><?= i18n("Cancel") ?></a>
        </div>
    </form>
</div>