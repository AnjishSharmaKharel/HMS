function switchRole(role, el) {
    document.querySelectorAll('.role-tab').forEach(tab => tab.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.form-section').forEach(section => section.classList.remove('active'));

    if (role === 'admin') {
        document.getElementById('adminSection').classList.add('active');
        pageTitle.innerText = "Admin Login";
        pageSubtitle.innerText = "Access admin panel";
    } else if (role === 'staff') {
        document.getElementById('staffSection').classList.add('active');
        pageTitle.innerText = "Staff Login";
        pageSubtitle.innerText = "Access staff panel";
    } else {
        document.getElementById('customerSection').classList.add('active');
        pageTitle.innerText = "Customer Login";
        pageSubtitle.innerText = "Book your stay";
    }
}
