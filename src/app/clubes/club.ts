/*--Modelo de los clubes--*/
export class Club{

	constructor(
		public id: string,
		public nombre: string,
		public creador: string,
		public genero: string,
		public privacidad: string,
		public descripcion: string
	){}
}