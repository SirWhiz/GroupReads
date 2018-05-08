import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Autor } from '../mantenimientoLibros/autor';

@Component({
	selector: 'autores',
	templateUrl: './autores.component.html',
	providers: [LibrosService]
})
export class AutoresComponent{

	public perfil: string;
	public autores: Autor[];
	public noAutores: boolean;

	constructor(private _router: Router,private _librosService: LibrosService){
		this.perfil = "";
		this.noAutores = false;
	}

	ngOnInit(){

		if(localStorage.getItem('perfil')==null || localStorage.getItem('perfil')=='n'){
			this._router.navigate(['/login']);
		}

		if(localStorage.getItem('perfil')=='a'){
			this.perfil = 'a';
		}else if(localStorage.getItem('perfil')=='c'){
			this.perfil = 'c';
		}

		this._librosService.getAutores().subscribe(
			result => {
				console.log(result);
				if(result.code == 200){
					this.autores = result.data;
				}else{
					this.noAutores = true;
				}
			}, error => {
				console.log(error);
			}
		);
	}


}