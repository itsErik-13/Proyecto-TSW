class ProjectsModel extends Fronty.Model {

  constructor() {
    super('ProjectsModel'); //call super

    // model attributes
    this.projects = [];
  }

  setSelectedProject(project) {
    this.set((self) => {
      self.selectedProject = project;
    });
  }

  setPayments(payments) {
    this.set((self) => {
      self.payments = payments;
    });
  }

  setProjects(projects) {
    this.set((self) => {
      self.projects = projects;
    });
  }

  setMembers(members) {
    this.set((self) => {
      self.selectedProject.members = members;
    });
  }
}
