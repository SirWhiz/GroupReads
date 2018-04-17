import { Component } from '@angular/core';
import { Router, NavigationEnd } from '@angular/router';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {

	public title:string = "";
	constructor(private router: Router){
		this.title = "GroupReads";
	}

	ngOnInit(){
		this.router.events.subscribe((evt) => {
			if(!(evt instanceof NavigationEnd)){
				return;
			}
			window.scrollTo(0,0)
		});
	}
}