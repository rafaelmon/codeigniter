
cppRepoFallasDataStore = new Ext.data.Store({
    id: 'cppRepoFallasDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            waitMsg: 'Por favor espere...',
            method: 'POST'
    }),
      baseParams:{limit: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id'
      },[ 
        {name: 'id',                type: 'int',    mapping: 'id'},        
        {name: 'usuario_alta',      type: 'string', mapping: 'usuario_alta'},
        {name: 'fecha_alta',        type: 'string', mapping: 'fecha_alta'},
        {name: 'fh_ini',            type: 'string', mapping: 'fh_ini'},
        {name: 'fh_fin',            type: 'string', mapping: 'fh_fin'},
        {name: 'estado',            type: 'string', mapping: 'estado'},
//        {name: 'fecha_evento',      type: 'string', mapping: 'fecha_evento'},
        {name: 'descripcion',       type: 'string', mapping: 'descripcion'},
        {name: 'equipo',            type: 'string', mapping: 'equipo'},
        {name: 'producto',          type: 'string', mapping: 'producto'},
        {name: 'sector',            type: 'string', mapping: 'sector'},
        {name: 'criticidad',        type: 'string', mapping: 'criticidad'},
        {name: 'hrs',               type: 'float',  mapping: 'hrs'},
        {name: 'monto',             type: 'float',  mapping: 'monto'},
        {name: 'unidades_perdidas',   type: 'float',  mapping: 'unidades_perdidas'}
      ]),
      sortInfo:{field: 'fh_ini', direction: "DESC"},
      baseParams:{filtros: ''},
      remoteSort: true
    });
//asigno el datastore al paginadorRepoFallas
paginadorRepoFallas= new Ext.PagingToolbar({
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
paginadorRepoFallas.bindStore(cppRepoFallasDataStore);


cppRepoFallasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id',
        width: 30,        
//        fixed:true,
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Usuario Alta',
        dataIndex: 'usuario_alta',
        sorcpple: true,
        width:  120,
//        fixed:true,
        readOnly: true,
        renderer: showTooltipUsuarioAlta,
//        readOnly: permiso_modificar
        align:'left'
      },{
        header: 'Fecha Alta',
        dataIndex: 'fecha_alta',
        sortable: true,
        width:  75,
        fixed:true,
        readOnly: true,
        renderer: showTooltipFechaAlta,
        align:'center'
      },{
        header: 'Inicio',
        dataIndex: 'fh_ini',
        sortable: true,
        width:  100,
        fixed:true,
        readOnly: true,
        renderer: showTooltipInicio,
        align:'center'
      },{
        header: 'Fin',
        dataIndex: 'fh_fin',
        sortable: true,
        width:  100,
        fixed:true,
        readOnly: true,
        renderer: showTooltipFin,
        align:'center'
      },{
        header: 'Estado',
        dataIndex: 'estado',
        sortable: false,
        width:  90,
        fixed:true,
        readOnly: true,
        renderer: showEstado,
        align:'center'
//      },{
//        header: 'Fecha Evento',
//        dataIndex: 'fecha_evento',
//        sortable: true,
//        width:  75,
//        fixed:true,
//        readOnly: true,
//        align:'center'
      },{
        header: 'Descripcion',
        dataIndex: 'descripcion',
        sorcpple: true,
        width:  250,
        fixed:true,
        readOnly: true,
        renderer: showTooltipDescripcion,
        align:'left'
      },{
        header: 'Equipo',
        dataIndex: 'equipo',
        sorcpple: true,
        width:  150,
        fixed:true,
        readOnly: true,
        renderer: showTooltip,
        align:'center'
      },{
        header: 'Producto',
        dataIndex: 'producto',
        sorcpple: true,
        width:  150,
        fixed:false,
        readOnly: true,
        renderer: showTooltip,
        align:'left'
      },{
        header: 'Sector',
        dataIndex: 'sector',
        sorcpple: true,
        width:  120,
        fixed:true,
        readOnly: true,
        renderer: showTooltip,
        align:'center'
      },{
        header: 'Criticidad',
        dataIndex: 'criticidad',
        sorcpple: false,
        width:  40,
//        fixed:false,
        readOnly: true,
        renderer: showCriticidadEvento,
        align:'center'
      },{
        header: 'Hrs',
        dataIndex: 'hrs',
        sorcpple: true,
        width:  70,
        fixed:false,
        readOnly: true,
        tooltip:'Horas perdidas',
        renderer: showHrs,
        align:'right'
      },{
        header: 'TN',
        dataIndex: 'unidades_perdidas',
        sorcpple: true,
        width:  100,
        fixed:false,
        readOnly: true,
        tooltip:'Toneladas perdidas',
        renderer: showToneladas,
        align:'right'
      },{
        header: 'U$S',
        dataIndex: 'monto',
        sorcpple: true,
        width:  100,
        fixed:false,
        readOnly: true,
        tooltip:'Monto U$S perdidas',
        renderer: showMonto,
        align:'right'
      }
  ]
    );
  
   cppRepoFallasGridPanel =  new Ext.grid.GridPanel({
        id: 'cppRepoFallasGridPanel',
        store: cppRepoFallasDataStore,
        cm: cppRepoFallasColumnModel,
        region:'center',
        autoScroll:true,
        enableColLock:false,
        trackMouseOver:true, 
        loadMask: true, 
//        viewConfig: {
//            forceFit: true
//        },
        plugins:[],
//        clicksToEdit:2,
        height:500,
//        layout: 'fit',
//        selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
        bbar:[paginadorRepoFallas],
        tbar: []
    });   
cppRepoFallasGridPanel.on('cellclick',function(grid, rowIndex, columnIndex){
    
    var id_evento=store.data.items[rowIndex].data.id;
    if(columnIndex<(grid.colModel.config.length)-1)
    {
    }
});
cppRepoFallasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

cppRepoFallasPanelPpal = new Ext.Panel(
{
        collapsible: false,
        split: false,
        header: false,
        region:'center',
        height: 300,
        minSize: 100,
        maxSize: 350,	
        margins: '0 5 5 5',
        html:'<p>grilla de eventos</p>',
        layout: 'fit',
        items:[] //cppRepoFallasGridPanel
});

    var altura=Ext.getBody().getSize().height - 60;
    cppRepoFallasGridPanel.setHeight(altura);

    Ext.getCmp('browser').on('resize',function(comp){
            cppRepoFallasGridPanel.setWidth(this.getSize().width);
            cppRepoFallasGridPanel.setHeight(Ext.getBody().getSize().height - 60);
    });



  
function showTooltip(value, metaData,record){
//    var deviceDetail = record.get('hallazgo');
    metaData.attr += 'ext:qtip="'+ value + '"';
    return value;
}

function showCriticidadEvento (value,metaData,superData){
    var id=superData.json.id_criticidad;
    var deviceDetail = superData.json.criticidad; //get('criticidad');

    switch (id)
    {
        case '1': //critica
            metaData.attr = 'style="background-color:#FF0000; color:#FFF;"'; //Rojo
            metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
            break;
        case '2': //alta
            metaData.attr = 'style="background-color:#DF7401; color:#FFF;"'; //Naranja
            metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
            break;
        case '3': //Menor
            metaData.attr = 'style="background-color:#088A08; color:#FFF;"';//verde
            metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
            break;
        default:
            break;
    }
    return value;
}

function showEstado (value,metaData,superData)
{
    var estilo_color = superData.json.estilo_color;
    var id_estado    = superData.json.id_estado;
    if(id_estado == 5 || id_estado == 4)
        metaData.attr = 'style="background-color:'+estilo_color+'; color:#000;"';
    else
        metaData.attr = 'style="background-color:'+estilo_color+'; color:#FFF;"';
    
    return value;
}

cppRepoFallasGridPanel.on('celldblclick', abrir_popup_repoFallas);
function abrir_popup_repoFallas(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
//    var grid= Ext.getCmp('cppEventosGridPanel');
    var cm= grid.getColumnModel();
    var WinRepoFallas;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    var cppla=['<p><div class="titulo">Detetalle del evento nro '+data.id_evento+'<br></div></p>'];
    
    cm.config.forEach(function(a)
    {
        if(a.header != "Acciones")
        {
            if (data[a.dataIndex]!="")
            {
                if(a.header == "Habilitado")
                {
                    data[a.dataIndex] = "Habilitado";
                }
                var nodo=['<p><div class="col1">'+a.header+':</div><div class="col2">'+data[a.dataIndex]+'</div></p>'];
            }
            else
            {
                if(a.header == "Habilitado")
                {
                    data[a.dataIndex] = "Deshabilitado";
                }
                var nodo=['<p><div class="col1">'+a.header+':</div><div class="col2">s/d</div></p>'];
            }
            cppla.push(nodo);
        }
        
    });

    var html = enc.concat(cppla);
    var html = html.concat(pie);

    WinRepoFallas = new Ext.Window({
            title: 'Detalle del evento nro '+data.id_evento,
            closable: true,
            modal:true,
            //closeAction: 'hide',
            width: 650,
            boxMinWidth:650,
            height: 250,
            boxMinHeight:250,
            plain: true,
            autoScroll:true,
            layout: 'absolute',
            html: html.join(''),
//                                items: [],
            buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                            WinRepoFallas.hide();
                            WinRepoFallas.destroy();

                    }
            }]
    });
    WinRepoFallas.show();
}

function showTooltipDescripcion(value, metaData,record){
    var descripcion = record.get('descripcion');
    if (descripcion != '')
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}

function showTooltipUsuarioAlta(value, metaData,record){
    var usuario_alta = record.get('usuario_alta');
    if (usuario_alta != '')
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}

function showTooltipFechaAlta(value, metaData,record){
    var fecha_alta = record.get('fecha_alta');
    if (fecha_alta != '')
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}

function showTooltipInicio(value, metaData,record){
    var fh_ini = record.get('fh_ini');
    if (fh_ini != '')
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}

function showTooltipFin(value, metaData,record){
    var fh_fin = record.get('fh_fin');
    if (fh_fin != '')
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}

function showTooltipFechaEvento(value, metaData,record){
    var fecha_evento = record.get('fecha_evento');
    if (fecha_evento != '')
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}

function showTooltipSector(value, metaData,record){
    var sector = record.get('sector');
    if (sector != '')
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}

function showToneladas(value, metaData,record){
    value = Ext.util.Format.number(value,'0.000,00/i');
    var unidades_perdidas = record.get('unidades_perdidas');
    if (unidades_perdidas != 0)
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    else
    {
        metaData.attr += 'ext:qtip= "Sin valor asignado"';
    }
    return value;
}

function showHrs(value, metaData,record){
    value = Ext.util.Format.number(value,'0.000,00/i');
    var hrs = record.get('hrs');
    metaData.attr += 'ext:qtip="'+ value + '"';
    return value;
}

function showMonto (value,metaData,superData)
{
    value = Ext.util.Format.number(value,'0.000,00/i');
    var set_monto = superData.json.set_monto;
    if(set_monto == 0)
    {
        value = 'Sin Monto';
        metaData.attr = 'style="background-color:#DF0101; color:#DF0101;"';
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    else
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}