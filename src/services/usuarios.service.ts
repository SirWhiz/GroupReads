import { Injectable } from '@angular/core';
import { Http, Response, Headers, RequestOptions } from '@angular/http';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';
import { Usuario } from '../app/registro/usuario';
import { GLOBAL } from './global';

@Injectable()
export class UsuariosService{
    public url:string;

    constructor(
        public _http: Http
    ){
        this.url=GLOBAL.url;
    }

    getUsuario(correo:string){
        return this._http.get(this.url+'usuarios/'+correo).map(res => res.json());
    }

    getPaises(){
        return this._http.get(this.url+'paises').map(res => res.json());
    }

    loginUsuario(usuario:Usuario){
        let json = JSON.stringify(usuario);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'loginusuario',params,{headers:headers})
                .map(res => res.json());
    }

    registrarUsuario(usuario:Usuario){
        let json = JSON.stringify(usuario);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'nuevousuario',params,{headers:headers})
                .map(res => res.json());
    }

}