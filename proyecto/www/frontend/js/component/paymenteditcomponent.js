class PaymentEditComponent extends Fronty.ModelComponent {
  constructor(projectsModel, userModel, router) {
    super(Handlebars.templates.paymentedit, projectsModel);
    this.projectsModel = projectsModel;

    this.userModel = userModel; // global
    this.addModel('user', userModel);
    this.router = router;

    this.projectsService = new ProjectsService();

    this.addEventListener('click', '#editPayment', () => {
      var newPayment = {};
      newPayment.subject = $('#subject').val();
      newPayment.payerName = $('#payerName').val();
      newPayment.totalAmount = $('#totalAmount').val();
      newPayment.payerName = $('#payerName').val();
      newPayment.debtors = [];
      $('input[name="selectedUsers[]"]:checked').each(function () {
        newPayment.debtors.push($(this).val());
      });

      this.projectsService.updatePayment(this.projectsModel.selectedProject.idProject, this.projectsModel.selectedPayment.idPayment ,newPayment)
        .then(() => {
          this.projectsModel.paymentErrors = null;
          window.location.hash = '#view-project?idProject=' + this.projectsModel.selectedProject.idProject;
        }).fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
            this.projectsModel.set(() => {
              this.projectsModel.paymentErrors = xhr.responseJSON;
            });
          } else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        });
      })
  }

  onStart() {
    var selectedId = this.router.getRouteQueryParam('idProject');
    var selectedPaymentId = this.router.getRouteQueryParam('idPayment');
    this.loadProject(selectedId);
    this.setMembers(selectedId);
    this.setPayment(selectedId, selectedPaymentId);
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

  setPayment(projectId, paymentId) {
    if (projectId != null && paymentId != null) {
      this.projectsService.getPayment(projectId, paymentId)
        .then((payment) => {
          this.projectsModel.setSelectedPayment(payment);
        });
    }
  }
}
