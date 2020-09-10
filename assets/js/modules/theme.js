import { Tooltip, Popover } from 'bootstrap';

function initIconNotifications() {
  const iconNotifications = document.querySelector('.icon-notifications');
  if (iconNotifications) {
    const unreadNotifications = document.querySelector('.unread-notifications');
    const bellShake = document.querySelector('.bell-shake');

    if (iconNotifications.getAttribute('data-unread-notifications') === 'true') {
      unreadNotifications.style.display = 'block';
    } else {
      unreadNotifications.style.display = 'none';
    }

    // bell shake
    const shakingInterval = setInterval(() => {
      if (iconNotifications.getAttribute('data-unread-notifications') === 'true') {
        if (bellShake.classList.contains('shaking')) {
          bellShake.classList.remove('shaking');
        } else {
          bellShake.classList.add('shaking');
        }
      }
    }, 5000);

    iconNotifications.addEventListener('show.bs.dropdown', () => {
      bellShake.setAttribute('data-unread-notifications', 'false');
      clearInterval(shakingInterval);
      bellShake.classList.remove('shaking');
      unreadNotifications.style.display = 'none';
    });
  }
}

function initPreloader() {
  const preloader = document.querySelector('.preloader');
  if (preloader) {
    setTimeout(() => {
      preloader.classList.add('show');

      setTimeout(() => {
        document.querySelector('.loader-element').classList.add('hide');
      }, 200);
    }, 1000);
  }
}

function initTooltips() {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
  const tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => {
    return new Tooltip(tooltipTriggerEl);
  });
}

function initPopovers() {
  const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="popover"]'));
  const popoverList = popoverTriggerList.map((popoverTriggerEl) => {
    return new Popover(popoverTriggerEl);
  });
}

function init() {
  initPreloader();
  initIconNotifications();
  initBackgroundChecks();
  initTooltips();
  initPopovers();
}

export default init;
