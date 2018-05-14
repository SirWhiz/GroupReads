import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
	selector: 'agregaramigos',
	templateUrl: './agregaramigos.component.html',
	providers: [UsuariosService]
})
export class AgregarAmigosComponent{

	public amigos:Usuario[];
	public usuarios:Usuario[];
	public pendientes:Usuario[];
	public usuario: Usuario;
	public perfil: string;
	public existeAmigos: boolean;
	public existeUsuarios: boolean;
	public existePendientes: boolean;
	public filtro: string;
	public filtroUsuarios: string;

	constructor(public snackBar: MatSnackBar,private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.usuarios = new Array();
		this.amigos = new Array();
		this.pendientes = new Array();
		this.perfil = "";
		this.filtro = "";
		this.filtroUsuarios = "";
		this.existeAmigos = true;
		this.existeUsuarios = true;
		this.existePendientes = true;
	}

	ngOnInit(){
		if(localStorage.getItem('correo')==null || localStorage.getItem('perfil')=='a'){
			this._router.navigate(['/login']);
		}
		this.perfil = localStorage.getItem('perfil');

		this._usuariosService.getUsuario(localStorage.getItem('correo')).subscribe(
			result => {
				if(result.code == 200){
					this.usuario = result.data;
					//Lista de amigos
					this._usuariosService.getAmigos(this.usuario.id).subscribe(
						result => {
							if(result.code == 200){
								this.amigos = result.data;
							}else{
								this.existeAmigos = false;
							}
						}
					);
					//Lista de no amigos
					this._usuariosService.getNoAmigos(this.usuario.id).subscribe(
						result => {
							if(result.code == 200){
								this.usuarios = result.data;
							}else{
								this.existeUsuarios = false;
							}
						}, error => {console.log(error)}
					);
					//Usuarios pendientes de aceptar
					this._usuariosService.getPendientes(this.usuario.id).subscribe(
						result => {
							if(result.code == 200){
								this.pendientes = result.data;
							}else{
								this.existePendientes = false;
							}
						}, error => {console.log(error)}
					);
				}
			}, error => {
				console.log(error);
			}
		);
	}

	borrarAmigo(amigo: Usuario){
		this._usuariosService.borrarAmigo(this.usuario.id,amigo.id).subscribe(
			result => {
				if(result.code == 200){
    				this.snackBar.open("Usuario borrado de la lista de amigos", "Aceptar", {
      					duration: 2500,
    				});
				}
			}, error => {console.log(error)}
		);
		var i=0;
		for(i=0;i<this.amigos.length;i++){
			if(this.amigos[i].id == amigo.id){
				this.amigos.splice(i,1);
			}
		}
		if(this.usuarios.length == 0){
			this.existeUsuarios = true;
		}
		this.usuarios.push(amigo);

		if(this.amigos.length == 0){
			this.existeAmigos = false;
		}
	}

	peticionAmigo(amigo: Usuario){
		this._usuariosService.peticionAmigo(this.usuario.id,amigo.id).subscribe(
			result => {
				if(result.code == 200){
    				this.snackBar.open("Petición de amistad enviada a "+amigo.nombre, "Aceptar", {
      					duration: 2500,
    				});
				}
			}, error => {console.log(error)}
		);

		var i=0;
		for(i=0;i<this.usuarios.length;i++){
			if(this.usuarios[i].id == amigo.id){
				this.usuarios.splice(i,1);
			}
		}
		if(this.pendientes.length == 0){
			this.existePendientes = true;
		}
		this.pendientes.push(amigo);
		if(this.usuarios.length == 0){
			this.existeUsuarios = false;
		}
	}

	borrarPeticion(amigo: Usuario){
		this._usuariosService.borrarPeticion(this.usuario.id,amigo.id).subscribe(
			result => {
				if(result.code == 200){
    				this.snackBar.open("Petición de amistad eliminada", "Aceptar", {
      					duration: 2500,
    				});
				}
			}, error => {console.log(error)}
		);

		var i=0;
		for(i=0;i<this.pendientes.length;i++){
			if(this.pendientes[i].id == amigo.id){
				this.pendientes.splice(i,1);
			}
		}
		if(this.usuarios.length == 0){
			this.existeUsuarios = true;
		}
		this.usuarios.push(amigo);
		if(this.pendientes.length == 0){
			this.existePendientes = false;
		}
	}

	filtrarAmigos(){

	}

	filtrarUsuarios(){

	}

}