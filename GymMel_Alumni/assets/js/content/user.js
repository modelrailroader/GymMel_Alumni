import languageDE from 'datatables.net-plugins/i18n/de-DE.mjs';

import pdfmake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfmake.vfs = pdfFonts.pdfMake.vfs;

import * as JSZip from "jszip";
window.JSZip = JSZip;

import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-responsive-dt';

import { validatePassword } from '../utils/password'

export const handleCreateUser = () => {
    const passwordInput = document.getElementById('password');
    const helpTextPassword = document.getElementById('helpTextPassword');
    const submitButton = document.getElementById('submit');

    if (passwordInput) {
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
};

export const handleEditUser = () => {
    const newPassword = document.getElementById('newPassword');
    const passwordDiv = document.getElementById('passwordDiv');
    const confirmPasswordDiv = document.getElementById('confirmPasswordDiv');
    const submitButton = document.getElementById('submit');
    const helpTextConfirmPasswordInput = document.getElementById('helpTextConfirmPassword');
    const helpTextPasswordInput = document.getElementById('helpTextPassword');

    if (newPassword) {
        newPassword.addEventListener('change', function () {
            if (this.checked) {
                passwordDiv.style.display = 'block';
                confirmPasswordDiv.style.display = 'block';
                if (helpTextConfirmPasswordInput.textContent !== '' || helpTextPasswordInput.textContent !== '') {
                    submitButton.disabled = true;
                }
            } else {
                passwordDiv.style.display = 'none';
                confirmPasswordDiv.style.display = 'none';
                submitButton.disabled = false;
            }
        });

        const twofactor_active = document.getElementById('2fa');
        const twofactor_new = document.getElementById('new_2fa');

        if (twofactor_new) {
            twofactor_new.addEventListener('change', function () {
                if (this.checked) {
                    twofactor_active.checked = false;
                }
            });

            twofactor_active.addEventListener('change', function () {
                if (this.checked) {
                    twofactor_new.checked = false;
                }
            });
        }

        const confirmPasswordInput = document.getElementById('confirmPassword');
        const passwordInput = document.getElementById('password');

        passwordInput.addEventListener('keyup', function () {
            helpTextPasswordInput.textContent = validatePassword(passwordInput.value);
        });

        submitButton.addEventListener('click', function () {
            if (newPassword.checked) {
                if (validatePassword(passwordInput.value) !== '') {
                    event.preventDefault();
                }
                if (passwordInput.value !== confirmPasswordInput.value) {
                    helpTextConfirmPasswordInput.textContent = 'Die Passwörter stimmen nicht überein.';
                    event.preventDefault();
                }
            }

        });
    }
};

export const handleShowUsers = () => {
    const usersTable = document.getElementById('usersTable');
    if (usersTable) {
        const table = new DataTable(usersTable, {
            "language": languageDE,
            "dom": "Bflrtip",
            responsive: true,
            "buttons": [{
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                }]
        });
    }
};