import { validatePassword } from '../utils/password'

export const handleCreateUser = () => {
    const passwordInput = document.getElementById('password');
    const helpTextPassword = document.getElementById('helpTextPassword');
    const submitButton = document.getElementById('submit');

    passwordInput.addEventListener('keyup', function () {
        helpTextPassword.textContent = validatePassword(passwordInput.value);
    });

    const confirmPasswordInput = document.getElementById('confirmPassword');
    const helpTextConfirmPassword = document.getElementById('helpTextConfirmPassword');

    submitButton.addEventListener('click', function () {
        if (validatePassword(passwordInput.value) !== '') {
            event.preventDefault();
        }
        if (passwordInput.value !== confirmPasswordInput.value) {
            helpTextConfirmPassword.textContent = 'Die Passwörter stimmen nicht überein.';
            event.preventDefault();
        }
    });
}


