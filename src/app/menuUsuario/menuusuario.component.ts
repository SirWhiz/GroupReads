import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';

@Component({
	selector: 'menuusuario',
	templateUrl: './menuusuario.component.html',
})
export class MenuUsuarioComponent{

	public nombre_componente:string;

	constructor(public _router:Router,public _usuariosService:UsuariosService){
		this.nombre_componente = "MenuUsuarioComponent";
	}

	logout(){
		localStorage.removeItem('correo');
		localStorage.removeItem('perfil');
		this._router.navigate(['/']);
	}

}