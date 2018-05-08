import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Genero } from '../mantenimientoLibros/genero';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'dialogoeditargenero',
	templateUrl: './dialogoeditargenero.component.html',
	providers: [LibrosService]
})
export class DialogoEditarGenero{

	public genero: Genero;
	public generos: Genero[];
	public nombreActual: string;

	constructor(  
		public dialogRef: MatDialogRef<DialogoEditarGenero>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _librosService: LibrosService,
        public snackBar:MatSnackBar,
        public _router:Router,)
		{
			this.genero = data.genero;
			this.generos = data.generos;

			this.nombreActual = this.genero.nombre;
		}

	onSubmit(){
		if(this.genero.nombre == this.nombreActual){
			this.dialogRef.close();
		}else{
			this.genero.nombre = this.nombreActual;
			this._librosService.editarGenero(this.genero).subscribe(
				result => {
					if(result.code == 200){
						this.dialogRef.close();
	    				this.snackBar.open("Genero modificado correctamente", "Aceptar", {
      						duration: 2500,
    					});			
					}else{
	    				this.snackBar.open("Error al modificar el genero", "Aceptar", {
      						duration: 2500,
    					});	
					}
				}, error => {
					console.log(error);
				}
			);
		}
	}

	borrar(){
		this._librosService.deleteGenero(this.genero.id).subscribe(
			result => {
				if(result.code == 200){
					this.dialogRef.close();
    				this.snackBar.open("Genero borrado correctamente", "Aceptar", {
  						duration: 2500,
					});
					var index = this.generos.indexOf(this.genero, 0);
					if (index > -1) {
					   this.generos.splice(index, 1);
					}
				}else{
					this.dialogRef.close();
    				this.snackBar.open("Error al borrar el genero", "Aceptar", {
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