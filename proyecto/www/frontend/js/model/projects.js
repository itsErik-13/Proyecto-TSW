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

  setProjects(projects) {
    this.set((self) => {
      self.projects = projects;
    });
  }
}
