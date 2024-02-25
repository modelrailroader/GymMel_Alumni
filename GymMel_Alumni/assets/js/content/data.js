/**
 * JavaScript functions for handling the network data.
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

import {Dropdown, Modal, Toast} from 'bootstrap';
import {createToast} from "../utils/notifications";

export const handleShowData = () => {
    pdfmake.vfs = pdfFonts.pdfMake.vfs;
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
            "buttons": [{
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
            else {
                editAlumniForm.reportValidity();
            }
            event.preventDefault();
        });
    }
};

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