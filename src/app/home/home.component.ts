import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { Libro } from '../mantenimientoLibros/libro';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { DialogoPwdComponent } from './dialogopwd.component';
import { Chart } from 'chart.js';

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

	constructor(private dialog:MatDialog,private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.libros = new Array();
		this.esAdmin = false;
		this.noClub = false;
		this.noLibros = false;
	}

	ngOnInit(){
		this.usuario.tipo = localStorage.getItem('perfil');
		this._usuariosService.getUsuario(localStorage.getItem('correo')).subscribe(
			result => {
				if(result.code==200){
					this.usuario = result.data;
					if(this.usuario.tipo == "a"){
						//Comprobar si ha cambiado la contraseña
						this.usuario.pwd = "admin";
						this._usuariosService.loginUsuario(this.usuario).subscribe(
							result => {
								if(result.code == 200){
									this.dialog.open(DialogoPwdComponent,{
										width:'500px',
									});
								}
							}, error => {console.log(error);}
						);
						this._usuariosService.totalesAdmin().subscribe(
							result => {
								if(result.code == 200){
									this.crearGrafico(result.data.usuarios,result.data.libros,result.data.autores);
								}
							}, error => {console.log(error);}
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

	crearGrafico(usuarios:number,libros:number,autores:number){
		var ctx = document.getElementById("myChart");
		var myChart = new Chart(ctx, {
		    type: 'bar',
		    data: {
		        labels: ["Usuarios","Libros","Autores"],
		        datasets: [{
		            label: 'Total',
		            data: [usuarios,libros,autores],
		            backgroundColor: [
		                'rgba(255, 99, 132, 0.2)',
		                'rgba(54, 162, 235, 0.2)',
		                'rgba(255, 206, 86, 0.2)',
		                'rgba(75, 192, 192, 0.2)',
		                'rgba(153, 102, 255, 0.2)',
		                'rgba(255, 159, 64, 0.2)'
		            ],
		            borderColor: [
		                'rgba(255,99,132,1)',
		                'rgba(54, 162, 235, 1)',
		                'rgba(255, 206, 86, 1)',
		                'rgba(75, 192, 192, 1)',
		                'rgba(153, 102, 255, 1)',
		                'rgba(255, 159, 64, 1)'
		            ],
		            borderWidth: 1
		        }]
		    },
		    options: {
		        scales: {
		            yAxes: [{
		                ticks: {
		                    beginAtZero:true
		                }
		            }]
		        }
		    }
		});
	}

}