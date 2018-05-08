import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Autor } from '../mantenimientoLibros/autor';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'dialogoborrarautor',
	templateUrl: './dialogoborrarautor.component.html',
	providers: [LibrosService]
})
export class DialogoBorrarAutor{

	public autor: Autor;

	constructor(  
		public dialogRef: MatDialogRef<DialogoBorrarAutor>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _librosService: LibrosService,
        public snackBar:MatSnackBar,
        public _router:Router,)
		{
			this.autor = data.autor;
			console.log(this.autor);
	}

	borrar(){
		this._librosService.deleteAutor(this.autor.id).subscribe(
			result => {
				if(result.code == 200){
					this.dialogRef.close();
    				this.snackBar.open("Autor borrado correctamente", "Aceptar", {
      					duration: 2500,
    				});
    				this._router.navigate(['/autores']);
				}else{
					this.dialogRef.close();
    				this.snackBar.open("Error al borrar el autor", "Aceptar", {
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