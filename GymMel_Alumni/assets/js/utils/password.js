export const validatePassword = (password) => {
    if (password.length < 8) {
        return 'Dein Passwort muss mindestens 8 Zeichen enthalten.';
    } else if (!/[A-Z]/.test(password)) {
        return 'Dein Passwort muss Groß- und Kleinbuchstaben enthalten.';
    } else if (!/[0-9]/.test(password)) {
        return 'Dein Passwort muss mindestens 1 Ziffer enthalten.';
    } else if (!/[^A-Za-z0-9]/.test(password)) {
        return 'Dein Passwort muss mindestens 1 Sonderzeichen enthalten.';
    } else {
        return '';
    }
}


