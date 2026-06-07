document.addEventListener('click', event => {
  document.querySelectorAll('.header-menu[open]').forEach(menu => {
    if (!menu.contains(event.target)) {
      menu.removeAttribute('open');
    }
  });
});

document.querySelectorAll('[data-dual-range]').forEach(range => {
  const minInput = range.querySelector('input[name="min_price"]');
  const maxInput = range.querySelector('input[name="max_price"]');
  const minLabel = range.querySelector('[data-min-label]');
  const maxLabel = range.querySelector('[data-max-label]');

  const sync = changed => {
    let min = Number(minInput.value);
    let max = Number(maxInput.value);

    if (min > max) {
      if (changed === minInput) {
        max = min;
        maxInput.value = max;
      } else {
        min = max;
        minInput.value = min;
      }
    }

    minLabel.textContent = min.toLocaleString();
    maxLabel.textContent = max.toLocaleString();
  };

  minInput.addEventListener('input', () => sync(minInput));
  maxInput.addEventListener('input', () => sync(maxInput));
  sync();
});

document.querySelectorAll('[data-confirm-open]').forEach(button => {
  button.addEventListener('click', () => {
    const modal = document.getElementById(button.dataset.confirmOpen);
    if (modal) modal.hidden = false;
  });
});

document.addEventListener('click', event => {
  if (event.target.matches('[data-confirm-close]')) {
    event.target.closest('.modal-backdrop').hidden = true;
  }
});

const imageTransfers = new WeakMap();

document.querySelectorAll('[data-image-cropper]').forEach(wrapper => {
  const input = wrapper.querySelector('[data-file-input]');
  const preview = wrapper.querySelector('[data-preview]');

  input.addEventListener('change', async () => {
    const file = input.files[0];
    if (!file) return;

    const cropped = await openImageCropper(file, {
      aspect: Number(wrapper.dataset.aspect || 1),
      maxWidth: Number(wrapper.dataset.maxWidth || 720),
      maxHeight: Number(wrapper.dataset.maxHeight || 720),
    });

    if (!cropped) {
      input.value = '';
      return;
    }

    const transfer = new DataTransfer();
    transfer.items.add(cropped);
    input.files = transfer.files;
    preview.src = URL.createObjectURL(cropped);
  });
});

document.querySelectorAll('[data-image-repeater]').forEach(wrapper => {
  const input = wrapper.querySelector('[data-file-input]');
  const list = wrapper.querySelector('[data-image-list]');
  const maxImages = Number(wrapper.dataset.maxImages || 20);
  imageTransfers.set(input, new DataTransfer());

  input.addEventListener('change', async () => {
    const files = [...input.files];
    const transfer = imageTransfers.get(input);

    for (const file of files) {
      if (list.children.length >= maxImages) {
        alert(`コンテンツ画像は最大${maxImages}枚まで追加できます。`);
        break;
      }

      const cropped = await openImageCropper(file, {
        aspect: 16 / 10,
        maxWidth: 1280,
        maxHeight: 900,
      });

      if (!cropped) continue;

      transfer.items.add(cropped);
      const record = document.createElement('div');
      record.className = 'image-record';
      record.innerHTML = `<img src="${URL.createObjectURL(cropped)}" alt=""><span>追加画像 ${list.children.length + 1}</span>`;
      list.appendChild(record);
    }

    input.files = transfer.files;
  });
});

function openImageCropper(file, options) {
  return new Promise(resolve => {
    const url = URL.createObjectURL(file);
    const modal = document.createElement('div');
    modal.className = 'modal-backdrop crop-modal';
    modal.innerHTML = `
      <div class="modal-card modal-card--crop" role="dialog" aria-modal="true" aria-label="画像クロップ">
        <div class="crop-stage">
          <img src="${url}" alt="">
          <div class="crop-box" tabindex="0"></div>
        </div>
        <div class="form-actions crop-actions">
          <button class="button button--primary" type="button" data-crop-apply>クロップ</button>
          <button class="button button--ghost" type="button" data-crop-cancel>戻る</button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

    const img = modal.querySelector('img');
    const box = modal.querySelector('.crop-box');
    const cleanup = value => {
      URL.revokeObjectURL(url);
      modal.remove();
      resolve(value);
    };

    img.addEventListener('load', () => {
      const rect = img.getBoundingClientRect();
      const aspect = options.aspect || rect.width / rect.height;
      let boxWidth = rect.width * 0.72;
      let boxHeight = boxWidth / aspect;

      if (boxHeight > rect.height * 0.72) {
        boxHeight = rect.height * 0.72;
        boxWidth = boxHeight * aspect;
      }

      box.style.width = `${boxWidth}px`;
      box.style.height = `${boxHeight}px`;
      box.style.left = `${(rect.width - boxWidth) / 2}px`;
      box.style.top = `${(rect.height - boxHeight) / 2}px`;
    });

    let dragging = false;
    let startX = 0;
    let startY = 0;
    let startLeft = 0;
    let startTop = 0;

    box.addEventListener('pointerdown', event => {
      dragging = true;
      startX = event.clientX;
      startY = event.clientY;
      startLeft = parseFloat(box.style.left || 0);
      startTop = parseFloat(box.style.top || 0);
      box.setPointerCapture(event.pointerId);
    });

    box.addEventListener('pointermove', event => {
      if (!dragging) return;
      const imgRect = img.getBoundingClientRect();
      const boxRect = box.getBoundingClientRect();
      const nextLeft = Math.max(0, Math.min(startLeft + event.clientX - startX, imgRect.width - boxRect.width));
      const nextTop = Math.max(0, Math.min(startTop + event.clientY - startY, imgRect.height - boxRect.height));
      box.style.left = `${nextLeft}px`;
      box.style.top = `${nextTop}px`;
    });

    box.addEventListener('pointerup', () => {
      dragging = false;
    });

    modal.querySelector('[data-crop-cancel]').addEventListener('click', () => cleanup(null));
    modal.querySelector('[data-crop-apply]').addEventListener('click', () => {
      const imgRect = img.getBoundingClientRect();
      const boxRect = box.getBoundingClientRect();
      const scaleX = img.naturalWidth / imgRect.width;
      const scaleY = img.naturalHeight / imgRect.height;
      const sx = (boxRect.left - imgRect.left) * scaleX;
      const sy = (boxRect.top - imgRect.top) * scaleY;
      const sw = boxRect.width * scaleX;
      const sh = boxRect.height * scaleY;
      const ratio = Math.min(1, options.maxWidth / sw, options.maxHeight / sh);
      const canvas = document.createElement('canvas');
      canvas.width = Math.round(sw * ratio);
      canvas.height = Math.round(sh * ratio);
      canvas.getContext('2d').drawImage(img, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height);
      canvas.toBlob(blob => {
        cleanup(new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' }));
      }, 'image/jpeg', 0.86);
    });
  });
}
