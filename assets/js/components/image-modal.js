function closeImageModal() {
  const modal = document.querySelector('[data-component="image-modal"]');
  modal.style.display = 'none';
}

function showImageModal(event) {
  const image = event.currentTarget;
  const modal = document.querySelector('[data-component="image-modal"]');
  if (modal) {
    modal.style.display = 'block';

    const modalImage = modal.querySelector('.image-modal-content');
    modalImage.src = image.src;

    const modalClose = modal.querySelector('.image-modal-close');
    modalClose.addEventListener('click', closeImageModal);
  }
}

function init() {
  const images = document.querySelectorAll('[data-component="image-expandable"]');
  images.forEach((image) => {
    image.addEventListener('click', showImageModal);
  });
}

export default init;
