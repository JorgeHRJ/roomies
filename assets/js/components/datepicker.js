import { Datepicker } from 'vanillajs-datepicker';

function initDatepicker(element) {
  return new Datepicker(element, {
    buttonClass: 'btn',
    format: 'dd/mm/yyyy'
  });
}

function initDatepickerComponent() {
  const datepickers = [].slice.call(document.querySelectorAll('[data-component="datepicker"]'));
  const datepickersList = datepickers.map((element) => {
      return initDatepicker(element);
  });
}

export {
  initDatepickerComponent,
  initDatepicker
};
