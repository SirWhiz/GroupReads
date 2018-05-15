import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';

@Component({
	selector: 'clubes',
	templateUrl: './clubes.component.html',
	providers: [UsuariosService]
})
export class ClubesComponent{

	public usuario:Usuario;
	public noClub:boolean;

	constructor(private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.noClub = false;
	}

	ngOnInit(){

		if(localStorage.getItem('correo')==null){
			this._router.navigate(['/login']);
		}else{
			this.usuario.tipo = localStorage.getItem('perfil');
		}

		this._usuariosService.getUsuario(localStorage.getItem('correo')).subscribe(
			result => {
				if(result.code == 200){
					this.usuario = result.data;
					this._usuariosService.comprobarTieneClub(this.usuario.id).subscribe(
						result => {
							if(result.code == 200){
								this.noClub = false;
							}else{
								this.noClub = true;
							}
						}, error => {console.log(error);}
					);
				}
			}, error => {console.log(error);}
		);

	}
}