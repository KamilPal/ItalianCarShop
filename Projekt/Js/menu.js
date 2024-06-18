document.getElementById('login-trigger').addEventListener('click', function(e) {
    e.preventDefault(); 
    var dropdown = document.getElementById('login-dropdown');
    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
});

document.addEventListener('click', function(e) {
    var loginIcon = document.getElementById('login-trigger');
    var dropdown = document.getElementById('login-dropdown');
    if (!loginIcon.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
