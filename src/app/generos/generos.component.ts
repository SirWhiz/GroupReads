import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Genero } from '../mantenimientoLibros/genero';
import { DialogoNuevoGenero } from './dialogonuevogenero.component';
import { DialogoEditarGenero } from './dialogoeditargenero.component';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'generos',
	templateUrl: './generos.component.html',
	providers: [LibrosService]
})
export class GenerosComponent{

	public perfil: string;	
	public generos: Genero[];
	public noGeneros: boolean;

	constructor(private _router: Router,private _librosService: LibrosService,public dialog: MatDialog){
		this.perfil = "";
		this.noGeneros = false;
		this.generos = new Array();
	}

	ngOnInit(){

		if(localStorage.getItem('perfil')==null || localStorage.getItem('perfil')=='n'){
			this._router.navigate(['/login']);
		}

		if(localStorage.getItem('perfil')=='a'){
			this.perfil = 'a';
		}else if(localStorage.getItem('perfil')=='c'){
			this.perfil = 'c';
		}

		this._librosService.getGeneros().subscribe(
			result => {
				if(result.code == 200){
					this.generos = result.data;
				}else{
					this.noGeneros = true;
				}
			}, error => {
				console.log(error);
			}
		);
	}

	editar(genero: Genero){
		console.log(genero);
		this.dialog.open(DialogoEditarGenero,{
			width:'500px',
			data: { genero: genero, generos: this.generos }
		});
	}

	nuevo(){
		this.dialog.open(DialogoNuevoGenero,{
			width:'500px',
			data: { generos: this.generos }
		});
	}
}