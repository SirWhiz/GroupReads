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
import { NuevoAutorComponent } from './autores/nuevoautor.component';
import { EditarAutorComponent } from './autores/editarautor.component';

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
    DialogoLibroComponent,
    DialogoPortadaComponent,
    DialogoBorrarFotoAutor,
    MenuUsuarioComponent,
    MenuAdminComponent,
    ColaboradoresComponent,
    MenuColaboradorComponent,
    LibrosComponent,
    NuevoLibroComponent,
    EditarLibroComponent,
    GenerosComponent,
    DialogoNuevoGenero,
    DialogoEditarGenero,
    AutoresComponent,
    NuevoAutorComponent,
    EditarAutorComponent,
    DialogoBorrarAutor
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
                    DialogoBorrarFotoAutor
                    ]
})
export class AppModule { }
