tareasDataStore = new Ext.data.Store({
    id: 'tareasDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
    }),
      baseParams:{limit: TAM_PAGINA}, // this parameter is passed for any HTTP request
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
        {name: 'id_estado_vto',     type: 'string', mapping: 'id_estado_vto'},
        {name: 'estado_vto',        type: 'string', mapping: 'estado_vto'},
        {name: 'fecha_alta',         type: 'string', mapping: 'fecha_alta'},
        {name: 'fecha_accion',         type: 'string', mapping: 'fecha_accion'},
        {name: 'usuario_alta',   type: 'string', mapping: 'usuario_alta'},
        {name: 'usuario_responsable',   type: 'string', mapping: 'usuario_responsable'},
        {name: 'id_estado',        type: 'int', mapping: 'id_estado'},
        {name: 'estado',        type: 'string', mapping: 'estado'},
        {name: 'area',        type: 'string', mapping: 'area'},
        {name: 'editada',        type: 'string', mapping: 'editada'},
        {name: 'obs',        type: 'string', mapping: 'obs'},
        {name: 'fuente',        type: 'string', mapping: 'fuente'},
        {name: 'th',        type: 'string', mapping: 'th'},
      ]),
//      sortInfo:{field: 'id_tarea', direction: "ASC"},
      remoteSort: true
    });
//asigno el datastore al paginador
paginador.bindStore(tareasDataStore);

buscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_tarea', 'tarea','descripcion'],
    disableIndexes:['descripcion','fecha_alta','fecha_vto','fecha_accion','estado','estado','area','grado_crit'],
    align:'right',
    minChars:3
});
  
tareasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_tarea',
        width: 45,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Fecha Alta',
        dataIndex: 'fecha_alta',
        sortable: true,
        width:  80,
        fixed:true,
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
        header: 'Fuente',
        dataIndex: 'fuente',
        sortable: false,
        width:  100,
        fixed:false,
//        readOnly: true,
        align:'left'
      },{
        header: 'Detalle del hallazgo',
        dataIndex: 'hallazgo',
        width:  220,
        sortable: false,
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
        width:  270,
        sortable: false,
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
        header: 'Vto.',
        dataIndex: 'estado_vto',
        sortable: true,
        width:  60,
        fixed:true,
        readOnly: true,
        renderer: showEstadoVto,
        align:'center'
      },{
        header: 'Usuario Solicitante',
        dataIndex: 'usuario_alta',
        sortable: true,
        width:  150,
        align:'left'
      },{
        header: 'Usuario Responsable',
        dataIndex: 'usuario_responsable',
        sortable: true,
        width:  150,
        align:'left'
      },{
        header: 'Area Responsable',
        dataIndex: 'area',
        sortable: true,
        width:  100,
        renderer:showQtipArea,
        align:'left'
      }]
    );
  
   tareasListingGridPanel =  new Ext.grid.GridPanel({
        id: 'tareasListingGridPanel',
        store: tareasDataStore,
        cm: tareasColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[buscador],
        clicksToEdit:2,
        height:500,
//        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar:[paginador],
        tbar: [/*
           ESTACIO+'Filtros->','Tipo de Herramienta:',tiposHerramientaTareaFiltro,'&emsp;|&emsp;',
            {
                text: 'Quitar Filtros',
    //            tooltip: 'e...',
                iconCls:'quitar_filtros',
                handler: clickBtnQuitarFiltros
            }
        */]
    });   

tareasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

tareasSuperiorPanel = new Ext.Panel(
{
        collapsible: false,
        split: false,
        header: true,
        title: 'Listado de tareas obsoletas',
        region:'center',
        height: 300,
        minSize: 100,
        maxSize: 350,	
        margins: '0 5 5 5',
        html:'<p>panel superior</p>',
        layout: 'fit',
        items:[tareasListingGridPanel]
});

    var altura=Ext.getBody().getSize().height - 60;
    tareasListingGridPanel.setHeight(altura);

    Ext.getCmp('browser').on('resize',function(comp){
            tareasListingGridPanel.setWidth(this.getSize().width);
            tareasListingGridPanel.setHeight(Ext.getBody().getSize().height - 60);

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
function showBtn(value,metaData,record,rowindex,colindex,c,btn){
    var a=record.json.id_estado;
    var th=record.json.th;
    var usuario=record.json.id_responsable
//    console.log(permiso_btn +"--"+ usuario);
    if(permiso_btn==usuario)
    {
        if(a==1 || a==4 || a==5)
        {
            switch(btn)
            {
                case 'cerrar':
                    return 'x-grid-center-icon';                
                    break;
                case 'rechazar':
                    if (th==3)
                        return 'x-hide-display';  
                    else
                        return 'x-grid-center-icon';                
                    break;
                case 'stop':
                    if (th==3)
                        return 'x-grid-center-icon';                
                    else
                        return 'x-hide-display';  
                    break;
            }
        }
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
        case '6':
        metaData.attr = 'style="background-color:#A4A4A4; color:#FFF;"';
        break;
        case '7':
        metaData.attr = 'style="background-color:#151515; color:#FFF;"';
        break;
        case '8':
        metaData.attr = 'style="background-color:#000000; color:#FFF;"';
        break;
        
    }
    var deviceDetail = superData.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        
    return value;
}
function showEstadoVto (value,metaData,superData){
    var estado=superData.json.id_estado_vto;
    switch (estado)
    {
        case '0':
//        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
        metaData.attr = 'style="background-color:#FFF; color:#FFF;"';
        break;
        case '1':
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
tareasListingGridPanel.on('cellclick',clickBtnVerHisotrial);
function clickBtnVerHisotrial(grid, rowIndex, columnIndex){
    if(columnIndex<=10)
    {
        ID_TAREA=grid.store.data.items[rowIndex].data.id_tarea;
        historialTareaDataStore.load({params: {id:ID_TAREA,start: 0}});
        historialAccionesTareaDataStore.load({params: {id:ID_TAREA,start: 0}});
        tareasHistorialesPanel.setTitle('Historial de la Taréa Nro: '+ID_TAREA);
        tareasHistorialesPanel.show();
        tareasHistorialesPanel.expand(false);
    }
//        return iDTramite;
}
tareasListingGridPanel.on('celldblclick', abrir_popup_tareasTareas);
function abrir_popup_tareasTareas(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
                var panelContentTareas;
                var winTareasTareas;
                var enc=['<html>','<div class="tabla_popup_grilla">'];
                var pie=['<br class="popup_clear"/></div>','</html>'];
                var nodos=[
                            '<p>',
                                '<div class="titulo">Detalle Tarea Nro '+data.id_tarea+'<br></div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Fecha de Alta:</div>',
                                '<div class="col2">'+data.fecha_alta+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Fecha Vencimiento:</div>',
                                '<div class="col2">'+data.fecha_vto+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Fuente:</div>',
                                '<div class="col2">'+data.fuente+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Usuario Alta:</div>',
                                '<div class="col2">'+data.usuario_alta+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Usuario Responsable:</div>',
                                '<div class="col2">'+data.usuario_responsable+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Area:</div>',
                                '<div class="col2">'+data.area+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Estado:</div>',
                                '<div class="col2">'+data.estado+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Grado criticidad:</div>',
                                '<div class="col2">'+data.grado_crit+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Hallazgo:</div>',
                                '<div class="col2_s">'+data.hallazgo+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Tarea:</div>',
                                '<div class="col2_s">'+data.tarea+'</div>',
                            '</p>'
                ];
                
                if (data.obs!="")
                    {
                        nodos.push('<p>');
                        nodos.push('<div class="col1"><div class ="col_rechazo">Motivo rechazo:</div></div>');
                        nodos.push('<div class="col2_s"><div class ="col_rechazo">'+data.obs+'</div></div>');
                        nodos.push('</p>');
                    }
                if (data.obs_ob!="" && data.obs_ob!= null)
                    {
                        nodos.push('<p>');
                        nodos.push('<div class="col1"><div class ="col_rechazo">Texto Obsoleto:</div></div>');
                        nodos.push('<div class="col2_s"><div class ="col_rechazo">'+data.obs_ob+'</div></div>');
                        nodos.push('</p>');
                    }
                var html = enc.concat(nodos);
                var html = html.concat(pie);

                        winTareasTareas = new Ext.Window({
                                title: 'Tarea Nro '+data.id_tarea,
                                closable: true,
                                modal:true,
                                //closeAction: 'hide',
                                width: 790,
                                boxMinWidth:790,
                                height: 550,
                                boxMinHeight:550,
                                plain: true,
                                autoScroll:true,
                                layout: 'absolute',
                                html: html.join(''),
//                                items: [],
                                buttons: [{
                                        text: 'Cerrar',
                                        handler: function(){
                                                winTareasTareas.hide();
                                                winTareasTareas.destroy();

                                        }
                                }]
                        });
//                };
                winTareasTareas.show();

}