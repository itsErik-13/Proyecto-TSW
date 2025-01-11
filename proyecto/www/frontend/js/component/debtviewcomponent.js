class DebtViewComponent extends Fronty.ModelComponent {
  constructor(projectsModel, userModel, router) {
    super(Handlebars.templates.debtview, projectsModel);

    this.projectsModel = projectsModel; // posts
    this.userModel = userModel; // global
    this.addModel("user", userModel);
    this.router = router;

    this.projectsService = new ProjectsService();
  }

  onStart() {
    var selectedId = this.router.getRouteQueryParam("idProject");
    this.setDebts(selectedId);
  }

  setDebts(projectId) {
    if (projectId != null) {
      this.projectsService.getDebts(projectId).then((debts) => {
        this.projectsModel.setDebts(debts);
      });
    }
  }
}
