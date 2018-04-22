/*--Modelo de los usuarios--*/
export class Usuario{

	constructor(
		public nombre_ape: string,
		public nick: string,
		public correo: string,
		public pwd: string,
		public pwd2: string,
		public fecha_nacimiento: string,
	){}

}