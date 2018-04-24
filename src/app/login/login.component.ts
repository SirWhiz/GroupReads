import { Component } from '@angular/core';
import { MenuComponent } from '../menu/menu.component';

@Component({
	selector: 'login',
	templateUrl: './login.component.html' 
})
export class LoginComponent{
	
	public nombre_componente:string;
	public correo:string;
	public pwd:string;

	constructor(){
		this.nombre_componente = "LoginComponent";
		this.correo = "";
		this.pwd = "";
	}

	comprobar(){
		console.log(this.correo,this.pwd);
	}
}