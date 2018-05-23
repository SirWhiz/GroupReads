/* --- Modelo de los comentarios --- */
export class Comentario{

	constructor(
		public id:number,
		public idclub:string,
		public nombreUsuario:string,
		public foto:string,
		public comentario:string,
	){}

	setidclub(idclub:string){
		this.idclub = idclub;
	}

	getidclub(){
		return this.idclub;
	}

	setnombreUsuario(nombreUsuario:string){
		this.nombreUsuario = nombreUsuario;
	}

	getnombreUsuario(){
		return this.nombreUsuario;
	}

	setfoto(foto:string){
		this.foto = foto;
	}

	getfoto(){
		return this.foto;
	}

	setcomentario(comentario:string){
		this.comentario = comentario;
	}

	getcomentario(){
		return this.comentario;
	}
}