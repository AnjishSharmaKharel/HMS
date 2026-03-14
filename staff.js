document.querySelector('form').addEventListener('submit', function(e) {
    let valid = true;
    
    const username = document.getElementById('username');
    const usernameRegex = /^[a-zA-Z0-9_]{3,}$/;
    if (!usernameRegex.test(username.value)) {
        alert("Username must be at least 3 characters and alphanumeric.");
        valid = false;
    }

    const name = document.getElementById('name');
    const nameRegex = /^[a-zA-Z\s\-.']{2,}$/;
    if (!nameRegex.test(name.value)) {
        alert("Name must contain valid letters and be at least 2 characters.");
        valid = false;
    }

    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value)) {
        alert("Please enter a valid email address.");
        valid = false;
    }

    const password = document.getElementById('password');
    if (password.value.length < 8) {
        document.getElementById('passwordError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('passwordError').style.display = 'none';
    }

    if (!valid) {
        e.preventDefault();
    }
});

document.querySelectorAll('form').forEach(function(form) {
    if (form.querySelector('input[name="update_staff"]')) {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const username = document.getElementById('edit_username');
            const usernameRegex = /^[a-zA-Z0-9_]{3,}$/;
            if (!usernameRegex.test(username.value)) {
                alert('Username must be at least 3 characters and alphanumeric.');
                valid = false;
            }
            const name = document.getElementById('edit_name');
            const nameRegex = /^[a-zA-Z\s\-\.']{2,}$/;
            if (!nameRegex.test(name.value)) {
                alert('Name must contain valid letters and be at least 2 characters.');
                valid = false;
            }
            const email = document.getElementById('edit_email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                alert('Please enter a valid email address.');
                valid = false;
            }
            const password = document.getElementById('edit_password');
            if (password.value && password.value.length < 8) {
                alert('Password must be at least 8 characters.');
                valid = false;
            }
            if (!valid) {
                e.preventDefault();
            }
        });
    }
});
