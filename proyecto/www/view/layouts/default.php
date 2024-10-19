<?php
//file: view/layouts/default.php

$view = ViewManager::getInstance();
$currentuser = $view->getVariable("currentusername");

?><!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <title>PayBuddy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    'rubik': ['Rubik', 'sans-serif']
                }
            }
        }
    </script>
</head>

<body
    class="bg-amber-900 sm:bg-red-400 md:bg-indigo-500 lg:bg-green-400 xl:bg-fuchsia-500 2xl:bg-[#edb705] flex flex-col items-center justify-center h-screen">
    <!-- header -->
    <header>
        <div class="absolute top-5 left-5 flex items-center space-x-2">
            <img src="<?php echo '/controller/ImageController.php?image=logo2.png'; ?>" alt="Logo" class="w-10 h-10">
            <h1 class="text-3xl font-bold text-white">Paybuddy</h1>
        </div>
        <di class="absolute top-5 right-5 flex items-center space-x-2">
            <?php if (isset($currentuser)): ?>
                <?= sprintf($currentuser) ?>
                <a href="index.php?controller=users&amp;action=logout" class="pl-2"><?= i18n("Logout") ?></a>


            <?php else: ?>
                <a href="index.php?controller=users&amp;action=login"><?= i18n("Login") ?></a>
            <?php endif ?>
            </div>
    </header>


    <main  class="flex flex-col justify-center items-center h-full w-full">
        <div id="flash">
            <?= $view->popFlash() ?>
        </div>
        <?= $view->getFragment(ViewManager::DEFAULT_FRAGMENT) ?>
    </main>

    <footer class="mt-auto">
        <?php
        include(__DIR__ . "/language_select_element.php");
        ?>
    </footer>

</body>

</html>