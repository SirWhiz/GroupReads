import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { MatSnackBar } from '@angular/material/snack-bar';
import { LibrosService } from '../../services/libros.service';
import { Genero } from './genero';
import { Libro } from './libro';
import { Autor } from './autor';
import { GLOBAL } from '../../services/global';
declare var $ :any;

@Component({
	selector: 'nuevolibros',
	templateUrl: './nuevolibro.component.html',
	providers: [LibrosService]
})
export class NuevoLibroComponent{

	public libro: Libro;
	public perfil: string;
	public generos: Genero[];
	public autores: Autor[];
	public autoresLibro: Array<any>;
	public filesToUpload;
	public resultUpload;
	public nombreArray:any[];
	public isbnRepetido: boolean;

	constructor(private snackBar:MatSnackBar,private _router: Router,public _libroService:LibrosService){
		this.libro = new Libro("","","","","","","");
		this.perfil = "";
		this.isbnRepetido = false;
		this.autoresLibro = new Array();
		this.filesToUpload = new Array();
		this.nombreArray = new Array();
	}

	ngOnInit(){

		if(localStorage.getItem('correo')==null || localStorage.getItem('perfil')=='n'){
			this._router.navigate(['/home']);
		}else{
			this.perfil = localStorage.getItem('perfil');
		}

		this._libroService.getGeneros().subscribe(
			result => {
				if(result.code == 200){
					this.generos = result.data;
				}
			},error => {
				console.log(error);
			}
		);

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

	comprobarisbn(){
		this._libroService.comprobarisbn(this.libro.isbn).subscribe(
			result => {
				if(result.code == 200){
					this.isbnRepetido = true;
				}else{
					this.isbnRepetido = false;
				}
			}, error => {
				console.log(error);
			}
		);
	}

	cambiar(event,id:any){
		if(event.target.checked){
			this.autoresLibro.push(id);
		}else{
			var i=0;
			for(i=0;i<this.autoresLibro.length;i++){
				if(this.autoresLibro[i] == id){
					this.autoresLibro.splice(i,1);
				}
			}
		}
	}

	fileChangeEvent(fileInput:any){
		this.filesToUpload = <Array<File>>fileInput.target.files;
		console.log(this.filesToUpload);
	}

	onSubmit(){

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
						this.saveLibro();
					}, (error) => {
						console.log(error);
				});
			}
		}else{
			this.saveLibro();
		}
	}

	saveLibro(){
		console.log(this.libro);
		var fecha = new Date();
		var dia = fecha.getDate();
		var mes = fecha.getMonth()+1;
		var anio = fecha.getFullYear();
		this.libro.fechaAlta = anio+'-'+mes+'-'+dia;
		this._libroService.registrarLibro(this.libro).subscribe(
			response => {
				console.log(response);
				if(response.code == 200){
					//Libro registrado correctamente
					this.guardarAutores(this.libro.isbn);
					this._router.navigate(['/libros']);
				}else{
					console.log(response);
				}
			}, error => {
				console.log(error);
			}
		);
	}

	guardarAutores(isbn:any){
		this._libroService.asignarAutores(this.autoresLibro,isbn).subscribe(
			response => {
				console.log(response);
			}, error => {
				console.log(error);
			}
		);
	}
}