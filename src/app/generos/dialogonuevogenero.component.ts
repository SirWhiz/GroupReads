import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Genero } from '../mantenimientoLibros/genero';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'dialogonuevogenero',
	templateUrl: './dialogonuevogenero.component.html',
	providers: [LibrosService]
})
export class DialogoNuevoGenero{

	public perfil: string;	
	public generos: Genero[];
	public genero: Genero;

	constructor(  
		public dialogRef: MatDialogRef<DialogoNuevoGenero>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _librosService: LibrosService,
        public snackBar:MatSnackBar,
        public _router:Router,)
		{
			this.perfil = "";
			this.generos = data.generos;
			this.genero = new Genero("","");
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
	}

	onSubmit(){

		this._librosService.addGenero(this.genero).subscribe(
			result => {
				if(result.code == 200){
					this.dialogRef.close();
    				this.snackBar.open("Genero añadido correctamente", "Aceptar", {
      					duration: 2500,
    				});
    				this.generos.push(this.genero);
				}else{
					this.dialogRef.close();
    				this.snackBar.open("Error al añadir el genero", "Aceptar", {
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