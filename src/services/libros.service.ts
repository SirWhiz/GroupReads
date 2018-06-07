import { Injectable } from '@angular/core';
import { Http, Response, Headers, RequestOptions } from '@angular/http';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';
import { Libro } from '../app/mantenimientoLibros/libro';
import { Autor } from '../app/mantenimientoLibros/autor';
import { Genero } from '../app/mantenimientoLibros/genero';
import { GLOBAL } from './global';

@Injectable()
export class LibrosService{
   
    public url:string;

    constructor(
        public _http: Http
    ){
        this.url=GLOBAL.url;
    }

    instalar(pwd:string){
        return this._http.get(this.url+'install/'+pwd).map(res => res.json());
    }

    comprobarInstalacion(){
        return this._http.get(this.url+'checkinstall').map(res => res.json());
    }

    getUsuario(correo:string){
        return this._http.get(this.url+'usuarios/'+correo).map(res => res.json());
    }

    getClub(id:string){
        return this._http.get(this.url+'getclub/'+id).map(res => res.json());
    }

    getPaises(){
        return this._http.get(this.url+'paises').map(res => res.json());
    }

    getGeneros(){
    	return this._http.get(this.url+'generos').map(res => res.json());
    }

    addGenero(genero: Genero){
        let json = JSON.stringify(genero);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'nuevogenero',params,{headers:headers})
                .map(res => res.json());
    }

    editarGenero(genero: Genero){
        let json = JSON.stringify(genero);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'editargenero',params,{headers:headers})
                .map(res => res.json());
    }

    addAutor(autor: Autor){
        let json = JSON.stringify(autor);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'nuevoautor',params,{headers:headers})
                .map(res => res.json());
    }

    updateAutor(autor: Autor){
        let json = JSON.stringify(autor);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'updateautor',params,{headers:headers})
                .map(res => res.json());
    }

    deleteAutor(id: string){
        return this._http.get(this.url+'deleteautor/'+id).map(res => res.json());
    }

    deleteGenero(id:string){
        return this._http.get(this.url+'deletegenero/'+id).map(res => res.json());
    }

    getAutores(){
    	return this._http.get(this.url+'autores').map(res => res.json());
    }

    getAutor(id:string){
        return this._http.get(this.url+'autor/'+id).map(res => res.json());
    }

    getAutoresLibro(isbn: string){
        return this._http.get(this.url+'autores/'+isbn).map(res => res.json());
    }

    getFullAutores(isbn:string){
        return this._http.get(this.url+'fullautores/'+isbn).map(res => res.json());   
    }

    getComentarios(idclub:string,isbn:string){
        return this._http.get(this.url+'getcomments/'+idclub+'/'+isbn).map(res => res.json());
    }

    getLibros(){
        return this._http.get(this.url+'libros').map(res => res.json());
    }

    getLibro(isbn: string){
        return this._http.get(this.url+'libro/'+isbn).map(res => res.json());
    }

    getLibrosLeidos(idclub:string){
        return this._http.get(this.url+'finishedbooks/'+idclub).map(res => res.json());
    }

    getLibrosRecomendados(idclub:string){
        return this._http.get(this.url+'recommendedbooks/'+idclub).map(res => res.json());   
    }

    comprobarisbn(isbn: string){
        return this._http.get(this.url+'checkisbn/'+isbn).map(res => res.json());
    }

    borrarLibro(libro: Libro){
        let json = JSON.stringify(libro);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'deletelibro',params,{headers:headers})
                .map(res => res.json());
    }

    borrarPortada(isbn: string){
        return this._http.get(this.url+'deleteportada/'+isbn).map(res => res.json());
    }

    updatePortada(libro:Libro){
        let json = JSON.stringify(libro);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'actualizarportada/'+libro.isbn,params,{headers:headers})
                .map(res => res.json());
    }

    updateFotoAutor(autor: Autor){
        let json = JSON.stringify(autor);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'actualizarfotoautor/'+autor.id,params,{headers:headers})
                .map(res => res.json());
    }

    deleteFotoAutor(autor: Autor){
        let json = JSON.stringify(autor);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'deletefotoautor',params,{headers:headers})
                .map(res => res.json());  
    }

    updateAutores(autores,isbn){
        let json = JSON.stringify(autores);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'actualizarautores/'+isbn,params,{headers:headers})
                .map(res => res.json());
    }

    getLibrosFiltro(filtro:string){
        return this._http.get(this.url+'librosfiltro/'+filtro).map(res => res.json());
    }

    getAutoresFiltro(filtro:string){
       return this._http.get(this.url+'autoresfiltro/'+filtro).map(res => res.json());
    }

    updateLibro(libro:Libro,isbn:string){
        let json = JSON.stringify(libro);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'actualizarlibro/'+isbn,params,{headers:headers})
                .map(res => res.json());
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