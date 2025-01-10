class MemberAddComponent extends Fronty.ModelComponent {
    constructor(projectsModel, userModel, router) {
      super(Handlebars.templates.memberadd, projectsModel);
      this.projectsModel = projectsModel;
  
      this.userModel = userModel; // global
      this.addModel('user', userModel);
      this.router = router;
  
      this.projectsService = new ProjectsService();
  
      this.addEventListener('click', '#addMember', () => {
        var newMember = {};
        newMember.email = $('#email').val();

        this.projectsService.addMember(this.projectsModel.selectedProject.idProject, newMember)
          .then(() => {
            this.projectsModel.memberErrors = null;
            window.location.hash = '#view-members?idProject=' + this.projectsModel.selectedProject.idProject;
          }).fail((xhr, errorThrown, statusText) => {
            if (xhr.status == 400) {
              this.projectsModel.set(() => {
                this.projectsModel.memberErrors = xhr.responseText;
              });
            } else {
              alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
            }
          });
        });
    }
  
    onStart() {
      var selectedId = this.router.getRouteQueryParam('idProject');
      this.loadProject(selectedId);
      this.setMembers(selectedId);
      this.projectsModel.loggedUser = this.userModel.currentUser;
      initFlowbite();
    }
  
    loadProject(projectId) {
      if (projectId != null) {
        this.projectsService.getProject(projectId)
          .then((project) => {
            this.projectsModel.setSelectedProject(project);
          });
      }
    }
  
    setMembers(projectId) {
      if (projectId != null) {
        this.projectsService.getMembers(projectId)
          .then((members) => {
            this.projectsModel.setMembers(members);
          });
      }
    }
  }
  