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

import {Dropdown, Modal} from 'bootstrap';

export const handleShowData = () => {
    pdfmake.vfs = pdfFonts.pdfMake.vfs;
    const alumniTable = document.getElementById('alumniTable');
    if (alumniTable) {
        const table = new DataTable(alumniTable, {
            "language": languageDE,
            "dom": "Bflrtip",
            responsive: true,
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
        const delete_items = document.querySelectorAll('#item-delete');
        delete_items.forEach(function (item) {
            item.addEventListener('click', function (event) {
                const userConfirmation = confirm('Wollen Sie den Eintrag ' + item.getAttribute('data-name') + ' wirklich löschen?');
                if (!userConfirmation) {
                    event.preventDefault();
                }
            });
        });
        table.on('draw', function () {
            const delete_items = document.querySelectorAll('#item-delete');
            delete_items.forEach(function (item) {
                item.addEventListener('click', function (event) {
                    const userConfirmation = confirm('Wollen Sie den Eintrag ' + item.getAttribute('data-name') + ' wirklich löschen?');
                    if (!userConfirmation) {
                        event.preventDefault();
                    }
                });
            });
        });
    }
};

export const handleFindDuplicates = () => {
    const modal = document.getElementById('modalDuplicates');
    const findDuplicatesButton = document.getElementById('findDuplicatesButton');
    findDuplicatesButton.addEventListener('click', function () {
        const modal = new Modal(document.getElementById('modalDuplicates'));
        modal.show();
    })
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
                const currentPageNumber = pageCounter.getAttribute('data-current-page');
                switchPage(currentPageNumber, 'next');
            });
            previous.addEventListener('click', function () {
                const currentPageNumber = pageCounter.getAttribute('data-current-page');
                switchPage(currentPageNumber, 'previous');
            });
            const ignoreButtons = document.querySelectorAll('#ignoreButton');
            ignoreButtons.forEach(function (ignoreButton) {
                ignoreButton.addEventListener('click', function () {
                    const collapsesCurrentPage = document.querySelectorAll('.accordion-collapse');
                    collapsesCurrentPage.forEach(function (collapse) {
                        if (collapse.getAttribute('data-page') === pageCounter.getAttribute('data-current-page')) {
                            collapse.classList.remove('show');
                        }
                    });
                    const statusText = document.getElementById('status' + pageCounter.getAttribute('data-current-page'));
                    statusText.innerText = 'Ignoriert';
                    statusText.style.color = 'red';
                    const statusIcon = document.getElementById('iconStatus' + pageCounter.getAttribute('data-current-page'));
                    statusIcon.className = 'bi bi-ban';
                    statusIcon.style.color = 'red';
                    statusText.setAttribute('data-current-status', 'ignored');
                    switchPage(ignoreButton.getAttribute('data-page'), 'next');
                });
            })
            const mergeButtons = document.querySelectorAll('#mergeDuplicatesButton');
            mergeButtons.forEach(function (mergeButton) {
                mergeButton.addEventListener('click', function (event) {
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
                    const alumniIdOfSelectedMerge = dropdownLink.getAttribute('data-alumni-id');
                    let allIds = [];
                    dropdownLinks.forEach(function (element) {
                        if (element.getAttribute('data-page') === dropdownLink.getAttribute('data-page')) {
                            allIds.push(element.getAttribute('data-alumni-id'));
                        }
                    });
                    const sourceDir = document.getElementById('sourceDir').value;
                    fetch('mergeDuplicates.php', {
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
                            console.log(data.success);
                            if (data.success === true) {
                                updateStatus('merged', dropdownLink);
                                console.log("1");
                            } else {
                                updateStatus('error', dropdownLink);
                                console.log("2");
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            updateStatus('error', dropdownLink);
                            console.log("3");
                        });
                    const collapsesCurrentPage = document.querySelectorAll('.accordion-collapse');
                    collapsesCurrentPage.forEach(function (collapse) {
                        if (collapse.getAttribute('data-page') === dropdownLink.getAttribute('data-page')) {
                            collapse.classList.remove('show');
                        }
                    });
                    mergeButtons.forEach(function (mergeButton) {
                        if (mergeButton.getAttribute('data-page') === dropdownLink.getAttribute('data-page')) {
                            const dropdown = new Dropdown(mergeButton);
                            dropdown.hide();
                        }
                    });
                    ignoreButtons.forEach(function (ignoreButton) {
                        if (ignoreButton.getAttribute('data-page') === dropdownLink.getAttribute('data-page')) {
                            ignoreButton.disabled = true;
                        }
                    });
                    switchPage(dropdownLink.getAttribute('data-page'), 'next');
                })
            });
        }
    }
    const allDone = document.getElementById('allDone');
    allDone.addEventListener('click', function (event) {
        event.preventDefault();
        location.reload();
    });
}

const updateStatus = (currentStatus, dropdownLink) => {
    const statusText = document.getElementById('status' + dropdownLink.getAttribute('data-page'));
    statusText.innerText = (currentStatus === 'merged') ? 'Zusammengeführt' : 'Es ist ein Fehler aufgetreten.';
    statusText.style.color = (currentStatus === 'merged') ? 'green' : 'red';
    const statusIcon = document.getElementById('iconStatus' + dropdownLink.getAttribute('data-page'));
    statusIcon.className = (currentStatus === 'merged') ? 'bi bi-check-all' : 'bi bi-bug';
    statusIcon.style.color = (currentStatus === 'merged') ? 'green' : 'red';
    statusText.setAttribute('data-current-status', ((currentStatus === 'merged') ? 'merged' : 'error'));
}

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