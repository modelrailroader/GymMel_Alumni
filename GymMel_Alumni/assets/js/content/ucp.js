import { Modal } from 'bootstrap';
import { validatePassword } from '../utils/password.js';

export const handleUcp = () => {
    const ucpForm = document.getElementById('ucpForm');
    if (ucpForm) {
        const passwordInput = document.getElementById('password');
        const helpTextPassword = document.getElementById('helpTextPassword');
        const submitButton = document.getElementById('submit');

        passwordInput.addEventListener('keyup', function () {
            helpTextPassword.textContent = validatePassword(passwordInput.value);
        });

        const confirmPasswordInput = document.getElementById('confirmPassword');
        const helpTextConfirmPassword = document.getElementById('helpTextConfirmPassword');

        const twofactorConfigButton = document.getElementById('twofactorConfigButton');

        twofactorConfigButton.addEventListener('click', function () {
            const twofactorConfig = new Modal(document.getElementById('twofactorConfig'));
            twofactorConfig.show();
        });

        const divTwofactorConfig = document.getElementById('divTwofactorConfig');
        const twofactorActive = document.getElementById('2fa');

        twofactorActive.addEventListener('change', function () {
            if (this.checked) {
                divTwofactorConfig.style.display = 'block';
            } else {
                divTwofactorConfig.style.display = 'none';
            }
        });

        if (twofactorActive.checked) {
            divTwofactorConfig.style.display = 'block';
        } else {
            divTwofactorConfig.style.display = 'none';
        }

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
};


