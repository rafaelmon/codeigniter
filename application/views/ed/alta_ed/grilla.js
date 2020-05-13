//periodosJS = new Ext.data.JsonStore({
//    url: CARPETA+'/combo_periodos',
//    root: 'rows',
//    fields: ['id_periodo', 'periodo']
//});
//periodosJS.load();
//periodosJS.on('load' , function(  js , records, options )
//{
//    var tRecord = Ext.data.Record.create(
//        {name: 'id_periodo', type: 'int'},
//        {name: 'periodo', type: 'string'}
//    );
//    var myNewT = new tRecord({
//        id_estado: -1,
//        estado   : 'Todos'
//    });
//    periodosJS.insert( 0, myNewT);	
//});

altaEEDDDataStore = new Ext.data.Store({
    id: 'altaEEDDDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id'
    },[ 
      {name: 'id_alta',                 type: 'int',    mapping: 'id_alta'},        
      {name: 'periodo',                 type: 'string', mapping: 'periodo'},
      {name: 'id_periodo',              type: 'int',    mapping: 'id_periodo'},
      {name: 'id_usuario',              type: 'int',    mapping: 'id_usuario'},
      {name: 'usuario',                 type: 'string', mapping: 'usuario'},
      {name: 'empresa',                 type: 'string', mapping: 'empresa'},
      {name: 'area',                    type: 'string', mapping: 'area'},
      {name: 'gerencia',                type: 'string', mapping: 'gerencia'},
      {name: 'id_estado',               type: 'int',    mapping: 'id_estado'},
      {name: 'estado',                  type: 'string', mapping: 'estado'},
      {name: 'id_usuario_supervisor',   type: 'int',    mapping: 'id_usuario_supervisor'},
      {name: 'supervisor',              type: 'string', mapping: 'supervisor'},
      {name: 'verificado',              type: 'boolean', mapping: 'verificado'},
      {name: 'duplicado',               type: 'boolean', mapping: 'duplicado'}
    ]),
    sortInfo:{field: 'id', direction: "ASC"},
    remoteSort: true
});
paginador.bindStore(altaEEDDDataStore);
	
//function setParamsEd(){
//    altaEEDDDataStore.setBaseParam('f_id_periodo',Ext.getCmp('periodos-filtro').getValue());
//};
 usuariosSupervisorDS = new Ext.data.Store({
        id:'idAprobadoresDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_usuarios',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_usuario'
        }, [
            {name: 'id_usuario', mapping: 'id_usuario'},
            {name: 'nomape', mapping: 'nomape'},
//            {name: 'usuario', mapping: 'usuario'},
//            {name: 'puesto', mapping: 'puesto'}
        ])
    });

    // Custom rendering Template
//    var aprobadorTpl = new Ext.XTemplate(
//        '<tpl for="."><div class="search-item">',
//            '<h3><span>{nomape}</h3>({usuario})</span>',
//        '</div></tpl>'
//    );
    var aprobadorTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
usuariosSupervisorCombo = new Ext.form.ComboBox({
        id:'aprobadoresCombo',
        store: usuariosSupervisorDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Usuario',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '100%',
        forceSelection : true,
        minChars:3,
//        labelStyle: 'font-weight:bold;',
        pageSize:10,
        tabIndex: 11,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
//        tpl: aprobadorTpl,
//        itemSelector: 'div.search-item'
    });  
verificadoCheck = new Ext.grid.CheckColumn({
        id:'verificadoCheck',
        header: "OK",
        dataIndex: 'verificado',
        width: 90,
        sortable: true,
        align:'center',
        menuDisabled:true,
        pintar_deshabilitado: false,
        pintar_habilitado: true,
        pintar_deshabilitado_color: '#FF0000',
        pintar_habilitado_color: '#298A08',
        disabled: true,
        tabla: 'ed_evaluaciones_alta',
        campo_id: 'id_alta'
    });
botonesAltaEEDDAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acción',
                hideable:false,
                align:'left',
                width:  90,
                tooltip:'Acciones',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/list-delete.png',
                    iconCls :'col_accion',
                    tooltip:'Eliminar',
                    hidden: false,
                    handler: clickBtnEliminar
                    }
                ]
});

altaEEDDColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_alta',
        width: 40,        
        sortable: true,
        hidden: false
    },{
        header: 'Periodo',
        dataIndex: 'periodo',
        width: 100,
        sortable: true,
    },{
        header: 'Usuario',
        dataIndex: 'usuario',
        width: 140,
        sortable: true,
    },{
        header: 'Empresa',
        dataIndex: 'empresa',
        width: 120,
        sortable: true,
    },{
        header: 'Area',
        dataIndex: 'area',
        width: 150,
        sortable: true,
    },{
        header: 'Gerencia',
        dataIndex: 'gerencia',
        width: 170,
        sortable: true,
    },{
        header: 'Supervisor <b>(*)</b>',
        dataIndex: 'supervisor',
        width:  200,
        sortable: true,
        readOnly: permiso_modificar,
        editor:usuariosSupervisorCombo,
        renderer:showEditable
    },{
        header: '¿Duplicado?',
        dataIndex: 'duplicado',
        align:'center',
        width:  80,
        sortable: true,
        renderer:showDuplicado
    },verificadoCheck,botonesAltaEEDDAction
]);
    
//periodosFiltro = new Ext.form.ComboBox({
//    id:'periodos-filtro',
//    forceSelection : true,
//    value: 'Todos',
//    store: periodosJS,
//    editable : false,
//    displayField: 'periodo',
//    valueField:'id_periodo',
//    allowBlank: false,
//    width:  200,
//    selectOnFocus:true,
//    triggerAction: 'all'
//});
//periodosFiltro.on('select', filtrarGrillaEd);
//function filtrarGrillaEd (combo, record, index){
//    altaEEDDDataStore.load({
//            params: {
//                    f_id_periodo: Ext.getCmp('periodos-filtro').getValue()
//            }
//    });	
//};
altaEEDDBbuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['id_evalluacion','estado','periodo','verificado','duplicado'],
    align:'left',
    minChars:3
});



altaEEDDListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'altaEEDDListingEditorGrid',
    title: 'Nuevas evaluaciones de desempeño',
    store: altaEEDDDataStore,
    cm: altaEEDDColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    tbar: [
        {
            text: 'Ver competencias',
            tooltip: 'ver configuracion actual de competencias y subcompetencias',
            iconCls:'mied_pdf_ico',                      // reference to our css
            disabled:false,
            handler: function (){
                var nom="ED-DEMO";
                window.open(CARPETA_PDF+'/ver_demo/'+nom)
            }, 
            hidden: !permiso_alta
        },
        {
            text: 'Generar EEDD',
            tooltip: 'Generar evaluaciones masivas',
            iconCls:'add',                     
            handler: clickBtnGenerarEEDD
         },
        {
            text: 'Generar ED',
            tooltip: 'Generar evaluaciones de usuario',
            iconCls:'add',                     
            handler: clickBtnAltaUnica
         },
        {
            text: 'Eliminar no verificados',
            tooltip: 'Eliminar y limpiar la grilla de los registros sin tilde de verificado..',
            iconCls:'remove',                     
            handler: clickBtnEliminarNoVerificados
         },
        {
            text: 'Iniciar evaluaciones',
            tooltip: 'Iniciar el proceso de evaluación con las evaluaciones generadas...',
            iconCls:'lanzar_eedd',         
            handler: clickBtnIniciarEEDD
         },
//        '<b>Filtrar por:</b> Periodos',periodosFiltro
    ],
    plugins:[verificadoCheck,altaEEDDBbuscador], 
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador
 });   
altaEEDDListingEditorGrid.on('afteredit', guardarCambiosGrillaAlta);
altaEEDDDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

 
function showEstado (value,metaData,superData){
    var estado=superData.json.id_estado;
    switch (estado)
    {
        case '1':
        metaData.attr = 'style="background-color:#00B0F0; color:#FFF;"';
        break;
        case '2':
        metaData.attr = 'style="background-color:#FFFF00; color:#FFF;"';
        break;
        case '3':
        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"';
        break;
        case '4':
        metaData.attr = 'style="background-color:#688A08; color:#FFF;"';
        break;
        case '5':
        metaData.attr = 'style="background-color:#04B404; color:#FFF;"';
        break;        
    }
    var deviceDetail = superData.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';

    return value;
}
   
var altura=Ext.getBody().getSize().height - 60;
altaEEDDListingEditorGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
    altaEEDDListingEditorGrid.setWidth(this.getSize().width);
    altaEEDDListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

});

//function clickBtnGenerarEEDD(){};
//function clickBtnGenerarEEDD(){
//    msgProcess('Generando...');
//    Ext.Ajax.request({  
//            waitMsg: 'Por favor espere',
//            url: CARPETA+'/generar', 
//            params: { 
//               periodo:  1
//              }, 
//            success: function(response){
//              Ext.MessageBox.hide(); 
//              var result=eval(response.responseText);
//              switch(result){
//              case 1:  // Success : simply reload
//                break;
//              default:
//              	altaEEDDDataStore.reload();
//                break;
//              }
//            },
//            failure: function(response){
//              var result=response.responseText;
//              Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
//              }
//         });
//};
function clickBtnEliminar(grid,rowIndex,colIndex,item,event){
      msgProcess('Eliminando...');
      var id=grid.getStore().getAt(rowIndex).json.id_alta;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/eliminar',
        method: 'POST',
        params: {
          id:id  
        }, 
        success: function(response){              
          var result=eval(response.responseText);
//          Ext.MessageBox.hide(); 
          if(result.success){
            Ext.MessageBox.alert('Operación OK','El registro ha sido eliminado correctamente');
            altaEEDDDataStore.reload();
          } 
          else
            Ext.MessageBox.alert('Error',result.msg);
              
        },
        failure: function(response){
//          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
  }
function clickBtnEliminarNoVerificados(grid,rowIndex,colIndex,item,event){
      msgProcess('Eliminando...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/eliminarNoVerificados',
        method: 'POST',
        success: function(response){              
          var result=eval(response.responseText);
//          Ext.MessageBox.hide(); 
          if(result.success){
            Ext.MessageBox.alert('Operación OK','Registros eliminados correctamente');
            altaEEDDDataStore.reload();
          } 
          else
            Ext.MessageBox.alert('Error',result.msg);
              
        },
        failure: function(response){
//          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
  }
function clickBtnIniciarEEDD(grid,rowIndex,colIndex,item,event){
      msgProcess('Iniciando Evaluaciones...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/iniciar',
        method: 'POST',
        success: function(response){              
          var result=eval(response.responseText);
//          Ext.MessageBox.hide(); 
          if(result.success){
            Ext.MessageBox.alert('Operación Realizada',result.msg);
            altaEEDDDataStore.reload();
          } 
          else
            Ext.MessageBox.alert('Error',result.msg);
              
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
function guardarCambiosGrillaAlta(oGrid_event){
        var fields = [];
        fields.push('supervisor');
        var values = [];
        values.push(oGrid_event.value);
        var encoded_array_f = Ext.encode(fields);
        var encoded_array_v = Ext.encode(values);
		 
   Ext.Ajax.request({   
      waitMsg: 'Por favor espere...',
      url: CARPETA+'/modificar',
      params: {
		 id_alta: oGrid_event.record.data.id_alta,     
		 campos : encoded_array_f,
		 valores : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            altaEEDDDataStore.commitChanges();
            altaEEDDDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            altaEEDDDataStore.commitChanges();
            altaEEDDDataStore.reload();
            break;          
         case 3:
            Ext.MessageBox.alert('Error','No debe ingresar como supervisor al mismo usuario');
            altaEEDDDataStore.commitChanges();
            altaEEDDDataStore.reload();
            break;          
         default:
            Ext.MessageBox.alert('Error','No hay conexión con la base de datos. Asegurese de tener conexion');
            break;
         }
      },
      failure: function(response){
         var result=response.responseText;
         Ext.MessageBox.alert('Uh uh...','No hay conexión con la base de datos. Intenta otra vez');    
      }                      
   });   
  }
  function showEditable(v, p, record){
      if (v!=null && v!='')
        p.attr = 'style="background-color:#F3F781";';
    else
        p.attr = 'style="background-color:#DC0000";';
        
       return v;
  }
  function showDuplicado (value,metaData,superData){
    var grado=superData.json.duplicado;
    switch (grado)
    {
        case 1:
        case '1':
        metaData.attr = 'style="background-color:#DC0000; color:#FFF;"';
        value="Si";
        break;
        case 0:
        case '0':
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
        value="No";
        break;
    }
    return value;
}