import { Component } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { DialogoLibroComponent } from '../dialogoLibro/dialogolibro.component';
import { DialogoPortadaComponent } from '../dialogoPortada/dialogoportada.component';
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
	public nombreArray:any[];
	public autores: Autor[];
	public filesToUpload;
	public resultUpload;
	public existeLibro: boolean;
	public autoresElegidos: Array<any>;
	public isbnActual: string;
	public tituloActual: string;

	constructor(public dialog: MatDialog,private activatedRoute: ActivatedRoute,private snackBar:MatSnackBar,private _router: Router,public _libroService:LibrosService){
		this.libro = new Libro("","","","","","","","");
		this.perfil = "";
		this.existeLibro = true;
		this.generos = new Array();
		this.autoresLibro = new Array();
		this.autores = new Array();
		this.autoresElegidos = new Array();
		this.nombreArray = new Array();
		this.filesToUpload = new Array();
		this.isbnActual = "";
		this.tituloActual = "";
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

		var expIsbn = new RegExp(/^[0-9]{9,15}$/);
		if(expIsbn.test(this.libro.isbn)){
			this._libroService.getLibro(this.libro.isbn).subscribe(
				result => {
					console.log(result);
					if(result.code == 200){
						this.libro = result.data;
						this.obtenerAutoresTodos();
						this.obtenerAutoresLibro(this.libro.isbn);
						this.isbnActual = this.libro.isbn;
						this.tituloActual = this.libro.titulo;
					}else{
						this.existeLibro = false;
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
		}else{
			this.existeLibro = false;
		}
	}

	obtenerAutoresLibro(isbn: string){
		this._libroService.getAutoresLibro(isbn).subscribe(
			result => {
				if(result.code == 200){
					this.autoresLibro = result.data;
					for(let autor of this.autoresLibro){
						this.autoresElegidos.push(autor.id);
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

	fileChangeEvent(fileInput:any){
		this.filesToUpload = <Array<File>>fileInput.target.files;
		console.log(this.filesToUpload);
	}

	onSubmit(){
		this._libroService.updateLibro(this.libro,this.isbnActual).subscribe(
			result => {
				console.log(result);
				if(result.code == 200){
					this._libroService.updateAutores(this.autoresElegidos,this.libro.isbn).subscribe(
						result => {
							console.log(result);
							if(result.code == 200){
								this.snackBar.open("Libro actualizado correctamente", "OK", {
									duration: 2500,
								});	
							}
						}, error => {
							console.log(error);
						}
					);
				}else{
					this.snackBar.open("Error al actualizar el libro", "OK", {
						duration: 2500,
					});
				}
			}, error => {
				console.log(error);
			}
		);
	}

	onSubmitimg(){
		if(this.filesToUpload.length >= 1){
			this.nombreArray = this.filesToUpload[0].name.split('.');
			if(this.nombreArray[this.nombreArray.length-1]!='jpeg' && this.nombreArray[this.nombreArray.length-1]!='png' && this.nombreArray[this.nombreArray.length-1]!='jpg'){
				this.snackBar.open("La imagen debe ser jpeg/png", "OK", {
					duration: 2500,
				});
			}else{
				this._libroService.makeFileRequest(GLOBAL.url+'upload-cover', [], this.filesToUpload)
				.then((result)=>{
					this.resultUpload = result;
					this.libro.portada = this.resultUpload.filename;
					this.actualizarLibro();
				}, (error) => {
					console.log(error);
				});
			}
		}
	}

	actualizarLibro(){
		this._libroService.updatePortada(this.libro).subscribe(
			result => {
				this.snackBar.open("Portada actualizada correctamente", "OK", {
					duration: 2500,
				});
			}, error => {
				console.log(error);
			}
		);
	}

	public abrirDialogo(){
		this.dialog.open(DialogoLibroComponent,{
			width:'500px',
			data: { libro: this.libro }
		});
	}

	public dialogoPortada(){
		this.dialog.open(DialogoPortadaComponent,{
			width:'500px',
			data: { libro: this.libro }
		});
	}

}