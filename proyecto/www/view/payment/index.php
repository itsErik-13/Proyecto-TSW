<?php
//file: view/proyectos/index.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$project = $view->getVariable("project");

$currentuser = $view->getVariable("currentusername");

$payments = $view->getVariable("payments");

$errors = $view->getVariable("errors");

$view->setVariable("title", i18n("Payments"));

?>

<div class="bg-[#323231] rounded-lg p-4 shadow-lg max-w-4xl w-full text-center">
    <h2 class="text-3xl font-bold text-white mb-6"><?= $project->getProjectName() ?></h2>

    <nav>
        <ul class="flex justify-center space-x-10 text-white text-lg font-bold">
            <li>
                <a class="text-<?=$project->getTheme()?> underline"><?=i18n("Payments") ?></a>
            </li>
            <li>
                <a href="index.php?controller=debt&amp;action=index&amp;idProject=<?= $project->getIdProject() ?>" class="hover:text-<?=$project->getTheme()?>"><?=i18n("Debts") ?></a>
            </li>
            <li>
                <a href="index.php?controller=project&amp;action=viewMembers&amp;idProject=<?= $project->getIdProject() ?>"
                    class="hover:text-<?=$project->getTheme()?>"><?=i18n(key: "Members") ?></a>
            </li>
        </ul>
    </nav>
</div>

<div class="bg-[#323231] rounded-lg p-8 shadow-lg max-w-4xl w-full text-center mt-2">
    <!-- Lista de pagos -->
    <div
        class="space-y-4 overflow-y-auto max-h-[320px] pr-2 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-thumb]:rounded-full">
        <?php if ($payments != null): ?>
            <?php foreach ($payments as $payment): ?>
                <div class="flex flex-col justify-between bg-[#3d3d3d] p-4 rounded text-white space-y-2">
                    <!-- Información de Pago Principal -->
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-money-bill-1-wave text-white"></i>
                            <span><?= $payment->getSubject() ?></span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <b>$<?= $payment->getTotalAmount() ?></b>
                            <a
                                href="index.php?controller=payment&amp;action=edit&amp;idProject=<?= $project->getIdProject() ?>&amp;idPayment=<?= $payment->getIdPayment() ?>">
                                <button class="text-white hover:text-red-500" type="button">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                            </a>
                        </div>
                    </div>

                    <!-- Información de Usuario y Beneficiario -->
                    <div class="relative flex justify-between items-center text-sm">
                        <p><?= $payment->getPayerName() ?></p>
                        <i class="fa-solid fa-arrow-right text-white absolute left-1/2 transform -translate-x-1/2"></i>
                        <div class="flex items-center space-x-4">
                            <button data-modal-target="modal-viewmemberspayment<?= $payment->getIdPayment() ?>"
                                data-modal-toggle="modal-viewmemberspayment<?= $payment->getIdPayment() ?>"
                                class="text-white hover:text-yellow-400" type="button">
                                <p class="underline"><?= count($payment->getDebtors()) ?> usuarios</p>
                            </button>
                            <button data-modal-target="modal-deletepayment<?= $payment->getIdPayment() ?>"
                                data-modal-toggle="modal-deletepayment<?= $payment->getIdPayment() ?>"
                                class="text-white hover:text-red-500" type="button">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="modal-viewmemberspayment<?= $payment->getIdPayment() ?>" tabindex="-1" aria-hidden="true"
                    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 max-w-2xl max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-[#323231]">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                <!-- Contenedor para el título -->
                                <div class="mr-4">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Miembros
                                    </h3>
                                </div>

                                <!-- Contenedor para el botón -->
                                <div class="ml-4">
                                    <!-- Use data-modal-toggle to close modal -->
                                    <button type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-modal-toggle="modal-viewmemberspayment<?= $payment->getIdPayment() ?>">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal body -->
                            <div class="p-6 space-y-6">
                                <?php foreach ($payment->getDebtors() as $debtor): ?>
                                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                        <?= $debtor ?>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="modal-deletepayment<?= $payment->getIdPayment() ?>" tabindex="-1" aria-hidden="true"
                    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 max-w-2xl max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-[#323231]">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                <!-- Contenedor para el título -->
                                <div class="mr-4">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    <?= i18n("Do you want to delete the payment?") ?>
                                    </h3>
                                </div>

                                <!-- Contenedor para el botón -->
                                <div class="ml-4">
                                    <!-- Use data-modal-toggle to close modal -->
                                    <button type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-modal-toggle="modal-deletepayment<?= $payment->getIdPayment() ?>">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                                <!-- Form delete -->
                                <form id="deleteProject" class="text-white" method="POST"
                                    action="index.php?controller=payment&amp;action=delete">
                                    <input type="text" value="<?= $payment->getIdPayment() ?>" name="idPayment" hidden>
                                    <input type="text" value="<?= $payment->getIdProject() ?>" name="idProject" hidden>
                                    <button type="submit"
                                        class="text-white bg-red-500 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"><i
                                            class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <!-- Botón añadir nuevo pago -->
    <div class="flex justify-center mt-4">
        <a href="index.php?controller=payment&amp;action=add&amp;idProject=<?= $project->getIdProject() ?>">
            <button class="px-4 py-2 bg-[#edb705] hover:bg-[#ab8403] text-white rounded-lg">
                <i class="fa-solid fa-money-bill-1-wave"></i>
            </button>
        </a>
    </div>
</div>