import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Club } from './club';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Genero } from '../mantenimientoLibros/genero';

@Component({
	selector: 'dialogoexpulsar',
	templateUrl: './dialogoexpulsar.component.html',
	providers: [UsuariosService]
})
export class DialogoExpulsarComponent{

    public club:Club;
    public miembro:Usuario;
    public miembros:Usuario[];

	constructor(
        public dialogRef: MatDialogRef<DialogoExpulsarComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _usuariosService: UsuariosService,
        public snackBar:MatSnackBar,
        public _router:Router,
    ){
        this.club = data.club;
        this.miembro = data.miembro;
        this.miembros = data.miembros;
    }

    expulsar(){
        this._usuariosService.expulsarMiembro(this.miembro.id,this.club.id).subscribe(
            result => {
                if(result.code == 200){
                    this.dialogRef.close();
                    this.snackBar.open("Usuario expulsado correctamente", "Aceptar", {
                        duration: 2500,
                    });
                    var i=0;
                    for(i=0;i<this.miembros.length;i++){
                        if(this.miembros[i].id == this.miembro.id){
                            this.miembros.splice(i,1);
                        }
                    }
                }
            }, error => {console.log(error);}
        );
    }

    public close(){
        this.dialogRef.close();
    }

}
