function handleFileInput(event) {
  const input = event.currentTarget;

  const filename = input.value.split('\\').pop();
  if (filename !== '') {
    input.nextElementSibling.querySelector('.form-file-text').innerText = filename;
  }

  const reader = new FileReader();
  reader.onload = () => {
    const output = document.querySelector('[data-component="image-preview"]');
    output.src = reader.result;
    if (output.classList.contains('hidden')) {
      output.classList.remove('hidden');
    }
  };
  reader.readAsDataURL(input.files[0]);
}

function init() {
  const imageInput = document.querySelector('[data-component="image-file-input"]');
  const imagePreview = document.querySelector('[data-component="image-preview"]');
  if (imageInput && imagePreview) {
    imageInput.addEventListener('change', handleFileInput);
  }
}

export default init;
