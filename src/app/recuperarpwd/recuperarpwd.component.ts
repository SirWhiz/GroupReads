import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { MenuComponent } from '../menu/menu.component';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
	selector: 'recuperarpwd',
	templateUrl: 'recuperarpwd.component.html',
	providers: [UsuariosService]
})
export class RecuperarpwdComponent{

	public correo:string;

	constructor(private _router:Router,private _usuariosService:UsuariosService,public snackBar: MatSnackBar){
		this.correo = "";
	}

	ngOnInit(){
	}

	onSubmit(){
		this._usuariosService.getUsuario(this.correo).subscribe(
			result => {
				if(result.code == 200){

				}else{
    				this.snackBar.open("Parece que no hay nadie registrado con ese correo, revísalo", "OK",{
      					duration: 3000,
    				});
				}
			}, error => {console.log(error);}
		);
	}

}