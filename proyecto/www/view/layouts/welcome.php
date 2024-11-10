<?php
// file: view/layouts/welcome.php

$view = ViewManager::getInstance();

?><!DOCTYPE html>
<html>


<head>
	<title><?= $view->getVariable("title", "no title") ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
		rel="stylesheet">
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script>
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script> -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

	<link rel="icon" href="<?php echo '/controller/ImageController.php?image=logo.png'; ?>" type="image/x-icon">
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
	class="bg-[#edb705] flex flex-col justify-center items-center h-screen ">
	<header>
		<div class="absolute top-5 left-5 flex items-center space-x-2">
			<img src="<?php echo '/controller/ImageController.php?image=logo.png'; ?>" alt="Logo" class="w-10 h-10">
			<h1 class="text-3xl font-bold text-white">Paybuddy</h1>
		</div>
	</header>
	<main class="flex flex-col justify-center items-center h-full w-full">
		<!-- flash message -->
		<div id="flash">
			<?= $view->popFlash() ?>
		</div>
		<?= $view->getFragment(ViewManager::DEFAULT_FRAGMENT) ?>
	</main>
	<footer class="mt-auto ">
		<?php
		include(__DIR__ . "/language_select_element.php");
		?>
	</footer>
	
</body>

</html>