class RegisterComponent extends Fronty.ModelComponent {
  constructor(userModel, router) {
    super(Handlebars.templates.register, userModel);
    this.userModel = userModel;
    this.userService = new UserService();
    this.router = router;

    this.addEventListener('click', '#loginlink', () => {
      this.userModel.set(() => {
        this.router.goToPage('login');
      });
    });

    this.addEventListener('click', '#registerbutton', () => {
      this.userService.register({
          username: $('#registerusername').val(),
          password: $('#registerpassword').val(),
          email: $('#registeremail').val(),
          password2: $('#registerpassword2').val()
        })
        .then(() => {
          alert(I18n.translate('User registered! Please login'));
          this.userModel.set((model) => {
            model.registerErrors = {};
            this.router.goToPage('login');
          });
        })
        .fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
            this.userModel.set(() => {
              this.userModel.registerErrors = xhr.responseJSON;
            });
          } else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        });
    });
  }
}
