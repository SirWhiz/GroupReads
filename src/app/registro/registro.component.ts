import { Component } from '@angular/core';
import { Usuario } from './usuario';
declare var $: any;

@Component({
	selector: 'registro',
	templateUrl: './registro.component.html'
})
export class RegistroComponent{

	public nombre_componente = "RegistroComponent";
	public fechaActual = new Date();
	public anio = 0;
	public mes = 0;
	public dia = 0;
	public usuario:Usuario;

	constructor(){
		this.usuario = new Usuario("","","","","","");

		$("#fecha").blur(function(){
			this.usuario.fecha_nacimiento=$("#fecha").val();
		});
	}
	
	ngOnInit(){
		/* -- Configurar Datepicker -- */
		$.datepicker.setDefaults($.datepicker.regional['es']); 
		this.anio = this.fechaActual.getFullYear();
		this.mes = this.fechaActual.getMonth();
		this.dia = this.fechaActual.getDate();
		$("#fecha").datepicker({
		    changeMonth: true,
		    changeYear: true,
		    showOtherMonths: true,
		    selectOtherMonths: true,
		    yearRange: "1960:"+this.anio,
		    dateFormat: "dd/mm/yy",
		    maxDate: new Date(this.anio, this.mes, this.dia),
		    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
		    firstDay: 1,
		    monthNamesShort: ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"]
		});
	}

	onSubmit(){
		console.log(this.usuario);
	}
}