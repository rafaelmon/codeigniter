bcDataStore = new Ext.data.Store({
    id: 'bcDataStore',
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
        {name: 'id_bc',             type: 'int',        mapping: 'id_bc'},        
        {name: 'id_usuario_alta',   type: 'int',        mapping: 'id_usuario_alta'},        
        {name: 'usuario_alta',      type: 'string',     mapping: 'usuario_alta'}, 
        {name: 'id_usuario_inicio', type: 'int',        mapping: 'id_usuario_inicio'},        
        {name: 'usuario_inicio',    type: 'string',     mapping: 'usuario_inicio'}, 
        {name: 'fecha_alta',        type: 'string',     mapping: 'fecha_alta'},
        {name: 'descr',             type: 'string',     mapping: 'descr'},
        {name: 'id_estado',         type: 'string',     mapping: 'id_estado'},
        {name: 'estado',            type: 'string',     mapping: 'estado'},
        {name: 'detalle_rechazo',   type: 'string',     mapping: 'detalle_rechazo'},
        {name: 'tareas',            type: 'int',        mapping: 'tareas'},
        {name: 'alcance',            type: 'int',        mapping: 'alcance'},
        {name: 'status',            type: 'int',        mapping: 'status'},
        {name: 'cump',            type: 'float',        mapping: 'cump'}
      ]),
//      sortInfo:{field: 'id_bc', direction: "ASC"},
      remoteSort: true
});
bcPaginador= new Ext.PagingToolbar({
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
bcPaginador.bindStore(bcDataStore);


botonesBcAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'GR',
                hideable:false,
                align:'center',
                width:  50,
                tooltip:'Aprobar y comenzar BC',
                hidden:!permiso_btn_gr,
		items:[{
                    icon:URL_BASE+'images/accept.png',
                    iconCls :'col_accion',
                    tooltip:'Aprobar e iniciar...',
                    hidden: false,
//                    renderer: showCriticidadBc,
                    getClass:showBtnAprobarBc,
//                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnAprobarBc 
                }
                ,{
                    icon:URL_BASE+'images/rechazar7.png',
                    iconCls :'col_accion',
                    tooltip:'Rechazar',
                    hidden: false,
//                    renderer: showCriticidadBc,
                    getClass:showBtnAprobarBc,
//                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnRechazarBc 
                }
                ,{
                    icon:URL_BASE+'images/stop2.png',
                    iconCls :'col_accion',
                    tooltip:'Detener y cancelar BC',
                    hidden: false,
//                    renderer: showCriticidadBc,
                    getClass:showBtnCancelarBc,
//                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnCancelarBc 
                }
            ]
});
    
bcBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_tarea', 'tarea','descripcion'],
    disableIndexes:['id_bc','fecha_alta','tareas'],
    align:'right',
    minChars:3
});

bcColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_bc',
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
        width:  70,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Usuario Alta',
        dataIndex: 'usuario_alta',
        width:  160,
        renderer: showQtipPuesto1Bc,
        sortable: true
      },{
        header: 'Usuario Inicio',
        dataIndex: 'usuario_inicio',
        width:  160,
        renderer: showQtipPuesto2Bc,
        sortable: true
      },{
        header: 'Descipción',
        dataIndex: 'descr',
        width:  350,
        fixed:true,
        renderer: showQtipDescBc,
        sortable: true
      },{
        header: 'Alcance',
        dataIndex: 'alcance',
        width:  50,
        align:'center',
//        renderer: showEstadoBc,
        sortable: true
      },{
        header: 'Avance',
        dataIndex: 'status',
        width:  50,
        align:'center',
//        renderer: showEstadoBc,
        sortable: true
      },{
        header: 'Cump.',
        dataIndex: 'cump',
        width:  50,
        align:'center',
        renderer: pctStatus,
        sortable: true
      },{
        header: 'Estado',
        dataIndex: 'estado',
        width:  70,
        align:'center',
        renderer: showEstadoBc,
        sortable: true
      },botonesBcAction
     ]
    );

bcDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

bcGridPanel =  new Ext.grid.GridPanel({
    id: 'bcGridPanel',
    store: bcDataStore,
    cm: bcColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true,
    viewConfig: {
        forceFit: false
    },
    plugins:[bcBuscador],
    bbar:[bcPaginador],
    tbar: [
        {
            text: 'Nueva BC',
            tooltip: 'Crear nueva BC...',
            iconCls:'add',                      // reference to our css
            handler: bcAltaDisplayFormWindow,
            hidden: !permiso_alta
        }
    ]
});   

bcPanel = new Ext.Panel(
{
        collapsible: false,
        collapsed:false,
        split: true,
        title: 'Bajadas en Cascada (BC)',
        region: 'center',
        height: 300,
        minSize: 100,
        maxSize: 350,
        margins: '0 5 5 5',
        html:'<p>panel central</p>',
        layout: 'fit',
        items : [bcGridPanel]
});
    
    var altura=Ext.getBody().getSize().height - 60;
	bcGridPanel.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
            bcGridPanel.setWidth(this.getSize().width);
            bcGridPanel.setHeight(Ext.getBody().getSize().height - 60);

	});

  function clickBtnAprobarBc(grid, rowIndex){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnAprobarBc(grid, rowIndex);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
        
function go_clickBtnAprobarBc(grid, rowIndex){
   msgProcess('Iniciando Bajada en cascada');
    var id=grid.getStore().getAt(rowIndex).json.id_bc;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/aprobar',
        method: 'POST',
        params: {
        id:id  
        }, 
        success: function(response){              
        var result=eval(response.responseText);
        switch(result){
        case 0:
            Ext.MessageBox.alert('Error','El usuario no tiene permisos para aprobar la BC');
            break;
        case 1:
            Ext.MessageBox.alert('Operación OK','La BC ha sido aprobada e iniciada correctamente');
            bcDataStore.reload();
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
  
function showBtnAprobarBc(value,metaData,record){
        var estado=record.json.id_estado;
        if (estado==1)
            return 'x-grid-center-icon';                
        else
            return 'x-hide-display';  
};
function showBtnCancelarBc(value,metaData,record){
        var estado=record.json.id_estado;
        if (estado==2)
            return 'x-grid-center-icon';                
        else
            return 'x-hide-display';  
};
function showEstadoBc (value,metaData,superData){
    var estado=superData.json.id_estado;
    switch (estado)
    {
        case '1'://Pendiente
            metaData.attr = 'style="background-color:#01A9DB; color:#FFF;"'; //Celeste
            break;
        case '2'://en curso
            metaData.attr = 'style="background-color:#088A08; color:#FFF;"';//verde
            break;
        case '3'://rechazada
            metaData.attr = 'style="background-color:#151515; color:#A6A69C;"';//gris
            var detalle_rechazo=superData.json.detalle_rechazo;
            metaData.attr += 'ext:qtip="'+ detalle_rechazo + '"';
        break;
        case '4'://Cumplida
//        metaData.attr = 'style="background-color:#F6EE03; color:#A6A69C;"';//amarillo
        metaData.attr = 'style="background-color:#0101DF; color:#FFF;"';//gris
        break;
        case '5'://Cancelada
            metaData.attr = 'style="background-color:#CA3204; color:#FFF;"';//Rojo
//        metaData.attr = 'style="background-color:#F6EE03; color:#A6A69C;"';//amarillo
        break;
            
    }
    return value;
}
function showQtipDescBc(value, metaData,record){
    var deviceDetail = record.get('descr');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipPuesto1Bc(value, metaData,record){
    var deviceDetail = record.get('puesto1');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipPuesto2Bc(value, metaData,record){
    var deviceDetail = record.get('puesto2');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
// example of custom renderer function
    function pctStatus(val){
        if(val > 0){
            return '<span style="color:green;">' + val + '%</span>';
        }else if(val < 0){
            return '<span style="color:red;">' + val + '%</span>';
        }
        return val;
    }