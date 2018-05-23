import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Libro } from '../mantenimientoLibros/libro';
import { Usuario } from '../registro/usuario';
import { Club } from '../clubes/club';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { DialogoMasInformacionComponent } from './dialogomasinformacion.component';

@Component({
	selector:'librosleidos',
	templateUrl:'./librosleidos.component.html',
	providers: [LibrosService]
})
export class LibrosLeidosComponent{

	public librosLeidos:Libro[];
	public recomendados:Libro[];
	public usuario:Usuario;
	public club:Club;

	constructor(private _router: Router,private _librosService: LibrosService,public dialog: MatDialog){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.club = new Club("","","","","","","");
		this.librosLeidos = new Array();
		this.recomendados = new Array();
	}

	ngOnInit(){

		this._librosService.getUsuario(localStorage.getItem('correo')).subscribe(
			result => {
				if(result.code == 200){
					this.usuario = result.data;
					this._librosService.getClub(this.usuario.id).subscribe(
						result => {
							if(result.code == 200){
								this.club = result.data;
								this.obtenerLibrosLeidos(this.club.id);
								this.obtenerLibrosRecomendados(this.club.id);
							}
						}, error => {console.log(error);}
					);
				}
			}, error => {console.log(error);}
		);
	}

	obtenerLibrosLeidos(idclub:string){
		this._librosService.getLibrosLeidos(idclub).subscribe(
			result => {
				if(result.code == 200){
					this.librosLeidos = result.data;
				}
			}, error => {console.log(error);}
		);
	}

	obtenerLibrosRecomendados(idclub:string){
		this._librosService.getLibrosRecomendados(idclub).subscribe(
			result => {
				console.log(result);
				if(result.code == 200){
					this.recomendados = result.data;
				}
			}, error => {console.log(error);}
		);
	}

	masInformacion(libro:Libro){
		this.dialog.open(DialogoMasInformacionComponent,{
			width:'500px',
			height:'600px',
			data: { club:this.club.id,libro: libro }
		});
	}
}