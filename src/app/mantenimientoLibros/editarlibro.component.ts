import { Component } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { MatSnackBar } from '@angular/material/snack-bar';
import { LibrosService } from '../../services/libros.service';
import { Genero } from './genero';
import { Libro } from './libro';
import { Autor } from './autor';
import { GLOBAL } from '../../services/global';

@Component({
	selector: 'editarlibro',
	templateUrl: './editarlibro.component.html',
	providers: [LibrosService]
})
export class EditarLibroComponent{

	public libro: Libro;
	public perfil: string;
	public generos: Genero[];
	public autores: Autor[];
	public existeLibro: boolean;

	constructor(private activatedRoute: ActivatedRoute,private snackBar:MatSnackBar,private _router: Router,public _libroService:LibrosService){
		this.libro = new Libro("","","","","","","");
		this.perfil = "";
		this.existeLibro = false;
		this.generos = new Array();
	}

	ngOnInit(){

		if(localStorage.getItem('correo')==null || localStorage.getItem('perfil')=='n'){
			this._router.navigate(['/home']);
		}else{
			this.perfil = localStorage.getItem('perfil');
		}

		this.activatedRoute.params.forEach((params: Params) => {
			this.libro.isbn = params['isbn'];
		});

		this._libroService.getLibro(this.libro.isbn).subscribe(
			result => {
				if(result.code == 200){
					this.libro = result.data;
					this.existeLibro = true;
					console.log(this.libro);
				}
			}, error => {
				console.log(error);
			}
		);

		this._libroService.getGeneros().subscribe(
			result => {
				if(result.code == 200){
					this.generos = result.data;
				}
			},error => {
				console.log(error);
			}
		);
	}

}