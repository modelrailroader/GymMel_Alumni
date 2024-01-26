/**
 * JavaScript functions for validating passwords.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2024 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2024-01-01
 */

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

export const handlePasswordToggles = () => {
    const toggles = document.querySelectorAll('#togglePassword');
    toggles.forEach(function (toggle) {
        toggle.addEventListener('click', function() {
            console.log('click');
            const passwordInput = document.getElementById(toggle.getAttribute('data-input'));
            const icon = document.getElementById(toggle.getAttribute('data-icon'));
            if (passwordInput.getAttribute('type') === 'password') {
                passwordInput.setAttribute('type', 'text');
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
            else {
                passwordInput.setAttribute('type', 'password');
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        })
    })
}


