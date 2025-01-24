const backgroundButton = document.getElementById('backgroundButton');
const typoButton = document.getElementById('typoButton');
const selectedModule = document.getElementById('selectedModule');
const selectedModuleType = document.getElementById('selectedModuleType');
const colorPreview = document.getElementById('color-preview');
const colorPicker = document.getElementById('color-picker');
const colorContainer = document.getElementById('colorContainer');
const typoContentContainer = document.getElementById('typoContentContainer');

selectedModule.classList.remove('visible');
selectedModuleType.textContent = '';
typoContentContainer.classList.remove('visible');
colorContainer.classList.remove('visible');

backgroundButton.addEventListener('click', () => {
  selectModuleType('background');
});

typoButton.addEventListener('click', () => {
  selectModuleType('typo');
});

function selectModuleType(type) {
  if (type === 'background' || type === 'typo') {
    selectedModuleType.textContent = type.charAt(0).toUpperCase() + type.slice(1);
    selectedModule.classList.add('visible');

    if (type === 'background') {
      colorContainer.style.display = 'block';
      typoContentContainer.style.display = 'none';
    } else if (type === 'typo') {
      colorContainer.style.display = 'none';
      typoContentContainer.style.display = 'flex';
    }
  }
}

colorPreview.addEventListener('click', () => {
  colorPicker.click();
});

colorPicker.addEventListener('input', (event) => {
  colorPreview.style.backgroundColor = event.target.value;
});

window.addEventListener('DOMContentLoaded', () => {
  document.getElementById('clickout').value = 'https://github.com/juliawiniarska/Zadanie-rekrutacyjne---Julia-Winiarska';
  document.getElementById('width').value = '100';
  document.getElementById('height').value = '100';
  document.getElementById('positionX').value = '0';
  document.getElementById('positionY').value = '0';
  colorPreview.style.backgroundColor = '#FFA500';
  document.getElementById('content').value = 'Zadanie rekrutacyjne-Julia-Winiarska';
});

document.getElementById('generateButton').addEventListener('click', async () => {
  const type = selectedModule.classList.contains('visible') ? selectedModuleType.textContent.toLowerCase() : '';
  if (!type) {
    alert('Please select a module type.');
    return;
  }

  const clickout = document.getElementById('clickout').value.trim();
  const width = document.getElementById('width').value.trim();
  const height = document.getElementById('height').value.trim();
  const positionX = document.getElementById('positionX').value.trim();
  const positionY = document.getElementById('positionY').value.trim();
  const color = colorPicker.value;
  const content = document.getElementById('content').value.trim();

  const payload = {
    type,
    width,
    height,
    link: clickout,
    positionX,
    positionY,
  };

  if (type === 'background') {
    payload.color = color;
  } else if (type === 'typo') {
    payload.content = content;
  }

  try {
    const response = await fetch('http://localhost:5500/api/modules', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    const downloadUrl = `http://localhost:5500/api/modules/${data.id}/download`;

    const downloadResponse = await fetch(downloadUrl);
    if (!downloadResponse.ok) {
      throw new Error(`HTTP error while downloading! status: ${downloadResponse.status}`);
    }

    const blob = await downloadResponse.blob();
    const downloadLink = document.createElement('a');
    downloadLink.href = window.URL.createObjectURL(blob);
    downloadLink.download = 'module.zip';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
  } catch (error) {
    console.error('Error creating or downloading module:', error);
    alert('An error occurred while creating or downloading the module.');
  }
});