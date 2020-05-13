periodosTopsAdminJS = new Ext.data.JsonStore({
	url: CARPETA+'/periodos_combo',
	root: 'rows',
	fields: ['id_periodo', 'periodo']
});
periodosTopsAdminJS.load();
periodosTopsAdminJS.on('load' , function(  js , records, options ){
	var tRecord = Ext.data.Record.create(
            {name: 'id_periodo', type: 'int'},        
            {name: 'periodo', type: 'string'}
	);
	var myNewT = new tRecord({
		id_periodo: -1,
		periodo   : 'Todos'
	});
	periodosTopsAdminJS.insert( 0, myNewT);	
} );
periodosTopsAdminFiltro = new Ext.form.ComboBox({
    id:'periodosTopsAdminFiltro',
    forceSelection : true,
    value: PERIODO,
    store: periodosTopsAdminJS,
    editable : false,
    displayField: 'periodo',
    valueField:'id_periodo',
    allowBlank: false,
    width:  200,
    selectOnFocus:true,
    triggerAction: 'all'
});
periodosTopsAdminFiltro.on('select', filtrarGrillaTopsAdmin);

ddpTopsAdminDataStore = new Ext.data.GroupingStore({
    id: 'ddpTopsAdminDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
    root: 'rows',
    totalProperty: 'total',
    id: 'id_top'
    },[ 
    {name: 'id_top',        type: 'int',        mapping: 'id_top'},        
    {name: 'periodo',       type: 'string',     mapping: 'periodo'},
    {name: 'fecha_alta',    type: 'string',     mapping: 'fecha_alta'},
    {name: 'usuario',       type: 'string',     mapping: 'usuario'},
    {name: 'supervisor',    type: 'string',     mapping: 'supervisor'},
    {name: 'aprobador',     type: 'string',     mapping: 'aprobador'},
    {name: 'gerencia',      type: 'string',     mapping: 'gerencia'},
    {name: 'puesto',        type: 'string',     mapping: 'puesto'},
    {name: 'estado',        type: 'string',     mapping: 'estado'},
    {name: 's_pesos',       type: 'float',      mapping: 's_pesos'},
    {name: 'q_obj',         type: 'int',        mapping: 'q_obj'},
    {name: 'q_obj_sup',     type: 'int',        mapping: 'q_obj_sup'},
    {name: 'q_obj_aprob',   type: 'int',        mapping: 'q_obj_aprob'},
    {name: 'habilitado',    type: 'int',        mapping: 'habilitado'},
    {name: 's_pesoreal',    type: 'float',      mapping: 's_pesoreal'}
    ]),
    sortInfo:{field: 'estado', direction: "ASC"},  
//    groupField:'estado',
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(ddpTopsAdminDataStore);

botonesAction = new Ext.grid.ActionColumn({
    editable:false,
    menuDisabled:true,
    header:'Acción',
    hideable:false,
    align:'center',
    width:  120,
//    tooltip:'Ver TOP',
    hidden:false,
    items:[
        {
            icon:URL_BASE+'images/tooloptions.png',
            iconCls :'col_accion',
            tooltip:'Ver TOP',
            getClass: showBtnVerTopDr,
            handler: clickBtnVerTop
        }
        ,{
            icon:URL_BASE+'images/user_edit_blue.png',
            iconCls :'col_accion',
            tooltip:'Editar supervisor',
            //getClass: showBtnVerTopDr,
            handler: dFW_ddpEditaSupervisor
        }
        ,{
            icon:URL_BASE+'images/user_edit_red.png',
            iconCls :'col_accion',
            tooltip:'Editar aprobador',
            //getClass: showBtnVerTopDr,
            handler: dFW_ddpEditaAprobador
        }
        ,{
            icon:URL_BASE+'images/edit-find.png',
            iconCls :'col_accion',
            tooltip:'Auditoria',
            handler: clickBtnVerHistorial
        }
        ,{
            icon:URL_BASE+'images/101.png',
            iconCls :'col_accion',
            tooltip:'Eliminar TOP',
    //        hidden: true,
//            getClass: showBtn,
            handler: clickBtnEliminarTop
        }
    ]
});

//ddpTopsAdminhabilitadaCheck = new Ext.grid.CheckColumn({
//    id:'habilitado',
//    header: "Habilitado",
//    dataIndex: 'habilitado',
//    width: 60,
//    sortable: true,
//    menuDisabled:true,
//    pintar_deshabilitado:true,
//    disabled: false, //-->NO FUNCIONA
//    tabla: 'ddp_tops',
//    align:'center',
//    campo_id: 'id_top'
//});


ddpTopsAdminColumnModel = new Ext.grid.ColumnModel(
    [
        {
            header: '#',
            readOnly: true,
            dataIndex: 'id_top',
            width: 40,        
            sortable: true,
            renderer: function(value, cell){
                cell.css = "readonlycell";
                return value;
                },
            hidden: false
        }
        ,{
            header: 'Periodo',
            dataIndex: 'periodo',
            width: 70,
            sortable: true,
            renderer: showQTipPeriodo
        }
        ,{
            header: 'Estado',
            dataIndex: 'estado',
            width: 100,
            sortable: true,
            renderer: showEstado
        }
        ,{
            header: 'Usuario',
            dataIndex: 'usuario',
            width: 170,
            sortable: true
        }
        ,{
            header: 'Puesto',
            dataIndex: 'puesto',
            width: 200,
            sortable: false
        }
        ,{
            header: 'Gerencia',
            dataIndex: 'gerencia',
            width: 200,
            sortable: true
        }
        ,{
            header: 'Supervisor',
            dataIndex: 'supervisor',
            width: 170,
            sortable: true
        }
        ,{
            header: 'Aprobador',
            dataIndex: 'aprobador',
            width: 170,
            sortable: true
        }
        ,{
            header: '&Sigma; <b>Obj</b>',
            tooltip:'Cantidad de Objetivos',
            dataIndex: 'q_obj',
            width: 50,
            align:'center',
            summaryType: 'sum',
            sortable: false
        }
        ,{
            header: '&Sigma; <b>Peso</b>',
            tooltip:'Sumatoria de Pesos',
            dataIndex: 's_pesos',
            width: 60,
            align:'center',
            summaryType: 'sum',
            sortable: false
        }
        ,{
            header: '&Sigma; <b>Peso real</b>',
            tooltip:'Cantidad de pesos reales',
            dataIndex: 's_pesoreal',
            width: 60,
            align:'center',
            summaryType: 'sum',
            sortable: false
        }
        ,botonesAction
//        {
//        header: '&Sigma; <b>Obj sup</b>',
//        tooltip:'Cantidad de Objetivos para supervisar',
//        dataIndex: 'q_obj_sup',
//        width: 70,
//        align:'center',
//        summaryType: 'sum',
//        sortable: false
//        },
        /*ddpTopsAdminhabilitadaCheck,*/
    ]
);
ddpTopsAdminBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
    //    readonlyIndexes:[''],
    disableIndexes:['id_top','periodo','q_obj','s_pesos','q_obj_aprob','q_obj_sup','estado','s_pesoreal'],
    align:'left',
    minChars: 3
});
  
   ddpTopsAdminGrid =  new Ext.grid.GridPanel({
        id: 'ddpTopsAdminGrid',
        title: 'Administración TOPs - Listado de personal',
        store: ddpTopsAdminDataStore,
        cm: ddpTopsAdminColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        renderTo: 'grillita',
//        viewConfig: {
//            forceFit: false
//        },
        view: new Ext.grid.GroupingView({
            forceFit:true,
            showGroupName:false,
            groupTextTpl: '{text} <b style="color:#A4A4A4;">({[values.rs.length]} {[values.rs.length > 1 ? "objetivos" : "objetivo"]})</b>'
        }),
        plugins:[ddpTopsAdminBuscador/*,ddpTopsAdminhabilitadaCheck*/],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
//        tbar: ['<b>Filtrar por-></b> Período',periodosTopsAdminFiltro]
        tbar: [{
                text: 'Descargar reporte',
                //tooltip: 'e...',
                iconCls:'archivo_excel_ico',
                handler: clickBtnExcelTopAdmin
            }]
    }); 

  ddpTopsAdminDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

var altura=Ext.getBody().getSize().height - 60;
ddpTopsAdminGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
        ddpTopsAdminGrid.setWidth(this.getSize().width);
        ddpTopsAdminGrid.setHeight(Ext.getBody().getSize().height - 60);

});
function msgProcess(titulo){
    Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:300, 
        wait:true, 
        waitConfig: {interval:200}
    });
}
function clickBtnExcelTopAdmin (){
    Ext.Ajax.request({ 
        url: LINK_GENERICO+'/sesion',
        method: 'POST',
        waitMsg: 'Por favor espere...',
        success: function(response, opts) {
            var result=parseInt(response.responseText);
            switch (result)
            {
                case 0:
                case '0':
                    location.assign(URL_BASE_SITIO+"admin");
                    break;
                case 1:
                case '1':
                    go_clickBtnExcelTopAdmin();
                    break;
            }
      },
        failure: function(response) {
            location.assign(URL_BASE_SITIO+"admin");
        }
        });
}
function go_clickBtnExcelTopAdmin(){
    var txt='¿Confirma la descarga del archivo?';
    Ext.MessageBox.confirm('Confirmar',txt, function(btn, text){
        if(btn=='yes'){
            msgProcess('Generando...');
            var body = Ext.getBody();
            var ddpAdimTopsDownloadFrame = body.createChild({
                 tag: 'iframe',
                 cls: 'x-hidden',
                 id: 'ddp-admin-app-upload-frame',
                 name: 'uploadframe'
             });
            var ddpAdimTopsDownloadForm = body.createChild({
                 tag: 'form',
                 cls: 'x-hidden',
                 id: 'ddp-admin-app-upload-form',
                 target: 'ddp-admin-app-upload-frame'
             });
            Ext.Ajax.request({
                url: CARPETA+'/excel/',
                timeout:10000,
                scope :this,
                params: {
                    query   : ddpTopsAdminDataStore.baseParams.query,
                    fields  : ddpTopsAdminDataStore.baseParams.fields,
                    sort    : ddpTopsAdminDataStore.baseParams.sort,
                    dir     : ddpTopsAdminDataStore.baseParams.dir
                },
                form: ddpAdimTopsDownloadForm,
                callback:function (){
                Ext.Msg.alert('Status', 'Datos generados correctamente.');
            },
                isUpload: true,
                 success: function(response, opts) {
                 },
                failure: function(response, opts) {
                 }
            });
            Ext.Msg.alert('Descarga de archivo', 'Descarga en proceso. Por Favor aguarde a que se abra la ventana de descarga...');
        }
    });
}

function showQTipPeriodo(value, metaData,record){
    var deviceDetail = record.get('periodo');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipSupervisores(value, metaData,record){
    var deviceDetail = record.get('supervisores');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function clickBtnVerTop(grid,rowIndex,colIndex,item ,event){
    var id=grid.getStore().getAt(rowIndex).json.id_top;
    Ext.get('browser').load({
        url: CARPETA+"/top_usuario/33",
        params: {id: id},
        scripts: true,
        text: "Cargando..."
    });
}
function filtrarGrillaTopsAdmin (combo, record, index){
    ddpTopsAdminDataStore.load({
            params: {
                    filtro_id_periodo: Ext.getCmp('periodosTopsAdminFiltro').getValue()
            }
    });	
};
function showBtnVerTopDr(value,metaData,record){
    var a=record.json.id_top;
    if(a=="" || a==0 || a=='0' || a==null)
    {
        return 'x-hide-display';  
    }            
    else
        return 'x-grid-center-icon';                
};
function showEstado(value,metaData,record){
    var a=record.json.id_estado;
    switch (a) {
        case '1':
        case 1:
            metaData.attr = 'style="color:#000;background-color:#FFFA54;"';
            break;
        case '2':
        case 2:
            metaData.attr = 'style="color:#000;background-color:#96CCFF;"';
            break;
        case '4':
        case 4:
            metaData.attr = 'style="color:#FFF;background-color:#21610B;"';
            break;
            
        default:
            metaData.attr = 'style="color:#FFF;background-color:#FF0000;"';
            break;
    }
    return value;
};

function clickBtnEliminarTop (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"sys");break;case 1:case '1':go_clickBtnEliminarTop(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"sys");}});}

function go_clickBtnEliminarTop(grid,rowIndex,colIndex,item,event)
{
    var record = grid.getStore().getAt(rowIndex);
    var usuario = record.data.usuario;
    var periodo = record.data.periodo;
    Ext.MessageBox.confirm('Confirmar','¿Confirma que desea eliminar la TOP del usuario '+ usuario +' del periodo ' + periodo +'?', function(btn, text){
        if(btn=='yes'){
            var id_top=record.data.id_top;
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/eliminar_top', 
                params: { 
                    id_top  : id_top
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                        case 0:
                            Ext.MessageBox.alert('Error','No se pudo eliminar la TOP.');
                            break;
                        case 1:
                            Ext.MessageBox.alert('Operación OK','Registro eliminado correctamente');
                            ddpTopsAdminDataStore.reload();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','TOP inexistente.');
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','No tiene los permisos necesarios para realizar dicha acción.');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo eliminar la TOP.');
                            break;
                    }
                },
                failure: function(response){
                    var result=eval(response.responseText);
                    Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
                }
           });
        }
    });
}

