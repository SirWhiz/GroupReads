import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Usuario } from '../registro/usuario';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { MatSnackBar } from '@angular/material/snack-bar';
declare var jquery:any;
declare var $ :any;

@Component({
	selector: 'instalacion',
	templateUrl: './instalacion.component.html',
	providers: [LibrosService]
})
export class InstalacionComponent{

	public control:string;
	public usuario:Usuario;
	public instalar:boolean;

	constructor(public snackBar: MatSnackBar,private _router: Router,private _librosService: LibrosService,public dialog: MatDialog){
		this.control = "c";
		this.usuario = new Usuario("","","","","","","","","","","");
		this.instalar = false;
	}

	ngOnInit(){

		this._librosService.comprobarInstalacion().subscribe(
			result => {
				if(result.code == 200){
					this.instalar = true;
				}else{
					this.instalar = false;
				}
			}, error => {console.log(error);}
		);

	}

	ver(){
		$('#pwd').attr({type:"text"});
	}

	nover(){
		$('#pwd').attr({type:"password"});	
	}

	onSubmit(){

		this._librosService.instalar(this.usuario.pwd).subscribe(
			result => {
				if(result.code == 200){
					this.snackBar.open("Estructura creada correctamente", "Aceptar", {
	  					duration: 2500,
					});
					this._router.navigate(['/login']);
				}else{
					this.snackBar.open("Se ha producido un error al crear la estructura", "Aceptar", {
	  					duration: 2500,
					});
				}
			}, error => {console.log(error);}
		);

	}

}