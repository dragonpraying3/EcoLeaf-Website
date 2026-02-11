let selectedEventId = null;

function openOtpModal(eventId){
  selectedEventId = eventId;

  const hid = document.getElementById('otpEventId');
  if (hid) hid.value = eventId;

  const modal = document.getElementById('otpModal');
  if (!modal) return;

  modal.classList.add('show');
  modal.setAttribute('aria-hidden','false');

  const inputs = modal.querySelectorAll('.otp-inputs input');
  inputs.forEach(i => i.value = '');
  if (inputs.length) inputs[0].focus();
}

function closeOtpModal(){
  const modal = document.getElementById('otpModal');
  if (!modal) return;
  modal.classList.remove('show');
  modal.setAttribute('aria-hidden','true');
}

// close modal when clicking outside
document.addEventListener('click', e => {
  const modal = document.getElementById('otpModal');
  if (modal && e.target === modal) closeOtpModal();
});

// auto move cursor + backspace
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.otp-inputs input').forEach((input, index, inputs) => {

    input.addEventListener('input', () => {
      input.value = input.value.replace(/\D/g,'');
      if (input.value && index < inputs.length - 1) {
        inputs[index + 1].focus();
      }
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && index > 0) {
        inputs[index - 1].focus();
      }
    });

  });

  const form = document.getElementById('otpForm');
  if (!form) return;

  form.addEventListener('submit', (e) => {
    const inputs = form.querySelectorAll('.otp-inputs input');
    const otp = Array.from(inputs).map(i => i.value.trim()).join('');

    if (otp.length !== inputs.length) {
      e.preventDefault();
      alert("Please enter full OTP.");
      return;
    }

    let hidden = form.querySelector('input[name="otp"]');
    if (!hidden) {
      hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = 'otp';
      form.appendChild(hidden);
    }
    hidden.value = otp;
  });
});
