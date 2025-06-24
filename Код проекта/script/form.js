document.addEventListener('DOMContentLoaded', function() {
  const phoneInput = document.getElementById('phone');
  const phoneMask = new IMask(phoneInput, {
      mask: '+{7} (000) 000-00-00',
      lazy: false,
      placeholderChar: '_'
  });

  const form = document.querySelector('.form-block__form');
  const nameInput = document.getElementById('name');
  const commentInput = document.getElementById('comment');
  const submitButton = document.querySelector('.form-block__button');
  const originalText = submitButton.textContent;
  
  form.addEventListener('submit', async function(e) {
      e.preventDefault();
      resetErrors();
      
      if (!validateForm()) return;
      
      submitButton.disabled = true;
      startLoadingAnimation();
      
      try {
          const response = await fetch('submit.php', {
              method: 'POST',
              body: new FormData(form)
          });
          
          const result = await response.json();
          
          if (result.error) {
              showError(nameInput, result.error);
          } else {
              showSuccessMessage();
              form.reset();
              phoneMask.updateValue();
          }
      } catch (error) {
          showError(nameInput, error);
      } finally {
          stopLoadingAnimation();
      }
  });

  function validateForm() {
      let isValid = true;
      
      if (nameInput.value.trim().length < 2) {
          showError(nameInput, 'Имя слишком короткое');
          isValid = false;
      } else if (nameInput.value.trim().length > 20) {
          showError(nameInput, 'Имя слишком длинное');
          isValid = false;
      }
      
      if (!phoneMask.masked.isComplete) {
          showError(phoneInput, 'Введите полный номер телефона');
          isValid = false;
      }
      
      if (commentInput.value.trim().length < 10) {
          showError(commentInput, 'Сообщение слишком короткое');
          isValid = false;
      } else if (commentInput.value.trim().length > 500) {
          showError(commentInput, 'Сообщение слишком длинное');
          isValid = false;
      }
      
      return isValid;
  }

  function startLoadingAnimation() {
      submitButton.innerHTML = `
          <span style="display:inline-block; animation: spin 1s linear infinite">↻</span> Отправка
      `;
  }

  function stopLoadingAnimation() {
      submitButton.textContent = originalText;
      submitButton.disabled = false;
  }

  function showSuccessMessage() {
      submitButton.textContent = '✓ Отправлено';
      submitButton.style.backgroundColor = '#25D366';
      
      setTimeout(() => {
          submitButton.textContent = originalText;
          submitButton.style.backgroundColor = '#F83D3D';
          
          const successMessage = document.createElement('div');
          successMessage.textContent = 'Спасибо! Ваше сообщение отправлено.';
          successMessage.style.color = '#25D366';
          successMessage.style.marginTop = '20px';
          successMessage.style.fontSize = '24px';
          successMessage.style.textAlign = 'center';
          successMessage.style.animation = 'fadeIn 0.5s ease-out';
          
          form.appendChild(successMessage);
          
          setTimeout(() => {
              successMessage.style.animation = 'fadeOut 0.5s ease-out';
              setTimeout(() => successMessage.remove(), 500);
          }, 3000);
      }, 1500);
  }

  function showError(input, message) {
      const errorElement = document.createElement('div');
      errorElement.className = 'error-message';
      errorElement.textContent = message;
      errorElement.style.color = '#F83D3D';
      errorElement.style.marginTop = '5px';
      errorElement.style.fontSize = '16px';
      errorElement.style.fontFamily = 'AR One Sans, sans-serif';
      
      input.style.borderColor = '#F83D3D';
      input.style.boxShadow = '0 0 0 2px rgba(248, 61, 61, 0.3)';
      
      input.parentNode.appendChild(errorElement);
      
      input.animate([
          { transform: 'translateX(0)' },
          { transform: 'translateX(-5px)' },
          { transform: 'translateX(5px)' },
          { transform: 'translateX(0)' }
      ], {
          duration: 300,
          iterations: 3
      });
  }

  function resetErrors() {
      document.querySelectorAll('.error-message').forEach(el => el.remove());
      
      [nameInput, phoneInput, commentInput].forEach(input => {
          input.style.borderColor = 'rgba(255, 255, 255, 0.3)';
          input.style.boxShadow = 'none';
      });
  }

  const style = document.createElement('style');
  style.textContent = `
      @keyframes spin {
          from { transform: rotate(0deg); }
          to { transform: rotate(360deg); }
      }
      @keyframes fadeIn {
          from { opacity: 0; transform: translateY(10px); }
          to { opacity: 1; transform: translateY(0); }
      }
      @keyframes fadeOut {
          from { opacity: 1; transform: translateY(0); }
          to { opacity: 0; transform: translateY(-10px); }
      }
  `;
  document.head.appendChild(style);
});