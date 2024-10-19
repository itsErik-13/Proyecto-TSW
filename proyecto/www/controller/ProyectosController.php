<?php
//file: controller/PostController.php

require_once(__DIR__ . "/../model/User.php");

require_once(__DIR__ . "/../core/ViewManager.php");
require_once(__DIR__ . "/../controller/BaseController.php");

/**
 * Class ProyectosController
 *
 * Controller to make a CRUDL of Posts entities
 *
 * @author lipido <lipido@gmail.com>
 */
class ProyectosController extends BaseController
{

    /**
     * Reference to the ProyectoMapper to interact
     * with the database
     *
     * @var ProyectoMapper
     */
    //private $proyectoMapper;

    public function __construct()
    {
        parent::__construct();
        //$this->proyectoMapper = new ProyectoMapper();
    }

    /**
     * Action to list posts
     *
     * Loads all the posts from the database.
     * No HTTP parameters are needed.
     *
     * The views are:
     * <ul>
     * <li>posts/index (via include)</li>
     * </ul>
     */
    public function index()
    {
        // render the view (/view/proyectos/index.php)
        $this->view->render("proyectos", "index");
    }
}