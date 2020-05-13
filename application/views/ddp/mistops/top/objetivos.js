
miTopObjetivosDS = new Ext.data.GroupingStore({
    id: 'miTopObjetivosDS',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado_objetivos', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
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
    {name: 'fecha_evaluacion',  type: 'string',         mapping: 'fecha_evaluacion'},
    {name: 'real1',             type: 'float',         mapping: 'real1'},
    {name: 'real2',             type: 'float',         mapping: 'real2'},
    {name: 'pesoreal',          type: 'float',         mapping: 'pesoreal'}
    ]),
    sortInfo:{field: 'orden', direction: "ASC"},
    groupField:'dimension',
    remoteSort: true
});
miTopObjetivosDS.load({params: {start: 0, limit: TAM_PAGINA, id_top: ID_TOP}});
// var summaryObj = new Ext.ux.grid.GroupSummary();
//var paginadorMiTopObj= new Ext.PagingToolbar({
//    pageSize: parseInt(TAM_PAGINA),
//    displayInfo: true,
//    beforePageText:'Página',
//    afterPageText:'de {0}',
//    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
//    firstText:'Primera Página',
//    lastText:'Última Página',
//    prevText:'Anterior',
//    nextText:'Siguiente',
//    refreshText:'Actualizar',
//    buttonAlign:'left',
//    emptyMsg:'No hay registros para listar'
//});
//paginadorMiTopObj.bindStore(miTopObjetivosDS);
//paginadorMiTopObj.on('beforechange', function(){
//    var dimPanel=Ext.getCmp('miTopDimensionesGridPanel');
//    var dimDS=dimPanel.getStore();
//    dimDS.reload();
//});

miTopBotonesObjAction = new Ext.grid.ActionColumn({
    editable:false,
    menuDisabled:true,
    header:'Acci&oacute;n',
    hideable:false,
    align:'center',
    width:  80,
    tooltip:'Editar',
    hidden:false,
    items:[{
            icon:URL_BASE+'images/edit.png',
            iconCls :'col_accion',
            tooltip:'Editar',
            hidden: false,
            getClass:showBtnEditarMiTop,
//            hidden: (!permiso_alta||!rol_editor),
            handler: clickBtnEditObjMiTop
        },{
            icon:URL_BASE+'images/delete.gif',
            iconCls :'col_accion',
            tooltip:'Eliminar',
            getClass:showBtnEliminarMiTop,
            handler: clickBtnEliminarObjetivoMiTop
        },{
            icon:URL_BASE+'images/edit-find.png',
            iconCls :'col_accion',
            tooltip:'Ver historial',
            handler: clickBtnVerHistorial
        },{
            icon:URL_BASE+'images/1_check.png',
            iconCls :'col_accion',
            tooltip:'Enviar al supervisor',
            getClass:showBtnLiberarMiTop,
            handler: clickBtnLiberarObjetivoMiTop
        },{
            icon:URL_BASE+'images/2_check.png',
            iconCls :'col_accion',
            tooltip:'Aprobar',
            getClass:showBtnAprobarMiTop,
            handler: clickBtnAprobarObjetivoMiTop
        }]
});

miTopObjetivosColumnModel = new Ext.grid.ColumnModel(
        
    [   {
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
        }//new Ext.grid.RowNumberer()
        ,{
        header: 'Dimension',
        dataIndex: 'dimension',
        width: 90,
        sortable: false,
        hidden: true
        },
        {
        header: 'Estado',
        dataIndex: 'estado',
        width: 90,
        sortable: false,
        hidden: false
        }
        ,{
        id: 'obj',
        header: 'Objetivo',
        dataIndex: 'obj',
        width: 200,
        sortable: false,
        renderer: showobjetivo
        },{
        header: 'Indicador',
        dataIndex: 'indicador',
        width: 90,
        sortable: false,
        renderer: showQtipIndicador
        },{
        header: 'Fuente de datos',
        dataIndex: 'fd',
        width: 80,
        sortable: false,
        renderer: showQtipFD
        },{
        header: 'Valor referencia',
        dataIndex: 'valor_ref',
        width: 120,
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
//        summaryType: 'sum',
        sortable: false
        }
        ,{
            header: 'F. Revisión',
            dataIndex: 'fecha_evaluacion',
            width: 50,
            align:'center',
//            summaryType: 'sum',
            sortable: false
        }
        ,miTopBotonesObjAction
    ]
);

//paginadorDdpObj= new Ext.PagingToolbar({
//    pageSize: parseInt(TAM_PAGINA),
//    displayInfo: true,
//    beforePageText:'Página',
//    afterPageText:'de {0}',
//    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
//    firstText:'Primera Página',
//    lastText:'Última Página',
//    prevText:'Anterior',
//    nextText:'Siguiente',
//    refreshText:'Actualizar',
//    buttonAlign:'left',
//    emptyMsg:'No hay registros para listar'
//});
//paginadorDdpObj.bindStore(miTopObjetivosDS);

miTopObjetivosGridPanel =  new Ext.grid.EditorGridPanel({
        id: 'miTopObjetivosGridPanel',
//        title:'objetivos',
        store: miTopObjetivosDS,
        cm: miTopObjetivosColumnModel,
        view: new Ext.grid.GroupingView({
            forceFit:true,
            showGroupName:false,
            groupTextTpl: '<p style="color:#{[values.rs[0].json.css_bc]}">{text} <b style="color:#A4A4A4">({[values.rs.length]} de {[values.rs[0].json.q_obj]})</b></p>'
//            groupTextTpl: '{text} <b style="color:#A4A4A4;">({[values.rs.length]} {[values.rs.length > 1 ? "objetivos" : "objetivo"]})</b>'
        }),
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        autoExpandColumn:'coltopobjetivo',
        anchor: 100,
        viewConfig: {
            forceFit: true
        },
        plugins:[/*summaryObj*/],
        clicksToEdit:3,
        height:500,
        autoScroll :true,
//        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: [],
        tbar: [
            {
                id:'botonaltaobjQue',
                text: 'Nuevo Objetivo QUE',
                iconCls:'add',
                disabled :!TOP.btn_altaObj,
                handler: altaQue
            }
            ,'&emsp;|&emsp;'
            ,{
                id:'botonaltaobjComo',
                text: 'Nuevo Objetivo COMO',
                iconCls:'add',
                disabled :!TOP.btn_altaObj,
                handler: altaComo
            }
//            ,'&emsp;|&emsp;'
//            ,{
//                id:'botonaltaobjOrg',
//                text: 'Nuevo Objetivo ORG',
//                disabled :!TOP.btn_altaObj,
//                handler: altaOrg
//            }
        ]
});

var altura=Ext.getBody().getSize().height - 178;
miTopObjetivosGridPanel.setHeight(altura);
Ext.getCmp('browser').on('resize',function(comp){
//    var dimPanel=Ext.getCmp('miTopDimensionesPanel');
//    var widthDimPanel=dimPanel.getInnerWidth();
//    console.log(this.getSize().width-widthDimPanel);
    miTopObjetivosGridPanel.setWidth(this.getSize().width);
    miTopObjetivosGridPanel.doLayout();
    miTopObjetivosGridPanel.setHeight(Ext.getBody().getSize().height - 178);
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
//objetivosGridPanel.on('afteredit', guardarObjetivo);
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
//				objetivosDS.commitChanges();
//				objetivosDS.reload();
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
function showBtnLiberarMiTop(value,metaData,record){
    var e=record.json.id_estado;
//    console.log(e);
    if (e==1 || e==5)
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
};
function showBtnAprobarMiTop(value,metaData,record){
    var a=record.json.id_dimension;
    if(a!="" && a!=6)
    {
        var e=record.json.id_estado;
        if (e==4)
        {
            return 'x-grid-center-icon';                
        }
        else
                return 'x-hide-display';  

    }            
    else
        return 'x-hide-display';  
};
function showBtnEditarMiTop(value,metaData,record){
    var a=record.json.id_dimension;
    var e=record.json.id_estado;
    if(a!="" && a!=6)
    {
        if (e==1 || e==4 || e==5)
        {
            return 'x-grid-center-icon';                
        }
        else
            return 'x-hide-display'; 
    }
    else
        return 'x-hide-display'; 
};
function showBtnEliminarMiTop(value,metaData,record){
    var a=record.json.id_dimension;
    var e=record.json.id_estado;
    if(a!="" && a!=6)
    {
        if (e==1 || e==4 || e==5)
        {
            return 'x-grid-center-icon';                
        }
        else
            return 'x-hide-display'; 
    }
    else
        return 'x-hide-display'; 
};
function clickBtnEditObjMiTop(grid,rowIndex,colIndex,item,event){
      var id=grid.getStore().getAt(rowIndex).json.id_objetivo;
//      console.log(id);
        dFW_ddpNuevoObetivo(id);
//    Ext.get('browser').load({
//        url: CARPETA+"/editDocumento/15",
//        scripts: true,
//        params: {id:id},
//        text: "Cargando Formulario..."
//    });
};
function clickBtnLiberarObjetivoMiTop (grid,rowIndex,colIndex,item,event){
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
            Ext.MessageBox.alert('Operación OK','El objetivo ha sido liberado');
            miTopObjetivosDS.reload();
            break;
          case 'false':
          case false:
            Ext.MessageBox.alert('Error',result.error);
            miTopObjetivosDS.reload();
            break;
          default:
            Ext.MessageBox.alert('Error',result.error);
            miTopObjetivosDS.reload();
            break;
          }        
        },
        failure: function(response){
//          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
  }
function clickBtnAprobarObjetivoMiTop (grid,rowIndex,colIndex,item,event){
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
            miTopObjetivosDS.reload();
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

function clickBtnEliminarObjetivoMiTop (grid,rowIndex,colIndex,item,event){
    var txt=grid.getStore().getAt(rowIndex).json.obj;
    var id=grid.getStore().getAt(rowIndex).json.id_objetivo;
    Ext.MessageBox.confirm('Confirmation','<b>Confirma la eliminación del objetivo: </b><br><i>'+txt+'</i><br><br>Identificador Nro:'+id, function(btn){
         if(btn=='yes')
         {
            msgProcess('Eliminando objetivo...');
            var id=grid.getStore().getAt(rowIndex).json.id_objetivo;
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
                switch(result){
                case '1':
                case 1:
//                  var dim_GP=Ext.getCmp('miTopDimensionesGridPanel');
//                  var dimensionesDS=dim_GP.getStore();
//                  dimensionesDS.load();
//                  var rowdim=dim_GP.selModel.rowNav.scope.lastActive;
//                  dimensionesDS.on('load',function(){
//                      dim_GP.selModel.selectRow(rowdim,false,false);
//                  });
//                  dim_GP.selModel.selectRow(rowdim);
                  Ext.MessageBox.alert('Operación OK','El objetivo ha sido eliminado');
                  miTopObjetivosDS.reload();
                  break;
                case '0':
                case 0:
                  Ext.MessageBox.alert('Error','Error al intentar eliminar registro, por favor comunique al área sistemas');
                  break;
                default:
                  Ext.MessageBox.alert('Error',result.error);
                  break;
                }        
              },
              failure: function(response){
                Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
              }                      
            });
             
         }
    });  
    
  }
  
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
