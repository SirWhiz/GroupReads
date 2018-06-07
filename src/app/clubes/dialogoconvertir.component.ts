import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Club } from './club';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Genero } from '../mantenimientoLibros/genero';

@Component({
	selector: 'convertir',
	templateUrl: './dialogoconvertir.component.html',
	providers: [UsuariosService]
})
export class DialogoConvertirComponent{

    public usuario:Usuario;
    public club:Club;

	constructor(
        public dialogRef: MatDialogRef<DialogoConvertirComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _usuariosService: UsuariosService,
        public snackBar:MatSnackBar,
        public _router:Router,
    ){
        this.usuario = data.usuario;
        this.club = data.club;
    }

    convertir(){
        this._usuariosService.convertirUsuario(this.usuario,this.club.id).subscribe(
            result => {
                if(result.code == 200){
                    this.dialogRef.close();
                    this.snackBar.open(this.usuario.nombre+" es ahora el líder", "Aceptar", {
                        duration: 2500,
                    });
                    this.club.idCreador=this.usuario.id;
                }else{
                    this.dialogRef.close();
                    this.snackBar.open("Error al hacer líder a "+this.usuario.nombre, "Aceptar", {
                        duration: 2500,
                    });   
                }
            }, error => {console.log(error);}
        );
    }

    public close(){
        this.dialogRef.close();
    }

}
