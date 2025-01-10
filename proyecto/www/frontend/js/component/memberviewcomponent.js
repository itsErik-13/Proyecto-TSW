class MemberViewComponent extends Fronty.ModelComponent {
  constructor(projectsModel, userModel, router) {
    super(Handlebars.templates.memberview, projectsModel);

    this.projectsModel = projectsModel; // posts
    this.userModel = userModel; // global
    this.addModel("user", userModel);
    this.router = router;

    this.projectsService = new ProjectsService();
  }

  onStart() {
    var selectedId = this.router.getRouteQueryParam("idProject");
    this.setMembers(selectedId);
  }

  setMembers(projectId) {
    if (projectId != null) {
      this.projectsService.getMembers(projectId).then((members) => {
        this.projectsModel.setMembers(members);
      });
    }
  }
}
