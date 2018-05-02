import { Component } from '@angular/core';
import { Router } from '@angular/router';

@Component({
	selector: 'menuadmin',
	templateUrl: './menuadmin.component.html',
})
export class MenuAdminComponent{

	public nombre_componente:string;

	constructor(public _router:Router){
		this.nombre_componente = "MenuAdminComponent";
	}

	logout(){
		localStorage.removeItem('correo');
		localStorage.removeItem('perfil');
		this._router.navigate(['/']);
	}

}