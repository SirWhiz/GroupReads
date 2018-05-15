import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Club } from './club';

@Component({
	selector: 'nuevoclub',
	templateUrl: './nuevoclub.component.html',
	providers: [UsuariosService]
})
export class NuevoClubComponent{

	public usuario:Usuario;
	public club:Club;

	constructor(private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.club = new Club("","","","","","");
	}

	ngOnInit(){

		if(localStorage.getItem('correo')==null){
			this._router.navigate(['/login']);
		}else{
			this.usuario.tipo = localStorage.getItem('perfil');
		}

		this._usuariosService.getUsuario(localStorage.getItem('correo')).subscribe(
			result => {
				if(result.code == 200){
					this.usuario = result.data;
				}
			}, error => {console.log(error);}
		);

	}
}