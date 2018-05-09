import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';

@Component({
	selector: 'home',
	templateUrl: './home.component.html',
	providers: [UsuariosService]
})
export class HomeComponent{

	public usuario:Usuario;
	public esAdmin:boolean;
	public noClub:boolean;
	public totalUsuarios:string;
	public totalLibros:string;
	public totalAutores:string;

	constructor(private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.esAdmin = false;
		this.noClub = true;
		this.totalUsuarios = "";
		this.totalLibros = "";
		this.totalAutores = "";
	}

	ngOnInit(){

		this.usuario.tipo = localStorage.getItem('perfil');

		this._usuariosService.getUsuario(localStorage.getItem('correo')).subscribe(
			result => {
				if(result.code==200){
					this.usuario = result.data;
					if(this.usuario.tipo == "a"){
						//Total de usuarios
						this._usuariosService.totalUsuarios().subscribe(
							result => {
								if(result.code==200){
									this.totalUsuarios = result.data;
								}
							},
							error => {
								console.log(error);
							}
						);
						//Total de libros
						this._usuariosService.totalLibros().subscribe(
							result => {
								if(result.code==200){
									this.totalLibros = result.data;
								}
							},
							error => {
								console.log(error);
							}
						);
						//Total de autores
						this._usuariosService.totalAutores().subscribe(
							result => {
								if(result.code == 200){
									this.totalAutores = result.data;
								}
							}, error => {
								console.log(error);
							}
						);
					}
				}
			},
			error => {
				console.log(error);
			}
		);
	}
}