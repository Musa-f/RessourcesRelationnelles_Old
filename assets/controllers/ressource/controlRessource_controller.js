import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';

export default class extends Controller {
    static targets = [];

    connect() {}

    validateRessource(event) {
        console.log("hey");
        let button = event.currentTarget;
        const ressourceId = button.dataset.ressourceId;
        const userId = button.closest('tr').querySelector('[data-user-id]').dataset.userId;

        const url = button.dataset.url;
        fetch(url, {
            method: 'POST',
            body: JSON.stringify({ ressourceId, userId }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur lors de la validation de la ressource.');
            }
            return response.json();
        })
        .then(data => {
            this.removeTableRow(button.closest('tr'));
            this.showToastMessage(`La ressource ${ressourceId} a été validée.`);
        })
        .catch(error => {
            console.error('Erreur :', error);
        });
    }

    removeTableRow(row) {
        row.remove();
    }

    showToastMessage(message) {
        const toastElement = document.querySelector('.toast-container .toast');
        const toastBody = toastElement.querySelector('.toast-body');
        toastBody.textContent = message;

        const toast = new Toast(toastElement);
        toast.show();
    }

    rejectRessource()
    {
        //TODO: demander la raison du refus
    }
}