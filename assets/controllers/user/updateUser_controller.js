import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [];

    connect() {}

    nextStep(event) {
        let button=event.currentTarget;
        document.getElementById('step' + button.dataset.id).style.display = 'none';
        document.getElementById('step' + (parseInt(button.dataset.id) + 1)).style.display = 'block';

    }
    
    submitForm() {
    
        alert('Formulaire soumis avec succès !');
    }
    
    
}