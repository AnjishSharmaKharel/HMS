(function() {
    const form = document.getElementById('addCustomerForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        let valid = true;
        const name = document.getElementById('new_fullname');
        const nameRegex = /^[a-zA-Z\s\-.']{2,}$/;
        if (!nameRegex.test(name.value)) {
            alert("Full name must contain valid letters and be at least 2 characters.");
            valid = false;
        }
        const email = document.getElementById('new_email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            alert("Please enter a valid email address.");
            valid = false;
        }
        const phone = document.getElementById('new_phone');
        const phoneRegex = /^[0-9\-\+\s\(\)]{10,}$/;
        if (phone.value && !phoneRegex.test(phone.value)) {
            alert("Please enter a valid phone number (digits, spaces, dashes).");
            valid = false;
        }
        const password = document.getElementById('new_password');
        if (password.value.length < 8) {
            alert("Password must be at least 8 characters.");
            valid = false;
        }
        if (!valid) {
            e.preventDefault();
        }
    });
})();

const editForm = document.getElementById('editCustomerForm');
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        let valid = true;
        const name = document.getElementById('customer_fullname');
        const nameRegex = /^[a-zA-Z\s\-.']{2,}$/;
        if (!nameRegex.test(name.value)) {
            alert("Full name must contain valid letters and be at least 2 characters.");
            valid = false;
        }
        const email = document.getElementById('customer_email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            alert("Please enter a valid email address.");
            valid = false;
        }
        const phone = document.getElementById('customer_phone');
        const phoneRegex = /^[0-9\-\+\s\(\)]{10,}$/;
        if (phone.value && !phoneRegex.test(phone.value)) {
            alert("Please enter a valid phone number (digits, spaces, dashes).");
            valid = false;
        }
        if (!valid) {
            e.preventDefault();
        }
    });
}
