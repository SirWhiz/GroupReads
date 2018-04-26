import { Component } from '@angular/core';
import { Usuario } from './usuario';
import { MenuComponent } from '../menu/menu.component';
import { UsuariosService } from '../../services/usuarios.service';
declare var $: any;

@Component({
	selector: 'registro',
	templateUrl: './registro.component.html',
	providers: [UsuariosService]
})
export class RegistroComponent{

	public nombre_componente = "RegistroComponent";
	public usuario:Usuario;
	public existeCorreo:boolean;

	constructor(
		private _usuariosService: UsuariosService
	){
		this.usuario = new Usuario("","","","","","","");
		this.existeCorreo=false;
	}
	
	onSubmit(){
		this.usuario.fecha = this.usuario.fecha.getDate()+"/"+(parseInt(this.usuario.fecha.getMonth())+1)+"/"+this.usuario.fecha.getFullYear();
		console.log(this.usuario);
	}

	comprobar(){
		this._usuariosService.getUsuario(this.usuario.correo).subscribe(
			result => {
				if(result.code==200){
					this.existeCorreo=true;
				}else{
					this.existeCorreo=false;
				}
			},
			error => {
				console.log(<any>error);
			}
		);
	}
}