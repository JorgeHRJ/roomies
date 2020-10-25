import '../css/app.scss';

// import modules
import themeModule from './modules/theme';

// import components
import buttonLoaderComponent from './components/button_loader';

// import controllers
import initHome from './controllers/home_controller';

themeModule();
buttonLoaderComponent();

if (document.querySelector('[data-controller="home"]')) {
  initHome();
}
