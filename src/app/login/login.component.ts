import { Component } from '@angular/core';
import { MenuComponent } from '../menu/menu.component';
import { Usuario } from '../registro/usuario';
import { UsuariosService } from '../../services/usuarios.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
	selector: 'login',
	templateUrl: './login.component.html',
	providers: [UsuariosService]
})
export class LoginComponent{
	
	public nombre_componente:string;
	public usuario:Usuario;

	constructor(
		public snackBar: MatSnackBar,
		private _usuariosService: UsuariosService
	){
		this.usuario = new Usuario("","","","","","","","");
		this.nombre_componente = "LoginComponent";
	}

	comprobar(){
		this._usuariosService.loginUsuario(this.usuario).subscribe(
			response => {
				if(response.code==200){
					//Datos de usuario correctos
					localStorage.setItem('correo',this.usuario.correo);
				}else{
					//Error al comprobar los datos
    				this.snackBar.open("Compruebe los datos", "Aceptar", {
      					duration: 2500,
    				});
				}
			},
			error => {
				console.log("error");
			}
		);
	}
}