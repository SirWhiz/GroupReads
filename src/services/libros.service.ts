import { Injectable } from '@angular/core';
import { Http, Response, Headers, RequestOptions } from '@angular/http';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';
import { Libro } from '../app/mantenimientoLibros/libro';
import { GLOBAL } from './global';

@Injectable()
export class LibrosService{
   
    public url:string;

    constructor(
        public _http: Http
    ){
        this.url=GLOBAL.url;
    }

    getGeneros(){
    	return this._http.get(this.url+'generos').map(res => res.json());
    }

    getAutores(){
    	return this._http.get(this.url+'autores').map(res => res.json());
    }

    getLibros(){
        return this._http.get(this.url+'libros').map(res => res.json());
    }

    registrarLibro(libro:Libro){
        let json = JSON.stringify(libro);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'nuevolibro',params,{headers:headers})
                .map(res => res.json());
    }

    asignarAutores(autores,isbn){
        let json = JSON.stringify(autores);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'autoreslibros/'+isbn,params,{headers:headers})
                .map(res => res.json());
    }

    makeFileRequest(url:string, params:Array<string>, files:Array<File>){
        return new Promise((resolve, reject)=>{
            var formData:any = new FormData();
            var xhr = new XMLHttpRequest();

            for(var i=0; i<files.length; i++){
                formData.append('uploads[]', files[i], files[i].name);
            }

            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4){
                    if(xhr.status == 200){
                        resolve(JSON.parse(xhr.response));
                    }else{
                        reject(xhr.response);
                    }
                }
            };

            xhr.open("POST", url, true);
            xhr.send(formData);
        });
    }

}