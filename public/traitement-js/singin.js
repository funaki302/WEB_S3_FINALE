// Mark script as loaded (useful for console debugging)
window.__singinScriptLoaded = true;

function __singinInit() {
	window.__singinLoaded = true;

	const form = document.querySelector('form[role="form"]');
	const email = document.getElementById('email');
	const password = document.getElementById('password');
	const emailError = document.getElementById('emailError');
	const signInBtn = document.getElementById('signInBtn');
	const toggle = document.getElementById('togglePassword');

	if (!form || !email || !password) return;

	const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	function showEmailError(msg) {
		if (emailError) {
			emailError.textContent = msg;
			emailError.style.display = 'block';
		}
		email.classList.add('is-invalid');
	}

	function hideEmailError() {
		if (emailError) {
			emailError.textContent = '';
			emailError.style.display = 'none';
		}
		email.classList.remove('is-invalid');
	}

	function validateEmail() {
		const val = (email.value || '').trim();
		if (val === '') {
			showEmailError('Le champ email est requis.');
			return false;
		}

		if (!emailRegex.test(val)) {
			showEmailError('Entrez une adresse email valide.');
			return false;
		}
		hideEmailError();
		return true;
	}

	// Expose for quick console debugging (available immediately after init)
	window.validateEmail = validateEmail;

	email.addEventListener('input', validateEmail);
	email.addEventListener('blur', validateEmail);

	// Main behavior: validate on submit; prevent submission if invalid
	form.addEventListener('submit', function (e) {
		if (!validateEmail()) {
			e.preventDefault();
			email.focus();
		}
	});

	// Fallback: if button is type=button somewhere, still submit programmatically
	if (signInBtn) {
		signInBtn.addEventListener('click', function () {
			if (validateEmail()) {
				form.submit();
			} else {
				email.focus();
			}
		});
	}

	if (toggle) {
		toggle.addEventListener('click', function () {
			const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
			password.setAttribute('type', type);
			toggle.textContent = type === 'password' ? '👁' : '🙈';
			toggle.setAttribute('aria-pressed', String(type === 'text'));
		});
	}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', __singinInit);
} else {
	__singinInit();
}

