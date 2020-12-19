function handleFileInput(event) {
  const input = event.currentTarget;

  const filename = input.value.split('\\').pop();
  if (filename !== '') {
    input.nextElementSibling.querySelector('.form-file-text').innerText = filename;
  }

  const reader = new FileReader();
  reader.onload = () => {
    const imageOutput = document.querySelector('[data-component="image-preview"]');
    if (imageOutput) {
      imageOutput.src = reader.result;
      if (imageOutput.classList.contains('hidden')) {
        imageOutput.classList.remove('hidden');
      }
    }
  };
  reader.readAsDataURL(input.files[0]);
}

function init() {
  const fileInput = document.querySelector('[data-component="file-input"]');
  if (fileInput) {
    fileInput.addEventListener('change', handleFileInput);
  }
}

export default init;
