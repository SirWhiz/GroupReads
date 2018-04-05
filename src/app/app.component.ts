import { Component } from '@angular/core';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'GroupReads';

  ngOnInit(){
    var typed = new Typed(".auto", {
      strings: ["lectores^2500", "aventureros^2500", "amantes^2500",],
      typeSpeed:60,
      fadeOut: true,
      loop:true      
    });
  }
}
