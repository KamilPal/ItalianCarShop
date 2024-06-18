function validateForm() {
    var name = document.getElementById("name").value;
    var surname = document.getElementById("surname").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    var errors = [];

    if (!name.match(/^[a-zA-Z]{3,}$/)) {
        errors.push("Imię powinno zawierać tylko litery i mieć co najmniej 3 znaki.");
    }

    if (!surname.match(/^[a-zA-Z]{3,}$/)) {
        errors.push("Nazwisko powinno zawierać tylko litery i mieć co najmniej 3 znaki.");
    }

    if (!email.match(/^\S+@\S+\.\S+$/)) {
        errors.push("Email powinien być w prawidłowym formacie.");
    }

    if (password && password.length < 6) {
        errors.push("Hasło powinno mieć co najmniej 6 znaków.");
    }

    if (password && password !== confirmPassword) {
        errors.push("Hasła nie są zgodne.");
    }

    if (errors.length > 0) {
        alert(errors.join("\n"));
        return false;
    }
    return true;
}
