import '../css/app.scss';

// import modules
import themeModule from './modules/theme';

// import components
import buttonLoaderComponent from './components/button_loader';
import imagePreviewComponent from './components/image_preview';

// import controllers
import initHome from './controllers/home_controller';
import initExpense from './controllers/expense_controller';

themeModule();
buttonLoaderComponent();
imagePreviewComponent();

if (document.querySelector('[data-controller="home"]')) {
  initHome();
}

if (document.querySelector('[data-controller="expense"]')) {
  initExpense();
}
