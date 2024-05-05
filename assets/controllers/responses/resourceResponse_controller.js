import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["container"];
    linksArray;

    initialize(){
        this.linksArray = [];
    }

    connect() {
        this.page = 1;
        this.loadLinks();
        this.loadFormats();
        this.loadUsers();
        this.loadResources();
        this.loadCategories();
    }

    handleFilterChange() {
        this.page = 1;
        this.loadResources();
    }

    async loadResources() {
        let container = document.querySelector('.container-resource');
        let templateResource = document.getElementById('resource-template');
        let researchBox = document.querySelector('.research-box');
        let categorySelect = researchBox.querySelector('select.select-category').value;
        let linkSelect = researchBox.querySelector('select.select-link').value;
    
        try {
            const response = await fetch('/api/resources?page=' + this.page);
            const data = await response.json();
    
            for (const e of data.data) {
                let resource = templateResource.cloneNode(true);
                resource.classList.remove("d-none");
                resource.querySelector('h3.title').innerHTML = e.title;
                resource.querySelector('p.category').innerHTML = e.category.name;

                if(!e.active)
                    resource.querySelector('p.published').classList.remove('d-none');
                
                const iconClasses = {
                    0: "bi bi-person-fill",
                    1: "bi bi-person-heart",
                    2: "bi bi-people-fill",
                    3: "bi bi-briefcase-fill"
                };
                
                const iconHTML = `<i class="${iconClasses[e.type]}"></i> ${this.linksArray[e.type]}`;
                resource.querySelector('p.link').innerHTML = iconHTML;
                const content = await this.generateResourceContent(e);
                resource.querySelector('div.content-resource').innerHTML = content;
    
                container.append(resource);
            }
            this.page++;
        } catch (error) {
            console.error(error);
        }
    }
    

    loadCategories(){
        let selects  = document.querySelectorAll('select.select-category');
        selects.forEach(select => {
            fetch('/api/categories')
            .then(response => response.json())
            .then(data => {
                data.forEach(e => {
                    let option = document.createElement('option');
                    option.value = e.id;
                    option.innerHTML = e.name;
                    select.appendChild(option);
                });
            })
        });
    }

    loadFormats(){
        let selects = document.querySelectorAll('select.select-format');
        selects.forEach(select => {
            fetch('/api/formats')
            .then(response => response.json())
            .then(data => {
                data.forEach(e => {
                    let option = document.createElement('option');
                    option.value = e.id;
                    option.innerHTML = e.type;
                    select.appendChild(option);
                });
            })
        })
    }

    loadUsers(){
        let selects = document.querySelectorAll('select.select-user');
        selects.forEach(select => {
            fetch('/api/users')
            .then(response => response.json())
            .then(data => {
                data.forEach(e => {
                    let option = document.createElement('option');
                    option.value = e.id;
                    option.innerHTML = e.login;
                    select.appendChild(option);
                });
            })
        })
    }

    loadMore() {
        this.loadResources();
    }

    async generateResourceContent(e) {
        let content = '';
        switch (e.format.id) {
            case 1:
                try {
                    const response = await fetch(`/api/file/${e.id}/${e.files[0].id}`);
                    if (!response.ok) {
                        throw new Error('Server not responding');
                    }
                    const videoBlob = await response.blob();
                    const videoUrl = URL.createObjectURL(videoBlob);
                    content = `<video src="${videoUrl}" controls></video>`;
                } catch (error) {
                    console.error(error);
                }
                break;
            case 2:
                try {
                    const response = await fetch(`/api/file/${e.id}/${e.files[0].id}`);
                    if (!response.ok) {
                        throw new Error('Server Not Responding');
                    }
                    const pdfBlob = await response.blob();
                    const pdfUrl = URL.createObjectURL(pdfBlob);
                    content = `<embed src="${pdfUrl}" type="application/pdf" width="100%" height="400px" />`;
                } catch (error) {
                    console.error(error);
                }
                break;
            case 3:
                if (e.content.length > 100) {
                    const truncatedContent = e.content.substring(0, 200);
                    content = `<p>${truncatedContent}</p><i class="btn">Afficher plus</i>`;
                } else {
                    content = `<p>${e.content}</p>`;
                }
                break;
            case 4:
                content = `<p>Règles du jeu <br>${e.content}</p><button>Démarrer le chat en direct</button>`;
                break;
            case 5:
                content = `<div class="card">${e.content}</div>`;
                break;
            default:
                content = '';
        }
        return content;
    }

    loadLinks(){
        let selects = document.querySelectorAll('select.select-link');
        selects.forEach(select => {
            fetch('/api/links')
            .then(response => response.json())
            .then(data => {
                this.linksArray = data;
                let index = 0;
                data.forEach(e => {
                    let option = document.createElement('option');
                    option.innerHTML = e;
                    option.value = index;
                    select.appendChild(option);
                    index++;
                });
            })
        });
    }
}