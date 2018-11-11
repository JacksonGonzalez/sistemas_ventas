<template>
            <main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Ingreso
                        <button type="button" @click="mostrarDetalle()" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <template v-if="listado==1">
                    <!-- LISTADO -->  
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select class="form-control col-md-3" v-model="criterio">
                                        <option value="tipo_comprobante">Tipo Comprobante</option>
                                        <option value="num_comprobante">Número Comprobante</option>
                                        <option value="fecha_hora">Fecha - Hora</option>
                                        </select>
                                        <input type="text" v-model="buscar" @keyup.enter="listarIngreso(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                        <button type="submit" @click="listarIngreso(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>Usuario</th>
                                            <th>Proveedor</th>
                                            <th>Tipo Comprobante</th>
                                            <th>Serie Comprobante</th>
                                            <th>Numero Comprobante</th>
                                            <th>Fecha Hora</th>
                                            <th>Total</th>
                                            <th>Impuesto</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ingreso in arrayIngreso" :key="ingreso.id">
                                            <td>
                                                <button type="button" @click="abrirModal('ingreso','actualizar',ingreso)" class="btn btn-success btn-sm">
                                                <i class="icon-eye"></i>
                                                </button>
                                                <template v-if="ingreso.estado == 'Registrado'">
                                                    <button type="button" @click="desactivarIngreso(ingreso.id)" class="btn btn-danger btn-sm">
                                                        <i class="icon-trash"></i>
                                                    </button>
                                                </template>
                                                
                                            </td>
                                            <td v-text="ingreso.usuario"></td>
                                            <td v-text="ingreso.nombre"></td>
                                            <td v-text="ingreso.tipo_comprobante"></td>
                                            <td v-text="ingreso.serie_comprobante"></td>
                                            <td v-text="ingreso.num_comprobante"></td>
                                            <td v-text="ingreso.fecha_hora"></td>
                                            <td v-text="ingreso.total"></td>
                                            <td v-text="ingreso.impuesto"></td>
                                            <td v-text="ingreso.estado"></td>
                                        </tr>                                
                                    </tbody>
                                </table>
                            </div>
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item" v-if="pagination.current_page > 1">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Ant</a>
                                    </li>
                                    <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar,criterio)" v-text="page"></a>
                                    </li>
                                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Sig</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <!-- FIN LISTADO -->
                    </template>

                    <template v-else>
                    <!-- DETALLE -->
                        <div class="card-body">
                            <div class="form-group row border">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="">Proveedor(*)</label>
                                        <v-select
                                            :on-search="selectProveedor"
                                            label="nombre"
                                            :options="arrayProveedor"
                                            placeholder="Buscar Proveedores..."
                                            :onChange="getDatosProveedor"                                        
                                        >

                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Impuesto(*)</label>
                                    <input type="text" class="form-control" v-model="impuesto">
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Tipo de Comprobante(*)</label>
                                        <select name="form-control" class="form-control" id="" v-model="tipo_comprobante">
                                            <option value="">Seleccione un tipo de Comprobante</option>
                                            <option value="BOLETA">Boleta</option>
                                            <option value="FACTURA">Factura</option>
                                            <option value="TICKET">Ticket</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Serie Comprobante</label>
                                        <input type="text" class="form-control" v-model="serie_comprobante" placeholder="000x">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Numero Comprobante</label>
                                        <input type="text" class="form-control" v-model="num_comprobante" placeholder="000xx">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row border">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Artículo <span style="color:red;" v-show="idarticulo==0">(* Seleccione un Artículo)</span></label>
                                        <div class="form-inline">
                                            <input type="text" class="form-control" v-model="codigo" @keyup.enter="buscarArticulo()" placeholder="Ingrese artículo">
                                            <button class="btn btn-primary">...</button>
                                            <input type="text" readonly class="form-control" v-model="articulo">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Precio <span style="color:red;" v-show="precio==0">(* Ingrese Precio)</span></label>
                                        <input type="number" value="0" step="any" v-model="precio" class="form-control">

                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Cantidad <span style="color:red;" v-show="cantidad==0">(* cantidad)</span></label>
                                        <input type="number" value="0" v-model="cantidad" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button @click="agregarDetalle()" class="btn btn-success form-control btnagregar"><i class="icon-plus"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row border">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Opciones</th>
                                                <th>Artículo</th>
                                                <th>Precío</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <!-- CUANDO TENGA DATOS -->
                                        <tbody v-if="arrayDetalle.length">
                                            <tr v-for="(detalle, index) in arrayDetalle" :key="detalle.id"> 
                                                <td>
                                                    <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                                        <i class="icon-close"></i>
                                                    </button>
                                                </td>
                                                <td v-text="detalle.articulo"></td>
                                                <td>
                                                    <input v-model="detalle.precio" type="number" value="3" class="form-control">
                                                </td>
                                                <td>
                                                    <input v-model="detalle.cantidad" type="number" value="5" class="form-control">
                                                </td>
                                                <td>
                                                    {{ detalle.precio * detalle.cantidad}}
                                                </td>
                                            </tr>
                                            
                                            <tr style="background-color: #CEECF5;">
                                                <td colspan="4" align="right"><strong>Total Parcial: </strong></td>
                                                <td>$ 52554</td>
                                            </tr>

                                            <tr style="background-color: #CEECF5;">
                                                <td colspan="4" align="right"><strong>Total Impuesto: </strong></td>
                                                <td>$ 547</td>
                                            </tr>
                                            
                                            <tr style="background-color: #CEECF5;">
                                                <td colspan="4" align="right"><strong>Total Neto: </strong></td>
                                                <td>$ 4567</td>
                                            </tr>
                                        </tbody>
                                        <!-- CUANDO NO TENGA DATOS -->
                                        <tbody v-else>
                                            <tr>
                                                <td colspan="5" class="text-center">No hay Articulos Registrados</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-secondary" @click="ocultarDetalle()">Cerrar</button>
                                    <button type="button" class="btn btn-primary" @click="registrarIngreso()">Registrar Compra</button>
                                </div>
                            </div>
                        </div>
                    <!-- FIN DEL DETALLE -->
                    </template>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
        </main>
</template>

<script>
    import vSelect from '../../../../node_modules/vue-select';

    export default {
        data (){
            return {
                ingreso_id: 0,
                idproveedor : 0,
                nombre : '',
                tipo_comprobante : 'BOLETA',
                serie_comprobante : '',
                num_comprobante : '',
                impuesto : 0.19,
                total : 0.0,
                arrayIngreso: [],
                arrayDetalle : [],
                arrayProveedor: [],
                listado: 1,
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorIngreso : 0,
                errorMostrarMsjIngreso : [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'num_comprobante',
                buscar : '',
                arrayArticulo: 0,
                idarticulo: 0,
                codigo: '',
                articulo : '',
                precio : 0,
                cantidad : 0
            }
        },
        components:{
            vSelect
        },
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                
                var from = this.pagination.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }  

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;             

            }
        },
        methods : {
            listarIngreso (page,buscar,criterio){
                let me=this;
                var url= '/ingreso?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayIngreso = respuesta.ingresos.data;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectProveedor(search,loading){
                let me=this;
                loading(true)

                var url= '/proveedor/selectProveedor?filtro='+search;
                axios.get(url).then(function (response) {
                    let respuesta = response.data;
                    q: search
                    me.arrayProveedor=respuesta.proveedores;
                    loading(false)
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getDatosProveedor(val1){
                let me = this;
                me.loading = true;
                me.idproveedor = val1.id;
            },
            buscarArticulo(){
                let me=this;

                var url= '/articulo/buscarArticulo?filtro=' + me.codigo;
                axios.get(url).then(function (response) {
                    let respuesta = response.data;
                    me.arrayArticulo=respuesta.articulos;

                    if(me.arrayArticulo.length > 0){
                        me.articulo = me.arrayArticulo[0]['nombre'];
                        me.idarticulo = me.arrayArticulo[0]['id'];
                    }else{
                        me.articulo = 'No existen artículos';
                        me.idarticulo = 0;
                    }
                    
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina(page,buscar,criterio){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarIngreso(page,buscar,criterio);
            },
            encuentra(id){
                var sw= false;
                for (let i = 0; i < this.arrayDetalle.length; i++) {
                    if(this.arrayDetalle[i].idarticulo == id){
                        sw = true
                    }
                    
                }

                return sw;
            },
            agregarDetalle(){
                let me = this;

                if(me.idarticulo == 0 || me.cantidad == 0 || me.precio == 0){

                }else{
                    if(me.encuentra(me.idarticulo)){
                        swal({
                            type: 'error',
                            title: 'Error...',
                            text: 'Ese articulo ya se encuentra registrado!'
                        })
                    }else{
                        me.arrayDetalle.push({
                            idarticulo: me.idarticulo,
                            articulo : me.articulo,
                            cantidad : me.cantidad,
                            precio : me.precio
                        });

                        me.codigo = '';
                        me.idarticulo = 0;
                        me.articulo = '';
                        me.cantidad = 0;
                        me.precio = 0;
                    }
                }
            },
            eliminarDetalle(index){
                let me = this;
                me.arrayDetalle.splice(index, 1);
            },
            registrarPersona(){
                if (this.validarPersona()){
                    return;
                }
                
                let me = this;

                axios.post('/user/registrar',{
                    'nombre': this.nombre,
                    'tipo_documento': this.tipo_documento,
                    'num_documento' : this.num_documento,
                    'direccion' : this.direccion,
                    'telefono' : this.telefono,
                    'email' : this.email,
                    'usuario': this.usuario,
                    'password': this.password,
                    'idrol' : this.idrol

                }).then(function (response) {
                    me.cerrarModal();
                    me.listarPersona(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarPersona(){
               if (this.validarPersona()){
                    return;
                }
                
                let me = this;

                axios.put('/user/actualizar',{
                    'nombre': this.nombre,
                    'tipo_documento': this.tipo_documento,
                    'num_documento' : this.num_documento,
                    'direccion' : this.direccion,
                    'telefono' : this.telefono,
                    'email' : this.email,
                    'usuario': this.usuario,
                    'password': this.password,
                    'idrol' : this.idrol,
                    'id': this.persona_id
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarPersona(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                }); 
            },
            desactivarUsuario(id){

                swal({
                    title: 'Estas seguro de desactivar este Usuario?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.value) {
                            
                            let me = this;

                            axios.put('/user/desactivar',{
                                'id' : id
                            }).then(function (response) {
                            
                                me.listarPersona(1, '', 'nombre');

                                swal(
                                'Desactivado!',
                                'Su usuario ha sido desactivado.',
                                'success'
                                )
                            }).catch(function (error) {
                                console.log(error);
                            });

                        }
                })
            },
            activarUsuario(id){

                swal({
                    title: 'Estas seguro de activar este Usuario?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.value) {
                            
                            let me = this;

                            axios.put('/user/activar',{
                                'id' : id
                            }).then(function (response) {
                            
                                me.listarPersona(1, '', 'nombre');

                                swal(
                                'Activado!',
                                'Su usuario ha sido activado.',
                                'success'
                                )
                            }).catch(function (error) {
                                console.log(error);
                            });

                        }
                })
            },         
            validarPersona(){
                this.errorPersona=0;
                this.errorMostrarMsjPersona =[];

                if (!this.nombre) this.errorMostrarMsjPersona.push("El nombre de la persona no puede estar vacío.");
                if (!this.usuario) this.errorMostrarMsjPersona.push("El nombre de usuario no puede estar vacío.");
                if (!this.password) this.errorMostrarMsjPersona.push("La contraseña no puede estar vacía.");
                if (this.idrol == 0) this.errorMostrarMsjPersona.push("Debe seleccionar un rol.");

                if (this.errorMostrarMsjPersona.length) this.errorPersona = 1;

                return this.errorPersona;
            },
            mostrarDetalle(){
                this.listado = 0;
            },
            ocultarDetalle(){
                this.listado = 1;
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.nombre='';
                this.tipo_documento='RUT';
                this.num_documento='';
                this.direccion='';
                this.telefono='';
                this.email='';
                this.usuario='';
                this.password='';
                this.idrol = 0;
                this.errorPersona=0;

            },
            abrirModal(modelo, accion, data = []){
                this.selectRol();
                switch(modelo){
                    case "persona":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Usuario';
                                this.nombre= '';
                                this.tipo_documento='CC';
                                this.num_documento='';
                                this.direccion='';
                                this.telefono='';
                                this.email='';
                                this.usuario='';
                                this.password='';
                                this.idrol = 0;
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {
                                //console.log(data);
                                this.modal=1;
                                this.tituloModal='Actualizar Usuario';
                                this.tipoAccion=2;
                                this.persona_id=data['id'];
                                this.nombre = data['nombre'];
                                this.tipo_documento = data['tipo_documento'];
                                this.num_documento = data['num_documento'];
                                this.direccion = data['direccion'];
                                this.telefono = data['telefono'];
                                this.email = data['email'];
                                this.usuario = data['usuario'];
                                this.password = data['password'];
                                this.idrol = data['idrol'];
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.listarIngreso(1,this.buscar,this.criterio);
        }
    }
</script>
<style>    
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
    
    @media (min-width:600px) {
        .btnagregar {
            margin-top: 1.9rem;
        }
    }
</style>
