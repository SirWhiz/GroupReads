import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { LibrosService } from '../../services/libros.service';
import { Autor } from '../mantenimientoLibros/autor';

@Component({
	selector: 'nuevoautor',
	templateUrl: './nuevoautor.component.html',
	providers: [LibrosService]
})
export class NuevoAutorComponent{

	public perfil: string;
	public autores: Autor[];

	constructor(private _router: Router,private _librosService: LibrosService){
		this.perfil = "";
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

	}


}