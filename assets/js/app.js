import '../css/app.scss';

// import modules
import themeModule from './modules/theme';

// import components
import buttonLoaderComponent from './components/button_loader';
import fileInputComponent from './components/file_input';

// import controllers
import initHome from './controllers/home_controller';
import initExpense from './controllers/expense_controller';

themeModule();
buttonLoaderComponent();
fileInputComponent();

if (document.querySelector('[data-controller="home"]')) {
  initHome();
}

if (document.querySelector('[data-controller="expense"]')) {
  initExpense();
}
