import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { NgModule } from '@angular/core';
import { HttpModule } from '@angular/http';

import { routing, appRoutingProviders } from './app.routing';
import { AppComponent } from './app.component';
import { LoginComponent } from './login/login.component';
import { PrincipalComponent } from './principal/principal.component';
import { RegistroComponent } from './registro/registro.component';
import { MenuComponent } from './menu/menu.component';
import { HomeComponent } from './home/home.component';
import { DialogoComponent } from './dialogoImagen/dialogo.component';
import { DialogoLibroComponent } from './dialogoLibro/dialogolibro.component';
import { DialogoPortadaComponent } from './dialogoPortada/dialogoportada.component';
import { DialogoBorrarAutor } from './autores/dialogoborrarautor.component';
import { DialogoBorrarFotoAutor } from './autores/dialogoborrarfotoautor.component';
import { ConfiguracionComponent } from './configuracion/configuracion.component';
import { MenuUsuarioComponent } from './menuUsuario/menuusuario.component';
import { DialogoSolicitudesComponent } from './dialogoSolicitudes/dialogosolicitudes.component';
import { MenuAdminComponent } from './menuAdmin/menuadmin.component';
import { MenuColaboradorComponent } from './menuColaborador/menucolaborador.component';
import { ColaboradoresComponent } from './colaboradores/colaboradores.component';
import { LibrosComponent } from './mantenimientoLibros/libros.component';
import { NuevoLibroComponent } from './mantenimientoLibros/nuevolibro.component';
import { EditarLibroComponent } from './mantenimientoLibros/editarlibro.component';
import { GenerosComponent } from './generos/generos.component';
import { DialogoNuevoGenero } from './generos/dialogonuevogenero.component';
import { DialogoEditarGenero } from './generos/dialogoeditargenero.component';
import { AutoresComponent } from './autores/autores.component';
import { AmigosComponent } from './amigos/amigos.component';
import { AgregarAmigosComponent } from './agregarAmigos/agregaramigos.component';
import { NuevoAutorComponent } from './autores/nuevoautor.component';
import { EditarAutorComponent } from './autores/editarautor.component';
import { ClubesComponent } from './clubes/clubes.component';
import { NuevoClubComponent } from './clubes/nuevoclub.component';
import { DialogoEditarClubComponent } from './clubes/dialogoeditar.component';
import { DialogoAbandonarComponent } from './clubes/dialogoabandonar.component';
import { DialogoBorrarClubComponent } from './clubes/dialogoborrar.component';
import { DialogoExpulsarComponent } from './clubes/dialogoexpulsar.component';
import { ElegirLibroComponent } from './elegirLibro/elegirlibro.component';
import { LibrosLeidosComponent } from './librosleidos/librosleidos.component';
import { RecuperarpwdComponent } from './recuperarpwd/recuperarpwd.component';
import { DialogoPwdComponent } from './home/dialogopwd.component';
import { DialogoMasInformacionComponent } from './librosleidos/dialogomasinformacion.component';

import { MatInputModule,MatNativeDateModule } from '@angular/material';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatTabsModule } from '@angular/material/tabs';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatButtonModule } from '@angular/material';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatMenuModule } from '@angular/material/menu';
import { MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialogModule } from '@angular/material/dialog';
import { LoginGuard } from './login.guard';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    PrincipalComponent,
    RegistroComponent,
    MenuComponent,
    HomeComponent,
    ConfiguracionComponent,
    DialogoComponent,
    DialogoPwdComponent,
    DialogoLibroComponent,
    ElegirLibroComponent,
    DialogoPortadaComponent,
    LibrosLeidosComponent,
    DialogoBorrarFotoAutor,
    MenuUsuarioComponent,
    MenuAdminComponent,
    ColaboradoresComponent,
    MenuColaboradorComponent,
    LibrosComponent,
    NuevoLibroComponent,
    EditarLibroComponent,
    DialogoEditarClubComponent,
    DialogoMasInformacionComponent,
    GenerosComponent,
    DialogoBorrarClubComponent,
    DialogoNuevoGenero,
    DialogoEditarGenero,
    AmigosComponent,
    AutoresComponent,
    NuevoAutorComponent,
    EditarAutorComponent,
    DialogoBorrarAutor,
    DialogoAbandonarComponent,
    AgregarAmigosComponent,
    DialogoSolicitudesComponent,
    ClubesComponent,
    NuevoClubComponent,
    DialogoExpulsarComponent,
    RecuperarpwdComponent
  ],
  imports: [
    BrowserModule,
    routing,
    FormsModule,
    MatDatepickerModule,
    MatButtonModule,
    MatFormFieldModule,
    MatNativeDateModule,
    MatInputModule,
    BrowserAnimationsModule,
    MatTooltipModule,
    MatToolbarModule,
    MatMenuModule,
    HttpModule,
    MatSnackBarModule,
    MatTabsModule,
    MatDialogModule
  ],
  providers: [appRoutingProviders,LoginGuard],
  bootstrap: [AppComponent],
  entryComponents: [DialogoComponent,
                    DialogoLibroComponent,
                    DialogoPortadaComponent,
                    DialogoNuevoGenero,
                    DialogoEditarGenero,
                    DialogoBorrarAutor,
                    DialogoBorrarFotoAutor,
                    DialogoSolicitudesComponent,
                    DialogoEditarClubComponent,
                    DialogoAbandonarComponent,
                    DialogoBorrarClubComponent,
                    DialogoExpulsarComponent,
                    DialogoMasInformacionComponent,
                    DialogoPwdComponent
                    ]
})
export class AppModule { }
