function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    let strength = 0;
    let text = '';

    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[!@#$%^&*]/.test(password)) strength++;

    strengthFill.classList.remove('weak', 'fair', 'good', 'strong');

    if (strength === 0) {
        strengthFill.classList.add('weak');
        text = 'Very weak';
    } else if (strength === 1) {
        strengthFill.classList.add('weak');
        text = 'Weak';
    } else if (strength === 2) {
        strengthFill.classList.add('fair');
        text = 'Fair';
    } else if (strength === 3) {
        strengthFill.classList.add('good');
        text = 'Good';
    } else {
        strengthFill.classList.add('strong');
        text = 'Strong';
    }
    strengthText.textContent = text;
}

function showClientErrors(errors) {
    const container = document.getElementById('clientErrors');
    const list = document.getElementById('clientErrorList');
    list.innerHTML = '';
    errors.forEach(function(err) {
        const div = document.createElement('div');
        div.className = 'error-item';
        div.textContent = err;
        list.appendChild(div);
    });
    container.style.display = errors.length ? 'flex' : 'none';
}

function collectErrors() {
    const errors = [];
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const agreeTerms = document.getElementById('agreeTerms').checked;

    if (!firstName) errors.push('First name is required');
    if (!lastName) errors.push('Last name is required');

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        errors.push('Email is required');
    } else if (!emailRegex.test(email)) {
        errors.push('Invalid email format');
    }

    const phoneRegex = /^(?=(?:[^0-9]*[0-9]){10})[0-9\-\+\s\(\)]{10,20}$/;
    if (!phone) {
        errors.push('Phone number is required');
    } else if (!phoneRegex.test(phone)) {
        errors.push('Invalid phone number (must contain at least 10 digits)');
    }

    if (!password) {
        errors.push('Password is required');
    } else {
        if (password.length < 8) errors.push('Password must be at least 8 characters');
        if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
            errors.push('Password must contain at least one uppercase letter and one number');
        }
    }

    if (password !== confirmPassword) {
        errors.push('Passwords do not match');
    }

    if (!agreeTerms) {
        errors.push('You must agree to the Terms of Service');
    }

    return errors;
}

function bindLiveValidation() {
    ['firstName','lastName','email','phone','password','confirmPassword'].forEach(function(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('input', function() {
            if (id === 'password') checkPasswordStrength();
            showClientErrors(collectErrors());
        });
    });

    const agree = document.getElementById('agreeTerms');
    if (agree) {
        agree.addEventListener('change', function() {
            showClientErrors(collectErrors());
        });
    }
}

bindLiveValidation();
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const errors = collectErrors();
    if (errors.length) {
        e.preventDefault();
        showClientErrors(errors);
    } else {
        showClientErrors([]);
    }
});
