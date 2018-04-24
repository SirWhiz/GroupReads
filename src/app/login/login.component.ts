import { Component } from '@angular/core';

@Component({
	selector: 'login',
	templateUrl: './login.component.html' 
})
export class LoginComponent{
	
	public nombre_componente = "LoginComponent";
	public correo:string;
	public pwd:string;

	constructor(){
		this.correo = "";
		this.pwd = "";
	}

	comprobar(){
		console.log(this.correo,this.pwd);
	}
}