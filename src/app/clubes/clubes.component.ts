import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Club } from './club';
import { DialogoEditarClubComponent } from './dialogoeditar.component';
import { DialogoAbandonarComponent } from './dialogoabandonar.component';

@Component({
	selector: 'clubes',
	templateUrl: './clubes.component.html',
	providers: [UsuariosService]
})
export class ClubesComponent{

	public usuario:Usuario;
	public miembros:Usuario[];
	public club:Club;
	public clubes:Club[];
	public noClub:boolean;

	constructor(public dialog: MatDialog,public snackBar: MatSnackBar,private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.miembros = new Array();
		this.club = new Club("","","","","","");
		this.clubes = new Array();
		this.noClub = false;
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
					this._usuariosService.comprobarTieneClub(this.usuario.id).subscribe(
						result => {
							if(result.code == 200){
								this.noClub = false;
								this.getClub();
							}else{
								this.noClub = true;
								this.getClubesDisponibles();
							}
						}, error => {console.log(error);}
					);
				}
			}, error => {console.log(error);}
		);
	}

	getClub(){
		this._usuariosService.getClub(this.usuario.id).subscribe(
			result => {
				if(result.code == 200){
					this.club = result.data;
					this.getMiembros();
				}
			}, error => {
				console.log(error);
			}
		);
	}

	getClubesDisponibles(){
		this._usuariosService.getClubesDisponibles().subscribe(
			result => {
				if(result.code == 200){
					this.clubes = result.data;
				}
			}, error => {console.log(error);}
		);
	}

	getMiembros(){
		this._usuariosService.getMiembros(this.club.id).subscribe(
			result => {
				if(result.code == 200){
					this.miembros = result.data;
					console.log(this.miembros);
				}
			}, error => {console.log(error);}
		);
	}

	dialogoEditar(){
		this.dialog.open(DialogoEditarClubComponent,{
			width:'500px',
			data: { club: this.club }
		});
	}

	abandonar(){
		this.dialog.open(DialogoAbandonarComponent,{
			width:'500px',
			data: { club: this.club,usuario: this.usuario.id,noclub: this.noclub }
		});
	}

	unirse(club:Club){
		this._usuariosService.unirseClub(this.usuario.id,club.id).subscribe(
			result => {
				if(result.code == 200){
    				this.snackBar.open("¡Te acabas de unir a "+club.nombre+"!", "Aceptar", {
      					duration: 2500,
    				});
    				this.club = club;
    				this.noClub = false;
    				this.getMiembros();
				}
			}, error => {console.log(error);}
		);
	}

}