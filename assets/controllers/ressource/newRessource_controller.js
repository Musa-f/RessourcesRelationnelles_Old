import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';
import { Tooltip } from 'bootstrap';

export default class extends Controller {
    static targets = ["modal"];
    modalBody;
    formData;
    postData;

    connect() 
    {
        this.modalBody = document.getElementById("modalNewRessource");
        this.formData = new FormData();
        this.postData = {};

        $('select.select-users').select2({
            dropdownParent: $('#modalNewRessource')
        });
        
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(function(tooltipTriggerEl) { new Tooltip(tooltipTriggerEl); });
    }
    
    createRessource()
    {
        let url = this.modalBody.dataset.url;
        let data = new FormData();
        data.append("data", JSON.stringify(this.postData));

        if (this.postData.file){
            data.append("file", this.postData.file);
        }

        fetch(url, {
            method: 'POST',
            body: data,
        })
        .then(response => response.json())
        .then(data => {
            window.location.reload();
            alert("La ressource a bien été enregistrée. Un modérateur la validera bientôt.");
        })
        .catch(error => {
            console.error('Erreur lors de l\'envoi des données :', error);
        });
    }

    changeStep(event)
    {  
        let alertDiv = this.modalTarget.querySelector("div.alert-danger");
        try
        {
            alertDiv.classList.add("d-none");
            let currentStep = event.currentTarget.dataset.step;
            let currentForm = this.modalTarget.querySelector("div#formStep"+currentStep);
            this.checkStep(currentStep, currentForm);
            this.changeStyle(currentStep, currentForm);
            event.currentTarget.dataset.step = parseInt(currentStep) + 1;
        }
        catch(e)
        {
            alertDiv.classList.remove("d-none");
            alertDiv.innerText = e.message;
        }
    }

    changeStyle(currentStep, currentForm)
    {
        if (currentStep == 4) {
            this.createRessource();
            return;
        }
        let nextStep = parseInt(currentStep)+1;
        let nextForm = this.modalTarget.querySelector("div#formStep"+nextStep);    
        let nextTab = this.modalTarget.querySelector(`[data-step="${nextStep}"]`);
        currentForm.classList.remove("active-step");
        nextForm.classList.add("active-step");
        nextTab.classList.add("active-step-btn");      
    }

    checkStep(step, form)
    {
        switch(step){
            case '1':
                let title = form.querySelector("input#title").value;
                let category = this.selectOption(form.querySelector("select.select-category"));
                let link = this.selectOption(form.querySelector("select.select-link"));

                if(title != null && category != 0 && link != 0) {
                    this.postData.title = title;
                    this.postData.category = category;
                    this.postData.link = link;
                }else{
                    throw new Error("Les champs ne peuvent être vides");
                }
            break;
            case '2':
                let format = this.selectOption(form.querySelector("select.select-format"));
                if(format != 0)
                {
                    this.postData.format = format;
                    this.displaySelectedView(format);
                }
                else
                    throw new Error("Les champs ne peuvent être vides");      
            break;
            case '3':
                let activeForm = form.querySelector('div.active-step');
                let input = activeForm.querySelector('input[type="file"]');
                let textarea = activeForm.querySelector('textarea');
                let hasContent = false;

                if (textarea && textarea.value.trim() !== '')
                    hasContent = true;
                
                if (hasContent || (input && input.files.length > 0)) {
                    if (input && input.files.length > 0) {
                        let file = input.files[0];
                        this.postData.file = file; 
                        console.log(this.postData.file);
                    }
                
                    if (hasContent) {
                        let content = textarea.value.trim();
                        this.postData.content = content;
                    }
                } else {
                    throw new Error("Les champs ne peuvent être vides");
                }
            break;
            case '4':
                let visibility = form.querySelector("select.select-visibility");
                let visibilityValue = parseInt(visibility.value);

                if(visibilityValue == 1){
                    let usersSelect = form.querySelector("select.select-users");
                    let selectedUsers = Array.from(usersSelect.selectedOptions).map(option => option.value);

                    if (selectedUsers.length > 0) {
                        this.postData.visibility = visibilityValue;
                        this.postData.users = selectedUsers;
                    } else {
                        throw new Error("Au moins un utilisateur doit être sélectionné pour la visibilité partagée");
                    }
                }else{ 
                    this.postData.visibility = visibilityValue;
                }
            break;
        }
    }

    selectOption(element)
    {
        return element.selectedIndex;
    }

    displaySelectedView(format)
    {
        const formats = {
            1: "formatVideo",
            2: "formatCourse",
            3: "formatArticle",
            4: "formatActivity",
            5: "formatChallenge",
            6: "formatOnlineGame"
        };
    
        const targetId = formats[format];
        if (targetId) {
            this.modalTarget.querySelector(`div#${targetId}`).classList.add("active-step");
        }
    }

    toggleUserSelectVisibility() 
    {
        const visibilitySelect = this.modalBody.querySelector('select.select-visibility');
        const divSelect = this.modalBody.querySelector('div.container-form-select');
        const visibilityValue = parseInt(visibilitySelect.value);

        if (visibilityValue == 1) {
            divSelect.classList.remove("d-none");
        } else {
            divSelect.classList.add("d-none");
        }
    }

}