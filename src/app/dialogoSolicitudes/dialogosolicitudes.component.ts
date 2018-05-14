import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { UsuarioP } from '../registro/usuariop';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'dialogosolicitudes',
	templateUrl: './dialogosolicitudes.component.html',
	providers: [UsuariosService]
})
export class DialogoSolicitudesComponent{

    public solicitudes:Usuario[];
    public usuario:UsuarioP;
    public amigos:Usuario[];

	constructor(
        public dialogRef: MatDialogRef<DialogoSolicitudesComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _usuariosService: UsuariosService,
        public snackBar:MatSnackBar,
        public _router:Router,
    ){
        this.solicitudes = new Array();
        this.usuario = data.usuario;
        this.amigos = data.amigos;
    }

    ngOnInit(){
        this._usuariosService.getUsuariosSolicitudes(this.usuario.id).subscribe(
            result => {
                if(result.code == 200){
                    this.solicitudes = result.data;
                }
            }, error => {console.log(error)}
        );
    }

    aceptarSolicitud(solicitud: Usuario){
        this._usuariosService.aceptarSolicitud(this.usuario.id,solicitud.id).subscribe(
            result => {
                if(result.code == 200){
                    var i=0;
                    for(i=0;i<this.solicitudes.length;i++){
                        if(this.solicitudes[i].id == solicitud.id){
                            this.solicitudes.splice(i,1);
                        }
                    }
                    this.usuario.peticiones--;
                    this.amigos.push(solicitud);
                    this.snackBar.open("Peticion aceptada", "Aceptar", {
                        duration: 2500,
                    });
                    if(this.solicitudes.length==0){
                        this.dialogRef.close();
                    }
                }
            }, error => {console.log(error)}
        );
    }

    deleteSolicitud(solicitud: Usuario){
        this._usuariosService.borrarSolicitud(this.usuario.id,solicitud.id).subscribe(
            result => {
                if(result.code == 200){
                    var i=0;
                    for(i=0;i<this.solicitudes.length;i++){
                        if(this.solicitudes[i].id == solicitud.id){
                            this.solicitudes.splice(i,1);
                        }
                    }
                    this.usuario.peticiones--;
                    this.snackBar.open("Peticion rechazada", "Aceptar", {
                        duration: 2500,
                    });
                    if(this.solicitudes.length==0){
                        this.dialogRef.close();
                    }
                }
            }, error => {console.log(error)}
        );
    }
}