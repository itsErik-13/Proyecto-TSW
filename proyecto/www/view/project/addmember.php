<?php
//file: view/proyectos/index.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$project = $view->getVariable("project");

$member = $view->getVariable("member");

$currentuser = $view->getVariable("currentusername");

$errors = $view->getVariable("errors");

$view->setVariable("title", i18n("Add member"));

?>

<div class="bg-[#323231] rounded-lg p-8 shadow-lg max-w-sm w-full">
    <h2 class="text-3xl font-bold text-white text-center mb-6"><?= i18n("Add member") ?></h2>
    <form class="space-y-4 text-[#fff] max-w-md mx-auto" action="index.php?controller=project&amp;action=addMember"
        method="POST">
        <input type="hidden" name="idProject" value="<?= $project->getIdProject() ?>" />
        <div class="relative flex items-center">
            <input type="email" name="email" placeholder="<?= i18n(key: "Email") ?>"
                class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all"
                value="<?= $member->getEmail() ?>"/>
            <div class="absolute right-4 flex items-centerpointer-events-none">
                <i class="fa-regular fa-envelope text-gray-300"></i>
            </div>
        </div>
        <p class="text-red-600 inline"><?= isset($errors["email"]) ? i18n($errors["email"]) : "" ?></p>
        <p class="text-red-600 inline"><?= isset($errors["general"]) ? $errors["general"] : "" ?></p>
        <div class="relative flex items-centerpointer-events-none">
            <button type="submit" name="submit"
                class="absolute right-0 px-3 py-3 bg-[#edb705] hover:bg-[#ab8403] text-white rounded-lg active:bg-[#006bff]"><img
                    src="<?php echo '/controller/ImageController.php?image=add_person.svg'; ?>"></button>
        </div>

    </form>
    <div class="mt-4 ">
        <a href="index.php?controller=project&amp;action=viewMembers&amp;idProject=<?= $project->getIdProject() ?>"
            class="text-yellow-400 hover:text-yellow-500 text-sm underline"><?= i18n("Cancel") ?></a>
    </div>
</div>