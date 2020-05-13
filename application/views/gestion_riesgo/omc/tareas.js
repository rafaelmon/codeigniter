omcTareasDataStore = new Ext.data.Store({
    id: 'omcTareasDataStore',
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
        {name: 'id_grado_crit',     type: 'string', mapping: 'id_grado_crit'},
        {name: 'grado_crit',        type: 'string', mapping: 'grado_crit'},
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
paginador.bindStore(omcTareasDataStore);

//botonesOmcTareasAction = new Ext.grid.ActionColumn({
//		width: 15,
//                editable:false,
//                menuDisabled:true,
//                header:'Acciónes',
//                hideable:false,
//                align:'center',
//                width:  90,
//                tooltip:'Acciones sobre la tarea',
//                 hidden:false,
//		items:[{
//                    icon:URL_BASE+'images/aprobar2.png',
//                    iconCls :'col_accion',
//                    tooltip:'Cerrar',
//                    hidden: true,
//                    getClass:showBtn,
////                    hidden: (!permiso_alta||!rol_editor),
//                    handler: clickBtnCerrar
//                },{
//                    icon:URL_BASE+'images/rechazar4.png',
//                    iconCls :'col_accion',
//                    tooltip:'Rechazar',
//                    hidden: true,
//                    getClass:showBtn,
//                    handler: clickBtnRechazarTarea
//                },{
//                    icon:URL_BASE+'images/edit.png',
//                    iconCls :'col_accion',
//                    tooltip:'Editar',
//                    hidden: true,
//                    getClass:showBtnEditar,
//                    handler: clickBtnEditarTarea
//                }]
//});

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
  
omcTareasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_tarea',
        width: 55,        
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
        header: '&deg;Crit',
        dataIndex: 'grado_crit',
        tooltip:'Grado de criticidad',
        sortable: true,
        width:  50,
        fixed:true,
        readOnly: true,
        renderer: showGrado,
        align:'center'
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
//      ,botonesOmcTareasAction
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
  
   omcTareasListingGridPanel =  new Ext.grid.GridPanel({
        id: 'omcTareasListingGridPanel',
        store: omcTareasDataStore,
        cm: omcTareasColumnModel,
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
        tbar: [
            /*{
                text: 'Nuevo tarea',
                tooltip: 'Crear una nueva tarea...',
                iconCls:'add',                      // reference to our css
//                handler: displayFormWindow,
                hidden: !permiso_alta
            },ESTACIO+'<b>Filtros-><b>','Estado',estadosTareaFiltro,'&emsp;|&emsp;',
            {
                text: 'Quitar Filtros',
    //            tooltip: 'e...',
                iconCls:'quitar_filtros',
                handler: clickBtnQuitarFiltros
            }
            /*, '-', { 
                text: 'Eliminar',
                tooltip: 'Eliminar la tarea seleccionada',
                handler: confirmDeleteOmcTareas,   // Confirm before deleting
                iconCls:'remove',
                hidden: !permiso_eliminar
            }*/
        ]
    });   

//omcTareasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

omcTareasPanel = new Ext.Panel(
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
        items:[omcTareasListingGridPanel]
});

//OmcTareasListingGridPanel.on('afteredit', guardartarea);
  
  
// guarda los cambios en los datos del tarea luego de la edicion
//function guardartarea(oGrid_event)
//{
////    console.log(oGrid_event);
//    var fields = [];
//    fields.push(oGrid_event.field);
//    var values = [];
//    values.push(oGrid_event.value);
//    var encoded_array_f = Ext.encode(fields);
//    var encoded_array_v = Ext.encode(values);
//    Ext.Ajax.request({   
//        waitMsg: 'Por favor espere...',
//        url: CARPETA+'/modificar',
//        params: {
//            id: oGrid_event.record.data.id_tarea,     
//            campos : encoded_array_f,
//            valores : encoded_array_v
//        }, 
//        success: function(response){              
//            var result=eval(response.responseText);
//            switch(result){
//                case 1:
//                    omcTareasDataStore.commitChanges();
//                    omcTareasDataStore.reload();
//                    break;
//                default:
//                    Ext.MessageBox.alert('Uh uh...','No se pudo actualizar...');
//                    break;
//            }
//        },
//        failure: function(response){
//            var result=response.responseText;
//            Ext.MessageBox.alert('error','No se pudo conectar a la Base de Datos. Intente mas tarde');    
//        }                      
//    });  
//}
  
  	var altura=Ext.getBody().getSize().height - 60;
	omcTareasListingGridPanel.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		omcTareasListingGridPanel.setWidth(this.getSize().width);
		omcTareasListingGridPanel.setHeight(Ext.getBody().getSize().height - 60);

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
function showBtnEditar(value,metaData,record){
    var a=record.json.id_estado;
    var usuario=record.json.id_usuario_alta
//    console.log(a);
    if(permiso_btn==usuario)
    {
        if(a==3)
            return 'x-grid-center-icon';                
        else
            return 'x-hide-display';  
    }
    else
        return 'x-hide-display';  
};

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
function showGrado (value,metaData,superData){
    var grado=superData.json.id_grado_crit;
    switch (grado)
    {
        case 1:
        case '1':
        metaData.attr = 'style="background-color:#DC0000; color:#FFF;"';
        break;
        case 2:
        case '2':
        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"';
        break;
        case 3:
        case '3':
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
        break;
        default:
        metaData.attr = 'style="background-color:#D0D0D0; color:#FFF;"';
    }
    return value;
}
 function clickBtnCerrar(grid,rowIndex,colIndex,item,event){
      msgProcess('Cerrando Tarea');
      var id=grid.getStore().getAt(rowIndex).json.id_tarea;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/cerrar',
        method: 'POST',
        params: {
          id:id  
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case 0:
            Ext.MessageBox.alert('Error','El usuario no tiene permisos para cerrar esta tarea');
            break;
          case 1:
            Ext.MessageBox.alert('Operación OK','La Tarea ha sido cerrada correctamente');
            omcTareasDataStore.reload();
            break;
          default:
            Ext.MessageBox.alert('Error','No se ha podido realizar la accion, por favor reintente');
            break;
          }        
        },
        failure: function(response){
//          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
  }
 function clickBtnRechazar(grid,rowIndex,colIndex,item,event){
      msgProcess('Guardando...');
      var id=grid.getStore().getAt(rowIndex).json.id_tarea;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/rechazar',
        method: 'POST',
        params: {
          id:id  
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case 0:
            Ext.MessageBox.alert('Error','El usuario no tiene permisos para cerrar esta tarea');
            break;
          case 1:
            Ext.MessageBox.alert('Operación OK','La Tarea ha sido rechazada');
            omcTareasDataStore.reload();
            break;
          default:
            Ext.MessageBox.alert('Error','No se ha podido realizar la accion, por favor reintente');
            break;
          }        
        },
        failure: function(response){
//          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
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
estadosTareaFiltro.on('select', filtrarGrilla);

function filtrarGrilla( combo, record, index ){
//    switch (combo.id)
//    {
//        case 'estadosTareaFiltro':
//            var id_estado=combo.getValue();
//            var id_tipo_herramienta=2;
//            break;
//            var id_estado=combo.getValue();
//    }
    var id_estado            =Ext.getCmp('estadosTareaFiltro').getValue();
    var id_tipo_herramienta  =Ext.getCmp('tiposHerramientaTareaFiltro').getValue();
    
    var fields = [];
        fields.push('id_estado');
        fields.push('id_tipo_herramienta');
    var values = [];
    values.push(id_estado);
    values.push(id_tipo_herramienta);
// 
	var encoded_array_f = Ext.encode(fields);
	var encoded_array_v = Ext.encode(values);
//	var encoded_array_v = Ext.encode(values);
    omcTareasDataStore.setBaseParam('filtros',encoded_array_v);
    omcTareasDataStore.load();
}
function clickBtnQuitarFiltros(){
    Ext.getCmp('estadosTareaFiltro').reset();
    Ext.getCmp('tiposHerramientaTareaFiltro').reset();
//    store1=Ext.getCmp('estadosTareaFiltro').getStore();
//    store1.setBaseParam('id_estado','');
//    store1.load();
    omcTareasDataStore.setBaseParam('filtros','');
    omcTareasDataStore.load();
}
omcTareasListingGridPanel.on('celldblclick', abrir_popup_tareas_omc);
function abrir_popup_tareas_omc(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
    var winDoc;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    
//    if(data.tipo_wf==1)
//        data.revisores="No LLeva"
    for (var i in data)
        if (data[i]=='')
            data[i]="&nbsp";
    var txt_ar;
    switch (data.ar)
    {
        case '0': //No eficiente
        txt_ar = '<div style="color:#FF0000;">No</div>'; //verde
        break;
        case '1': //Eficiente
        txt_ar = '<div style="color:#088A08;">Si</div>'; //Rojo
        break;
    }
    var labels=[
        {titulo:"Tarea Nro",            valor:data.id_tarea}
        ,{titulo:"Estado",              valor:data.estado}
        ,{titulo:"Grado Crit.",         valor:data.grado_crit}
        ,{titulo:"Fecha alta",          valor:data.fecha_alta}
        ,{titulo:"Fecha L&iacute;mite",        valor:data.fecha_vto}
        ,{titulo:"Usuario Alta",        valor:data.usuario_alta}
        ,{titulo:"Usuario Resp.",       valor:data.usuario_responsable}
        ,{titulo:"Area Resp.",          valor:data.area}
        ,{titulo:"Hallazgo",            valor:data.hallazgo}
        ,{titulo:"Tarea",               valor:data.tarea}
        
    ];
    var nodos= [];
    
    labels.forEach(function(entry){
        var nodo=['<p>',
                    '<div class="col1">'+String(entry.titulo)+':</div>',
                    '<div class="col2">'+String(entry.valor)+'</div>',
                '</p>'];
        nodos.push(nodo.join(''));
    });
    var html=enc;
    html.push(nodos.join(''));
    html.push(pie.join(''));

    winDoc = new Ext.Window({
            title: 'Detalle de Tarea',
            closable: true,
            modal:true,
            //closeAction: 'hide',
            width: 800,
            boxMinWidth:600,
            height: 450,
            boxMinHeight:300,
            plain: true,
            autoScroll:true,
            layout: 'absolute',
            html: html.join(''),
//                                items: [],
            buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                            winDoc.hide();
                            winDoc.destroy();

                    }
            }]
    });
    winDoc.show();
}