import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Club } from './club';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Genero } from '../mantenimientoLibros/genero';

@Component({
	selector: 'nuevoclub',
	templateUrl: './nuevoclub.component.html',
	providers: [UsuariosService]
})
export class NuevoClubComponent{

	public usuario:Usuario;
	public club:Club;
	public generos:Genero[];

	constructor(public snackBar: MatSnackBar,private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.club = new Club("","","","","","","");
		this.generos = new Array();
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
					this.cargarGeneros();
				}
			}, error => {console.log(error);}
		);
	}

	onSubmit(){
		this.club.creador = this.usuario.id;
		
		this._usuariosService.addClub(this.club).subscribe(
			result => {
				if(result.code == 200){
					this._router.navigate(['/clubes']);
    				this.snackBar.open("Club registrado correctamente", "Aceptar", {
      					duration: 2500,
    				});
				}
			}, error => {console.log(error);}
		);
	}

	cargarGeneros(){
		this._usuariosService.getGeneros().subscribe(
			result => {
				if(result.code == 200){
					this.generos = result.data;
				}
			}, error => {console.log(error)}
		);
	}

	cambiar(event,tipo:string){
		if(event.target.checked){
			this.club.privacidad = tipo;
		}
	}
}