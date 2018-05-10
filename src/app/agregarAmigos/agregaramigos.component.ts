import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';

@Component({
	selector: 'agregaramigos',
	templateUrl: './agregaramigos.component.html',
	providers: [UsuariosService]
})
export class AgregarAmigosComponent{

	public amigos:Usuario[];
	public usuarios:Usuario[];
	public usuario: Usuario;
	public perfil: string;
	public existeAmigos: boolean;
	public existeUsuarios: boolean;
	public filtro: string;
	public filtroUsuarios: string;

	constructor(private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.usuarios = new Array();
		this.amigos = new Array();
		this.perfil = "";
		this.filtro = "";
		this.filtroUsuarios = "";
		this.existeAmigos = true;
		this.existeUsuarios = true;
	}

	ngOnInit(){

		if(localStorage.getItem('correo')==null || localStorage.getItem('perfil')=='a'){
			this._router.navigate(['/login']);
		}
		this.perfil = localStorage.getItem('perfil');

		this._usuariosService.getUsuario(localStorage.getItem('correo')).subscribe(
			result => {
				if(result.code == 200){
					this.usuario = result.data;
					//Lista de amigos
					this._usuariosService.getAmigos(this.usuario.id).subscribe(
						result => {
							if(result.code == 200){
								this.amigos = result.data;
							}else{
								this.existeAmigos = false;
							}
						}
					);
					//Lista de no amigos
					this._usuariosService.getNoAmigos(this.usuario.id).subscribe(
						result => {
							if(result.code == 200){
								this.usuarios = result.data;
							}else{
								this.existeUsuarios = false;
							}
						}, error => {console.log(error)}
					);
				}
			}, error => {
				console.log(error);
			}
		);

	}

	filtrarAmigos(){

	}

	filtrarUsuarios(){

	}

}