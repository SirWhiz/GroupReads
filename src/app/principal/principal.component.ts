import { Component } from '@angular/core';
import { Router } from '@angular/router';
import * as Typed from 'typed.js';
import * as $ from 'jquery';

@Component({
  selector: 'principal',
  templateUrl: './principal.component.html',
})
export class PrincipalComponent {
  title = 'GroupReads';

  constructor(private _router:Router){ }

  ngOnInit(){
    //Animación de escritura
    var typed = new Typed(".auto", {
      strings: ["lectores^2500", "aventureros^2500", "amantes^2500",],
      typeSpeed:60,
      fadeOut: true,
      loop:true      
    });

    //Boton para volver arriba
    $(window).scroll(function(){ 
        if ($(this).scrollTop() > 230) { 
            $('#scroll').fadeIn(); 
        } else { 
            $('#scroll').fadeOut(); 
        } 
    }); 
    //$(document.body).on('click', '.borrar' ,function() #scroll

    $(document.body).on('click','#scroll',function(){
      $("html, body").animate({ scrollTop: 0 }, 'slow'); 
        return false; 
    });

    if(localStorage.getItem('correo')!=null){
      this._router.navigate(['/home']);
    }

  }
}