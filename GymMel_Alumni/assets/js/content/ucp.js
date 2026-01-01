/**
 * JavaScript functions for handling the user control panel.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2026 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2024-01-01
 */

import {Modal, Toast} from 'bootstrap';
import { validatePassword } from '../utils/password.js';
import {createToast} from "../utils/notifications";

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
            const checks = [];
            if (validatePassword(passwordInput.value) !== '') {
                checks.push(false);
            }
            if (passwordInput.value !== confirmPasswordInput.value) {
                helpTextConfirmPassword.textContent = 'Die Passwörter stimmen nicht überein.';
                checks.push(false);
            }
            if (!ucpForm.checkValidity()) {
                ucpForm.reportValidity();
                checks.push(false);
            }
            if (checks.length === 0) {
                const response = fetch('api_int.php?action=saveUcpData', {
                    method: 'POST',
                    body: JSON.stringify({
                        username: document.getElementById('username').value,
                        password: document.getElementById('password').value,
                        email: document.getElementById('email').value,
                        twofactor: document.getElementById('2fa').checked
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        } else {
                            throw new Error(`Fehler bei der Anfrage: ${response.status} ${response.statusText}`);
                        }
                    })
                    .then(responseData => {
                        const toastElement = createToast(responseData.message, responseData.stored ? 'success' : 'danger');
                        document.getElementById('alert').insertAdjacentElement('beforeend', toastElement);
                        const toast = new Toast(toastElement);
                        toast.show();
                        window.scrollTo(0, 0);
                    })
                    .catch(error => {
                        console.error('Fehler bei der Fetch-Anfrage:', error);
                    });
            }
            event.preventDefault();
        });
    }
};


