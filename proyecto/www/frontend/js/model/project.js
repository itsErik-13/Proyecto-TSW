class ProjectModel extends Fronty.Model {

  constructor(idProject, projectName, theme, members) {
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

    if (members) {
      this.members = members;
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

  setMembers(members) {
    this.set((self) => {
      self.members = members;
    });
  }
}
