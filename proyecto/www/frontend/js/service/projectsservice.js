class ProjectsService {
  constructor() {

  }

  getProjects() {
    return $.get(AppConfig.backendServer+'/rest/project');
  }

  createProject(project) {
    return $.ajax({
      url: AppConfig.backendServer+'/rest/project',
      method: 'POST',
      data: JSON.stringify(project),
      contentType: 'application/json'
    });
  }

  deleteProject(id) {
    return $.ajax({
      url: AppConfig.backendServer+'/rest/project/' + id,
      method: 'DELETE'
    });
  }

  getPayments(idProject) {
    return $.get(AppConfig.backendServer+'/rest/project/' + idProject + '/payment');
  }

  createPayment(idProject, payment) {
    return $.ajax({
      url: AppConfig.backendServer+'/rest/project/' + idProject + '/payment',
      method: 'POST',
      data: JSON.stringify(payment),
      contentType: 'application/json'
    });
  }

  updatePayment(idProject, idPayment, payment) {
    return $.ajax({
      url: AppConfig.backendServer+'/rest/project/' + idProject + '/payment/' + idPayment,
      method: 'PUT',
      data: JSON.stringify(payment),
      contentType: 'application/json'
    });
  }

  deletePayment(idProject, idPayment) {
    return $.ajax({
      url: AppConfig.backendServer+'/rest/project/' + idProject + '/payment/' + idPayment,
      method: 'DELETE'
    });
  }

  getDebts(idProject) {
    return $.get(AppConfig.backendServer+'/rest/project/' + idProject + '/debt');
  }

  getMembers(idProject) {
    return $.get(AppConfig.backendServer+'/rest/project/' + idProject + '/member');
  }

  addMember(idProject, member) {
    return $.ajax({
      url: AppConfig.backendServer+'/rest/project/' + idProject + '/member',
      method: 'POST',
      data: JSON.stringify(member),
      contentType: 'application/json'
    });
  }

  getProject(idProject) {
    return $.get(AppConfig.backendServer + '/rest/project/' + idProject);
  }

}