<?php
//file: view/user/login.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();
$view->setVariable("title", "Login");
$errors = $view->getVariable("errors");
$user = $view->getVariable("user");
?>

<div class="bg-[#323231] rounded-lg p-8 shadow-lg max-w-sm w-full">
    <h2 class="text-3xl font-bold text-white text-center mb-6"><?= i18n("Login") ?></h2>
    <form class="space-y-4 text-[#fff] max-w-md mx-auto" action="index.php?controller=user&amp;action=login"
        method="POST">
        <div class="relative flex items-center">
            <input type="text" name="userName" placeholder="<?= i18n("Username") ?>"
                class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all" value="<?= $user->getUserName() ?>"/>
            <div class="absolute right-4 flex items-centerpointer-events-none">
                <i class="fa-regular fa-user text-gray-300"></i>
            </div>
        </div>

        <div class="relative flex items-center">
            <input type="password" name="password" placeholder="<?= i18n("Password") ?>"
                class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all" value="<?= $user->getPassword() ?>"/>
            <div class="absolute right-4 flex items-centerpointer-events-none">
                <i class="fa-regular fa-eye text-gray-300"></i>
            </div>
        </div>

        <p class="text-red-600 inline"><?= isset($errors["general"]) ? $errors["general"] : "" ?></p>
        <div class="relative flex items-centerpointer-events-none">
            <button type="submit"
                class="absolute right-0 px-3 py-3 bg-[#edb705] hover:bg-[#ab8403] text-white rounded-lg active:bg-[#006bff]"><img
                    src="<?php echo '/controller/ImageController.php?image=login.svg'; ?>"></button>
        </div>

    </form>

    <div class="mt-4 ">
        <a href="index.php?controller=user&amp;action=register"
            class="text-yellow-400 hover:text-yellow-500 text-sm underline"><?= i18n("Register here!") ?></a>
    </div>
</div>