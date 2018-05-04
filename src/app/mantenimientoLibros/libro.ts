/*--Modelo de los libros--*/
export class Libro{

	constructor(
		public isbn: string,
		public titulo: string,
		public paginas: string,
		public fechaAlta: any,
		public genero: string,
		public portada: string,
		public nombre_genero: string,
	){}
}