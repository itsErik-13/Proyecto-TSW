class ProjectsComponent extends Fronty.ModelComponent {
  constructor(projectsModel, userModel, router) {
    super(Handlebars.templates.projectstable, projectsModel, null, null); //change template
    
    this.projectsModel = projectsModel;
    this.userModel = userModel;
    this.addModel('user', userModel);
    this.router = router;

    this.projectsService = new ProjectsService();

    this.addEventListener('click', '#addProject', () => {
      var newProject = {};
      newProject.projectName = $('#projectName').val();
      newProject.theme = document.querySelector('input[name="theme"]:checked').value;
      
      this.projectsService.createProject(newProject)
        .then(() => {
          this.updateProjects();
        })
        .catch((error) => {
          this.projectsModel.set((model) => {
            model.projectError = error.responseText;
          });
        });
    });
    
  }

  onStart() {
    this.updateProjects();
  }

  updateProjects() {
    if (this.userModel.isLogged){
      this.projectsService.getProjects().then((data) => {      
        this.projectsModel.setProjects(
          data.map(
            (item) => new ProjectModel(item.idProject, item.projectName, item.theme)
        ));   
        initFlowbite();
      });
    }else{
      this.router.goToPage('login');
    }
  }

  // Override
  createChildModelComponent(className, element, id, modelItem) {
    return new ProjectRowComponent(modelItem, this.userModel, this.router, this);
  }
}

class ProjectRowComponent extends Fronty.ModelComponent {
  constructor(projectModel, userModel, router, projectsComponent) {
    super(Handlebars.templates.projectrow, projectModel, null, null);  // change template
    
    this.projectsComponent = projectsComponent;
    
    this.userModel = userModel;
    this.addModel('user', userModel); // a secondary model
    
    this.router = router;
    this.addEventListener('click', '.deleteProject', (event) => {
        var projectId = event.target.getAttribute('item');
        this.projectsComponent.projectsService.deleteProject(projectId)
          .fail(() => {
            alert('project cannot be deleted')
          })
          .always(() => {
            this.projectsComponent.updateProjects();
          });
    });

    
  }

}
