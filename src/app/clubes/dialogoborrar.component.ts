import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Club } from './club';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Genero } from '../mantenimientoLibros/genero';

@Component({
	selector: 'dialogoborrar',
	templateUrl: './dialogoborrar.component.html',
	providers: [UsuariosService]
})
export class DialogoBorrarClubComponent{

    public club:Club;

	constructor(
        public dialogRef: MatDialogRef<DialogoBorrarClubComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _usuariosService: UsuariosService,
        public snackBar:MatSnackBar,
        public _router:Router,
    ){
        this.club = data.club;
    }

    delete(){
        this._usuariosService.deleteClub(this.club.id).subscribe(
            result => {
                if(result.code == 200){
                    this.dialogRef.close();
                    this.snackBar.open("Club borrado correctamente", "Aceptar", {
                        duration: 2500,
                    });
                    this._router.navigate(['/home']);
                }
            }, error => {console.log(error);}
        );
    }

    public close(){
        this.dialogRef.close();
    }

}
