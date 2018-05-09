import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Autor } from '../mantenimientoLibros/autor';

@Component({
	selector: 'autores',
	templateUrl: './autores.component.html',
	providers: [LibrosService]
})
export class AutoresComponent{

	public perfil: string;
	public autores: Autor[];
	public noAutores: boolean;
	public filtro: string;

	constructor(private snackBar:MatSnackBar,private _router: Router,private _librosService: LibrosService){
		this.perfil = "";
		this.filtro = "";
		this.noAutores = false;
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

		this._librosService.getAutores().subscribe(
			result => {
				if(result.code == 200){
					this.autores = result.data;
				}else{
					this.noAutores = true;
				}
			}, error => {
				console.log(error);
			}
		);
	}

	editar(autor:Autor){
		this._router.navigate(['/editar-autor/'+autor.id]);
	}

	filtrarAutores(){
		if(this.filtro!=''){
			this._librosService.getAutoresFiltro(this.filtro).subscribe(
				result => {
					if(result.code == 200){
						this.autores = result.data;
					}else{
						this.snackBar.open("No se han encontrado autores con ese criterio", "Aceptar", {
	  						duration: 2500,
	    				});
					}
				}, error => {
					console.log(error);
				}
			);
		}else{
			this._librosService.getAutores().subscribe(
				result => {
					if(result.code == 200){
						this.autores = result.data;
					}else{
						this.noAutores = true;
					}
				}, error => {
					console.log(error);
				}
			);	
		}
	}

}