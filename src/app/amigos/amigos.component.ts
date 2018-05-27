import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { UsuarioP } from '../registro/usuariop';
import { Usuario } from '../registro/usuario';
import { DialogoSolicitudesComponent } from '../dialogoSolicitudes/dialogosolicitudes.component'
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { ChatComponent } from '../chat/chat.component';

@Component({
	selector: 'amigos',
	templateUrl: './amigos.component.html',
	providers: [UsuariosService]
})
export class AmigosComponent{

	public usuario:UsuarioP;
	public amigos: Usuario[];
	public existeAmigos: boolean;

	constructor(private _router: Router,private _usuariosService: UsuariosService,public dialog: MatDialog){
		this.usuario = new UsuarioP("","","","","","","","","","","",0);
		this.amigos = new Array();
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
								this.usuario.peticiones = result.data;
							}
						}
					);
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
				}
			}, error => {
				console.log(error);
			}
		);
	}

	abrirChat(amigo:Usuario){
		this.dialog.open(ChatComponent,{
			width:'600px',
			height:'600px',
			data: { usuario: this.usuario, amigo: amigo }
		});
	}

	verSolicitudes(){
		this.dialog.open(DialogoSolicitudesComponent,{
			width:'500px',
			data: { usuario: this.usuario, amigos: this.amigos }
		});
	}
}