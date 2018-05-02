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
		this.totalUsuarios = "0";
		this.totalLibros = "0";
		this.totalAutores = "0";
	}

	ngOnInit(){
		this._usuariosService.getUsuario(localStorage.getItem('correo')).subscribe(
			result => {
				if(result.code==200){
					this.usuario = result.data;
				}
			},
			error => {
				console.log(error);
			}
		);

		if(localStorage.getItem('perfil')=='n'){
			this.esAdmin=false;
		}else if(localStorage.getItem('perfil')=='a'){
			this.esAdmin=true;

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
		}
	}
}