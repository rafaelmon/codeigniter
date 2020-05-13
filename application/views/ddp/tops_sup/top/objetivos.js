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
supTopObjetivosDS = new Ext.data.GroupingStore({
    id: 'supTopObjetivosDS',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado_objetivos', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA, top:TOP.id_top}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        idProperty: 'id_objetivo'
    },[ 
        {name: 'id_objetivo',       type: 'int',            mapping: 'id_objetivo'},        
        {name: 'dimension',         type: 'string',         mapping: 'dimension'},        
    //    {name: 'oe',            type: 'string',         mapping: 'oe'},
        {name: 'obj',               type: 'string',         mapping: 'obj'},
        {name: 'indicador',         type: 'string',         mapping: 'indicador'},
        {name: 'fd',                type: 'string',         mapping: 'fd'},
        {name: 'valor_ref',         type: 'string',         mapping: 'valor_ref'},
        {name: 'peso',              type: 'float',          mapping: 'peso'},
        {name: 'orden',             type: 'int',            mapping: 'orden'},
        {name: 'id_estado',         type: 'int',            mapping: 'id_estado'},        
        {name: 'estado',            type: 'string',         mapping: 'estado'},
        {name: 'actor',             type: 'string',         mapping: 'actor'},
        {name: 'real1',             type: 'float',          mapping: 'real1'},
        {name: 'real2',             type: 'float',          mapping: 'real2'},
        {name: 'pesoreal',          type: 'float',          mapping: 'pesoreal'},
        {name: 'fecha_evaluacion',  type: 'string',         mapping: 'fecha_evaluacion'}
    ]),
    sortInfo:{field: 'orden', direction: "ASC"},
    groupField:'dimension',
    remoteSort: true
});
supTopObjetivosDS.load({params: {start: 0, limit: TAM_PAGINA}});
// var summaryObj = new Ext.ux.grid.GroupSummary();

supTopBotonesObjAction = new Ext.grid.ActionColumn({
    width: 15,
    editable:false,
    menuDisabled:true,
    header:'Acci&oacute;n',
    hideable:false,
    align:'center',
    width:  100,
    tooltip:'Editar',
        hidden:false,
    items:[{
            icon:URL_BASE+'images/edit.png',
            iconCls :'col_accion',
            tooltip:'Editar',
            hidden: true,
            getClass:showBtnEditar,
//            hidden: (!permiso_alta||!rol_editor),
            handler: clickBtnEditObj
        },{
            icon:URL_BASE+'images/edit-find.png',
            iconCls :'col_accion',
            tooltip:'Ver historial',
            handler: clickBtnVerHistorial
        },{
            icon:URL_BASE+'images/1_check.png',
            iconCls :'col_accion',
            tooltip:'Enviar al usuario',
            getClass:showBtnLiberar,
            handler: clickBtnLiberarObjetivo
        },{
            icon:URL_BASE+'images/2_check.png',
            iconCls :'col_accion',
            tooltip:'Aprobar',
            getClass:showBtnAprobar,
            handler: clickBtnAprobarObjetivo
        }]
});

supTopObjetivosColumnModel = new Ext.grid.ColumnModel(
    [   
        {
            header: '#',
            readOnly: true,
            dataIndex: 'id_objetivo',
            width: 40,        
            sortable: true,
            renderer: function(value, cell){
                cell.css = "readonlycell";
                return value;
                },
            hidden: false
        }
        ,{
        header: 'dimension',
        dataIndex: 'dimension',
        width: 90,
        sortable: false,
        menuDisabled: true,
        hidden: true
        },
        {
        header: 'Estado',
        dataIndex: 'actor',
        width: 90,
        sortable: false,
        menuDisabled: true,
        hidden: false
        }
        ,{
        header: 'Objetivo',
        dataIndex: 'obj',
        width: 200,
        sortable: false,
        hidden: false,
        renderer: showobjetivo
        }
        ,{
        header: 'Indicador',
        dataIndex: 'indicador',
        width: 90,
        sortable: false,
//        editor: new Ext.form.TextField({
//            allowBlank: true,
//            disabled: !permiso_modificar_obj
//          }),
        renderer: showQtipIndicador
        },{
        header: 'Fuente de datos',
        dataIndex: 'fd',
        width: 80,
        sortable: false,
//        editor: new Ext.form.TextField({
//            allowBlank: true,
//            disabled: !permiso_modificar_obj
//          }),
        renderer: showQtipFD
        },{
        header: 'Valor referencia',
        dataIndex: 'valor_ref',
//        editor: new Ext.form.TextField({
//            allowBlank: true,
//            disabled: !permiso_modificar_obj
//          }),
        width: 120,
//        summaryRenderer: function(v, params, data){
//                    return  '<div style="text-align:right;">Subtotal:</div>';
//                },
        sortable: false,
        renderer: showQtipVRef
        }
        ,{
            header: 'Peso',
    //        tooltip:'Pesos',
            dataIndex: 'peso',
            width: 50,
            align:'center',
    //         editor: new Ext.ux.form.SpinnerField({
    //            allowBlank: true,
    //            disabled: !permiso_modificar
    //          }),
//            summaryType: 'sum',
            sortable: false
        }
       ,{
            header: 'F. Revisión',
            dataIndex: 'fecha_evaluacion',
            width: 50,
            align:'center',
            sortable: false
        }
        ,supTopBotonesObjAction
    ]
);

paginadorDdpSupTopObj= new Ext.PagingToolbar({
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
paginadorDdpSupTopObj.bindStore(supTopObjetivosDS);

supTopObjetivosGridPanel =  new Ext.grid.EditorGridPanel({
        id: 'supTopObjetivosGridPanel',
//        title:'supTopObjetivos',
        store: supTopObjetivosDS,
        cm: supTopObjetivosColumnModel,
        view: new Ext.grid.GroupingView({
            forceFit:true,
            frame:false,
            showGroupName:false,
            enableGroupingMenu :false,
//            groupTextTpl: '<p style="color:#{[values.rs[0].json.css_bc]}">{text} <b style="color:#A4A4A4">({[values.rs.length]} {[res]})</b></p>'
            groupTextTpl: '<p style="color:#{[values.rs[0].json.css_bc]}">{text} <b style="color:#A4A4A4">({[values.rs.length]} de {[values.rs[0].json.q_obj]})</b></p>'
        }),
        enableColLock:false,
        stripeRows:true,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        autoExpandColumn:'coltopobjetivo',
        anchor: 100,
        viewConfig: {
            forceFit: true
        },
        plugins:[/*summaryObj*/],
//        clicksToEdit:3,
        height:500,
        autoScroll :true,
//        layout: 'fit',
//        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        tbar: [],
        bbar: []
});

var altura=Ext.getBody().getSize().height - 180;
supTopObjetivosGridPanel.setHeight(altura);
Ext.getCmp('browser').on('resize',function(comp){
    var dimPanel=Ext.getCmp('supTopDimensionesGridPanel');
//    var widthDimPanel=dimPanel.getInnerWidth();
//    console.log(this.getSize().width-widthDimPanel);
    supTopObjetivosGridPanel.setWidth(this.getSize().width);
    supTopObjetivosGridPanel.doLayout();
    supTopObjetivosGridPanel.setHeight(altura);
});


function showobjetivo (value,metaData,superData){
    var dim=superData.json.id_objetivo;
    var css_bc=superData.json.css_bc;
    var deviceDetail = value;
//    metaData.attr = 'style="background-color:#'+css_bc+'; color:#FFF;"';        
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipIndicador(value, metaData,record){
    var deviceDetail = record.get('indicador');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipFD(value, metaData,record){
    var deviceDetail = record.get('fd');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipVRef(value, metaData,record){
    var deviceDetail = record.get('valor_ref');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
//supTopObjetivosGridPanel.on('afteredit', guardarObjetivo);
//function guardarObjetivo(oGrid_event){
//    historialPanel.setTitle('Historial...');
//    historialPanel.collapse(true);					
//	   Ext.Ajax.request({   
//		  waitMsg: 'Por favor espere...',
//		  url: CARPETA+'/modificar_obj',
//		  params: {
//			 id: oGrid_event.record.data.id_objetivo,
//                         campo:oGrid_event.field,
//			 valor : oGrid_event.value
//		  }, 
//		  success: function(response){              
//			 var result=eval(response.responseText);
//                          var rowdim=dimensionesGridPanel.selModel.rowNav.scope.lastActive;
//			 switch(result){
//			 case 1:
//				supTopObjetivosDS.commitChanges();
//				supTopObjetivosDS.reload();
//                                SUM_DIM=0;
//                                dimensionesDS.load();
//                                dimensionesDS.on('load',function(){
//                                    dimensionesGridPanel.selModel.selectRow(rowdim,false,false);
//                                });
//                                dimensionesGridPanel.selModel.selectRow(rowdim);
//				break;
//			case 2:
//				Ext.MessageBox.alert('Error','No tiene permiso para realizar la operaci&oacute;n solicitada..');
//			break; 
//			case 3:
//				Ext.MessageBox.alert('Error','Formato de dato inv&aacute:lido.');
//			break;         
//			 default:
//				Ext.MessageBox.alert('Atenci&oacute;n','No se pudo actualizar el objetivo...');
//				break;
//			 }
//		  },
//		  failure: function(response){
//			 var result=response.responseText;
//			 Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');    
//		  }                      
//		});   
// 	}
       
function verPermiso(){
//    console.log("x"+permiso_modificar_obj);
    return true;
}
function showBtnLiberar(value,metaData,record){
    var e=record.json.id_estado;
    if (e==3)
    {
        return 'x-grid-center-icon';                
    }
    else
        return 'x-hide-display';  
};
function showBtnAprobar(value,metaData,record){
    var b=record.json.id_estado;
    if (b==2)
            return 'x-grid-center-icon';                
    else
            return 'x-hide-display';  
};
function showBtnEditar(value,metaData,record){
var e=record.json.id_estado;
        if (e==2 || e==3)
            return 'x-grid-center-icon';                
        else
            return 'x-hide-display'; 
};
function clickBtnEditObj(grid,rowIndex,colIndex,item,event){
      var id=grid.getStore().getAt(rowIndex).json.id_objetivo;
        ddpSupTopEditObjetivo_DFW(id);
//    Ext.get('browser').load({
//        url: CARPETA+"/editDocumento/15",
//        scripts: true,
//        params: {id:id},
//        text: "Cargando Formulario..."
//    });
};
function clickBtnLiberarObjetivo (grid,rowIndex,colIndex,item,event){
      var e=grid.getStore().getAt(rowIndex).json.id_estado;
      if (e==3)
      {
        msgProcess('Liberando objetivo...');
        var id=grid.getStore().getAt(rowIndex).json.id_objetivo;
          Ext.Ajax.request({   
          waitMsg: 'Por favor espere...',
          url: CARPETA+'/liberar',
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
              Ext.MessageBox.alert('Operación OK','El objetivo ha sido enviado al usuario para su revisión');
              supTopObjetivosDS.reload();
              break;
            case 'false':
            case false:
              Ext.MessageBox.alert('Error',result.error);
              supTopObjetivosDS.reload();
              break;
            default:
              Ext.MessageBox.alert('Error',result.error);
              supTopObjetivosDS.reload();
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
            Ext.MessageBox.alert('Error','Estado incorecto para liberar objetivo');         
  }
function clickBtnAprobarObjetivo (grid,rowIndex,colIndex,item,event){
      var e=grid.getStore().getAt(rowIndex).json.id_estado;
      if (e==2)
      {
        msgProcess('Aprobando objetivo...');
        var id=grid.getStore().getAt(rowIndex).json.id_objetivo;
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
              Ext.MessageBox.alert('Operación OK','El objetivo ha sido aprobado');
              supTopObjetivosDS.reload();
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
            Ext.MessageBox.alert('Error','Estado incorecto para liberar objetivo');         
  }
  function showEditor(a){
      return false;
  }