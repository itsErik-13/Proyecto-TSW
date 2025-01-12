class ProjectViewComponent extends Fronty.ModelComponent {
 
  constructor(projectsModel, userModel, router) {
    super(Handlebars.templates.projectview, projectsModel);

    this.projectsModel = projectsModel; // posts
    this.userModel = userModel; // global
    this.addModel('user', userModel);
    this.router = router;

    this.projectsService = new ProjectsService();
  }

  onStart() {
    
    var selectedId = this.router.getRouteQueryParam('idProject');
    
    this.projectsModel.paymentErrors = null;
    this.loadProject(selectedId);
    this.getPayments(selectedId);
    
  }

  loadProject(projectId) {
    if (projectId != null) {
      this.projectsService.getProject(projectId)
        .then((project) => {
          this.projectsModel.setSelectedProject(project);
        });
    }
  }

  getPayments(projectId) {
    if (projectId != null) {
      this.projectsService.getPayments(projectId)
        .then((payments) => {
          this.projectsModel.setPayments(payments.map(
            (item) => new PaymentModel(item["payerName"], item["idProject"], item["idPayment"], item["totalAmount"], item["subject"], item["debtors"])
          ));
          initFlowbite();
        });
    }else{
      this.router.goToPage('login');
    }
  }

  // Override
  createChildModelComponent(className, element, id, modelItem) {
    return new PaymentRowComponent(modelItem, this.userModel, this.router, this);
  }
  
}


class PaymentRowComponent extends Fronty.ModelComponent {
  constructor(paymentModel, userModel, router, projectViewComponent) {
    super(Handlebars.templates.paymentrow, paymentModel, null, null);  // change template    
    this.projectViewComponent = projectViewComponent;
    
    this.userModel = userModel;
    this.addModel('user', userModel); // a secondary model
    
    this.router = router;
    this.addEventListener('click', '.deletePayment', (event) => {
        var paymentId = event.target.getAttribute('idPayment');
        var projectId = event.target.getAttribute('idProject');
        this.projectViewComponent.projectsService.deletePayment(projectId, paymentId)
          .fail(() => {
            alert('project cannot be deleted')
          })
          .always(() => {
            this.projectViewComponent.getPayments(projectId);
          });
    }); 
  }

}
