/*--Modelo de los usuarios--*/
export class UsuarioP{

	constructor(
		public id: string,
		public nombre: string,
		public apellidos:string,
		public nick: string,
		public correo: string,
		public pwd: string,
		public pwd2: string,
		public fecha: any,
		public pais: string,
		public foto: string,
		public tipo: string,
		public peticiones: number
	){}
}