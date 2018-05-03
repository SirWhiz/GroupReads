import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
	selector: 'colaboradores',
	templateUrl: './colaboradores.component.html',
	providers: [UsuariosService]
})
export class ColaboradoresComponent{

	public usuario:Usuario;
	public usuarios:Usuario[];
	public noUsuarios:boolean;
	public filtro:string;

	constructor(private snackBar:MatSnackBar,private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.noUsuarios = false;
		this.filtro = '';
	}

	ngOnInit(){

		if(localStorage.getItem('correo')==null || localStorage.getItem('perfil')=='n'){
			this._router.navigate(['/home']);
		}else{
			this._usuariosService.getUsuarios().subscribe(
				result => {
					if(result.code == 200){
						this.usuarios = result.data;
						console.log(this.usuarios);
					}else{
						this.noUsuarios = true;
					}
				}, error => {
					console.log(error);
				}
			);
		}
	}

	filtrar(){
		if(this.filtro!=''){
			this._usuariosService.getUsuariosFiltro(this.filtro).subscribe(
				result => {
					if(result.code == 200){
						this.usuarios = result.data;
					}else{
						this.snackBar.open("No se han encontrado usuarios con ese criterio", "Aceptar", {
      						duration: 2500,
    					});
					}
				}, error =>{
					console.log(error);
				}
			);
		}else{
			this._usuariosService.getUsuarios().subscribe(
				result => {
					if(result.code == 200){
						this.usuarios = result.data;
					}else{
						this.noUsuarios = true;
					}
				}, error => {
					console.log(error);
				}
			);
		}
	}

	cambiar(user:Usuario){
		if(user.tipo=='n'){
			this._usuariosService.hacerColaborador(user.id).subscribe(
				result => {
					if(result.code == 200){
						user.tipo = 'c';
	    				this.snackBar.open(user.nombre+" "+user.apellidos+" ahora es colaborador", "Aceptar", {
      						duration: 2500,
    					});
					}
				}, error => {
					console.log(error);
				}
			);			
		}else if(user.tipo == 'c'){
			this._usuariosService.quitarColaborador(user.id).subscribe(
				result => {
					if(result.code == 200){
						user.tipo = 'n';
						this.snackBar.open(user.nombre+" "+user.apellidos+" ya no es colaborador", "Aceptar", {
      						duration: 2500,
    					});
					}
				}, error =>{
					console.log(error);
				}
			);
		}
	}
}