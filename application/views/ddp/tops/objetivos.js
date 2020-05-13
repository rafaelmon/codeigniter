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
objetivosDS = new Ext.data.GroupingStore({
    id: 'objetivos-ds2',
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
    {name: 'id_objetivo',   type: 'int',            mapping: 'id_objetivo'},        
    {name: 'oe',            type: 'string',         mapping: 'oe'},
    {name: 'op',            type: 'string',         mapping: 'op'},
    {name: 'indicador',     type: 'string',         mapping: 'indicador'},
    {name: 'fd',            type: 'string',         mapping: 'fd'},
    {name: 'valor_ref',     type: 'string',         mapping: 'valor_ref'},
    {name: 'peso',          type: 'float',          mapping: 'peso'},
    {name: 'orden',         type: 'int',            mapping: 'orden'},
    {name: 'id_estado',     type: 'int',            mapping: 'id_estado'},        
    {name: 'estado',        type: 'string',         mapping: 'estado'},
    ]),
    sortInfo:{field: 'orden', direction: "ASC"},
    groupField:'estado',
    remoteSort: true
});

// var summaryObj = new Ext.ux.grid.GroupSummary();

botonesObjAction = new Ext.grid.ActionColumn({
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
            tooltip:'Liberar',
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

objetivosColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),
        {
        header: 'estado',
        dataIndex: 'estado',
        width: 90,
        sortable: false,
        hidden: true
        },
        {
        header: 'Objetivo de la empresa',
        dataIndex: 'oe',
        width: 200,
        sortable: false,
        hidden: false,
        editable :showEditor.createDelegate(this),
//        editor: new Ext.form.TextArea({
//            allowBlank: true,
//            disabled: verPermiso()
//          }),
        renderer: showobjetivo
        },{
        id: 'coltopobjetivo',
        header: 'Objetivos personal',
        dataIndex: 'op',
        width: 200,
        sortable: false,
//        editor: new Ext.form.TextArea({
//            allowBlank: true,
//            disabled: !permiso_modificar_obj
//          }),
        renderer: showobjetivo
        },{
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
        summaryRenderer: function(v, params, data){
                    return  '<div style="text-align:right;">Subtotal:</div>';
                },
        sortable: false,
        renderer: showQtipVRef
        },{
        header: 'Peso',
//        tooltip:'Pesos',
        dataIndex: 'peso',
        width: 50,
        align:'center',
//         editor: new Ext.ux.form.SpinnerField({
//            allowBlank: true,
//            disabled: !permiso_modificar
//          }),
        summaryType: 'sum',
        sortable: false
        },
        botonesObjAction
    ]
);
objetivosGridPanel =  new Ext.grid.EditorGridPanel({
        id: 'objetivosGridPanel2',
//        title:'objetivos',
        store: objetivosDS,
        cm: objetivosColumnModel,
        view: new Ext.grid.GroupingView({
            forceFit:true,
            showGroupName:false,
            groupTextTpl: '{text} <b style="color:#A4A4A4;">({[values.rs.length]} {[values.rs.length > 1 ? "objetivos" : "objetivo"]})</b>'
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
        tbar: []
});
//objetivosDS.load({params: {start: 0, limit: TAM_PAGINA}});

var altura=Ext.getBody().getSize().height - 60;
objetivosGridPanel.setHeight(altura);
Ext.getCmp('browser').on('resize',function(comp){
//    var dimPanel=Ext.getCmp('dimensiones-grid-panel2');
//    var widthDimPanel=dimPanel.getInnerWidth();
//    console.log(this.getSize().width-widthDimPanel);
    objetivosGridPanel.setWidth(this.getSize().width);
    objetivosGridPanel.doLayout();
    objetivosGridPanel.setHeight(Ext.getBody().getSize().height - 60);
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
function showBtnLiberar(value,metaData,record){
    var e=record.json.id_estado;
    if (e==3)
    {
        var dim=record.json.id_dimension;
        if(dim!="" && dim!=6)
        {
            return 'x-grid-center-icon';                
        }
        else
            return 'x-hide-display';  
    }
    else
        return 'x-hide-display';  
};
function showBtnAprobar(value,metaData,record){
    var a=record.json.id_dimension;
    var b=record.json.id_estado;
    if(a!="" && a!=6 && b==2)
    {
        if (b==2)
                return 'x-grid-center-icon';                
        else
                return 'x-hide-display';  

    }            
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
        dFW_ddpEditObetivo(id);
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
              Ext.MessageBox.alert('Operación OK','El objetivo ha sido liberado');
              objetivosDS.reload();
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
              Ext.MessageBox.alert('Operación OK','El objetivo ha sido liberado');
              objetivosDS.reload();
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
      console.log(a);
      return false;
  }