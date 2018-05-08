import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Autor } from '../mantenimientoLibros/autor';
import { Pais } from '../registro/pais';
import { GLOBAL } from '../../services/global';

@Component({
	selector: 'nuevoautor',
	templateUrl: './nuevoautor.component.html',
	providers: [LibrosService]
})
export class NuevoAutorComponent{

	public perfil: string;
	public autor: Autor;
	public paises: Pais[];
	public filesToUpload;
	public resultUpload;
	public nombreArray:any[];

	constructor(private snackBar:MatSnackBar,private _router: Router,private _librosService: LibrosService){
		this.perfil = "";
		this.autor = new Autor("","","","","");
		this.paises = new Array();
		this.filesToUpload = new Array();
		this.nombreArray = new Array();
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

		this._librosService.getPaises().subscribe(
			result => {
				if(result.code == 200){
					this.paises = result.data;
				}
			}, error => {
				console.log(error);
			}
		);
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
				this._librosService.makeFileRequest(GLOBAL.url+'upload-autor', [], this.filesToUpload)
					.then((result)=>{
						this.resultUpload = result;
						this.autor.foto = this.resultUpload.filename;
						this.saveAutor();
					}, (error) => {
						console.log(error);
				});
			}
		}else{
			this.saveAutor();
		}
	}

	saveAutor(){
		this._librosService.addAutor(this.autor).subscribe(
			result => {
				if(result.code == 200){
					this.snackBar.open("Autor registrado correctamente", "OK", {
						duration: 2500,
					});
					this._router.navigate(['/autores']);
				}else{
					this.snackBar.open("Error al registrar el autor", "OK", {
						duration: 2500,
					});
				}
			}, error => {
				console.log(error);
			}
		);

	}


}