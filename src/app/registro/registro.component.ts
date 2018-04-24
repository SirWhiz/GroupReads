import { Component } from '@angular/core';
import { Usuario } from './usuario';
import { MenuComponent } from '../menu/menu.component';
declare var $: any;

@Component({
	selector: 'registro',
	templateUrl: './registro.component.html'
})
export class RegistroComponent{

	public nombre_componente = "RegistroComponent";
	public usuario:Usuario;

	constructor(){
		this.usuario = new Usuario("","","","","","");
	}
	
	onSubmit(){
		this.usuario.fecha = this.usuario.fecha.getDate()+"/"+(parseInt(this.usuario.fecha.getMonth())+1)+"/"+this.usuario.fecha.getFullYear();
		console.log(this.usuario);
	}
}