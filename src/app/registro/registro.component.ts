import { Component } from '@angular/core';
import { Usuario } from './usuario';
import { Router } from '@angular/router';
import { Pais } from './pais';
import { MenuComponent } from '../menu/menu.component';
import { UsuariosService } from '../../services/usuarios.service';
import { GLOBAL } from '../../services/global';
import { MatSnackBar } from '@angular/material/snack-bar';
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
	public nombreArray:any[];

	constructor(
		public snackBar: MatSnackBar,
		private _usuariosService: UsuariosService,
		private _router: Router,
	){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.existeCorreo=false;
		this.filesToUpload = new Array();
		this.nombreArray = new Array();
	}

	ngOnInit(){

		if(localStorage.getItem('correo')!=null){
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

		if(this.filesToUpload.length >= 1){
			this.nombreArray = this.filesToUpload[0].name.split('.');
			if(this.nombreArray[this.nombreArray.length-1]!='jpeg' && this.nombreArray[this.nombreArray.length-1]!='png'){
				this.snackBar.open("La imagen debe ser jpeg/png", "OK", {
					duration: 2500,
				});
			}else{
				this._usuariosService.makeFileRequest(GLOBAL.url+'upload-file', [], this.filesToUpload)
					.then((result)=>{
						console.log(result);
						this.resultUpload = result;
						this.usuario.foto = this.resultUpload.filename;
						this.saveUsuario();
					}, (error) => {
						console.log(error);
				});
			}
		}else{
			this.saveUsuario();
		}
	}

	saveUsuario(){
		this.usuario.fecha = this.usuario.fecha.getFullYear()+"/"+(parseInt(this.usuario.fecha.getMonth())+1)+"/"+this.usuario.fecha.getDate();
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