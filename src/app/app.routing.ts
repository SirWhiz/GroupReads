import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LoginGuard } from './login.guard';

//Importar componentes
import { AppComponent } from './app.component';
import { LoginComponent } from './login/login.component';
import { PrincipalComponent } from './principal/principal.component';
import { RegistroComponent } from './registro/registro.component';
import { HomeComponent } from './home/home.component';
import { ConfiguracionComponent } from './configuracion/configuracion.component';
import { ColaboradoresComponent } from './colaboradores/colaboradores.component';
import { LibrosComponent } from './mantenimientoLibros/libros.component';
import { NuevoLibroComponent } from './mantenimientoLibros/nuevolibro.component';
import { EditarLibroComponent } from './mantenimientoLibros/editarlibro.component';
import { GenerosComponent } from './generos/generos.component';
import { AutoresComponent } from './autores/autores.component';
import { NuevoAutorComponent } from './autores/nuevoautor.component';
import { EditarAutorComponent } from './autores/editarautor.component';

const appRoutes: Routes = [
	{path: '', component: PrincipalComponent},
	{path: 'login', component: LoginComponent},
	{path: 'registro', component: RegistroComponent},
	{path: 'home', component: HomeComponent, canActivate: [LoginGuard]},
	{path: 'configuracion', component: ConfiguracionComponent},
	{path: 'colaboradores', component: ColaboradoresComponent},
	{path: 'libros', component: LibrosComponent},
	{path: 'nuevo-libro', component: NuevoLibroComponent},
	{path: 'editar-libro/:isbn', component: EditarLibroComponent},
	{path: 'generos', component: GenerosComponent},
	{path: 'autores', component: AutoresComponent},
	{path: 'nuevo-autor', component: NuevoAutorComponent},
	{path: 'editar-autor/:id', component: EditarAutorComponent},
	{path: '**', component: PrincipalComponent}
];

export const appRoutingProviders: any[] = [];

export const routing: ModuleWithProviders = RouterModule.forRoot(appRoutes);