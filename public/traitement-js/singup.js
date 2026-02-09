document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signupForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const signupBtn = document.getElementById('signupBtn');
    const toggle = document.getElementById('togglePassword');

    const nameError = document.getElementById('nameError');
    const emailError = document.getElementById('emailError');
    const phoneError = document.getElementById('phoneError');
    const passwordError = document.getElementById('passwordError');

    let emailCheckTimeout;

    // Validation du nom
    function validateName() {
        const name = nameInput.value.trim();
        if (name.length < 2) {
            showError(nameError, 'Le nom doit contenir au moins 2 caractères');
            return false;
        }
        showSuccess(nameError, 'Nom valide');
        return true;
    }

    // Validation du format email
    function validateEmailFormat() {
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError(emailError, 'Format d\'email invalide');
            return false;
        }
        return true;
    }

    // Vérification si l'email existe
    async function checkEmailExists() {
        const email = emailInput.value.trim();
        if (!validateEmailFormat()) return;

        try {
            const response = await fetch(`/api/check-email?email=${encodeURIComponent(email)}`);
            const data = await response.json();
            if (data.exists) {
                showError(emailError, 'Cet email existe déjà');
                return false;
            } else {
                showSuccess(emailError, 'Email disponible');
                return true;
            }
        } catch (error) {
            console.error('Erreur lors de la vérification de l\'email:', error);
            showError(emailError, 'Erreur de vérification');
            return false;
        }
    }

    // Validation du numéro malgache
    function validatePhone() {
        const phone = phoneInput.value.trim();
        // Regex pour numéros malgaches : +261XXXXXXXXX ou 0XXXXXXXXX
        const phoneRegex = /^(\+2613|03)[23378]\d{7}$/;
        if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
            showError(phoneError, 'Numéro malgache invalide (ex: +261321234567 ou 0321234567)');
            return false;
        }
        showSuccess(phoneError, 'Numéro valide');
        return true;
    }

    // Validation du mot de passe
    function validatePassword() {
        const password = passwordInput.value;
        
        // Vérifier la longueur minimale
        if (password.length < 8) {
            showError(passwordError, 'Le mot de passe doit contenir au moins 8 caractères');
            return false;
        }
        
        // Vérifier au moins une majuscule
        if (!/[A-Z]/.test(password)) {
            showError(passwordError, 'Le mot de passe doit contenir au moins une lettre majuscule');
            return false;
        }
        
        // Vérifier au moins un caractère spécial
        if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
            showError(passwordError, 'Le mot de passe doit contenir au moins un caractère spécial');
            return false;
        }
        
        showSuccess(passwordError, 'Mot de passe valide');
        return true;
    }

    // Fonctions utilitaires pour afficher les messages
    function showError(element, message) {
        element.textContent = message;
        element.className = 'text-danger small mt-1';
    }

    function showSuccess(element, message) {
        element.textContent = message;
        element.className = 'text-success small mt-1';
    }

    function clearMessage(element) {
        element.textContent = '';
    }

    // Événements
    nameInput.addEventListener('blur', validateName);
    nameInput.addEventListener('input', () => clearMessage(nameError));

    emailInput.addEventListener('blur', function() {
        if (validateEmailFormat()) {
            checkEmailExists();
        }
    });
    emailInput.addEventListener('input', function() {
        clearMessage(emailError);
        // Debounce la vérification d'existence
        clearTimeout(emailCheckTimeout);
        emailCheckTimeout = setTimeout(() => {
            if (validateEmailFormat()) {
                checkEmailExists();
            }
        }, 500);
    });

    phoneInput.addEventListener('blur', validatePhone);
    phoneInput.addEventListener('input', () => clearMessage(phoneError));

    passwordInput.addEventListener('blur', validatePassword);
    passwordInput.addEventListener('input', () => clearMessage(passwordError));

    // Toggle password visibility
    if (toggle) {
        toggle.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            toggle.innerHTML = type === 'password' 
                ? '<img src="/assets/icons/eye-fill.svg" alt="Show password" style="width:16px;height:16px;">' 
                : '<img src="/assets/icons/eye-slash-fill.svg" alt="Hide password" style="width:16px;height:16px;">';
            toggle.setAttribute('aria-pressed', String(type === 'text'));
        });
    }

    // Validation du formulaire avant soumission
    form.addEventListener('submit', function(e) {
        const isNameValid = validateName();
        const isEmailValid = validateEmailFormat();
        const isPhoneValid = validatePhone();
        const isPasswordValid = validatePassword();

        if (!isNameValid || !isEmailValid || !isPhoneValid || !isPasswordValid) {
            e.preventDefault();
            alert('Veuillez corriger les erreurs avant de soumettre le formulaire.');
            return false;
        }

        // Vérification finale de l'email
        e.preventDefault();
        checkEmailExists().then(emailAvailable => {
            if (emailAvailable) {
                form.submit();
            }
        });
    });
});
