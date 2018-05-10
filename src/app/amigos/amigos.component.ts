import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';

@Component({
	selector: 'amigos',
	templateUrl: './amigos.component.html',
	providers: [UsuariosService]
})
export class AmigosComponent{

	public usuario:Usuario;
	public amigos: Usuario[];
	public pendiente: number;
	public existeAmigos: boolean;

	constructor(private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.amigos = new Array();
		this.pendiente = 0;
		this.existeAmigos = true;
	}

	ngOnInit(){

		this.usuario.correo = localStorage.getItem('correo');

		this._usuariosService.getUsuario(this.usuario.correo).subscribe(
			result => {
				if(result.code == 200){
					//Solicitudes pendientes
					this.usuario=result.data;
					this._usuariosService.getSolicitudesPendientes(this.usuario.id).subscribe(
						result => {
							if(result.code == 200){
								this.pendiente = result.data;
							}
						}
					);
					//Lista de amigos
					this._usuariosService.getAmigos(this.usuario.id).subscribe(
						result => {
							if(result.code == 200){
								this.amigos = result.data;
								console.log(this.amigos);
							}else{
								this.existeAmigos = false;
							}
						}
					);
				}
			}, error => {
				console.log(error);
			}
		);

	}
}