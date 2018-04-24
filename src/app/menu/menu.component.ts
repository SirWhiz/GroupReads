import { Component } from '@angular/core';

@Component({
    selector: 'menu-superior',
    templateUrl: './menu.component.html'
})
export class MenuComponent{

    public nombre_componente:string;

    constructor(){
        this.nombre_componente = "MenuComponent";
    }
}