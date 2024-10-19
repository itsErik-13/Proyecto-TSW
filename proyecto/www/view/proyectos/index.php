<?php
//file: view/posts/index.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$currentuser = $view->getVariable(varname: "currentusername");

$view->setVariable("title", "Proyectos");

?>


<p class="text-green-800">TODO: AQUÍ SE VERÁN LOS PROYECTOS</p>
