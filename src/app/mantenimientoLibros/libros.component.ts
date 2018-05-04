import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { MatSnackBar } from '@angular/material/snack-bar';
import { LibrosService } from '../../services/libros.service';
import { Libro } from './libro';

@Component({
	selector: 'libros',
	templateUrl: './libros.component.html',
	providers: [LibrosService]
})
export class LibrosComponent{

	public libros: Libro[];
	public perfil: string;
	public noLibros: boolean;
	public filtro: string;

	constructor(private snackBar:MatSnackBar,private _router: Router,public _librosService:LibrosService){
		this.perfil = "";
		this.noLibros = false;
		this.filtro = "";
	}

	ngOnInit(){
		if(localStorage.getItem('correo')==null || localStorage.getItem('perfil')=='n'){
			this._router.navigate(['/home']);
		}else{
			this.perfil = localStorage.getItem('perfil');
		}

		this._librosService.getLibros().subscribe(
			result => {
				if(result.code == 200){
					this.libros = result.data;
				}else{
					this.noLibros = true;
				}
			}, error => {
				console.log(error);
			}
		);
	}

	editar(isbn:string){
		this._router.navigate(['/editar-libro/'+isbn]);
	}

	filtrarLibros(){
		if(this.filtro!=''){
			this._librosService.getLibrosFiltro(this.filtro).subscribe(
				result => {
					if(result.code == 200){
						this.libros = result.data;
					}else{
						this.snackBar.open("No se han encontrado libros con ese criterio", "Aceptar", {
	  						duration: 2500,
	    				});
					}
				}, error => {
					console.log(error);				
				}
			);
		}else{
			this._librosService.getLibros().subscribe(
				result => {
					if(result.code == 200){
						this.libros = result.data;
					}else{
						this.noLibros = true;
					}
				}, error => {
					console.log(error);
				}
			);
		}
	}
}