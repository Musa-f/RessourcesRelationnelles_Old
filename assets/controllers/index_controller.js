import { Controller } from '@hotwired/stimulus';
import { Popover } from 'bootstrap';

export default class extends Controller {
    connect() 
    {
        const popover = new Popover(document.getElementById('popover'))
    }
}
