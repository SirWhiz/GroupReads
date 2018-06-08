import { Component, Inject } from '@angular/core';
import { Router } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';
import { Usuario } from '../registro/usuario';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { Mensaje } from './mensaje';
import * as io from 'socket.io-client';

@Component({
	selector: 'chat',
	templateUrl: './chat.component.html',
	providers: [UsuariosService]
})
export class ChatComponent{

    public usuario:Usuario;
    public amigo:Usuario;
    public mensajes:Mensaje[];
    public texto:string;
    public socket:SocketIOClient.Socket;

	constructor(
        public dialogRef: MatDialogRef<ChatComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any,
        private _usuariosService: UsuariosService,
        public snackBar:MatSnackBar,
        public _router:Router,
    ){
        this.usuario = data.usuario;
        this.amigo = data.amigo;
        this.mensajes = new Array();
        this.texto = "";
        this.socket = io('http://localhost:8080');
    }

    ngOnInit(){
        this._usuariosService.obtenerMensajes(this.usuario.id,this.amigo.id).subscribe(
            result => {
                if(result.code == 200){
                    this.mensajes = result.data;
                }
            }, error => {console.log(error);}
        );

        this.socket.on('message', (data) => {
            if(data.de==this.amigo.id && data.para==this.usuario.id){
                this.mensajes.push(data);
            }
        });
    }

    enviar(){
        var nuevoMensaje = new Mensaje("","","","");
        nuevoMensaje.de = this.usuario.id;
        nuevoMensaje.para = this.amigo.id;
        nuevoMensaje.texto = this.texto;

        this.socket.emit('message', nuevoMensaje);

        this._usuariosService.enviarMensaje(nuevoMensaje).subscribe(
            result => {
                if(result.code == 200){
                    this.mensajes.push(nuevoMensaje);
                    this.texto = "";
                }
            }, error => {console.log(error);}
        );
    }

}
