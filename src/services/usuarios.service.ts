import { Injectable } from '@angular/core';
import { Http, Response, Headers, RequestOptions } from '@angular/http';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';
import { Usuario } from '../app/registro/usuario';
import { Libro } from '../app/mantenimientoLibros/libro';
import { Club } from '../app/clubes/club';
import { Mensaje } from '../app/chat/mensaje';
import { GLOBAL } from './global';

@Injectable()
export class UsuariosService{
    public url:string;

    constructor(
        public _http: Http
    ){
        this.url=GLOBAL.url;
    }

    obtenerMensajes(id:string,idamigo:string){
        return this._http.get(this.url+'getmessages/'+id+'/'+idamigo).map(res => res.json());
    }

    enviarMensaje(mensaje:Mensaje){
        let json = JSON.stringify(mensaje);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'sendmessage',params,{headers:headers})
                .map(res => res.json());
    }

    cambiarPassword(correo:string){
        return this._http.get(this.url+'forgotpwd/'+correo).map(res => res.json());
    }

    reinstalarAplicacion(){
        return this._http.get(this.url+'restart').map(res => res.json());
    }

    getSolicitudesPendientes(id: string){
        return this._http.get(this.url+'solicitudespen/'+id).map(res => res.json());
    }

    getGeneros(){
        return this._http.get(this.url+'generos').map(res => res.json());
    }

    abandonarClub(id:string,idclub:string){
        return this._http.get(this.url+'dejarclub/'+id+'/'+idclub).map(res => res.json());
    }

    editarClub(club:Club){
        let json = JSON.stringify(club);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'editclub',params,{headers:headers})
                .map(res => res.json());
    }

    deleteClub(id:string){
        return this._http.get(this.url+'deleteclub/'+id).map(res => res.json());
    }

    getUsuariosSolicitudes(id: string){
        return this._http.get(this.url+'peticiones/'+id).map(res => res.json());
    }

    comprobarVotacion(idclub:string){
        return this._http.get(this.url+'checkvotacion/'+idclub).map(res => res.json());
    }

    obtenerLibroVotado(id:string){
        return this._http.get(this.url+'checkvotedbook/'+id).map(res => res.json());   
    }

    comprobarAvotado(id:string){
        return this._http.get(this.url+'checkuservote/'+id).map(res => res.json());
    }

    votarLibro(id:string,idclub:string,isbn:string){
        return this._http.get(this.url+'votar/'+id+'/'+idclub+'/'+isbn).map(res => res.json());   
    }

    quitarVoto(id:string,idclub:string,isbn:string){
        return this._http.get(this.url+'removevote/'+id+'/'+idclub+'/'+isbn).map(res => res.json());
    }

    acabarVotacion(idclub:string,mayor:Libro){
        return this._http.get(this.url+'endvote/'+idclub+'/'+mayor.isbn).map(res => res.json());
    }

    getLibrosVotacion(idclub:string){
        return this._http.get(this.url+'getvotingbooks/'+idclub).map(res => res.json());   
    }

    getLibros(){
        return this._http.get(this.url+'librostop').map(res => res.json());
    }

    elegirLibro(idclub:string,isbn:string){
        return this._http.get(this.url+'bookforclub/'+idclub+'/'+isbn).map(res => res.json());   
    }

    getLibrosGenero(){
        return this._http.get(this.url+'librosgenero').map(res => res.json());   
    }

    getLibrosGeneroFiltrados(filtro:number){
        return this._http.get(this.url+'librosgenerofilter/'+filtro).map(res => res.json());   
    }

    confirmarLibros(idclub:string,isbn1:string,isbn2:string,isbn3:string){
        return this._http.get(this.url+'setvotacion/'+idclub+'/'+isbn1+'/'+isbn2+'/'+isbn3).map(res => res.json());
    }

    getClub(id:string){
        return this._http.get(this.url+'getclub/'+id).map(res => res.json());
    }

    getPeticiones(idclub:string){
        return this._http.get(this.url+'getrequests/'+idclub).map(res => res.json());   
    }

    getClubesDisponibles(){
        return this._http.get(this.url+'freeclubs').map(res => res.json());   
    }

    getClubesSolicitados(id:string){
        return this._http.get(this.url+'requestedclubs/'+id).map(res => res.json());   
    }

    borrarSolicitudClub(id:string,idclub:string){
        return this._http.get(this.url+'deleteclubreq/'+id+'/'+idclub).map(res => res.json());   
    }

    aceptarSolicitudClub(id:string,idclub:string){
        return this._http.get(this.url+'confirmclubreq/'+id+'/'+idclub).map(res => res.json());   
    }

    expulsarMiembro(id:string,idclub:string){
        return this._http.get(this.url+'kickmember/'+id+'/'+idclub).map(res => res.json());
    }

    comprobarLibro(idclub:string){
        return this._http.get(this.url+'checkbook/'+idclub).map(res => res.json());   
    }

    unirseClub(id:string,idclub:string){
        return this._http.get(this.url+'joinclub/'+id+'/'+idclub).map(res => res.json());
    }

    solicitarClub(id:string,idclub:string){
        return this._http.get(this.url+'requestclub/'+id+'/'+idclub).map(res => res.json());   
    }

    getComentarios(idclub:string,isbn:string){
        return this._http.get(this.url+'getcomments/'+idclub+'/'+isbn).map(res => res.json());
    }

    comentar(id:string,idclub:string,isbn:string,comentario:string){
        return this._http.get(this.url+'comment/'+id+'/'+idclub+'/'+isbn+'/'+comentario).map(res => res.json());   
    }

    acabarLibro(idclub:string,isbn:string){
        return this._http.get(this.url+'finishbook/'+idclub+'/'+isbn).map(res => res.json());
    }

    getMiembros(idclub:string){
        return this._http.get(this.url+'miembros/'+idclub).map(res => res.json());
    }

    getAmigos(id: string){
        return this._http.get(this.url+'amigos/'+id).map(res => res.json());
    }

    getAmigosFiltro(id: string,filtro: string){
        return this._http.get(this.url+'amigosfiltro/'+id+'/'+filtro).map(res => res.json());
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

    borrarSolicitud(id: string,idamigo: string){
        return this._http.get(this.url+'deletesolicitud/'+id+'/'+idamigo).map(res => res.json());      
    }

    comprobarTieneClub(id: string){
        return this._http.get(this.url+'checkuserclub/'+id).map(res => res.json());
    }

    aceptarSolicitud(id: string,idamigo: string){
        return this._http.get(this.url+'aceptarsolicitud/'+id+'/'+idamigo).map(res => res.json());              
    }

    getNoAmigos(id: string){
        return this._http.get(this.url+'noamigos/'+id).map(res => res.json());   
    }

    getNoAmigosFiltro(id: string,filtro: string){
        return this._http.get(this.url+'noamigosfiltro/'+id+'/'+filtro).map(res => res.json());   
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

    totalesAdmin(){
        return this._http.get(this.url+'totalesadmin').map(res => res.json());
    }

    addClub(club:Club){
        let json = JSON.stringify(club);
        let params = 'json='+json;
        let headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});

        return this._http.post(this.url+'addclub',params,{headers:headers})
                .map(res => res.json());
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