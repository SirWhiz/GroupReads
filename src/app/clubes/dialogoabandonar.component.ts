import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Club } from './club';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Genero } from '../mantenimientoLibros/genero';

@Component({
	selector: 'abandonarclub',
	templateUrl: './dialogoabandonar.component.html',
	providers: [UsuariosService]
})
export class DialogoAbandonarComponent{

    public club:Club;
    public id:string;
    public noclub:boolean;

	constructor(
        public dialogRef: MatDialogRef<DialogoAbandonarComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _usuariosService: UsuariosService,
        public snackBar:MatSnackBar,
        public _router:Router,
    ){
        this.club = data.club;
        this.id = data.usuario;
        this.noclub = data.noclub;
    }

    abandonar(){
        this._usuariosService.abandonarClub(this.id,this.club.id).subscribe(
            result => {
                if(result.code == 200){
                    this.dialogRef.close();
                    this._router.navigate(['/home']);
                    this.snackBar.open("Has dejado el club correctamente", "Aceptar", {
                        duration: 2500,
                    });
                }
            }, error => {console.log(error)}
        );
    }

    public close(){
        this.dialogRef.close();
    }

}
