/**
 * JavaScript functions for handling the network data.
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

import languageDE from 'datatables.net-plugins/i18n/de-DE.mjs';

import pdfmake from "pdfmake/build/pdfmake.js";
import pdfFonts from "pdfmake/build/vfs_fonts.js";

pdfmake.addVirtualFileSystem(pdfFonts);

import * as JSZip from "jszip";

window.JSZip = JSZip;

import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-responsive-dt';
import 'datatables.net-buttons/js/buttons.colVis.mjs';

import {Dropdown, Modal, Toast} from 'bootstrap';
import {createToast} from "../utils/notifications";

export const handleAddData = () => {
    const formAddData = document.getElementById('formAddData');
    if (formAddData) {

        // Show success alert if data is successfully deleted
        document.addEventListener('DOMContentLoaded', function () {
            const message = localStorage.getItem('message');
            const messageType = localStorage.getItem('messageType');
            console.log(message);
            console.log(messageType);
            if (message != null && messageType != null) {
                const alert = document.getElementById('alert');
                alert.classList.add(messageType);
                alert.innerText = message;
                alert.style.display = 'block';
                window.scrollTo(0, 0);
                localStorage.removeItem('message');
                localStorage.removeItem('messageType');
            }
        });
    }
};

export const handleShowData = () => {
    const alumniTable = document.getElementById('alumniTable');
    if (alumniTable) {
        const table = new DataTable(alumniTable, {
            "language": languageDE,
            responsive: true,
            layout: {
                top1Start: 'buttons',
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            },
            "buttons": [
                {
                    extend: 'colvis',
                    text: 'Spalten ein-/ausblenden',
                    columns: ':not(.noVis)'
                },
                {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }]
        });
        // Add EventListener as well to buttons on further pages to show confirmation
        table.on('draw', function () {
            const delete_items = document.querySelectorAll('#item-delete');
            delete_items.forEach(function (item) {
                item.addEventListener('click', function (event) {
                    const userConfirmation = confirm('Wollen Sie den Eintrag ' + item.getAttribute('data-name') + ' wirklich löschen?');
                    if (userConfirmation) {
                        deleteAlumni(item, table);
                    }
                    event.preventDefault();
                });
            });
        });
        table.draw();
    }

    const message = localStorage.getItem('message');
    const messageLocation = localStorage.getItem('messageLocation');
    if (message && messageLocation === 'showData') {
        const toastElement = createToast(message, 'success');
        document.getElementById('alert').insertAdjacentElement('beforeend', toastElement);
        const toast = new Toast(toastElement);
        toast.show();
        window.scrollTo(0, 0);
        localStorage.removeItem('message');
        localStorage.removeItem('messageLocation');
    }
};

export const handleEditAlumni = () => {
    const editAlumniForm = document.getElementById('editAlumniForm');
    if (editAlumniForm) {
        const submitButton = document.getElementById('submit');
        submitButton.addEventListener('click', function (event) {
            if (editAlumniForm.checkValidity()) {
                const response = fetch('api_int.php?action=editAlumni', {
                    method: 'POST',
                    body: JSON.stringify({
                        name: document.getElementById('name').value,
                        email: document.getElementById('email').value,
                        birthday: document.getElementById('birthday').value,
                        graduation_year: document.getElementById('graduation_year').value,
                        studies: document.getElementById('studies').value,
                        job: document.getElementById('job').value,
                        company: document.getElementById('company').value,
                        transfer_privacy: document.getElementById('transfer_privacy').checked,
                        id: document.getElementById('id').value
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
                        if (!responseData.stored) {
                            const toastElement = createToast(responseData.message, 'danger');
                            document.getElementById('alert').insertAdjacentElement('beforeend', toastElement);
                            const toast = new Toast(toastElement);
                            toast.show();
                            window.scrollTo(0, 0);
                        } else {
                            // Set localStorage to show success toast on overview-page
                            localStorage.setItem('message', responseData.message);
                            localStorage.setItem('messageLocation', 'showData');
                            window.location.href = 'showData.php';
                        }
                    })
                    .catch(error => {
                        console.error('Fehler bei der Fetch-Anfrage:', error);
                    });
            }
            else {
                editAlumniForm.reportValidity();
            }
            event.preventDefault();
        });
    }
};

export const handleChangeData = () => {
    const changeDataForm = document.getElementById('changeDataForm');
    if (changeDataForm) {
        const submitButton = document.getElementById('submit');
        submitButton.addEventListener('click', function (event) {
            event.preventDefault();
            if (changeDataForm.checkValidity()) {
                const response = fetch('api.php?action=editAlumni', {
                    method: 'POST',
                    body: JSON.stringify({
                        name: document.getElementById('name').value,
                        email: document.getElementById('email').value,
                        birthday: document.getElementById('birthday').value,
                        graduation_year: document.getElementById('graduation_year').value,
                        studies: document.getElementById('studies').value,
                        job: document.getElementById('job').value,
                        company: document.getElementById('company').value,
                        transfer_privacy: document.getElementById('transfer-privacy').checked,
                        id: document.getElementById('id').value
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
                        const alert = document.getElementById('alert');
                        alert.classList.add(responseData.stored ? 'alert-success' : 'alert-danger');
                        alert.innerText = responseData.message;
                        alert.style.display = 'block';
                        window.scrollTo(0, 0);
                    })
                    .catch(error => {
                        console.error('Fehler bei der Fetch-Anfrage:', error);
                    });
            }
            else {
                changeDataForm.reportValidity();
            }
        });

        // Show confirmation dialogue if user requests deletion of data
        const buttonRequestDeleteData = document.getElementById('buttonRequestDeleteData');
        if (buttonRequestDeleteData) {
            buttonRequestDeleteData.addEventListener('click', function (event) {
                event.preventDefault();
                const modalDeleteData = new Modal(document.getElementById('deleteDataModal'));
                modalDeleteData.show();
            });
        }

        const buttonDeleteData = document.getElementById('buttonDeleteData');
        if (buttonDeleteData) {
            buttonDeleteData.addEventListener('click', function (event) {
                const response = fetch('api.php?action=deleteAlumni', {
                    method: 'POST',
                    body: JSON.stringify({
                        id: document.getElementById('id').value,
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
                        if (responseData.success) {
                            localStorage.setItem('message', responseData.message);
                            localStorage.setItem('messageType', 'alert-success');
                            window.location.href = 'index.php';
                        } else {
                            const alert = document.getElementById('alert');
                            alert.classList.add('alert-danger');
                            alert.innerText = responseData.message;
                            alert.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Fehler bei der Fetch-Anfrage:', error);
                    });
            });
        }
    }

    const emailDataRequestForm = document.getElementById('emailDataRequestForm');
    if (emailDataRequestForm) {
        const submitButton = document.getElementById('submit');
        submitButton.addEventListener('click', function (event) {
            event.preventDefault();
            const response = fetch('api.php?action=requestData', {
                method: 'POST',
                body: JSON.stringify({
                    email: document.getElementById('email').value,
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
                    if (responseData.success) {
                        window.location.href = 'emailToken.php?id=' + responseData.id;
                    } else {
                        const alert = document.getElementById('alert');
                        alert.classList.add('alert-danger');
                        alert.innerText = responseData.message;
                        alert.style.display = 'block';
                        window.scrollTo(0, 0);
                    }
                })
                .catch(error => {
                    console.error('Fehler bei der Fetch-Anfrage:', error);
                });
        });
    }
}

export const handleEmailToken = () => {
    const code1 = document.getElementById('code1');
    const code2 = document.getElementById('code2');
    const code3 = document.getElementById('code3');
    const code4 = document.getElementById('code4');
    const code5 = document.getElementById('code5');
    const code6 = document.getElementById('code6');

    if (code1) {
        document.addEventListener('DOMContentLoaded', function () {
            // Focus first code-field if site is loaded
            code1.focus();

            // Timer for resending token
            let timeTokenResend = 59;
            const secondsResend = document.getElementById('secondsResend');
            const buttonTokenResend = document.getElementById('buttonTokenResend');

            const timerTokenResend = setInterval(() => {
                secondsResend.innerText = timeTokenResend.toString();

                if (timeTokenResend <= 0) {
                    clearInterval(timerTokenResend);
                    buttonTokenResend.style.pointerEvents = 'auto';
                    buttonTokenResend.style.textDecoration = 'underline';
                    buttonTokenResend.style.cursor = 'pointer';
                    buttonTokenResend.innerText = 'Verifizierungscode jetzt neu senden';
                    return;
                }

                timeTokenResend--;
            }, 1000);

            buttonTokenResend.addEventListener('click', function (event) {
                event.preventDefault();
                const response = fetch('api.php?action=resendToken', {
                    method: 'POST',
                    body: JSON.stringify({
                        id: id.value
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
                        if (responseData.success) {
                            window.location.reload();
                        } else {
                            const alert = document.getElementById('alert');
                            alert.innerText = responseData.message;
                            alert.style.display = 'block';
                            window.scrollTo(0, 0);
                        }
                    })
                    .catch(error => {
                        console.error('Fehler bei der Fetch-Anfrage:', error);
                    });
            })
        });

    }

    if (code1 && code2 && code3 && code4 && code5 && code6) {
        // Simplify entering code through jumping automatically to next fields
        const inputs = document.querySelectorAll('.code-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/\D/g, '');

                // Jump to next field
                if (input.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                // At backspace, jump to previous field
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        // Paste entire code
        inputs[0].addEventListener('paste', (e) => {
            e.preventDefault();

            const pasted = e.clipboardData
                .getData('text')
                .replace(/\D/g, '')
                .substring(0, inputs.length);

            [...pasted].forEach((char, index) => {
                inputs[index].value = char;
            });

            // Set focus to next empty field
            const nextIndex = pasted.length < inputs.length ? pasted.length : inputs.length - 1;
            inputs[nextIndex].focus();
        });
    }

    const emailTokenForm = document.getElementById('emailTokenForm');
    if (emailTokenForm) {
        const id = document.getElementById('id');
        const submitButton = document.getElementById('submit');

        submitButton.addEventListener('click', function (event) {
            const code = code1.value + code2.value + code3.value + '-' + code4.value + code5.value + code6.value;
            event.preventDefault();
            const response = fetch('api.php?action=emailToken', {
                method: 'POST',
                body: JSON.stringify({
                    code: code,
                    id: id.value
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
                    if (responseData.success) {
                        window.location.href = 'changeData.php?id=' + id.value;
                    } else {
                        const alert = document.getElementById('alert');
                        alert.innerText = responseData.message;
                        alert.style.display = 'block';
                        window.scrollTo(0, 0);
                    }
                })
                .catch(error => {
                    console.error('Fehler bei der Fetch-Anfrage:', error);
                });
        });
    }
}

export const handleFindDuplicates = () => {
    const modal = document.getElementById('modalDuplicates');
    const findDuplicatesButton = document.getElementById('findDuplicatesButton');
    if (findDuplicatesButton) {
        findDuplicatesButton.addEventListener('click', function () {
            const modal = new Modal(document.getElementById('modalDuplicates'));
            modal.show();
        });
    }
    if (modal) {
        const pages = document.querySelectorAll('.page');
        const previous = document.getElementById('previous');
        const next = document.getElementById('next');
        const page1 = document.getElementById('page1');
        if (page1) {
            page1.style.display = 'block';
            const pageCounter = document.getElementById('pageCounter');
            if (pageCounter.getAttribute('data-current-page') === '1') {
                previous.disabled = true;
            }
            if (pages.length === 1) {
                next.disabled = true;
            }
            next.addEventListener('click', function () {
                // Go to next page
                const currentPageNumber = pageCounter.getAttribute('data-current-page');
                switchPage(currentPageNumber, 'next');
            });
            previous.addEventListener('click', function () {
                // Go to previous page
                const currentPageNumber = pageCounter.getAttribute('data-current-page');
                switchPage(currentPageNumber, 'previous');
            });
            // Handle ignoring duplicate
            const ignoreButtons = document.querySelectorAll('#ignoreButton');
            ignoreButtons.forEach(function (ignoreButton) {
                ignoreButton.addEventListener('click', function () {
                    const collapsesCurrentPage = document.querySelectorAll('.accordion-collapse');
                    collapsesCurrentPage.forEach(function (collapse) {
                        // Close collapses as duplicate will be ignored
                        if (collapse.getAttribute('data-page') === pageCounter.getAttribute('data-current-page')) {
                            collapse.classList.remove('show');
                        }
                    });
                    // Set status text and icon
                    const statusText = document.getElementById('status' + pageCounter.getAttribute('data-current-page'));
                    statusText.innerText = 'Ignoriert';
                    statusText.style.color = 'red';
                    const statusIcon = document.getElementById('iconStatus' + pageCounter.getAttribute('data-current-page'));
                    statusIcon.className = 'bi bi-ban';
                    statusIcon.style.color = 'red';
                    switchPage(ignoreButton.getAttribute('data-page'), 'next');
                });
            })
            // Handle merging duplicates
            const mergeButtons = document.querySelectorAll('#mergeDuplicatesButton');
            mergeButtons.forEach(function (mergeButton) {
                mergeButton.addEventListener('click', function (event) {
                    // Open dropdown for choosing the alumni that should be approved while the others will be merged
                    event.preventDefault();
                    const dropdown = new Dropdown(mergeButton);
                    if (dropdown._isShown()) {
                        dropdown.hide();
                    } else {
                        dropdown.show();
                    }
                });
            });
            const dropdownLinks = document.querySelectorAll('.dropdown-item');
            dropdownLinks.forEach(function (dropdownLink) {
                dropdownLink.addEventListener('click', function () {
                    // When the alumni that should be kept is chosen
                    const alumniIdOfSelectedMerge = dropdownLink.getAttribute('data-alumni-id');
                    let allIds = [];
                    dropdownLinks.forEach(function (element) {
                        if (element.getAttribute('data-page') === dropdownLink.getAttribute('data-page')) {
                            allIds.push(element.getAttribute('data-alumni-id'));
                        }
                    });
                    // Send request
                    fetch('api_int.php?action=mergeDuplicates', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            alumniId: alumniIdOfSelectedMerge,
                            allIds: allIds
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.stored === true) {
                                updateStatus('merged', dropdownLink);
                            } else {
                                updateStatus('error', dropdownLink);
                            }
                        })
                        .catch(error => {
                            updateStatus('error', dropdownLink);
                        });
                    const collapsesCurrentPage = document.querySelectorAll('.accordion-collapse');
                    collapsesCurrentPage.forEach(function (collapse) {
                        // When the duplicate is merged, collapses can be closed
                        if (collapse.getAttribute('data-page') === dropdownLink.getAttribute('data-page')) {
                            collapse.classList.remove('show');
                        }
                    });
                    mergeButtons.forEach(function (mergeButton) {
                        if (mergeButton.getAttribute('data-page') === dropdownLink.getAttribute('data-page')) {
                            // Hide dropdown if merging is completed
                            const dropdown = new Dropdown(mergeButton);
                            dropdown.hide();
                        }
                    });
                    ignoreButtons.forEach(function (ignoreButton) {
                        // Disable Ignore-Button if merging is completed
                        if (ignoreButton.getAttribute('data-page') === dropdownLink.getAttribute('data-page')) {
                            ignoreButton.disabled = true;
                        }
                    });
                    // Finally go to next page
                    switchPage(dropdownLink.getAttribute('data-page'), 'next');
                })
            });
        }
    }
    const allDone = document.getElementById('allDone');
    if (allDone) {
        allDone.addEventListener('click', function (event) {
            // If all work is done, reload page to update the table
            event.preventDefault();
            location.reload();
        });
    }
}

// Handles the status texts and icons if duplicates are merged or an error occurs
const updateStatus = (currentStatus, dropdownLink) => {
    const statusText = document.getElementById('status' + dropdownLink.getAttribute('data-page'));
    statusText.innerText = (currentStatus === 'merged') ? 'Zusammengeführt' : 'Es ist ein Fehler aufgetreten.';
    statusText.style.color = (currentStatus === 'merged') ? 'green' : 'red';
    const statusIcon = document.getElementById('iconStatus' + dropdownLink.getAttribute('data-page'));
    statusIcon.className = (currentStatus === 'merged') ? 'bi bi-check-all' : 'bi bi-bug';
    statusIcon.style.color = (currentStatus === 'merged') ? 'green' : 'red';
}

// Handles switching pages
const switchPage = (currentPageNumber, direction) => {
    const previous = document.getElementById('previous');
    const pages = document.querySelectorAll('.page');
    const next = document.getElementById('next');
    const pageCounter = document.getElementById('pageCounter');
    const switchedPageNumber = (direction === 'next') ? (parseInt(currentPageNumber) + 1) : (parseInt(currentPageNumber) - 1);
    const currentPage = document.getElementById('page' + currentPageNumber)
    const switchPage = document.getElementById('page' + switchedPageNumber);
    if (switchPage) {
        switchPage.style.display = 'block';
        currentPage.style.display = 'none';
        pageCounter.setAttribute('data-current-page', switchedPageNumber.toString());
        pageCounter.innerText = 'Duplikat ' + switchedPageNumber + ' von ' + pages.length;
        previous.disabled = switchedPageNumber === 1;
        next.disabled = switchedPageNumber === pages.length;
    }
}

const deleteAlumni = (item, table) => {
    const id = item.getAttribute('data-id');
    const response = fetch('api_int.php?action=deleteAlumni', {
        method: 'POST',
        body: JSON.stringify({
            name: item.getAttribute('data-name'),
            id: id
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
            window.scrollTo(0, 0);
            if (responseData.deleted) {
                table.row('#alumni' + id).remove().draw();
            }
        })
        .catch(error => {
            console.error('Fehler bei der Fetch-Anfrage:', error);
        });
}