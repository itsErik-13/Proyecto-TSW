class ProjectsComponent extends Fronty.ModelComponent {
  constructor(projectsModel, userModel, router) {

    console.log('ProjectsComponent constructor');
    super(Handlebars.templates.projectstable, projectsModel, null, null); //change template
    console.log(Handlebars.templates); 
    
    this.projectsModel = projectsModel;
    this.userModel = userModel;
    this.addModel('user', userModel);
    this.router = router;

    this.projectsService = new ProjectsService();

    this.addEventListener('click', '#savebutton', () => {
      var newProject = {};
      newPost.title = $('#title').val();
      newPost.content = $('#content').val();
      newPost.author_id = this.userModel.currentUser;
      this.postsService.addPost(newPost)
        .then(() => {
          this.router.goToPage('posts');
        })
        .fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
            this.postsModel.set(() => {
              this.postsModel.errors = xhr.responseJSON;
            });
          } else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        });
    });
    
  }

  onStart() {
    console.log('ProjectsComponent onStart');
    
    this.updateProjects();
  }

  updateProjects() {
    this.projectsService.getProjects().then((data) => {      
      this.projectsModel.setProjects(
        data.map(
          (item) => new ProjectModel(item.idProject, item.projectName, item.theme)
      ));      
    })
  }

  // Override
  createChildModelComponent(className, element, id, modelItem) {
    return new ProjectRowComponent(modelItem, this.userModel, this.router, this);
  }
}

class ProjectRowComponent extends Fronty.ModelComponent {
  constructor(projectModel, userModel, router, projectsComponent) {
    super(Handlebars.templates.projectrow, projectModel, null, null);  // change template
    
    this.projectsComponent = projectsComponent;
    
    this.userModel = userModel;
    this.addModel('user', userModel); // a secondary model
    
    this.router = router;

    // this.addEventListener('click', '.remove-button', (event) => {
    //   if (confirm(I18n.translate('Are you sure?'))) {
    //     var postId = event.target.getAttribute('item');
    //     this.postsComponent.postsService.deletePost(postId)
    //       .fail(() => {
    //         alert('post cannot be deleted')
    //       })
    //       .always(() => {
    //         this.postsComponent.updatePosts();
    //       });
    //   }
    // });

    // this.addEventListener('click', '.edit-button', (event) => {
    //   var postId = event.target.getAttribute('item');
    //   this.router.goToPage('edit-post?id=' + postId);
    // });
  }

}
