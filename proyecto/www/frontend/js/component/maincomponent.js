class MainComponent extends Fronty.RouterComponent {
  constructor() {
    super('frontyapp', Handlebars.templates.main, 'maincontent');

    // models instantiation
    // we can instantiate models at any place
    this.userModel = new UserModel();
    this.userService = new UserService();
    this.projectsModel = new ProjectsModel();
    this.paymentsModel = new PaymentsModel();
    
    
    super.setRouterConfig({
      projects: {
        component: new ProjectsComponent(this.projectsModel, this.userModel, this),
        title: 'Projects'
      },
      'view-project': {
        component: new ProjectViewComponent(this.projectsModel, this.userModel, this),
        title: 'Project'
      },
      'view-members': {
        component: new MemberViewComponent(this.projectsModel, this.userModel, this),
        title: 'Member'
      },
      'add-payment': {
        component: new PaymentAddComponent(this.projectsModel, this.userModel, this),
        title: 'Add Payment'
      },
      'add-member': {
        component: new MemberAddComponent(this.projectsModel, this.userModel, this),
        title: 'Add Member'
      },
      'edit-payment': {
        component: new PaymentEditComponent(this.projectsModel, this.userModel, this),
        title: 'Edit Payment'
      },
      'view-debt': {
        component: new DebtViewComponent(this.projectsModel, this.userModel, this),
        title: 'Debts'
      }, 
      register: {
        component: new RegisterComponent(this.userModel, this),
        title: 'Register'
      },
      login: {
        component: new LoginComponent(this.userModel, this),
        title: 'Login'
      },
      defaultRoute: 'login'
    });

    Handlebars.registerHelper('currentPage', () => {
          return super.getCurrentPage();
    });

    this.addChildComponent(this._createUserBarComponent());
    this.addChildComponent(this._createLanguageComponent());

  }

  start() {
    // override the start() function in order to first check if there is a logged user
    // in sessionStorage, so we try to do a relogin and start the main component
    // only when login is checked
    this.userService.loginWithSessionData()
      .then((logged) => {
        if (logged != null) {
          this.userModel.setLoggeduser(logged);
        }
        super.start(); // now we can call start
      });
  }

  _createUserBarComponent() {
    var userbar = new Fronty.ModelComponent(Handlebars.templates.user, this.userModel, 'userbar');

    userbar.addEventListener('click', '#logoutbutton', () => {
      this.userModel.logout();
      this.userService.logout();
      this.goToPage('login');
    });

    return userbar;
  }

  _createLanguageComponent() {
    var languageComponent = new Fronty.ModelComponent(Handlebars.templates.language, this.routerModel, 'languagecontrol');
    // language change links
    languageComponent.addEventListener('click', '#englishlink', () => {
      I18n.changeLanguage('default');
      document.location.reload();
    });

    languageComponent.addEventListener('click', '#spanishlink', () => {
      I18n.changeLanguage('es');
      document.location.reload();
    });

    return languageComponent;
  }
}
