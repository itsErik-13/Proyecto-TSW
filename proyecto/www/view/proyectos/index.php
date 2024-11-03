<?php
//file: view/proyectos/index.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$proyectos = $view->getVariable("proyectos");

$currentuser = $view->getVariable("currentusername");

$errors = $view->getVariable("errors");

$view->setVariable("title", i18n("Projects"));

?>

<div class="bg-[#323231] rounded-lg p-8 shadow-lg max-w-4xl w-full text-center">
    <h2 class="text-3xl font-bold text-white mb-6"><?= i18n("Projects") ?></h2>

    <!-- Lista de proyectos -->
    <div class="space-y-4 overflow-y-auto max-h-[350px] pr-2
        [&::-webkit-scrollbar]:w-2
  [&::-webkit-scrollbar-track]:bg-gray-100
  [&::-webkit-scrollbar-thumb]:bg-gray-300
  dark:[&::-webkit-scrollbar-track]:bg-neutral-700
  dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500
  [&::-webkit-scrollbar-track]:rounded-full
  [&::-webkit-scrollbar-thumb]:rounded-full">

        <?php foreach ($proyectos as $proyecto): ?>

            <!-- Div para cada proyecto -->
            <div class="flex justify-between items-center bg-[#3d3d3d] p-4 rounded text-white">
                <a href="index.php?controller=proyectos&amp;action=viewMembers&amp;id=<?= $proyecto->getId() ?>">
                    <div class="flex items-center space-x-2">
                        <i class="fa-regular fa-folder-open text-<?= $proyecto->getTheme() ?>"></i>
                        <span><?= $proyecto->getName() ?></span>
                    </div>
                </a>
                <?php $modalId = "modal-" . $proyecto->getId(); ?>
                <button data-modal-target="<?= $modalId ?>" data-modal-toggle="<?= $modalId ?>"
                    class="text-white hover:text-red-500" type="button">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>

            <!-- Delete Project Modal -->
            <div id="<?= $modalId ?>" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 max-w-2xl max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-[#323231]">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                            <!-- Contenedor para el título -->
                            <div class="mr-4">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    <?= i18n("Do you want to delete the project?") ?>
                                </h3>
                            </div>

                            <!-- Contenedor para el botón -->
                            <div class="ml-4">
                                <!-- Use data-modal-toggle to close modal -->
                                <button type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-modal-toggle="<?= $modalId ?>">
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
                                action="index.php?controller=proyectos&amp;action=delete">
                                <input type="text" value="<?= $proyecto->getId() ?>" name="id" hidden>
                                <button type="submit"
                                    class="text-white bg-red-500 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"><i
                                        class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach ?>
    </div>

    <!-- Botón añadir nuevo proyecto -->
    <div class="flex justify-center mt-4">
        <button data-modal-target="modal-addProject" data-modal-toggle="modal-addProject"
            class="px-4 py-2 bg-[#edb705] hover:bg-[#ab8403] text-white rounded-lg">
            <i class="fa-solid fa-folder-plus"></i>
        </button>
    </div>


    <div id="modal-addProject" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 max-w-2xl max-h-full">
            <div class="relative bg-[#323231] rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white"><?= i18n("Add new project") ?></h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="modal-addProject">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                </div>
                <div class="p-4">
                    <form id="addProjectForm" class="text-white" method="POST"
                        action="index.php?controller=proyectos&amp;action=add">
                        <div class="relative flex items-center mb-4">
                            <input type="text" name="projectName" placeholder="<?= i18n("Project name") ?>"
                                class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all"
                                required />
                            <div class="absolute right-4 flex items-centerpointer-events-none">
                                <i class="fa-regular fa-folder-open text-gray-300"></i>
                            </div>
                        </div>
                        <p class="text-red-600 inline">
                            <?= isset($errors["projectName"]) ? i18n($errors["projectName"]) : "" ?></p>
                        <div class="flex flex-wrap mb-4">
                            <label class="flex items-center me-4 relative">
                                <input id="orange-radio" type="radio" name="projectTheme" value="orange-300"
                                    class="hidden peer" required />
                                <div
                                    class="w-8 h-8 rounded-full bg-orange-300 cursor-pointer hover:opacity-75 peer-checked:ring-2 peer-checked:ring-white peer-checked:ring-offset-2 peer-checked:ring-opacity-100">
                                </div>
                            </label>
                            <label class="flex items-center me-4 relative">
                                <input id="red-radio" type="radio" name="projectTheme" value="red-500"
                                    class="hidden peer" required />
                                <div
                                    class="w-8 h-8 rounded-full bg-red-500 cursor-pointer hover:opacity-75 peer-checked:ring-2 peer-checked:ring-white peer-checked:ring-offset-2 peer-checked:ring-opacity-100">
                                </div>
                            </label>
                            <label class="flex items-center me-4 relative">
                                <input id="green-radio" type="radio" name="projectTheme" value="green-400"
                                    class="hidden peer" required />
                                <div
                                    class="w-8 h-8 rounded-full bg-green-400 cursor-pointer hover:opacity-75 peer-checked:ring-2 peer-checked:ring-white peer-checked:ring-offset-2 peer-checked:ring-opacity-100">
                                </div>
                            </label>
                            <label class="flex items-center me-4 relative">
                                <input checked id="purple-radio" type="radio" name="projectTheme" value="purple-400"
                                    class="hidden peer" required />
                                <div
                                    class="w-8 h-8 rounded-full bg-purple-400 cursor-pointer hover:opacity-75 peer-checked:ring-2 peer-checked:ring-white peer-checked:ring-offset-2 peer-checked:ring-opacity-100">
                                </div>
                            </label>
                            <label class="flex items-center me-4 relative">
                                <input id="cyan-radio" type="radio" name="projectTheme" value="cyan-500"
                                    class="hidden peer" required />
                                <div
                                    class="w-8 h-8 rounded-full bg-cyan-500 cursor-pointer hover:opacity-75 peer-checked:ring-2 peer-checked:ring-white peer-checked:ring-offset-2 peer-checked:ring-opacity-100">
                                </div>
                            </label>
                            <label class="flex items-center me-4 relative">
                                <input id="yellow-radio" type="radio" name="projectTheme" value="yellow-400"
                                    class="hidden peer" required />
                                <div
                                    class="w-8 h-8 rounded-full bg-yellow-400 cursor-pointer hover:opacity-75 peer-checked:ring-2 peer-checked:ring-white peer-checked:ring-offset-2 peer-checked:ring-opacity-100">
                                </div>
                            </label>
                            <label class="flex items-center me-4 relative">
                                <input id="black-radio" type="radio" name="projectTheme" value="black"
                                    class="hidden peer" required />
                                <div
                                    class="w-8 h-8 rounded-full bg-black cursor-pointer hover:opacity-75 peer-checked:ring-2 peer-checked:ring-white peer-checked:ring-offset-2 peer-checked:ring-opacity-100">
                                </div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" name="submit"
                                class="text-white bg-[#edb705] hover:bg-[#ab8403] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                <i class="fa-solid fa-folder-plus"></i>
                            </button>
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm px-5 py-2.5"
                                data-modal-toggle="modal-addProject"><?= i18n("Cancel") ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>