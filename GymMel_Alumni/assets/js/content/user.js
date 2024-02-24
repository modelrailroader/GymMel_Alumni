/**
 * JavaScript functions for handling user management.
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

import {validatePassword} from '../utils/password'
import {createAlert, createToast} from "../utils/notifications";

import {Toast} from 'bootstrap';

export const handleCreateUser = () => {
    const createUserForm = document.getElementById('createUserForm');
    if (createUserForm) {
        const passwordInput = document.getElementById('password');
        const helpTextPassword = document.getElementById('helpTextPassword');
        const submitButton = document.getElementById('submit');

        passwordInput.addEventListener('keyup', function () {
            helpTextPassword.textContent = validatePassword(passwordInput.value);
        });

        const confirmPasswordInput = document.getElementById('confirmPassword');
        const helpTextConfirmPassword = document.getElementById('helpTextConfirmPassword');

        document.addEventListener('DOMContentLoaded', function () {
            submitButton.addEventListener('click', function () {
                const checks = [];
                if (validatePassword(passwordInput.value) !== '') {
                    checks.push(false);
                    event.preventDefault();
                }
                if (passwordInput.value !== confirmPasswordInput.value) {
                    helpTextConfirmPassword.textContent = 'Die Passwörter stimmen nicht überein.';
                    checks.push(false);
                    event.preventDefault();
                }
                if (!createUserForm.checkValidity()) {
                    createUserForm.reportValidity();
                    checks.push(false);
                    event.preventDefault();
                }
                if (checks.length === 0) {
                    const response = fetch('api_int.php?action=createUser', {
                        method: 'POST',
                        body: JSON.stringify({
                            username: document.getElementById('username').value,
                            password: document.getElementById('password').value,
                            email: document.getElementById('email').value
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
                            if (responseData.stored) {
                                document.getElementById('helpTextConfirmPassword').textContent = '';
                                document.getElementById('helpTextPassword').textContent = '';
                                createUserForm.reset();
                            }
                        })
                        .catch(error => {
                            console.error('Fehler bei der Fetch-Anfrage:', error);
                        });
                    event.preventDefault();
                }
            });
        });
    }
};

export const handleEditUser = () => {
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        const newPassword = document.getElementById('newPassword');
        const passwordDiv = document.getElementById('passwordDiv');
        const confirmPasswordDiv = document.getElementById('confirmPasswordDiv');
        const submitButton = document.getElementById('submit');
        const helpTextConfirmPasswordInput = document.getElementById('helpTextConfirmPassword');
        const helpTextPasswordInput = document.getElementById('helpTextPassword');

        // Set new password
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

            // Reset 2fa
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

            // Validate password
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const passwordInput = document.getElementById('password');

            passwordInput.addEventListener('keyup', function () {
                helpTextPasswordInput.textContent = validatePassword(passwordInput.value);
            });

            // Validate password and check if password and confirmPassword are equal if submit button is triggered
            submitButton.addEventListener('click', function () {
                if (newPassword.checked) {
                    if (validatePassword(passwordInput.value) !== '') {
                        event.preventDefault();
                    } else if (passwordInput.value !== confirmPasswordInput.value) {
                        helpTextConfirmPasswordInput.textContent = 'Die Passwörter stimmen nicht überein.';
                        event.preventDefault();
                    }
                }
                if (!editUserForm.checkValidity()) {
                    console.log("Yes");
                    editUserForm.reportValidity();
                    event.preventDefault();
                }
            });
        }
    }
};

export const handleShowUsers = () => {
    const usersTable = document.getElementById('usersTable');
    if (usersTable) {
        const table = new DataTable(usersTable, {
            "language": languageDE,
            responsive: true,
            layout: {
                top1Start: 'buttons',
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            },
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
        const delete_items = document.querySelectorAll('#item-delete');
        delete_items.forEach(function (item) {
            item.addEventListener('click', function (event) {
                const userConfirmation = confirm('Wollen Sie den Benutzer ' + item.getAttribute('data-name') + ' wirklich löschen?');
                if (userConfirmation) {
                    deleteUser(item, table);
                }
                event.preventDefault();
            });
        });
        // Add EventListener as well to buttons on further pages to show confirmation
        table.on('draw', function () {
            const delete_items = document.querySelectorAll('#item-delete');
            delete_items.forEach(function (item) {
                item.addEventListener('click', function (event) {
                    const userConfirmation = confirm('Wollen Sie den Benutzer ' + item.getAttribute('data-name') + ' wirklich löschen?');
                    if (userConfirmation) {
                        deleteUser(item, table);
                    }
                    event.preventDefault();
                });
            });
        });
    }
};

function deleteUser(item, table) {
    const userid = item.getAttribute('data-userid');
    const response = fetch('api_int.php?action=deleteUser', {
        method: 'POST',
        body: JSON.stringify({
            username_deleted: item.getAttribute('data-name'),
            userid: userid
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
            const toastElement = createToast(responseData.message, responseData.deleted ? 'success' : 'danger');
            document.getElementById('alert').insertAdjacentElement('beforeend', toastElement);
            const toast = new Toast(toastElement);
            toast.show();
            window.scrollTo(0,0);
            if (responseData.deleted) {
                table.row('#user' + userid).remove().draw();
            }
        })
        .catch(error => {
            console.error('Fehler bei der Fetch-Anfrage:', error);
        });
}