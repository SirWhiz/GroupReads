/*--Modelo de los autores--*/
export class Autor{
	constructor(
		public id: string,
		public nombre_ape: string,
		public fecha_nacimiento: any,
		public pais: string,
		public foto: string,
	){}
}