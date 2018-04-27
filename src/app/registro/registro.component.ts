import { Component } from '@angular/core';
import { Usuario } from './usuario';
import { Pais } from './pais';
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
	public paises: Pais[];
	public usuario:Usuario;
	public existeCorreo:boolean;

	constructor(
		private _usuariosService: UsuariosService
	){
		this.usuario = new Usuario("","","","","","","","");
		this.existeCorreo=false;
	}

	ngOnInit(){
		this._usuariosService.getPaises().subscribe(
			result => {
				if(result.code != 200){
					console.log(result);
				}else{
					this.paises = result.data;
				}
			}, error => {
				console.log(<any>error);
			}
		);
	}
	
	onSubmit(){
		this.usuario.fecha = this.usuario.fecha.getFullYear()+"/"+(parseInt(this.usuario.fecha.getMonth())+1)+"/"+this.usuario.fecha.getDate();
		console.log(this.usuario);

		this._usuariosService.registrarUsuario(this.usuario).subscribe(
			response => {
				if(response.code==200){
					//Usuario registrado correctamente
				}else{
					//Error al registrar usuario
					console.log(response);
				}
			},
			error => {
				console.log(<any>error);
			}
		);
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