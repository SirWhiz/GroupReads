import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Club } from './club';
import { Libro } from '../mantenimientoLibros/libro';
import { Comentario } from './comentarios';
import { DialogoEditarClubComponent } from './dialogoeditar.component';
import { DialogoAbandonarComponent } from './dialogoabandonar.component';
import { DialogoBorrarClubComponent } from './dialogoborrar.component';
import { DialogoExpulsarComponent } from './dialogoexpulsar.component';
import { Chart } from 'chart.js';

@Component({
	selector: 'clubes',
	templateUrl: './clubes.component.html',
	providers: [UsuariosService]
})
export class ClubesComponent{

	public usuario:Usuario;
	public miembros:Usuario[];
	public peticiones:Usuario[];
	public librosVotar:Libro[];
	public comentarios:Comentario[];
	public libroVotado:Libro;
	public libroActual:Libro;
	public nombreMostrar: string;
	public club:Club;
	public clubes:Club[];
	public solicitados:Club[];
	public noClub:boolean;
	public noLibro:boolean;
	public nuevoComentario:string;
	public votacion:boolean;
	public votado:boolean;
	public grafico:boolean;

	constructor(public dialog: MatDialog,public snackBar: MatSnackBar,private _router: Router,private _usuariosService: UsuariosService){
		this.usuario = new Usuario("","","","","","","","","","","");
		this.libroVotado = new Libro("","","","","","","","");
		this.libroActual = new Libro("","","","","","","","");
		this.miembros = new Array();
		this.club = new Club("","","","","","","");
		this.clubes = new Array();
		this.solicitados = new Array();
		this.peticiones = new Array();
		this.comentarios = new Array();
		this.librosVotar = new Array();
		this.noClub = false;
		this.grafico = true;
		this.noLibro = true;
		this.votacion = false;
		this.nombreMostrar = "";
		this.nuevoComentario = "";
		this.votado = false;
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
								this.getClub();
							}else{
								this.noClub = true;
								this.getClubesSolicitados();
							}
						}, error => {console.log(error);}
					);
				}
			}, error => {console.log(error);}
		);
	}

	getClub(){
		this._usuariosService.getClub(this.usuario.id).subscribe(
			result => {
				if(result.code == 200){
					this.club = result.data;
					this.nombreMostrar = this.club.nombreClub;
					this.getMiembros();
					if(this.club.idCreador == this.usuario.id && this.club.privacidad=='c'){
						this.getPeticiones();
					}
					this.comprobarLibro();
					this.comprobarVotacion();
				}
			}, error => {
				console.log(error);
			}
		);
	}

	comprobarVotacion(){
		this._usuariosService.comprobarVotacion(this.club.id).subscribe(
			result => {
				if(result.code == 200){
					this.votacion = true;
					this.librosVotacion();
				}else{
					this.votacion = false;
				}
			}, error => {console.log(error);}
		);
	}

	librosVotacion(){
		this._usuariosService.getLibrosVotacion(this.club.id).subscribe(
			result => {
				if(result.code == 200){
					this.librosVotar = result.data;
					this.comprobarAvotado();					
				}
			}, error => {console.log(error);}
		);
	}

	comprobarAvotado(){
		this._usuariosService.comprobarAvotado(this.usuario.id).subscribe(
			result => {
				if(result.code == 200){
					this.votado = true;
					this.obtenerLibroVotado();
				}else{
					this.votado = false;
				}
			}, error => {console.log(error);}
		);
	}

	obtenerLibroVotado(){
		this._usuariosService.obtenerLibroVotado(this.usuario.id).subscribe(
			result => {
				if(result.code == 200){
					this.libroVotado = result.data;
					this.crearGrafico();
				}
			}, error => {console.log(error);}
		);
	}

	getComentarios(){
		this._usuariosService.getComentarios(this.club.id,this.libroActual.isbn).subscribe(
			result => {
				if(result.code == 200){
					this.comentarios = result.data;
				}
			}, error => {console.log(error);}
		);
	}

	comentar(){
		this._usuariosService.comentar(this.usuario.id,this.club.id,this.libroActual.isbn,this.nuevoComentario).subscribe(
			result => {
				if(result.code == 200){    				
					this.addComentario();					
				}
			}, error => {console.log(error);}
		);
	}

	addComentario(){
		var nuevoComment = new Comentario(0,"","","","");
		nuevoComment.setidclub(this.club.id);
		nuevoComment.setnombreUsuario(this.usuario.nombre);
		nuevoComment.setfoto(this.usuario.foto);
		nuevoComment.setcomentario(this.nuevoComentario);
		this.comentarios.push(nuevoComment);
		this.nuevoComentario = "";
	}

	libroAcabado(){
		this._usuariosService.acabarLibro(this.club.id,this.libroActual.isbn).subscribe(
			result => {
				if(result.code == 200){
    				this.snackBar.open("Libro finalizado correctamente", "Aceptar", {
      					duration: 2500,
    				}); 
    				this.noLibro = true;
				}
			}, error => {console.log(error);}
		);
	}

	comprobarLibro(){
		this._usuariosService.comprobarLibro(this.club.id).subscribe(
			result => {
				if(result.code == 200){
					//El club se está leyendo un libro
					this.noLibro = false;
					this.libroActual = result.data;
					this.getComentarios();
				}else{
					this.noLibro = true;
				}
			}, error => {console.log(error);}
		);
	}

	votar(libro:Libro){
		this._usuariosService.votarLibro(this.usuario.id,this.club.id,libro.isbn).subscribe(
			result => {
				if(result.code == 200){
					this.votado = true;
					libro.votos++;
    				this.snackBar.open("Voto enviado correctamente", "Aceptar", {
      					duration: 2500,
    				});    				
    				this.libroVotado = libro; 
    				this.grafico = false;   				
				}
			}, error => {console.log(error);}
		);
	}

	crearGrafico(){
		var ctx = document.getElementById("myChart");
		var myChart = new Chart(ctx, {
		    type: 'bar',
		    data: {
		        labels: [this.librosVotar[0].titulo,this.librosVotar[1].titulo,this.librosVotar[2].titulo],
		        datasets: [{
		            label: 'Votos',
		            data: [this.librosVotar[0].votos,this.librosVotar[1].votos,this.librosVotar[2].votos],
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

	quitarVoto(libro:Libro){
		this._usuariosService.quitarVoto(this.usuario.id,this.club.id,libro.isbn).subscribe(
			result => {
				if(result.code == 200){
					this.votado = false;
					var i=0;
					for(i=0;i<this.librosVotar.length;i++){
						if(this.librosVotar[i].isbn == libro.isbn){
							this.librosVotar[i].votos--;
						}
					}
				}
			}, error => {console.log(error);}
		);
		this.grafico = false;
	}

	finalizar(){
		var mayor = this.librosVotar[0];
		if(this.librosVotar[1].votos > mayor.votos){
			mayor = this.librosVotar[1];
		}
		if(this.librosVotar[2].votos > mayor.votos){
			mayor = this.librosVotar[2];
		}
		if(this.librosVotar[1].votos == mayor.votos){
			if(this.librosVotar[1].titulo < mayor.titulo){
				mayor = this.librosVotar[1];
			}
		}
		if(this.librosVotar[2].votos == mayor.votos){
			if(this.librosVotar[2].titulo < mayor.titulo){
				mayor = this.librosVotar[2];
			}
		}
		this._usuariosService.acabarVotacion(this.club.id,mayor).subscribe(
			result => {
				if(result.code == 200){
    				this.snackBar.open("Votación finalizada con éxito", "Aceptar", {
      					duration: 2500,
    				}); 
    				this.votacion = false;
    				this.libroActual = mayor;
    				this.noLibro = false;
    				this.getComentarios();
				}
			}, error => {console.log(error);}
		);
	}

	getClubesDisponibles(){
		this._usuariosService.getClubesDisponibles().subscribe(
			result => {
				if(result.code == 200){
					this.clubes = result.data;
				}
			}, error => {console.log(error);}
		);
	}

	getClubesSolicitados(){
		this._usuariosService.getClubesSolicitados(this.usuario.id).subscribe(
			result => {
				if(result.code == 200){
					this.solicitados = result.data;
				}else{
					this.getClubesDisponibles();
				}
			}, error => {console.log(error);}
		);
	}

	borrarSolicitud(club:Club){
		this._usuariosService.borrarSolicitudClub(this.usuario.id,club.id).subscribe(
			result => {
				if(result.code == 200){
    				this.snackBar.open("Petición eliminada correctamente", "Aceptar", {
      					duration: 2500,
    				});
    				this.solicitados = [];
    				this.getClubesDisponibles();
				}
			}, error => {console.log(error);}
		);
	}

	getMiembros(){
		this._usuariosService.getMiembros(this.club.id).subscribe(
			result => {
				if(result.code == 200){
					this.miembros = result.data;
				}
			}, error => {console.log(error);}
		);
	}

	getPeticiones(){
		this._usuariosService.getPeticiones(this.club.id).subscribe(
			result => {
				if(result.code == 200){
					this.peticiones = result.data;
				}
			}, error => {console.log(error);}
		);
	}

	dialogoEditar(){
		this.dialog.open(DialogoEditarClubComponent,{
			width:'500px',
			data: { club: this.club }
		});
	}

	dialogoBorrar(){
		this.dialog.open(DialogoBorrarClubComponent,{
			width:'500px',
			data: { club: this.club }
		});
	}

	abandonar(){
		this.dialog.open(DialogoAbandonarComponent,{
			width:'500px',
			data: { club: this.club,usuario: this.usuario.id }
		});
	}

	expulsar(miembro:Usuario){
		this.dialog.open(DialogoExpulsarComponent,{
			width:'500px',
			data: { club: this.club,miembro: miembro,miembros: this.miembros }
		});
	}

	descartar(peticion:Usuario){
		this._usuariosService.borrarSolicitudClub(peticion.id,this.club.id).subscribe(
			result => {
				if(result.code == 200){
    				this.snackBar.open("Petición descartada", "Aceptar", {
      					duration: 2500,
    				});
					var i=0;
					for(i=0;i<this.peticiones.length;i++){
						if(this.peticiones[i].id == peticion.id){
							this.peticiones.splice(i,1);
						}
					}
				}
			}, error => {console.log(error);}
		);
	}

	aceptar(peticion:Usuario){
		this._usuariosService.aceptarSolicitudClub(peticion.id,this.club.id).subscribe(
			result => {
				if(result.code == 200){
    				this.snackBar.open("Petición aceptada", "Aceptar", {
      					duration: 2500,
    				});
					var i=0;
					for(i=0;i<this.peticiones.length;i++){
						if(this.peticiones[i].id == peticion.id){
							this.peticiones.splice(i,1);
						}
					}
					this.miembros.push(peticion);
				}
			}, error => {console.log(error);}
		);
	}

	unirse(club:Club){
		if(club.privacidad=='a'){
			this._usuariosService.unirseClub(this.usuario.id,club.id).subscribe(
				result => {
					if(result.code == 200){
	    				this.snackBar.open("¡Te acabas de unir a "+club.nombreClub+"!", "Aceptar", {
	      					duration: 2500,
	    				});
	    				this.club = club;
	    				this.noClub = false;
	    				this.getMiembros();
					}
				}, error => {console.log(error);}
			);
		}else{
			this._usuariosService.solicitarClub(this.usuario.id,club.id).subscribe(
				result => {
					if(result.code == 200){
	    				this.snackBar.open("Solicitud enviada correctamente", "Aceptar", {
	      					duration: 2500,
	    				});
	    				this.solicitados.push(club);
	    				this.clubes = [];
					}
				}, error => {console.log(error);}
			);
		}
	}

}