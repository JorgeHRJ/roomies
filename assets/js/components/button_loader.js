function showLoader(event) {
  const button = event.currentTarget;
  button.innerHTML = '';

  const loader = document.createElement('span');
  loader.classList.add('spinner-border');

  button.appendChild(loader);
}

function init() {
  const buttons = document.querySelectorAll('[data-component="button-loader"]');
  if (buttons) {
    buttons.forEach((button) => {
      button.addEventListener('click', showLoader);
    });
  }
}

export default init;
