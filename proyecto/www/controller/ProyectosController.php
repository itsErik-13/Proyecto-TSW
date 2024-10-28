<?php
//file: controller/PostController.php

require_once(__DIR__."/../model/Proyecto.php");
require_once(__DIR__."/../model/ProyectoMapper.php");
require_once(__DIR__."/../model/User.php");

require_once(__DIR__ ."/../core/ViewManager.php");
require_once(__DIR__ ."/../controller/BaseController.php");

/**
 * Class ProyectosController
 *
 * Controller to make a CRUDL of Proyects entities
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
    private $proyectoMapper;

    public function __construct()
    {
        parent::__construct();
		$this->view->setLayout("default");
        $this->proyectoMapper = new ProyectoMapper();
    }

    /**
     * Action to list proyects
     *
     * Loads all the proyects from the database.
     * No HTTP parameters are needed.
     *
     * The views are:
     * <ul>
     * <li>proyectos/index (via include)</li>
     * </ul>
     */
    public function index()
    {

		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding posts requires login");
		}
        // obtain the data from the database
		$proyectos = $this->proyectoMapper->findProyectsByMember($this->currentUser->getUsername());

        // put the array containing Proyect object to the view
        $this->view->setVariable("proyectos", $proyectos);

        // render the view (/view/proyectos/index.php)
        $this->view->render("proyectos", "index");
    }

    /**
	* Action to view a given proyect
	*
	* This action should only be called via GET
	*
	* The expected HTTP parameters are:
	* <ul>
	* <li>id: Id of the proyect (via HTTP GET)</li>
	* </ul>
	*
	* The views are:
	* <ul>
	* <li>proyectos/view: If proyect is successfully loaded (via include).	Includes these view variables:</li>
	* <ul>
	*	<li>post: The current Proyect retrieved</li>
	*	<li>comment: The current Comment instance, empty or
	*	being added (but not validated)</li>
	* </ul>
	* </ul>
	*
	* @throws Exception If no such post of the given id is found
	* @return void
	*
	*/
	public function view(){
		if (!isset($_GET["id"])) {
			throw new Exception("id is mandatory");
		}

		$proyectoid = $_GET["id"];

		// find the Proyect object in the database
		$proyecto = $this->proyectoMapper->findById($proyectoid);

		if ($proyecto == NULL) {
			throw new Exception("no such proyect with id: ".$proyectoid);
		}

		// put the Proyect object to the view
		$this->view->setVariable("proyecto", $proyecto);

		// // check if comment is already on the view (for example as flash variable)
		// // if not, put an empty Comment for the view
		// $comment = $this->view->getVariable("comment");
		// $this->view->setVariable("comment", ($comment==NULL)?new Comment():$comment);

		// render the pproyect (/view/proyectos/view.php)
		$this->view->render("proyectos", "view");

	}

    /**
	* Action to add a new proyect
	*
	* When called via GET, it shows the add form
	* When called via POST, it adds the proyect to the
	* database
	*
	* The expected HTTP parameters are:
	* <ul>
	* <li>title: Title of the proyect (via HTTP POST)</li>
	* <li>content: Content of the proyect (via HTTP POST)</li>
	* </ul>
	*
	* The views are:
	* <ul>
	* <li>proyectos/add: If this action is reached via HTTP GET (via include)</li>
	* <li>proyectos/index: If proyect was successfully added (via redirect)</li>
	* <li>proyectos/add: If validation fails (via include). Includes these view variables:</li>
	* <ul>
	*	<li>proyect: The current Proyect instance, empty or
	*	being added (but not validated)</li>
	*	<li>errors: Array including per-field validation errors</li>
	* </ul>
	* </ul>
	* @throws Exception if no user is in session
	* @return void
	*/
	public function add() {
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding proyects requires login");
		}

		$proyecto = new Proyecto();

		if (isset($_POST["submit"])) { // reaching via HTTP Post...

			// populate the Proyect object with data form the form
			$proyecto->setName($_POST["projectName"]);
			$proyecto->setTheme($_POST["projectTheme"]);

			// The user of the Proyecto is the currentUser (user in session)
			// $post->setAuthor($this->currentUser);

			try {
				// validate Proyecto object
				$proyecto->checkIsValidForCreate(); // if it fails, ValidationException

				// save the Post object into the database
				$this->proyectoMapper->save($proyecto, $this->currentUser);

				// POST-REDIRECT-GET
				// Everything OK, we will redirect the user to the list of proyectos
				// We want to see a message after redirection, so we establish
				// a "flash" message (which is simply a Session variable) to be
				// get in the view after redirection.
				$this->view->setFlash(sprintf(i18n("Proyect \"%s\" successfully added."),$proyecto ->getName()));

				// perform the redirection. More or less:
				// header("Location: index.php?controller=proyectos&action=index")
				// die();
				$this->view->redirect("proyectos", "index");

			}catch(ValidationException $ex) {
				// Get the errors array inside the exepction...
				$errors = $ex->getErrors();
				// And put it to the view as "errors" variable
				$this->view->setVariable("errors", $errors);
			}
		}

		// Put the Proyect object visible to the view
		$this->view->setVariable("proyecto", $proyecto);

		// render the view (/view/proyectos/add.php)
		$this->view->render("proyectos", "index");

	}

    // EDIT

    /**
	* Action to delete a proyect
	*
	* This action should only be called via HTTP POST
	*
	* The expected HTTP parameters are:
	* <ul>
	* <li>id: Id of the proyect (via HTTP POST)</li>
	* </ul>
	*
	* The views are:
	* <ul>
	* <li>proyectos/index: If proyect was successfully deleted (via redirect)</li>
	* </ul>
	* @throws Exception if no id was provided
	* @throws Exception if no user is in session
	* @throws Exception if there is not any post with the provided id
	* @throws Exception if the author of the post to be deleted is not the current user
	* @return void
	*/
	public function delete() {
		if (!isset($_POST["id"])) {
			throw new Exception("id is mandatory");
		}
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Editing proyects requires login");
		}
		
		// Get the Proyect object from the database
		$proyectoid = $_REQUEST["id"];
		$proyecto = $this->proyectoMapper->findById($proyectoid);

		// Does the post exist?
		if ($proyecto == NULL) {
			throw new Exception("no such proyect with id: ".$proyectoid);
		}

		// Delete the Post object from the database
		$this->proyectoMapper->delete($proyecto);

		// POST-REDIRECT-GET
		// Everything OK, we will redirect the user to the list of posts
		// We want to see a message after redirection, so we establish
		// a "flash" message (which is simply a Session variable) to be
		// get in the view after redirection.
		$this->view->setFlash(sprintf(i18n("Proyecto \"%s\" successfully deleted."),$proyecto ->getName()));

		// perform the redirection. More or less:
		// header("Location: index.php?controller=posts&action=index")
		// die();
		$this->view->redirect("proyectos", "index");

	}
}