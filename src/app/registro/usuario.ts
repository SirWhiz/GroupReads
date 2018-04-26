/*--Modelo de los usuarios--*/
export class Usuario{

	constructor(
		public nombre: string,
		public apellidos:string,
		public nick: string,
		public correo: string,
		public pwd: string,
		public pwd2: string,
		public fecha: any,
	){}

}