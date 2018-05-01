import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Pais } from '../registro/pais';
import { GLOBAL } from '../../services/global';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { DialogoComponent } from '../dialogoImagen/dialogo.component';

@Component({
	selector: 'configuracion',
	templateUrl: './configuracion.component.html',
	providers: [UsuariosService]
})
export class ConfiguracionComponent{

	public usuario:Usuario;
	public usuario2:Usuario;
	public correoActual:String;
	public paises: Pais[];
	public existeCorreo:boolean;
	public fechaMostrar:string;
	public pwdActual:boolean;
	public filesToUpload;
	public resultUpload;
	public nombreNuevo:string;

	constructor(public snackBar: MatSnackBar,private _router: Router,private _usuariosService: UsuariosService,public dialog: MatDialog){
		this.usuario = new Usuario("","","","","","","","","");
		this.usuario2 = new Usuario("","","","","","","","","");
		this.correoActual = "";
		this.pwdActual = true;
		this.fechaMostrar = "";
		this.filesToUpload = new Array();
		this.existeCorreo = false;
		this.nombreNuevo = "";
	}

	ngOnInit(){
		if(localStorage.getItem('correo')==null){
      		this._router.navigate(['/login']);
    	}

		this._usuariosService.getUsuario(localStorage.getItem('correo')).subscribe(
			result => {
				if(result.code==200){
					this.usuario = result.data;
					this.usuario.pwd = "";
					this.correoActual = this.usuario.correo;
					var fechaArray = this.usuario.fecha.split('-');
					this.usuario.fecha = fechaArray[0]+'/'+fechaArray[1]+'/'+fechaArray[2];
					this.fechaMostrar = fechaArray[2]+'/'+fechaArray[1]+'/'+fechaArray[0];
					console.log(this.usuario);
				}
			},
			error => {
				console.log(<any>error);
			}
		);

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

		this._usuariosService.updateUsuario(this.usuario).subscribe(
			response => {
				if(response.code==200){
					//Usuario registrado correctamente
					this.snackBar.open("Datos modificados correctamente", "OK", {
      					duration: 2500,
    				});
				}else{
					//Error al registrar usuario
					this.snackBar.open("Error al modificar los datos", "OK", {
      					duration: 2500,
    				});
				}
			},
			error => {
				console.log(<any>error);
			});
	}

	updatePwd(){

		this._usuariosService.updatePwd(this.usuario).subscribe(
			response => {
				if(response.code==200){
					//Usuario registrado correctamente
					this.snackBar.open("Contraseña modificada correctamente", "OK", {
      					duration: 2500,
    				});
				}else{
					//Error al registrar usuario
					this.snackBar.open("Error al modificar la contraseña", "OK", {
      					duration: 2500,
    				});
				}
			},
			error => {
				console.log(<any>error);
			});
	}

	comprobarActual(){
		this.usuario2.correo = localStorage.getItem('correo');
        this._usuariosService.loginUsuario(this.usuario2).subscribe(
			response => {
				if(response.code==200){
					//Datos de usuario correctos
					this.pwdActual = true;
				}else{
					//Error al comprobar los datos
    				this.pwdActual = false;
				}
			},
			error => {
				console.log("error");
			}
		);
	}

	fileChangeEvent(fileInput:any){
		this.filesToUpload = <Array<File>>fileInput.target.files;
		console.log(this.filesToUpload);
	}

	comprobar(){
		this._usuariosService.getUsuario(this.usuario.correo).subscribe(
			result => {
				if(result.code==200){
					if(result.data.correo!=this.correoActual){
						this.existeCorreo=true;
					}else{
						this.existeCorreo=false;
					}
				}else{
					this.existeCorreo=false;
				}
			},
			error => {
				console.log(<any>error);
			}
		);
	}

	onSubmitimg(){
		if(this.filesToUpload.length >= 1){
			this._usuariosService.makeFileRequest(GLOBAL.url+'upload-file', [], this.filesToUpload)
				.then((result)=>{
					this.resultUpload = result;
					this.usuario.foto = this.resultUpload.filename;
					this.actualizarUsuario();
				}, (error) => {
					console.log(error);
			});
		}
	}

	actualizarUsuario(){
		this._usuariosService.updateImg(this.usuario).subscribe(
			result => {
				if(result.code==200){
					this.snackBar.open("Imagen actualizada correctamente", "OK", {
      					duration: 2500,
    				});
				}else{
					this.snackBar.open("Error al actualizar la imagen", "OK", {
      					duration: 2500,
    				});
    				this.usuario.foto = '';
				}
			},
			error => {
				console.log(<any>error);
			}
		);
	}

	public abrirDialogo(){
		this.dialog.open(DialogoComponent,{
			width:'500px',
			data: { usuario: this.usuario }
		});
	}

	logout(){
		localStorage.removeItem('correo');
		localStorage.removeItem('perfil');
		this._router.navigate(['/']);
	}

}
