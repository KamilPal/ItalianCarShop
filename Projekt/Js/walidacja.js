function validateForm() {
    var name = document.getElementById("name").value;
    var surname = document.getElementById("surname").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    var country = document.getElementById("country").value;
    var city = document.getElementById("city").value;
    var address = document.getElementById("address").value;
    var payment = document.getElementById("payment").value;
    var errors = [];

    var polishChars = /^[a-zA-ZąęćńłóśżźĄĘĆŃŁÓŚŻŹ]{3,}$/;

    if (!name.match(polishChars)) {
        errors.push("Imię powinno zawierać tylko litery (w tym polskie) i mieć co najmniej 3 znaki.");
    }

    if (!surname.match(polishChars)) {
        errors.push("Nazwisko powinno zawierać tylko litery (w tym polskie) i mieć co najmniej 3 znaki.");
    }

    if (!email.match(/^\S+@\S+\.\S+$/)) {
        errors.push("Email powinien być w prawidłowym formacie.");
    }

    if (country.length < 3) {
        errors.push("Kraj powinien mieć co najmniej 3 znaki.");
    }

    if (city.length < 2) {
        errors.push("Miasto powinno mieć co najmniej 2 znaki.");
    }

    if (address.length < 5) {
        errors.push("Adres powinien mieć co najmniej 5 znaków.");
    }

    if (!payment) {
        errors.push("Proszę wybrać metodę płatności.");
    }

    if (password && password.length < 4) {
        errors.push("Hasło powinno mieć co najmniej 4 znaki.");
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
