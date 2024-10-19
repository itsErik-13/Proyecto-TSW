<?php
// file: view/layouts/language_select_element.php
?>
<ul id="languagechooser" class="list-none flex flex-wrap items-center justify-center text-gray-900 dark:text-white">
	<li><button onclick="location.href='index.php?controller=language&amp;action=change&amp;lang=es'"
			class="p-2 flex flex-row items-center border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:bg-gray-200 focus:outline-none">
			<span class="text-md">Es</span>
			<span class="ml-1"> <img src="<?php echo '/controller/ImageController.php?image=spain.png'; ?>"
					class="w-5 h-5" /></span>
		</button></li>
	<li><button onclick="location.href='index.php?controller=language&amp;action=change&amp;lang=en'"
			class="p-2 flex flex-row items-center border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:bg-gray-200 focus:outline-none">
			<span class="text-md">En</span>
			<span class="ml-1"> <img src="<?php echo '/controller/ImageController.php?image=united-kingdom.png'; ?>"
					class="w-5 h-5" /></span>
		</button></li>
</ul>