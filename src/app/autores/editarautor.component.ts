import { Component } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Autor } from '../mantenimientoLibros/autor';
import { Pais } from '../registro/pais';
import { MatSnackBar } from '@angular/material/snack-bar';
import { DialogoBorrarAutor } from './dialogoborrarautor.component';
import { DialogoBorrarFotoAutor } from './dialogoborrarfotoautor.component';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { GLOBAL } from '../../services/global';

@Component({
	selector: 'editarautor',
	templateUrl: './editarautor.component.html',
	providers: [LibrosService]
})
export class EditarAutorComponent{

	public perfil: string;
	public autor: Autor;
	public existeAutor: boolean;
	public nombreActual: string;
	public fechaMostrar: string;
	public paises: Pais[];
	public filesToUpload;
	public resultUpload;
	public nombreArray:any[];
	public fechaArray: Array<any>;

	constructor(public dialog: MatDialog,public snackBar: MatSnackBar,private activatedRoute: ActivatedRoute,private _router: Router,private _librosService: LibrosService){
		this.perfil = "";
		this.nombreActual = "";
		this.fechaMostrar = "";
		this.fechaArray = new Array();
		this.autor = new Autor("","","","","");
		this.paises = new Array();
		this.nombreArray = new Array();
		this.filesToUpload = new Array();
		this.existeAutor = true;
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

		this.activatedRoute.params.forEach((params: Params) => {
			this.autor.id = params['id'];
		});

		this._librosService.getAutor(this.autor.id).subscribe(
			result => {
				if(result.code == 200){
					this.autor = result.data;
					this.nombreActual = this.autor.nombre_ape;
					this.fechaArray = this.autor.fecha_nacimiento.split('-');
					this.fechaMostrar = this.fechaArray[2]+'/'+this.fechaArray[1]+'/'+this.fechaArray[0];
					var fechaSeparada = this.autor.fecha_nacimiento.split('-');
					this.autor.fecha_nacimiento = fechaSeparada[0]+'/'+fechaSeparada[1]+'/'+fechaSeparada[2];
					this._librosService.getPaises().subscribe(
						result => {
							if(result.code == 200){
								this.paises = result.data;
							}
						}, error => {
							console.log(error);
						}
					);
				}else{
					this.existeAutor = false;
				}
			}, error => {
				console.log(error);
			}
		);
	}

	abrirDialogo(){
		this.dialog.open(DialogoBorrarAutor,{
			width:'500px',
			data: { autor: this.autor }
		});
	}

	onSubmit(){
		this._librosService.updateAutor(this.autor).subscribe(
			result => {
				console.log(result);
				if(result.code == 200) {
    				this.snackBar.open("Autor modificado correctamente", "Aceptar", {
      					duration: 2500,
    				});
				}else{
    				this.snackBar.open("Error al modificar el autor", "Aceptar", {
      					duration: 2500,
    				});
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

	onSubmitimg(){
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
					this.actualizarAutor();
				}, (error) => {
					console.log(error);
				});
			}
		}
	}

	actualizarAutor(){
		this._librosService.updateFotoAutor(this.autor).subscribe(
			result => {
				if(result.code == 200){
					this.snackBar.open("Imagen actualizada correctamente", "OK", {
						duration: 2500,
					});
				}else{
					this.snackBar.open("Error al actualizar la imagen", "OK", {
						duration: 2500,
					});		
				}
			}, error => {
				console.log(error);
			}
		);
	}

	dialogoFoto(){
		this.dialog.open(DialogoBorrarFotoAutor,{
			width:'500px',
			data: { autor: this.autor }
		});
	}


}