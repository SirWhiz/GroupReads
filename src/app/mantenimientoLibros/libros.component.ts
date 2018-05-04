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

	constructor(private snackBar:MatSnackBar,private _router: Router,public _librosService:LibrosService){
		this.perfil = "";
		this.noLibros = false;
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
}