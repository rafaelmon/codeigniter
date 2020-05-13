auditoriaDataStore = new Ext.data.Store({
    id: 'auditoriaDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
    }),
//      baseParams:{tampagina: TAM_PAGINA}, 
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_historial'
      },[ 
        {name: 'id_auditoria',      type: 'int',    mapping: 'id_auditoria'},        
        {name: 'q_usuarios',        type: 'int',    mapping: 'q_usuarios'},        
        {name: 'q_hallazgos',       type: 'int',    mapping: 'q_hallazgos'},        
        {name: 'q_tareas',          type: 'int',    mapping: 'q_tareas'},        
        {name: 'usuario_alta',      type: 'string', mapping: 'usuario_alta'},
        {name: 'auditores',         type: 'string', mapping: 'auditores'},
        {name: 'programada',        type: 'string', mapping: 'programada'},
        {name: 'realizada',         type: 'string', mapping: 'realizada'},
        {name: 'sector',            type: 'string', mapping: 'sector'},
        {name: 'fecha',             type: 'string', mapping: 'fecha'},
        {name: 'fecha_alta',        type: 'string', mapping: 'fecha_alta'}
      ]),
//      sortInfo:{field: 'id_auditoria', direction: "ASC"},
      remoteSort: true
});
var auditoriaPaginador= new Ext.PagingToolbar({
    pageSize: TAM_PAGINA,
    displayInfo: true,
    beforePageText:'Página',
    afterPageText:'de {0}',
    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
    firstText:'Priemra Página',
    lastText:'Última Página',
    prevText:'Anterior',
    nextText:'Siguiente',
    refreshText:'Actualizar',
    buttonAlign:'left',
    emptyMsg:'No hay registros para listar'
});
auditoriaPaginador.bindStore(auditoriaDataStore);

botonesAuditoriaAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acciónes',
                hideable:false,
                align:'center',
                width:  90,
                tooltip:'Nuevo Hallazgo',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/report_plus.png',
                    iconCls :'col_accion',
                    tooltip:'Agregar hallazgo',
                    hidden: false,
                    getClass:showBtnNuevoHallazgo,
//                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnNuevoHallazgoAuditoria 
                }]
});
    
auditoriaBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_tarea', 'tarea','descripcion'],
    disableIndexes:['id_auditoria','fecha_alta','fecha'],
    align:'right',
    minChars:3
});

auditoriaColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_auditoria',
        width: 30,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Fecha',
        dataIndex: 'fecha',
        sortable: false,
        width:  70,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Usuarios',
        dataIndex: 'q_usuarios',
        tooltip:'Cantidad de usuarios',
        width:  30,
        sortable: true
      },{
        header: 'Auditor Alta',
        dataIndex: 'usuario_alta',
        width:  100,
        sortable: false
      },{
        header: 'Auditores',
        dataIndex: 'auditores',
        width:  300,
        sortable: false
      },{
        header: 'Prog.',
        tooltip:'¿Auditoría programada?',
        dataIndex: 'programada',
        width:  40,
        renderer:showSiNo,
        sortable: true,
        align:'center'
      },{
        header: 'Real.',
        tooltip:'¿Auditoría realizada?',
        dataIndex: 'realizada',
        width:  40,
        renderer:showSiNo,
        sortable: true,
        align:'center'
      },{
        header: 'H',
        tooltip:'Cantidad de Hallazgos',
        dataIndex: 'q_hallazgos',
        width:  40,
        sortable: true,
        align:'center'
      },{
        header: 'T',
        tooltip:'Cantidad de Tareas',
        dataIndex: 'q_tareas',
        width:  40,
        sortable: true,
        align:'center'
      },botonesAuditoriaAction
     ]
    );

auditoriaDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

auditoriaGridPanel =  new Ext.grid.GridPanel({
    id: 'auditoriaGridPanel',
    store: auditoriaDataStore,
    cm: auditoriaColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true,
    viewConfig: {
        forceFit: false
    },
    plugins:[auditoriaBuscador],
    bbar:[auditoriaPaginador],
    tbar: [
        {
            text: 'Nueva Auditoria',
            tooltip: 'Crear nueva Auditoria...',
            iconCls:'add',                      // reference to our css
            handler: clickBtnNuevaAuditoria,
            hidden: !permiso_alta
        }
    ]
});   

colAuditorias = new Ext.Panel(
{
        id:'colAuditorias',
        title: 'Listado de Auditorias',
//        region: 'center',
        columnWidth:.6,
        height: 300,
        layout: 'fit',
//        html:'<p>panel inferior</p>',
        items : [auditoriaGridPanel]
});

        



    

        
//        var altura=Ext.getCmp('colAuditorias').getSize().height - 60;
//	auditoriaGridPanel.setHeight(altura);
//	Ext.getCmp('colAuditorias').on('resize',function(comp){
//            auditoriaGridPanel.setWidth(this.getSize().width);
//            auditoriaGridPanel.setHeight(Ext.getBody().getSize().height - 60);
//
//	});

        
//function showQtipHallazgo(value, metaData,record){
//    var deviceDetail = record.get('hallazgo');
//    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
//    return value;
//}

function clickBtnNuevoHallazgoAuditoria (grid, rowIndex){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnNuevoHallazgoAuditoria(grid, rowIndex);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevoHallazgoAuditoria(grid, rowIndex){
    var record = grid.getStore().getAt(rowIndex); 
    var id_auditoria=record.data.id_auditoria;
//    console.log(id_auditoria);
    displayHallazgoAuditoriasFormWindow(id_auditoria);
}
function showBtnNuevoHallazgo(value,metaData,record){
    var usuario=record.json.id_observador
//    if(permiso_btn==usuario)
        return 'x-grid-center-icon';                
//    else
//        return 'x-hide-display';  
};
//function clickBtnNuevaAuditoria(){
//    console.log(this);
//}
function showSiNo (value,metaData,superData){
    switch (value)
    {
        case '0': //No eficiente
        metaData.attr = 'style="background-color:#FF0000; color:#FFF;"'; //verde
        value="No";
        break;
        case '1': //Eficiente
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"'; //Rojo
        value="Si";
        break;
//        case null: //vacio
//        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"'; //Naranja
//        break;
      
    }
    return value;
}