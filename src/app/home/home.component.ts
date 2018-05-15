import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Libro } from '../mantenimientoLibros/libro';

@Component({
	selector: 'home',
	templateUrl: './home.component.html',
	providers: [UsuariosService]
})
export class HomeComponent{

	public usuario:Usuario;
	public libros:Libro[];
	public esAdmin:boolean;
	public noClub:boolean;
	public noLibros:boolean;
	public totalUsuarios:string;
	public totalLibros:string;
	public totalAutores:string;

	constructor(private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.libros = new Array();
		this.esAdmin = false;
		this.noClub = false;
		this.noLibros = false;
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
					}else{
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
				}
			},
			error => {console.log(error);}
		);
		//Últimos libros añadidos
		this._usuariosService.getLibros().subscribe(
			result => {
				if(result.code == 200){
					this.libros = result.data;
				}else{
					this.noLibros = true;
				}
			}, error => {console.log(error);}
		);
	}
}