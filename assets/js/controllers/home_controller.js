function handleSubmitJoin(event) {
  event.preventDefault();

  const button = event.currentTarget;
  const input = document.querySelector('input#home_hash');

  window.location.href = button.dataset.path.replace('dummy', input.value);
}

function initJoinPage() {
  const submitJoinButton = document.querySelector('[data-action="submit-join"]');
  if (submitJoinButton) {
    submitJoinButton.addEventListener('click', handleSubmitJoin);
  }
}

function init() {
  if (document.querySelector('[data-page="join"]')) {
    initJoinPage();
  }
}

export default init;
