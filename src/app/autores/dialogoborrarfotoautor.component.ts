import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Autor } from '../mantenimientoLibros/autor';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'dialogoborrarfotoautor',
	templateUrl: './dialogoborrarfotoautor.component.html',
	providers: [LibrosService]
})
export class DialogoBorrarFotoAutor{

	public autor: Autor;

	constructor(  
		public dialogRef: MatDialogRef<DialogoBorrarFotoAutor>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _librosService: LibrosService,
        public snackBar:MatSnackBar,
        public _router:Router,)
		{
			this.autor = data.autor;
	}

	borrar(){
		this._librosService.deleteFotoAutor(this.autor).subscribe(
			result => {
				if(result.code == 200){
					this.dialogRef.close();
					this.snackBar.open("Imagen borrada correctamente", "OK", {
						duration: 2500,
					});
					this.autor.foto = '';
				}else{
					this.dialogRef.close();
					this.snackBar.open("Error al borrar la imagen", "OK", {
						duration: 2500,
					});
				}
			}, error => {
				console.log(error);
			}
		);
	}

    public close(){
        this.dialogRef.close();
    }
}