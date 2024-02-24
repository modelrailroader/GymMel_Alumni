/**
 * JavaScript functions for creating alerts and toasts
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2024 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2024-02-24
 */

export const createAlert = (message, typ) => {
    const alertElement = document.createElement('div');
    alertElement.classList.add('alert');
    alertElement.classList.add('alert-' + typ);
    alertElement.setAttribute('role', 'alert')
    alertElement.innerText = message;
    return alertElement;
}

export const createToast = (message, typ) => {
    const toastElement = document.createElement('div');
    toastElement.classList.add('toast');
    toastElement.classList.add('bg-' + typ);
    toastElement.classList.add('text-white');
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');
    toastElement.style.position = 'absolute';
    toastElement.style.top = '20px';
    toastElement.style.right = '30px';

    const toastBody = document.createElement('div');
    toastBody.classList.add('toast-body');
    toastBody.classList.add('d-flex');
    toastBody.classList.add('justify-content-between');
    const toastMessage = document.createElement('div');
    toastMessage.innerText = message;
    toastBody.appendChild(toastMessage);
    const closeButton = document.createElement('button');
    closeButton.classList.add('btn-close');
    closeButton.setAttribute('data-bs-dismiss', 'toast');
    closeButton.setAttribute('aria-label', 'close');
    closeButton.style.color = 'grey';
    toastBody.appendChild(closeButton);
    toastElement.appendChild(toastBody);
    return toastElement;
}