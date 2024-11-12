<?php
//file: view/user/register.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();
$errors = $view->getVariable("errors");
$user = $view->getVariable("user");
$view->setVariable("title", i18n("Register"));
?>

<div class="bg-[#323231] rounded-lg p-8 shadow-lg max-w-sm w-full h">
        <h2 class="text-3xl font-bold text-white text-center mb-6"><?= i18n("Register")?></h2>

        <form class="space-y-4 text-[#fff] max-w-md mx-auto" action="index.php?controller=user&amp;action=register" method="POST">
            <div class="relative flex items-center">
                <input type="text" name="userName" placeholder="<?= i18n("Username")?>"
                    class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all" value="<?= $user->getUserName() ?>"/>
                <div class="absolute right-4 flex items-centerpointer-events-none">
                    <i class="fa-regular fa-user text-gray-300"></i>
                </div>
            </div>
            <p class="text-red-600 inline"><?= isset($errors["userName"])?i18n($errors["userName"]):"" ?></p>

            <div class="relative flex items-center">
                <input type="email" name="email" placeholder="<?= i18n(key: "Email")?>"
                    class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all" value="<?= $user->getEmail() ?>"/>
                <div class="absolute right-4 flex items-centerpointer-events-none">
                    <i class="fa-regular fa-envelope text-gray-300"></i>
                </div>
            </div>
            <p class="text-red-600 inline"><?= isset($errors["email"])?i18n($errors["email"]):"" ?></p>

            <div class="relative flex items-center">
                <input type="password" name="password" placeholder="<?= i18n("Password")?>"
                    class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all" value="<?= $user->getPassword() ?>"/>
                <div class="absolute right-4 flex items-centerpointer-events-none">
                    <i class="fa-regular fa-eye text-gray-300"></i>
                </div>
            </div>
            <p class="text-red-600 inline"><?= isset($errors["password"])?i18n($errors["password"]):"" ?></p>
            
            <div class="relative flex items-center">
                <input type="password" name="password2" placeholder="<?= i18n("Password")?>"
                    class="px-4 py-3 bg-[#323231] w-full text-sm border outline-[#edb705] rounded transition-all" value="<?= $user->getPassword2() ?>"/>
                <div class="absolute right-4 flex items-centerpointer-events-none">
                    <i class="fa-regular fa-eye text-gray-300"></i>
                </div>
            </div>
            <p class="text-red-600 inline"><?= isset($errors["password2"])?i18n($errors["password2"]):"" ?></p>

            <div class="relative flex items-centerpointer-events-none">
                <button type="submit"
                    class="absolute right-0 px-3 py-3 bg-[#edb705] hover:bg-[#ab8403] text-white rounded-lg active:bg-[#006bff]"><img
                        src="<?php echo '/controller/ImageController.php?image=add_person.svg'; ?>"></button>
            </div>

        </form>

        <div class="mt-4 ">
            <a href="index.php?controller=user&amp;action=login" class="text-yellow-400 hover:text-yellow-500 text-sm underline"><?= i18n("I already have an account") ?></a>
        </div>
    </div>