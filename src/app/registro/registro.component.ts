import { Component } from '@angular/core';
import { Usuario } from './usuario';
import { Router } from '@angular/router';
import { Pais } from './pais';
import { MenuComponent } from '../menu/menu.component';
import { UsuariosService } from '../../services/usuarios.service';
import { GLOBAL } from '../../services/global';
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
	public filesToUpload;
	public resultUpload;

	constructor(
		private _usuariosService: UsuariosService,
		private _router: Router,
	){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.existeCorreo=false;
		this.filesToUpload = new Array();
	}

	ngOnInit(){

		if(localStorage.getItem('correo')!=null && localStorage.getItem('perfil')=='n'){
      		this._router.navigate(['/home']);
    	}

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

		if(this.filesToUpload.length >= 1){
			this._usuariosService.makeFileRequest(GLOBAL.url+'upload-file', [], this.filesToUpload)
				.then((result)=>{
					console.log(result);
					this.resultUpload = result;
					this.usuario.foto = this.resultUpload.filename;
					this.saveUsuario();
				}, (error) => {
					console.log(error);
			});
		}else{
			this.saveUsuario();
		}
	}

	saveUsuario(){
		this._usuariosService.registrarUsuario(this.usuario).subscribe(
			response => {
				if(response.code==200){
					//Usuario registrado correctamente
					this._router.navigate(['/login']);
				}else{
					//Error al registrar usuario
					console.log(response);
				}
			},
			error => {
				console.log(<any>error);
			});
	}

	fileChangeEvent(fileInput:any){
		this.filesToUpload = <Array<File>>fileInput.target.files;
		console.log(this.filesToUpload);
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