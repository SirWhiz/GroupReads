import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Club } from './club';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Genero } from '../mantenimientoLibros/genero';

@Component({
	selector: 'editarclub',
	templateUrl: './dialogoeditar.component.html',
	providers: [UsuariosService]
})
export class DialogoEditarClubComponent{

    public club:Club;
    public nombreMostrar:string;
    public generos:Genero[];

	constructor(
        public dialogRef: MatDialogRef<DialogoEditarClubComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _usuariosService: UsuariosService,
        public snackBar:MatSnackBar,
        public _router:Router,
    ){
        this.club = data.club;
        console.log(this.club);
        this.nombreMostrar = this.club.nombreClub;
    }

    ngOnInit(){
        this._usuariosService.getGeneros().subscribe(
            result => {
                if(result.code == 200){
                    this.generos = result.data;
                }
            }, error => {console.log(error)}
        );
    }

    cambiar(event,tipo:string){
        if(event.target.checked){
            this.club.privacidad = tipo;
        }
    }

    onSubmit(){
        this._usuariosService.editarClub(this.club).subscribe(
            result => {
                if(result.code == 200){
                    this.dialogRef.close();
                    this.snackBar.open("Club modificado correctamente", "Aceptar", {
                        duration: 2500,
                    });
                }
            }, error => {console.log(error);}
        );
    }
}
