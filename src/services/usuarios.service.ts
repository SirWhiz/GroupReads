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

    getSolicitudesPendientes(id: string){
        return this._http.get(this.url+'solicitudespen/'+id).map(res => res.json());
    }

    getAmigos(id: string){
        return this._http.get(this.url+'amigos/'+id).map(res => res.json());
    }

    borrarAmigo(id: string,idamigo: string){
        return this._http.get(this.url+'deleteamigo/'+id+'/'+idamigo).map(res => res.json());    
    }

    peticionAmigo(id: string,idamigo: string){
        return this._http.get(this.url+'peticionamigo/'+id+'/'+idamigo).map(res => res.json());    
    }

    borrarPeticion(id: string,idamigo: string){
        return this._http.get(this.url+'deletepeticion/'+id+'/'+idamigo).map(res => res.json());   
    }

    getNoAmigos(id: string){
        return this._http.get(this.url+'noamigos/'+id).map(res => res.json());   
    }

    getPendientes(id: string){
        return this._http.get(this.url+'pendientes/'+id).map(res => res.json());      
    }

    getUsuario(correo:string){
        return this._http.get(this.url+'usuarios/'+correo).map(res => res.json());
    }

    getUsuarios(){
        return this._http.get(this.url+'usuarios').map(res => res.json());
    }

    getUsuariosFiltro(filtro:string){
        return this._http.get(this.url+'usuariosfiltro/'+filtro).map(res => res.json());
    }

    getPaises(){
        return this._http.get(this.url+'paises').map(res => res.json());
    }

    hacerColaborador(id:any){
        return this._http.get(this.url+'colaborador/'+id).map(res => res.json());
    }

    quitarColaborador(id:any){
        return this._http.get(this.url+'normal/'+id).map(res => res.json());
    }

    totalUsuarios(){
        return this._http.get(this.url+'totalusuarios').map(res => res.json());
    }

    totalLibros(){
        return this._http.get(this.url+'totallibros').map(res => res.json());   
    }

    totalAutores(){
        return this._http.get(this.url+'totalautores').map(res => res.json());   
    }

    borrarImagen(usuario:Usuario){
        let json = JSON.stringify(usuario);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'borrarimg',params,{headers:headers})
                .map(res => res.json());
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

    updateUsuario(usuario:Usuario){
        let json = JSON.stringify(usuario);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'actualizar/'+usuario.correo,params,{headers:headers})
                .map(res => res.json());
    }

    updatePwd(usuario:Usuario){
        let json = JSON.stringify(usuario);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'actualizarpwd/'+usuario.correo,params,{headers:headers})
                .map(res => res.json());
    }

    updateImg(usuario:Usuario){
        let json = JSON.stringify(usuario);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'actualizarimg/'+usuario.correo,params,{headers:headers})
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