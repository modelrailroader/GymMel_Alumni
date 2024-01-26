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

import { Modal } from 'bootstrap';

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
        document.addEventListener('DOMContentLoaded', function () {
            const delete_items = document.querySelectorAll('#item-delete');
            delete_items.forEach(function (item) {
                item.addEventListener('click', function (event) {
                    const userConfirmation = confirm('Wollen Sie den Eintrag ' + item.getAttribute('data-name') + ' wirklich löschen?');
                    if (!userConfirmation) {
                        event.preventDefault();
                    }
                });
            });
        })
    }
};

export const handleFindDuplicates = () => {
    const modal = document.getElementById('modalDuplicates');
    const findDuplicatesButton = document.getElementById('findDuplicatesButton');
    findDuplicatesButton.addEventListener('click', function() {
        const modal = new Modal(document.getElementById('modalDuplicates'));
        modal.show();
    })
    if (modal) {
        const previous = document.getElementById('previous');
        const next = document.getElementById('next');
        const pages = document.querySelectorAll('.page');
        const page1 = document.getElementById('page1');
        page1.style.display = 'block';
        const pageCounter = document.getElementById('pageCounter');
        if (pageCounter.getAttribute('data-current-page') === '1') {
            previous.disabled = true;
        }
        next.addEventListener('click', function() {
            const currentPageNumber = pageCounter.getAttribute('data-current-page');
            const nextPageNumber = parseInt(currentPageNumber) + 1;
            const currentPage = document.getElementById('page' + currentPageNumber)
            const nextPage = document.getElementById('page' + nextPageNumber);
            nextPage.style.display = 'block';
            currentPage.style.display = 'none';
            pageCounter.setAttribute('data-current-page', nextPageNumber.toString());
            pageCounter.innerText = 'Duplikat ' + nextPageNumber + ' von ' + pages.length;
            if (nextPageNumber !== 1) {
                previous.disabled = false;
            }
            if (nextPageNumber === pages.length) {
                next.disabled = true;
            }
        });
        previous.addEventListener('click', function() {
            const currentPageNumber = pageCounter.getAttribute('data-current-page');
            const previousPageNumber = parseInt(currentPageNumber) - 1;
            const currentPage = document.getElementById('page' + currentPageNumber)
            const previousPage = document.getElementById('page' + previousPageNumber);
            previousPage.style.display = 'block';
            currentPage.style.display = 'none';
            pageCounter.setAttribute('data-current-page', previousPageNumber.toString());
            pageCounter.innerText = 'Duplikat ' + previousPageNumber + ' von ' + pages.length;
            if (previousPageNumber === 1) {
                previous.disabled = true;
            }
            if (previousPageNumber !== pages.length) {
                next.disabled = false;
            }
        });
    }
}


