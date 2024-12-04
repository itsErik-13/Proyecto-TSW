class ProjectModel extends Fronty.Model {

  constructor(idProject, projectName, theme) {
    super('ProjectModel'); //call super
    
    if (idProject) {
      this.idProject = idProject;
    }
    
    if (projectName) {
      this.projectName = projectName;
    }
    
    if (theme) {
      this.theme = theme;
    }
  }

  setName(projectName) {
    this.set((self) => {
      self.projectName = projectName;
    });
  }

  setTheme(theme) {
    this.set((self) => {
      self.theme = theme;
    });
  }
}
