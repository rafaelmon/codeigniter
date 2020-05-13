auditoriasHallazgosDataStore = new Ext.data.Store({
    id: 'auditoriasHallazgosDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado_hallazgos', 
            method: 'POST'
    }),
      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_tarea'
      },[ 
        {name: 'id_hallazgo',       type: 'int',        mapping: 'id_hallazgo'},        
        {name: 'id_auditoria',      type: 'int',        mapping: 'id_auditoria'},
        {name: 'hallazgo',          type: 'string',     mapping: 'hallazgo'},
        {name: 'q_tareas',          type: 'int',     mapping: 'q_tareas'}
      ]),
//      sortInfo:{field: 'id_tarea', direction: "ASC"},
      remoteSort: true
    });
 var paginadorHallazgos= new Ext.PagingToolbar({
    pageSize: parseInt(TAM_PAGINA),
    displayInfo: true,
    beforePageText:'Página',
    afterPageText:'de {0}',
    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
    firstText:'Primera Página',
    lastText:'Última Página',
    prevText:'Anterior',
    nextText:'Siguiente',
    refreshText:'Actualizar',
    buttonAlign:'left',
    emptyMsg:'No hay registros para listar'
});
//asigno el datastore al paginador
paginadorHallazgos.bindStore(auditoriasHallazgosDataStore);

botoneshallazgosTareasAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acciónes',
                hideable:false,
                align:'center',
                width:  90,
                tooltip:'Crear tareas para el hallazgo',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/add.gif',
                    iconCls :'col_accion',
                    tooltip:'Agregar tarea',
                    hidden: false,
//                    getClass:showBtnNuevaTarea,
//                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnNuevaTareaHallazgoAuditoria 
                }]
});

//buscador= new Ext.ux.grid.Search({
//    iconCls:'icon-zoom',
////    readonlyIndexes:['id_tarea', 'tarea','descripcion'],
//    disableIndexes:['id_tarea','descripcion','fecha_alta','fecha_vto','fecha_accion','estado','estado','area'],
//    align:'right',
//    minChars:3
//});

//Filtros
arrayEstadosTarea = new Ext.data.JsonStore({
	url: CARPETA+'/combo_estados',
	root: 'rows',
//        method: 'POST',
	fields: ['id_estado', 'estado']
//        autoload: true
});
//arraySedes.load();
	
arrayEstadosTarea.on('load' , function(  js , records, options ){
											   
	var tRecord = Ext.data.Record.create(
		{name: 'id_estado', type: 'int'},
		{name: 'estado', type: 'string'}
	);
	var myNewT = new tRecord({
		id_estado: '-1',
		estado: 'Todos'
	});
	arrayEstadosTarea.insert( 0, myNewT);	
} );
var estadosTareaFiltro = new Ext.form.ComboBox({
    id:'estadosTareaFiltro',
    forceSelection : true,
    value: 'Todos',
    store: arrayEstadosTarea,
    editable : false,
    displayField: 'estado',
    valueField:'id_estado',
    allowBlank: false,
    selectOnFocus:true,
    width: 150, 
    triggerAction: 'all'
//    clearFilterOnReset : false
});

//Fin Filtros
  
auditoriasHallazgosColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),{
        header: '#',
        readOnly: true,
        dataIndex: 'id_hallazgo',
        width: 30,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Detalle del hallazgo',
        dataIndex: 'hallazgo',
        width:  300,
        sortable: true,
        renderer:showQtipHallazgo,
        readOnly: permiso_modificar
      },{
        header: 'Tareas',
        dataIndex: 'q_tareas',
        width:  50,
        sortable: true
      },botoneshallazgosTareasAction]
    );
  
   auditoriasHallazgosListingGridPanel =  new Ext.grid.GridPanel({
        id: 'auditoriasHallazgosListingGridPanel',
        store: auditoriasHallazgosDataStore,
        cm: auditoriasHallazgosColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[],
//        clicksToEdit:2,
        height:500,
//        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar:[paginadorHallazgos],
        tbar: [{text: ''}]
    });   

//auditoriasHallazgosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});



colAuditoriasHallazgos = new Ext.Panel(
{
        title: 'Listado de hallazgos de auditoria nro:... (seleccione auditoria)',
//        region: 'center',
        columnWidth:.4,
        height: 300,
        layout: 'fit',
//        html:'<p>panel inferior</p>',
        items : [auditoriasHallazgosListingGridPanel]
});

  	var altura=Ext.getBody().getSize().height - 100;
	auditoriasHallazgosListingGridPanel.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		auditoriasHallazgosListingGridPanel.setWidth(this.getSize().width);
		auditoriasHallazgosListingGridPanel.setHeight(Ext.getBody().getSize().height - 100);

	});
        
function showQtipHallazgo(value, metaData,record){
    var deviceDetail = record.get('hallazgo');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showBtn(value,metaData,record){
    var a=record.json.id_estado;
    var usuario=record.json.id_responsable
//    console.log(permiso_btn +"--"+ usuario);
    if(permiso_btn==usuario)
    {
        if(a==1 || a==4 || a==5)
            return 'x-grid-center-icon';                
        else
            return 'x-hide-display';  
    }
    else
        return 'x-hide-display';  
};
function clickBtnNuevaTareaHallazgoAuditoria (grid, rowIndex){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnNuevaTareaHallazgoAuditoria(grid, rowIndex);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevaTareaHallazgoAuditoria(grid, rowIndex){
    var record = grid.getStore().getAt(rowIndex); 
    var id_hallazgo=record.data.id_hallazgo;
    var txt_hallazgo=record.data.hallazgo;
    console.log(txt_hallazgo);
    displayHallazgoAuditoriaTareaFormWindow(id_hallazgo,txt_hallazgo);
}
