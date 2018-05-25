import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { Usuario } from '../registro/usuario';
import { UsuariosService } from '../../services/usuarios.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
	selector: 'reinstalar',
	templateUrl: './reinstalar.component.html',
	providers: [UsuariosService]
})
export class ReinstalarComponent{

	public control:string;
	public usuario:Usuario;
	public pwdok:boolean;

	constructor(  
		public dialogRef: MatDialogRef<ReinstalarComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _usuariosService: UsuariosService,
        public snackBar:MatSnackBar,
        public _router:Router,)
		{
			this.control = 'c';
			this.usuario = new Usuario("","","","","","","","","","","");
			this.usuario.correo = "admin@admin.com";
			this.pwdok = false;
		}

	ngOnInit(){
	}

	onSubmit(){
		this._usuariosService.reinstalarAplicacion().subscribe(
			result => {
				if(result.code == 200){
    				this.snackBar.open("Datos borrados correctamente", "Aceptar", {
      					duration: 2500,
    				});
					this.dialogRef.close();
				}
			}, error => {console.log(error);}
		);
	}

	public comprobar(){
		if(this.usuario.pwd!=''){
			this._usuariosService.loginUsuario(this.usuario).subscribe(
				result => {
					if(result.code == 200){
						this.pwdok = true;
					}else{
						this.pwdok = false;
					}
				}, error => {console.log(error);}
			);
		}
	}
}