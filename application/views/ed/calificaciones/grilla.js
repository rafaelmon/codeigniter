periodosJS = new Ext.data.JsonStore({
    url: CARPETA+'/combo_periodos',
    root: 'rows',
    fields: ['id_periodo', 'periodo']
});
periodosJS.load();
periodosJS.on('load' , function(  js , records, options )
{
    var tRecord = Ext.data.Record.create(
        {name: 'id_periodo', type: 'int'},
        {name: 'periodo', type: 'string'}
    );
    var myNewT = new tRecord({
        id_periodo: -1,
        periodo   : 'Todos'
    });
    periodosJS.insert( 0, myNewT);	
});

calificacionesDataStore = new Ext.data.Store({
    id: 'calificacionesDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_calificacion'
    },[ 
      {name: 'id_calificacion',       type: 'int',    mapping: 'id_calificacion'},        
      {name: 'periodo',               type: 'string', mapping: 'periodo'},
      {name: 'id_periodo',            type: 'int',    mapping: 'id_periodo'},
      {name: 'calificacion',          type: 'string', mapping: 'calificacion'},
      {name: 'valor',                 type: 'int',    mapping: 'valor'},
      {name: 'habilitado',            type: 'int',    mapping: 'habilitado'},
    ]),
    sortInfo:{field: 'id_calificacion', direction: "ASC"},
    remoteSort: true
});
paginador.bindStore(calificacionesDataStore);
	
function setParamsEd(){
    calificacionesDataStore.setBaseParam('f_id_periodo',Ext.getCmp('periodos-filtro').getValue());
};
    
habilitadoCheck = new Ext.grid.CheckColumn({
        id:'habilitadoCheck',
        header: "Habilitado",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado: true,
//        pintar_habilitado: true,
//        pintar_deshabilitado_color: '#FF0000',
//        pintar_habilitado_color: '#298A08',
        disabled: false,
        tabla: 'ed_calificaciones',
        align:'center',
        campo_id: 'id_calificacion'
    });

calificacionesColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_calificacion',
        width: 40,        
        sortable: true,
        hidden: false,
        renderer:   function(value, cell){
                        cell.css = "readonlycell";
                        return value;
                    },
    },{
        header: 'Periodo',
        dataIndex: 'periodo',
        width: 100,
        sortable: true,
    },{
        header: 'Calificacion',
        dataIndex: 'calificacion',
        width: 140,
        sortable: true,
        readOnly: permiso_modificar,
        editor: new Ext.form.TextField({
                    disabled: !permiso_modificar,
                    allowBlank: false,
                    maxLength: 150
                })
    },{
        header: 'Valor',
        dataIndex: 'valor',
        width: 120,
        sortable: true,
        readOnly: permiso_modificar,
        editor: new Ext.form.TextField({
                    disabled: !permiso_modificar,
                    allowBlank: false,
                    maxLength: 150
                })
    },habilitadoCheck
]);
    
periodosFiltro = new Ext.form.ComboBox({
    id:'periodos-filtro',
    forceSelection : true,
    value: 'Todos',
    store: periodosJS,
    editable : false,
    displayField: 'periodo',
    valueField:'id_periodo',
    allowBlank: false,
    width:  200,
    selectOnFocus:true,
    triggerAction: 'all'
});
periodosFiltro.on('select', filtrarGrillaClasificaciones);

function filtrarGrillaClasificaciones (combo, record, index){
    calificacionesDataStore.load({
            params: {
                    f_id_periodo: Ext.getCmp('periodos-filtro').getValue()
            }
    });	
};
calificacionesBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['id_periodo','id_calificacion','periodo','habilitado'],
    align:'left',
    minChars:3
});

calificacionesListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'calificacionesListingEditorGrid',
    title: 'Calificaciones',
    store: calificacionesDataStore,
    cm: calificacionesColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    tbar: [
        {
            text: 'Nueva calificación',
            tooltip: 'Nueva calificación',
            iconCls:'add',                     
            handler: altaCalificacion,
         },'&nbsp&nbsp|&nbsp&nbsp&nbsp&nbsp<b>Filtrar por:</b> Periodos',periodosFiltro
    ],
    plugins:[habilitadoCheck,calificacionesBuscador],
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador
 });   
 
calificacionesListingEditorGrid.on('afteredit', guardarCambiosGrilla);
calificacionesDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

 
var altura=Ext.getBody().getSize().height - 60;
calificacionesListingEditorGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
    calificacionesListingEditorGrid.setWidth(this.getSize().width);
    calificacionesListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

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

function guardarCambiosGrilla(oGrid_event)
{
    var fields = [];
    fields.push(oGrid_event.field);
    var values = [];
    values.push(oGrid_event.value);
 
    var encoded_array_f = Ext.encode(fields);
    var encoded_array_v = Ext.encode(values);
    Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/modificar',
        params: {
            id: oGrid_event.record.data.id_calificacion,     
            campos : encoded_array_f,
            valores : encoded_array_v
        }, 
        success: function(response){              
            var result=eval(response.responseText);
            switch(result){
                case 1:
                    calificacionesDataStore.commitChanges();
                    calificacionesDataStore.reload();
                    break;          
                default:
                    Ext.MessageBox.alert('Uh uh...','No se pudo actualizar...');
                    break;    
            }
        },
        failure: function(response){
            var result=response.responseText;
            Ext.MessageBox.alert('error','No se pudo conectar a la Base de Datos. Intente mas tarde');    
        }                      
   });  
}