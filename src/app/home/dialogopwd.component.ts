import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Genero } from '../mantenimientoLibros/genero';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'dialogopwd',
	templateUrl: './dialogopwd.component.html',
	providers: [UsuariosService]
})
export class DialogoPwdComponent{

	constructor(  
		public dialogRef: MatDialogRef<DialogoPwdComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _usuariosService: UsuariosService,
        public snackBar:MatSnackBar,
        public _router:Router,)
		{

		}

	cambiar(){
		this.dialogRef.close();
		this._router.navigate(['/configuracion']);
	}
}