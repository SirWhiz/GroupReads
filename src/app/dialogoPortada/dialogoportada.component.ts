import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Libro } from '../mantenimientoLibros/libro';
import { Pais } from '../registro/pais';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'dialogoportada',
	templateUrl: './dialogoportada.component.html',
	providers: [LibrosService]
})
export class DialogoPortadaComponent{

    public libro:Libro;

	constructor(
        public dialogRef: MatDialogRef<DialogoPortadaComponent>,
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
        this._librosService.borrarPortada(this.libro.isbn).subscribe(
            result => {
                if(result.code == 200){
                    this.dialogRef.close();
                    this.libro.portada = '';
                    this.snackBar.open("Portada borrada correctamente", "OK", {
                        duration: 2500,
                    });
                }else{
                    this.dialogRef.close();
                    this.snackBar.open("Error al borrar la portada", "OK", {
                        duration: 2500,
                    });
                }
            }, error => {
                console.log(error);
            }
        );
    }

}
