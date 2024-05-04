import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [];

    connect() {}

    async searchResources(event) {
        let keyword = document.querySelector('.search-text input').value;
        let category = document.querySelector('.select-category').value;
        let link = document.querySelector('.select-link').value;

        try {
            const response = await fetch(`/api/resources/search?keyword=${keyword}&category=${category}&link=${link}`);
            const data = await response.json();

            console.log(data);

        } catch (error) {
            console.error('Erreur lors de la recherche de ressources:', error);
        }
    }

}