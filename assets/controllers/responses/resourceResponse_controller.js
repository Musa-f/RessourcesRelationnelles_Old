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

    loadResources() {
        let container = document.querySelector('.container-resource');
        let templateResource = document.getElementById('resource-template');
        fetch('/api/resources?page=' + this.page)
        .then(response => response.json())
        .then(data => {
            data.data.forEach(e => {
                let resource = templateResource.cloneNode(true);
                resource.classList.remove("d-none");
                resource.querySelector('h3.title').innerHTML = e.title;
                resource.querySelector('p.category').innerHTML = e.category.name;
                let link = e.type;
                
                switch(parseInt(e.type)){
                    case 0:
                        resource.querySelector('p.link').innerHTML = `<i class="bi bi-person-fill"></i> ${this.linksArray[e.type]}`;
                    break;
                    case 1:
                        resource.querySelector('p.link').innerHTML = `<i class="bi bi-person-heart"></i> ${this.linksArray[e.type]}`;
                    break;
                    case 2:
                        resource.querySelector('p.link').innerHTML = `<i class="bi bi-people-fill"></i> ${this.linksArray[e.type]}`;
                    break
                    case 3:
                        resource.querySelector('p.link').innerHTML = `<i class="bi bi-briefcase-fill"></i> ${this.linksArray[e.type]}`;
                    break
                }

                container.append(resource);
            })
            
            this.page++;
        });
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
}