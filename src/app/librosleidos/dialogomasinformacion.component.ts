import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Libro } from '../mantenimientoLibros/libro';
import { Autor } from '../mantenimientoLibros/autor';
import { Comentario } from '../clubes/comentarios';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'dialogomasinformacion',
	templateUrl: './dialogomasinformacion.component.html',
	providers: [LibrosService]
})
export class DialogoMasInformacionComponent{

	public libro:Libro;
	public autores:Autor[];
	public opiniones:Comentario[];
	public idclub:string;

	constructor(
		public dialogRef: MatDialogRef<DialogoMasInformacionComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _librosService: LibrosService,
        public snackBar:MatSnackBar,
        public _router:Router,)
		{
			this.idclub = data.club;
			this.libro = data.libro;
			this.opiniones = new Array();
		}

	ngOnInit(){

		this._librosService.getFullAutores(this.libro.isbn).subscribe(
			result => {
				if(result.code == 200){					
					this.autores = result.data;
				}
			}, error => {console.log(error);}
		);

		this._librosService.getComentarios(this.idclub,this.libro.isbn).subscribe(
			result => {
				if(result.code == 200){
					this.opiniones = result.data;
				}
			}, error => {console.log(error);}
		);

	}

    public close(){
        this.dialogRef.close();
    }
}