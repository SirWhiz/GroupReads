import { Component } from '@angular/core';
import * as Typed from 'typed.js';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'GroupReads';

  ngOnInit(){
  	//Animación de escritura
    var typed = new Typed(".auto", {
      strings: ["lectores^2500", "aventureros^2500", "amantes^2500",],
      typeSpeed:60,
      fadeOut: true,
      loop:true      
    });
  }
}
