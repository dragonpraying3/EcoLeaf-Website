// Edit Profile Script
// - Toggle password visibility in the form
document.addEventListener('DOMContentLoaded', () => {

  // Reset Password Modal
  const resetBtn = document.getElementById('resetPwdBtn');
  const modal = document.getElementById('resetPwdModal');
  const cancelReset = document.getElementById('cancelReset');
  const confirmReset = document.getElementById('confirmReset');
  const newPassword = document.getElementById('newPassword');
  const confirmPassword = document.getElementById('confirmPassword');
  const pwdError = document.getElementById('pwdError');
  const passwordHidden = document.getElementById('passwordHidden');
  const pwdHint = document.getElementById('pwdHint');
  const confirmHint = document.getElementById('confirmHint');

  const openModal = () => {
    modal && (modal.classList.remove('hidden'));
  }
  //clear the text if close modal
  const closeModal = () => {
    modal && (modal.classList.add('hidden'));
    if (pwdError) {
      pwdError.style.display = 'none';
      pwdError.textContent = '';
    }
    if (newPassword) {
      newPassword.value = '';
    }
    if (confirmPassword) {
      confirmPassword.value = '';
    }
    if (newPassword) {
      newPassword.classList.remove('is-valid', 'is-invalid');
    }
    if (confirmPassword) {
      confirmPassword.classList.remove('is-valid', 'is-invalid');
    }
    if (pwdHint) {
      pwdHint.classList.remove('ok', 'err');
      pwdHint.textContent = 'At least 8 chars, include a number and a special symbol.';
    }
    if (confirmHint) {
      confirmHint.classList.remove('ok', 'err');
      confirmHint.textContent = '';
    }
  }
  if (resetBtn && modal) {
    resetBtn.addEventListener('click', () => {
      openModal();
      setTimeout(() => {
        newPassword && newPassword.focus()
      }, 0);
    })
  }

  //set password pattern same as register
  const pattern = /^(?=.*[0-9])(?=.*[!@#$%^&(),.?":{}|<>])[A-Za-z0-9!@#$%^&(),.?":{}|<>]{8,}$/;
  const validateNewPwd = () => {
    const val = (newPassword?.value || '').trim();

    if (!newPassword) {
      return false
    }
    const ok = pattern.test(val);
    newPassword.classList.toggle('is-valid', ok);
    newPassword.classList.toggle('is-invalid', !ok && val.length > 0);
    if (pwdHint) {
      if (ok) {
        pwdHint.textContent = 'Valid.';
        pwdHint.classList.add('ok');
        pwdHint.classList.remove('err');
      }
      else if (val.length > 0) {
        pwdHint.textContent = 'Use at least 8 chars with a number and a special symbol.';
        pwdHint.classList.add('err');
        pwdHint.classList.remove('ok');
      }
      else {
        pwdHint.textContent = 'At least 8 chars, include a number and a special symbol.';
        pwdHint.classList.remove('ok', 'err');
      }
    }
    return ok
  }
  const validateConfirmPwd = () => {
    const val = (confirmPassword?.value || '').trim();
    const base = (newPassword?.value || '').trim();
    if (!confirmPassword) return false
    const ok = val.length > 0 && base.length > 0 && val === base;
    confirmPassword.classList.toggle('is-valid', ok);
    confirmPassword.classList.toggle('is-invalid', !ok && val.length > 0);
    if (confirmHint) {
      if (ok) {
        confirmHint.textContent = 'Passwords match.';
        confirmHint.classList.add('ok');
        confirmHint.classList.remove('err');
      }
      else if (val.length > 0) {
        confirmHint.textContent = 'Passwords do not match.';
        confirmHint.classList.add('err');
        confirmHint.classList.remove('ok');
      }
      else {
        confirmHint.textContent = '';
        confirmHint.classList.remove('ok', 'err');
      }
    }
    return ok
  }
  if (newPassword) {
    newPassword.addEventListener('input', () => {
      validateNewPwd(); validateConfirmPwd();
    })
    newPassword.addEventListener('blur', validateNewPwd);
  }
  if (confirmPassword) {
    confirmPassword.addEventListener('input', validateConfirmPwd);
    confirmPassword.addEventListener('blur', validateConfirmPwd);
  }
  if (cancelReset) {
    cancelReset.addEventListener('click', closeModal);
  }
  if (confirmReset) {
    confirmReset.addEventListener('click', () => {
      const pwd = (newPassword?.value || '').trim();
      const rep = (confirmPassword?.value || '').trim();
      const okPattern = validateNewPwd();
      const okConfirm = validateConfirmPwd();
      if (!pwd || !rep) {
        pwdError.style.display = 'block';
        pwdError.textContent = 'Please fill in both fields.';
        return
      }
      if (!okPattern) {
        pwdError.style.display = 'block';
        pwdError.textContent = 'Password must be at least 8 chars with a number and a special symbol.';
        return
      }
      if (!okConfirm) {
        pwdError.style.display = 'block';
        pwdError.textContent = 'Passwords do not match.';
        return
      }
      if (passwordHidden) {
        passwordHidden.value = pwd
      }
      // Submit form immediately after confirming reset
      const form = document.getElementById('profileForm');
      if (form) {
        form.submit();
      }
      closeModal();
    })
  }
}) 