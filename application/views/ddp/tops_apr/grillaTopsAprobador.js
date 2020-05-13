function msgProcess(titulo){
 Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:400, 
        wait:true, 
        waitConfig: {interval:200}
    });
}
periodosTopAprJS = new Ext.data.JsonStore({
	url: CARPETA+'/periodos_combo',
	root: 'rows',
	fields: ['id_periodo', 'periodo']
});
periodosTopAprJS.load();
periodosTopAprJS.on('load' , function(  js , records, options ){
	var tRecord = Ext.data.Record.create(
            {name: 'id_periodo', type: 'int'},        
            {name: 'periodo', type: 'string'}
	);
	var myNewT = new tRecord({
		id_periodo: -1,
		periodo   : 'Todos'
	});
	periodosTopAprJS.insert( 0, myNewT);	
} );
periodosTopAprFiltro = new Ext.form.ComboBox({
    id:'periodosTopAprFiltro',
    forceSelection : true,
    value: PERIODO,
    store: periodosTopAprJS,
    editable : false,
    displayField: 'periodo',
    valueField:'id_periodo',
    allowBlank: false,
    width:  200,
    selectOnFocus:true,
    triggerAction: 'all'
});
periodosTopAprFiltro.on('select', filtrarGrillaTopsApr);

ddpAprTopsDataStore = new Ext.data.Store({
    id: 'ddpAprTopsDataStore',
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
    {name: 'puesto',        type: 'string',     mapping: 'puesto'},
    {name: 'estado',        type: 'string',     mapping: 'estado'},
    {name: 's_pesos',       type: 'float',      mapping: 's_pesos'},
    {name: 'q_obj',         type: 'int',        mapping: 'q_obj'},
    {name: 'q_obj_sup',     type: 'int',        mapping: 'q_obj_sup'},
    {name: 'q_obj_aprob',   type: 'int',        mapping: 'q_obj_aprob'},
    {name: 'habilitado',    type: 'int',        mapping: 'habilitado'},
    {name: 's_pesoreal',    type: 'float',      mapping: 's_pesoreal'},
    ]),
    sortInfo:{field: 'estado', direction: "desc"},    
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(ddpAprTopsDataStore);

botonesAction = new Ext.grid.ActionColumn({
    editable:false,
    menuDisabled:true,
    header:'Acción',
    hideable:false,
    align:'left',
    width:  120,
//    tooltip:'Ver TOP',
    hidden:false,
    items:[{
        icon:URL_BASE+'images/tooloptions.png',
        iconCls :'col_accion',
        tooltip:'Ver TOP',
        getClass: showBtnVerTopDr,
        handler: clickBtnVerTop
    },{
        icon:URL_BASE+'images/doc_pdf.png',
        iconCls :'col_accion',
        tooltip:'Descargar TOP',
        getClass: showBtnVerTopDr,
        handler: clickBtnDescargarPDFDdpTopsApr
    },{
        icon:URL_BASE+'images/edit-find.png',
        iconCls :'col_accion',
        tooltip:'Auditoria',
        handler: clickBtnVerHistorialDDpTopAprAud
    },{
        icon:URL_BASE+'images/aprobar2.png',
        iconCls :'col_accion',
        tooltip:'Aprobar TOP',
        getClass: showBtnAprobarTop,
        handler: clickBtnAprobarTop
    },{
        icon:URL_BASE+'images/rechazar4.png',
        iconCls :'col_accion',
        tooltip:'Rechazar TOP',
        getClass: showBtnAprobarTop,
        handler: clickBtnRechazarTop
    }]
});

ddpAprTopsColumnModel = new Ext.grid.ColumnModel(
    [{
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
        },{
        header: 'Usuario',
        dataIndex: 'usuario',
        width: 200,
        sortable: true
        },{
        header: 'Periodo',
        dataIndex: 'periodo',
        width: 80,
        sortable: true,
        renderer: showQtipTOPs
        },{
        header: 'Puesto',
        dataIndex: 'puesto',
        width: 200,
        sortable: false
        },{
        header: 'Supervisor',
        dataIndex: 'supervisor',
        width: 200,
        sortable: true
        },{
        header: 'Estado Top',
        dataIndex: 'estado',
        width: 110,
        renderer: showEstadoDdpTopApr,
        align:'center',
        sortable: true
        },{
        header: '&Sigma; <b>Obj</b>',
        tooltip:'Cantidad de Objetivos',
        dataIndex: 'q_obj',
        width: 70,
        align:'center',
        summaryType: 'sum',
        sortable: false
        },{
        header: '&Sigma; <b>Peso</b>',
        tooltip:'Sumatoria de Pesos',
        dataIndex: 's_pesos',
        width: 70,
        align:'center',
        summaryType: 'sum',
        sortable: false
//        },{
//        header: '&Sigma; <b>Peso Real</b>',
//        tooltip:'Cantidad de Peso real',
//        dataIndex: 's_pesoreal',
//        width: 70,
//        align:'center',
//        summaryType: 'sum',
//        sortable: false
        },
//        {
//        header: '&Sigma; <b>Obj sup</b>',
//        tooltip:'Cantidad de Objetivos para supervisar',
//        dataIndex: 'q_obj_sup',
//        width: 70,
//        align:'center',
//        summaryType: 'sum',
//        sortable: false
//        },
        botonesAction
    ]
);
ddpAprBuscadorTOPs= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
    //    readonlyIndexes:['id_convocatoria'],
    disableIndexes:['id_top','periodo','habilitado','estado','q_obj','s_pesos'],
    align:'left',
    minChars:3
});
  
   ddpAprTopsGrid =  new Ext.grid.GridPanel({
        id: 'ddpAprTopsGrid',
        title: 'Tops generadas por el personal a cargo de su dependiente',
        store: ddpAprTopsDataStore,
        cm: ddpAprTopsColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[ddpAprBuscadorTOPs],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
//        tbar: ['<b>Filtrar por-></b> Período',periodosTopAprFiltro]
        tbar: []
    }); 

  ddpAprTopsDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

var altura=Ext.getBody().getSize().height - 60;
ddpAprTopsGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
        ddpAprTopsGrid.setWidth(this.getSize().width);
        ddpAprTopsGrid.setHeight(Ext.getBody().getSize().height - 60);

});

function showQtipTOPs(value, metaData,record){
    var deviceDetail = record.get('operario');
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
//    console.log(id);
    Ext.get('browser').load({
        url: CARPETA+"/top_usuario/31",
        params: {id: id},
        scripts: true,
        text: "Cargando..."
    });
}
function filtrarGrillaTopsApr (combo, record, index){
    ddpAprTopsDataStore.load({
            params: {
                    filtro_id_periodo: Ext.getCmp('periodosTopAprFiltro').getValue()
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
function showEstadoDdpTopApr(value,metaData,record){
    var e=record.json.id_estado;
    switch(e){
            case '1':
            case 1:
                metaData.attr = 'style="color:#000;background-color:#FFF933;"';
                break;
            case '2':
            case 2:
                metaData.attr = 'style="color:#FFF;background-color:#E46C0A;"';
                break;
            case '3':
            case 3:
                metaData.attr = 'style="color:#FFF;background-color:#00B0F0;"';
              break;
            case '4':
            case 4:
                metaData.attr = 'style="color:#FFF;background-color:#9BBB59;"';
              break;
            default:
              break;
            }        
        return value;                
};
function showBtnAprobarTop(value,metaData,record){
    var e=record.json.id_estado;
    if(e==2)
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
};

function clickBtnAprobarTop (grid,rowIndex,colIndex,item,event){
      var e=grid.getStore().getAt(rowIndex).json.id_estado;
      if (e==2)
      {
        msgProcess('Aprobando objetivo...');
        var id=grid.getStore().getAt(rowIndex).json.id_top;
          Ext.Ajax.request({   
          waitMsg: 'Por favor espere...',
          url: CARPETA+'/aprobar',
          method: 'POST',
          params: {
            id:id  
          }, 
          success: function(response){              
            var result=eval(response.responseText);
  //          Ext.MessageBox.hide(); 
            switch(result.success){
            case 'true':
            case true:
              Ext.MessageBox.alert('Operación OK','TOP aprobada correctamente');
              ddpAprTopsDataStore.reload();
              break;
            case 'false':
            case false:
              Ext.MessageBox.alert('Error',result.error);
              break;
            default:
              Ext.MessageBox.alert('Error',result.error);
              break;
            }        
          },
          failure: function(response){
  //          var result=response.responseText;
            Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
          }                      
        });
      }
      else
            Ext.MessageBox.alert('Error','Estado incorecto para éjecutar acción');         
  }
function clickBtnRechazarTop (grid,rowIndex,colIndex,item,event){
      var e=grid.getStore().getAt(rowIndex).json.id_estado;
      if (e==2)
      {
        msgProcess('Aprobando objetivo...');
        var id=grid.getStore().getAt(rowIndex).json.id_top;
          Ext.Ajax.request({   
          waitMsg: 'Por favor espere...',
          url: CARPETA+'/rechazar',
          method: 'POST',
          params: {
            id:id  
          }, 
          success: function(response){              
            var result=eval(response.responseText);
  //          Ext.MessageBox.hide(); 
            switch(result.success){
            case 'true':
            case true:
              Ext.MessageBox.alert('Operación OK','TOP rechazada...');
              ddpAprTopsDataStore.reload();
              break;
            case 'false':
            case false:
              Ext.MessageBox.alert('Error',result.error);
              break;
            default:
              Ext.MessageBox.alert('Error',result.error);
              break;
            }        
          },
          failure: function(response){
  //          var result=response.responseText;
            Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
          }                      
        });
      }
      else
            Ext.MessageBox.alert('Error','Estado incorecto para éjecutar acción');         
  }
  
  function clickBtnDescargarPDFDdpTopsApr (grid,rowIndex,colIndex,item ,event){
    var id_top=grid.getStore().getAt(rowIndex).json.id_top;
    var link = document.createElement("a");
    link.download = "excel";
    link.href =CARPETA_PDF+'/miTop/'+id_top ;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    delete link;
};