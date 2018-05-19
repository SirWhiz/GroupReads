import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Club } from '../clubes/club';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Libro } from '../mantenimientoLibros/libro';

@Component({
	selector: 'elegirlibro',
	templateUrl: './elegirlibro.component.html',
	providers: [UsuariosService]
})
export class ElegirLibroComponent{

	public usuario:Usuario;
	public libros:Libro[];
	public club:Club;
	public noLibros:boolean;
	public librosElegidos:Libro[];

	constructor(public snackBar: MatSnackBar,private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.libros = new Array();
		this.club = new Club("","","","","","","");
		this.noLibros = false;
		this.librosElegidos = new Array();
	}

	ngOnInit(){

		if(localStorage.getItem('correo')==null){
			this._router.navigate(['/login']);
		}else{
			this._usuariosService.getUsuario(localStorage.getItem('correo')).subscribe(
				result => {
					if(result.code == 200){
						this.usuario = result.data;
						this.comprobarClub();
					}else{
						this._router.navigate(['/login']);
					}
				}, error => {console.log(error);}
			);
		}
	}

	comprobarClub(){
		this._usuariosService.getClub(this.usuario.id).subscribe(
			result => {
				if(result.code == 200){
					this.club = result.data;
					if(this.club.idCreador!=this.usuario.id){
						this._router.navigate(['/login']);	
					}else{
						this.obtenerLibros();
					}
				}else{
					this._router.navigate(['/login']);
				}
			}, error => {console.log(error);}
		);
	}

	add(libro:Libro){
		if(this.librosElegidos.length<3){
			if(!this.librosElegidos.includes(libro)){
				this.librosElegidos.push(libro);
			}
		}
	}

	remove(libro:Libro){
		var i=0;
		for(i=0;i<this.librosElegidos.length;i++){
			if(this.librosElegidos[i].isbn == libro.isbn){
				this.librosElegidos.splice(i,1);
			}
		}
	}

	confirmar(){
		this._usuariosService.confirmarLibros(this.club.id,this.librosElegidos[0].isbn,this.librosElegidos[1].isbn,this.librosElegidos[2].isbn).subscribe(
			result => {
				if(result.code == 200){
					this._router.navigate(['/clubes']);
    				this.snackBar.open("Se ha creado una votación con los libros elegidos", "Aceptar", {
      					duration: 2500,
    				});
				}
			}, error => {console.log(error);}
		);
	}

	elegir(libro:Libro){
		this._usuariosService.elegirLibro(this.club.id,libro.isbn).subscribe(
			result => {
				if(result.code == 200){

				}
			}, error => {console.log(error);}
		);
	}

	obtenerLibros(){
		this._usuariosService.getLibrosGenero().subscribe(
			result => {
				if(result.code == 200){
					this.libros = result.data;
				}else{
					this.noLibros = true;
				}
			}, error => {console.log(error);}
		);
	}
}