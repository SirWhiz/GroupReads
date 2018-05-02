import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Pais } from '../registro/pais';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'dialogo',
	templateUrl: './dialogo.component.html',
	providers: [UsuariosService]
})
export class DialogoComponent{

    public usuario:Usuario;

	constructor(
        public dialogRef: MatDialogRef<DialogoComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _usuariosService: UsuariosService,
        public snackBar:MatSnackBar,
        public _router:Router,
    ){
        this.usuario = new Usuario("","","","","","","","","","","");
    }

    public close(){
        this.dialogRef.close();
    }

    public borrar(){
        this._usuariosService.getUsuario(localStorage.getItem('correo')).subscribe(
			result => {
				if(result.code==200){
                    this.usuario = result.data;
                    this._usuariosService.borrarImagen(this.usuario).subscribe(
                        response => {
                            if(response.code==200){
                                //Usuario registrado correctamente
                                this.dialogRef.close();
                                this.usuario.foto = '';
                                this.snackBar.open("Imagen borrada correctamente", "OK", {
                                      duration: 2500,
                                });
                                this.data.usuario.foto = '';
                            }else{
                                //Error al registrar usuario
                                this.dialogRef.close();
                                this.snackBar.open("Error al borrar la imagen", "OK", {
                                      duration: 2500,
                                });
                            }
                        },
                        error => {
                            console.log(<any>error);
                        });
				}
			},
			error => {
				console.log(<any>error);
			}
        );
    }

}
