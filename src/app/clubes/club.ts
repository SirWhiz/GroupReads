/*--Modelo de los clubes--*/
export class Club{

	constructor(
		public id: string,
		public nombreClub: string,
		public idCreador: string,
		public genero: string,
		public nombreGenero: string,
		public privacidad: string,
		public descripcion: string
	){}
}