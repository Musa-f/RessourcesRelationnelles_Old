import { Controller } from '@hotwired/stimulus';
import {Tooltip} from 'bootstrap';
import {Popover} from 'bootstrap';


export default class extends Controller {
    connect() 
    {
        const popover = document.getElementById('popover') != null ? new Popover(document.getElementById('popover')) : "";
        
        if(document.querySelectorAll('[data-bs-toggle="tooltip"]') != null){
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(function(tooltipTriggerEl) { new Tooltip(tooltipTriggerEl); });
        }
    }
}
