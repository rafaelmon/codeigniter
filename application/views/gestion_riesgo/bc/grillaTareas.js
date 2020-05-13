bcTareasDataStore = new Ext.data.Store({
    id: 'bcTareasDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado_tareas', 
            method: 'POST'
    }),
      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_tarea'
      },[ 
        {name: 'id_tarea',      type: 'int',    mapping: 'id_tarea'},        
        {name: 'hallazgo',      type: 'string', mapping: 'hallazgo'},
        {name: 'tarea',         type: 'string', mapping: 'tarea'},
        {name: 'fecha_vto',         type: 'string', mapping: 'fecha_vto'},
        {name: 'fecha_alta',         type: 'string', mapping: 'fecha_alta'},
        {name: 'fecha_accion',         type: 'string', mapping: 'fecha_accion'},
        {name: 'usuario_alta',   type: 'string', mapping: 'usuario_alta'},
        {name: 'usuario_responsable',   type: 'string', mapping: 'usuario_responsable'},
        {name: 'id_estado',        type: 'int', mapping: 'id_estado'},
        {name: 'estado',        type: 'string', mapping: 'estado'},
        {name: 'area',        type: 'string', mapping: 'area'},
        {name: 'editada',        type: 'string', mapping: 'editada'},
        {name: 'obs',        type: 'string', mapping: 'obs'}
      ]),
//      sortInfo:{field: 'id_tarea', direction: "ASC"},
      remoteSort: true
    });
//asigno el datastore al paginador
paginador.bindStore(bcTareasDataStore);

////Filtros
//arrayEstadosTarea = new Ext.data.JsonStore({
//	url: CARPETA+'/combo_estados',
//	root: 'rows',
////        method: 'POST',
//	fields: ['id_estado', 'estado']
////        autoload: true
//});
////arraySedes.load();
//	
//arrayEstadosTarea.on('load' , function(  js , records, options ){
//											   
//	var tRecord = Ext.data.Record.create(
//		{name: 'id_estado', type: 'int'},
//		{name: 'estado', type: 'string'}
//	);
//	var myNewT = new tRecord({
//		id_estado: '-1',
//		estado: 'Todos'
//	});
//	arrayEstadosTarea.insert( 0, myNewT);	
//} );
//var estadosTareaFiltro = new Ext.form.ComboBox({
//    id:'estadosTareaFiltro',
//    forceSelection : true,
//    value: 'Todos',
//    store: arrayEstadosTarea,
//    editable : false,
//    displayField: 'estado',
//    valueField:'id_estado',
//    allowBlank: false,
//    selectOnFocus:true,
//    width: 150, 
//    triggerAction: 'all'
////    clearFilterOnReset : false
//});
//
////Fin Filtros
  
bcTareasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_tarea',
        width: 30,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Fecha Alta',
        dataIndex: 'fecha_alta',
        sortable: false,
        width:  80,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Detalle del hallazgo',
        dataIndex: 'hallazgo',
        width:  220,
        sortable: true,
        renderer:showQtipHallazgo,
        readOnly: permiso_modificar
      },{
        header: 'Tarea a realizar',
        dataIndex: 'tarea',
        width:  220,
        sortable: true,
        renderer:showQtipTarea,
        readOnly: permiso_modificar
      },{
        header: 'Fecha Limite',
        dataIndex: 'fecha_vto',
        sortable: true,
        width:  80,
        fixed:true,
//        renderer:showFecha,
        readOnly: true,
        align:'center'
      },{
        header: 'Estado Actual',
        dataIndex: 'estado',
        sortable: false,
        width:  80,
        fixed:true,
        readOnly: true,
        renderer: showEstado,
        align:'center'
      },{
        header: 'Usuario Solicitante',
        dataIndex: 'usuario_alta',
        sortable: true,
        width:  180,
        align:'left'
      },{
        header: 'Usuario Responsable',
        dataIndex: 'usuario_responsable',
        sortable: true,
        width:  180,
        align:'left'
      },{
        header: 'Area Responsable',
        dataIndex: 'area',
        sortable: true,
        width:  100,
        renderer:showQtipArea,
        align:'left'
      }
//      ,botonesBcTareasAction
        ,
     {
        header: 'Fecha Accion',
        dataIndex: 'fecha_accion',
        sortable: false,
        width:  90,
        fixed:true,
        readOnly: true,
        align:'center'
      }]
    );
  
   bcTareasListingGridPanel =  new Ext.grid.GridPanel({
        id: 'bcTareasListingGridPanel',
        store: bcTareasDataStore,
        cm: bcTareasColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[],
        clicksToEdit:2,
        height:500,
//        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar:[paginador],
        tbar: []
    });   


bcTareasPanel = new Ext.Panel(
{
        collapsible: true,
        collapsed:true,
        split: false,
        header: true,
        title: 'Listado de Tareas',
        region:'south',
        height: 300,
        minSize: 100,
        maxSize: 350,	
        margins: '0 5 5 5',
        html:'<p>panel inferior</p>',
        layout: 'fit',
        items:[bcTareasListingGridPanel]
});
  	var altura=Ext.getBody().getSize().height - 60;
	bcTareasListingGridPanel.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		bcTareasListingGridPanel.setWidth(this.getSize().width);
		bcTareasListingGridPanel.setHeight(Ext.getBody().getSize().height - 60);

	});
        
function showQtipHallazgo(value, metaData,record){
    var deviceDetail = record.get('hallazgo');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipTarea(value, metaData,record){
    var deviceDetail = record.get('tarea');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipArea(value, metaData,record){
    var deviceDetail = record.get('area');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}

function showEstado (value,metaData,superData){
    var estado=superData.json.id_estado;
    switch (estado)
    {
        case '1':
        metaData.attr = 'style="background-color:#01A9DB; color:#FFF;"';
        break;
        case '2':
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
        break;
        case '3':
        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"';
        break;
        case '4':
        metaData.attr = 'style="background-color:#037DA2; color:#FFF;"';
        break;
        case '5':
        metaData.attr = 'style="background-color:#FF0000; color:#FFF;"';
        break;
        
    }
    var deviceDetail = superData.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        
    return value;
}

 
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
//estadosTareaFiltro.on('select', filtrarGrilla);

//function filtrarGrilla( combo, record, index ){
//    var id_estado            =Ext.getCmp('estadosTareaFiltro').getValue();
//    var id_tipo_herramienta  =Ext.getCmp('tiposHerramientaTareaFiltro').getValue();
//    
//    var fields = [];
//        fields.push('id_estado');
//        fields.push('id_tipo_herramienta');
//    var values = [];
//    values.push(id_estado);
//    values.push(id_tipo_herramienta);
//	var encoded_array_f = Ext.encode(fields);
//	var encoded_array_v = Ext.encode(values);
//    bcTareasDataStore.setBaseParam('filtros',encoded_array_v);
//    bcTareasDataStore.load();
//}
//function clickBtnQuitarFiltros(){
//    Ext.getCmp('estadosTareaFiltro').reset();
//    Ext.getCmp('tiposHerramientaTareaFiltro').reset();
////    store1=Ext.getCmp('estadosTareaFiltro').getStore();
////    store1.setBaseParam('id_estado','');
////    store1.load();
//    bcTareasDataStore.setBaseParam('filtros','');
//    bcTareasDataStore.load();
//}