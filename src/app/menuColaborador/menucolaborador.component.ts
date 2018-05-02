import { Component } from '@angular/core';
import { Router } from '@angular/router';

@Component({
	selector: 'menucolaborador',
	templateUrl: './menucolaborador.component.html',
})
export class MenuColaboradorComponent{

	public nombre_componente:string;

	constructor(public _router:Router){
		this.nombre_componente = "MenuColaboradorComponent";
	}

	logout(){
		localStorage.removeItem('correo');
		localStorage.removeItem('perfil');
		this._router.navigate(['/']);
	}

}