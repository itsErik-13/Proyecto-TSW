<?php
//file: view/proyectos/index.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$project = $view->getVariable("project");

$members = $view->getVariable("members");

$currentuser = $view->getVariable("currentusername");

$errors = $view->getVariable("errors");

$view->setVariable("title", i18n("Members"));

?>


<div class="bg-[#323231] rounded-lg p-4 shadow-lg max-w-4xl w-full text-center">
    <h2 class="text-3xl font-bold text-white mb-6"><?= $project->getProjectName() ?></h2>

    <nav>
        <ul class="flex justify-center space-x-10 text-white text-lg font-bold">
            <li>
                <a href="index.php?controller=project&amp;action=view&amp;idProject=<?= $project->getIdProject() ?>" class="hover:text-<?=$project->getTheme()?>"><?=i18n("Payments") ?></a>
            </li>
            <li>
                <a href="index.php?controller=debt&amp;action=index&amp;idProject=<?= $project->getIdProject() ?>" class="hover:text-<?=$project->getTheme()?>"><?=i18n("Debts") ?></a>
            </li>
            <li>
                <a class="text-<?=$project->getTheme()?> underline"><?=i18n("Members") ?></a>
            </li>
        </ul>
    </nav>
</div>

<div class="bg-[#323231] rounded-lg p-8 shadow-lg max-w-4xl w-full text-center mt-2">
    <!-- Listado de miembros -->

    <div
        class="space-y-4 overflow-y-auto max-h-[320px] pr-2 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-thumb]:rounded-full">
        <?php foreach ($members as $member): ?>
            <div class="flex flex-col justify-between bg-[#3d3d3d] p-4 rounded text-white space-y-2">
                <h1 class="text-xl font-bold text-white"><?= $member["userName"] ?></h1>
            </div>
        <?php endforeach ?>
    </div>

    <div class="flex justify-center mt-4">
        <a href="index.php?controller=project&amp;action=addMember&amp;idProject=<?= $project->getIdProject() ?>">
            <button class="px-4 py-2 bg-[#edb705] hover:bg-[#ab8403] text-white rounded-lg">
                <i class="fa-solid fa-user-plus"></i>
            </button>
        </a>

    </div>
</div>