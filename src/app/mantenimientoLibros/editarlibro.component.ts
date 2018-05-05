import { Component } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { DialogoLibroComponent } from '../dialogoLibro/dialogolibro.component';
import { MatSnackBar } from '@angular/material/snack-bar';
import { LibrosService } from '../../services/libros.service';
import { Genero } from './genero';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
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
	public autoresLibro: Autor[];
	public autores: Autor[];
	public existeLibro: boolean;
	public autoresElegidos: Array<any>;

	constructor(public dialog: MatDialog,private activatedRoute: ActivatedRoute,private snackBar:MatSnackBar,private _router: Router,public _libroService:LibrosService){
		this.libro = new Libro("","","","","","","");
		this.perfil = "";
		this.existeLibro = false;
		this.generos = new Array();
		this.autoresLibro = new Array();
		this.autores = new Array();
		this.autoresElegidos = new Array();
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
					this.obtenerAutoresTodos();
					this.obtenerAutoresLibro(this.libro.isbn);
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

	obtenerAutoresLibro(isbn: string){
		this._libroService.getAutoresLibro(isbn).subscribe(
			result => {
				if(result.code == 200){
					this.autoresLibro = result.data;
					for(let autor of this.autoresLibro){
						this.autoresElegidos.push(autor.idAutor);
					}
				}
			}, error => {
				console.log(error);
			}
		);
	}

	obtenerAutoresTodos(){
		this._libroService.getAutores().subscribe(
			result => {
				if(result.code == 200){
					this.autores = result.data;
				}
			}, error => {
				console.log(error);
			}
		);
	}

	cambiar(event,id:any){
		if(event.target.checked){
			this.autoresElegidos.push(id);
		}else{
			var i=0;
			for(i=0;i<this.autoresElegidos.length;i++){
				if(this.autoresElegidos[i] == id){
					this.autoresElegidos.splice(i,1);
				}
			}
		}
	}

	public abrirDialogo(){
		this.dialog.open(DialogoLibroComponent,{
			width:'500px',
			data: { libro: this.libro }
		});
	}

}