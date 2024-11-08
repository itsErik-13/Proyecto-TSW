<?php
//file: view/proyectos/index.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$proyecto = $view->getVariable("proyecto");

$currentuser = $view->getVariable("currentusername");

$errors = $view->getVariable("errors");

$view->setVariable("title", i18n("Payments") );

?>


<h1>Proyecto : <?= $proyecto->getId() ?></h1>