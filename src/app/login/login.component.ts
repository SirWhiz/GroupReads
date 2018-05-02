import { Component } from '@angular/core';
import { MenuComponent } from '../menu/menu.component';
import { Usuario } from '../registro/usuario';
import { UsuariosService } from '../../services/usuarios.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Router } from '@angular/router';

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
		private _usuariosService: UsuariosService,
		private _router: Router,
	){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.nombre_componente = "LoginComponent";
	}

	ngOnInit(){
		if(localStorage.getItem('correo')!=null){
      		this._router.navigate(['/home']);
    	}
	}

	comprobar(){
		this._usuariosService.loginUsuario(this.usuario).subscribe(
			response => {
				if(response.code==200){
					//Datos de usuario correctos
					localStorage.setItem('correo',this.usuario.correo);
					localStorage.setItem('perfil',response.data.tipo);
					this._router.navigate(['/home']);
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