import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Libro } from '../mantenimientoLibros/libro';
import { Pais } from '../registro/pais';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'dialogolibro',
	templateUrl: './dialogolibro.component.html',
	providers: [LibrosService]
})
export class DialogoLibroComponent{

    public libro:Libro;

	constructor(
        public dialogRef: MatDialogRef<DialogoLibroComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _librosService: LibrosService,
        public snackBar:MatSnackBar,
        public _router:Router,
    ){
        this.libro = data.libro;
    }

    public close(){
        this.dialogRef.close();
    }

    public borrar(){
        this._librosService.borrarLibro(this.libro).subscribe(
            result => {
                if(result.code == 200){
                    this.dialogRef.close();
                    this.snackBar.open("Libro borrado correctamente", "Aceptar", {
                        duration: 2500,
                    });
                    this._router.navigate(['/libros']);
                }
            }, error => {
                console.log(error);
            }
        );
    }

}
