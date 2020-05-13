
//objetivos1raEvDS = new Ext.data.GroupingStore({
//    id: 'objetivos1raev-ds',
//    proxy: new Ext.data.HttpProxy({
//            url: CARPETA+'/listado_objetivos', 
//            method: 'POST'
//        }),
//    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
//    reader: new Ext.data.JsonReader({
//        root: 'rows',
//        totalProperty: 'total',
//        idProperty: 'id_objetivo'
//    },[ 
//    {name: 'id_objetivo',   type: 'int',            mapping: 'id_objetivo'},        
//    {name: 'oe',            type: 'string',         mapping: 'oe'},
//    {name: 'op',            type: 'string',         mapping: 'op'},
//    {name: 'indicador',     type: 'string',         mapping: 'indicador'},
//    {name: 'fd',            type: 'string',         mapping: 'fd'},
//    {name: 'valor_ref',     type: 'string',         mapping: 'valor_ref'},
//    {name: 'peso',          type: 'float',          mapping: 'peso'},
//    {name: 'real1',         type: 'float',         mapping: 'real1'},
//    {name: 'orden',         type: 'int',            mapping: 'orden'},
//    {name: 'id_estado',     type: 'int',            mapping: 'id_estado'},        
//    {name: 'estado',        type: 'string',         mapping: 'estado'},
//    ]),
//    sortInfo:{field: 'orden', direction: "ASC"},
//    groupField:'estado',
//    remoteSort: true
//});

// var summaryObj1raEv = new Ext.ux.grid.GroupSummary();

botonesObj1raEvAction = new Ext.grid.ActionColumn({
    width: 15,
    editable:false,
    menuDisabled:true,
    header:'Acci&oacute;n',
    hideable:false,
    align:'left',
    width:  100,
    tooltip:'Acciones sobre obj',
    hidden:false,
    items:[{
            icon:URL_BASE+'images/edit.png',
            iconCls :'col_accion',
            tooltip:'Editar',
            getClass:showBtnEditar,
//            hidden: (!permiso_alta||!rol_editor),
            handler: clickBtnEditObj1raEv
        },{
            icon:URL_BASE+'images/edit-find.png',
            iconCls :'col_accion',
            tooltip:'Ver historial',
            handler: clickBtnVerHistorial
        }
        ,{
            icon:URL_BASE+'images/1_check.png',
            iconCls :'col_accion',
            tooltip:'Enviar al supervisor',
            getClass:showBtnLiberar,
            handler: clickBtnLiberarObj1raEv
        },{
            icon:URL_BASE+'images/2_check.png',
            iconCls :'col_accion',
            tooltip:'Aprobar',
            getClass:showBtnAprobar,
            handler: clickBtnAprobarObj1raEvetivo
        }
    ]
});

objetivos1raEvColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),
        {
        header: 'Dimension',
        dataIndex: 'dimension',
        width: 90,
        sortable: false,
        hidden: true
        },
        {
        header: 'Estado',
        dataIndex: 'actor',
        width: 90,
        sortable: false,
        hidden: false
        },
        {
        header: 'Objetivo',
        dataIndex: 'obj',
        width: 200,
        sortable: false,
        hidden: false,
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
        },{
        header: 'Peso',
//        tooltip:'Pesos',
        dataIndex: 'peso',
        width: 50,
        align:'center',
        summaryType: 'sum',
        sortable: false,
        renderer: showPeso
        },{
        header: 'F. Revisión',
        dataIndex: 'fecha_evaluacion',
        width: 50,
        align:'center',
//            summaryType: 'sum',
        sortable: false
        },{
        header: '% Alcanzado',
        tooltip:'Especifique el % alcanzao para el objetivo',
        dataIndex: 'real1',
        width: 80,
        align:'center',
        renderer: showPorcentaje,
//         editor: new Ext.form.NumberField({
//            allowBlank: true,
//            disabled: false
//          }),
        summaryType: 'sum',
        sortable: false
        },{
        header: 'Peso Real',
//        tooltip:'Pesos',
        dataIndex: 'pesoreal',
        width: 80,
        align:'center',
        renderer: showCeldaEvalReal1,
//         editor: new Ext.form.NumberField({
//            allowBlank: true,
//            disabled: false
//          }),
        summaryType: 'sum',
        sortable: false
        },
        botonesObj1raEvAction
    ]
);
objetivos1raEvGridPanel =  new Ext.grid.EditorGridPanel({
        id: 'objetivos1raEvGridPanel',
//        title:'objetivos',
        store: miTopObjetivosDS,
//        store: objetivos1raEvDS,
        cm: objetivos1raEvColumnModel,
        view: new Ext.grid.GroupingView({
            forceFit:true,
            showGroupName:false,
             groupTextTpl: '<p style="color:#{[values.rs[0].json.css_bc]}">{text} <b style="color:#A4A4A4">({[values.rs.length]} {[values.rs.length > 1 ? "objetivos" : "objetivo"]})</b></p>'
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
        plugins:[/*summaryObj1raEv*/],
        clicksToEdit:3,
        height:500,
        autoScroll :true,
//        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: [],
        tbar: []
});

var altura=Ext.getBody().getSize().height - 160;
objetivos1raEvGridPanel.setHeight(altura);
Ext.getCmp('browser').on('resize',function(comp){
//    var dimPanel=Ext.getCmp('miTopDimensionesPanel');
//    var widthDimPanel=dimPanel.getInnerWidth();
//    console.log(this.getSize().width-widthDimPanel);
    objetivos1raEvGridPanel.setWidth(this.getSize().width);
    objetivos1raEvGridPanel.doLayout();
    objetivos1raEvGridPanel.setHeight(altura);
});

// objetivos1raEvGridPanel.on('afteredit', guardarPorcentaje);
  
  
   // guarda los cambios en los datos del usuario luego de la edicion
  function guardarPorcentaje(oGrid_event)
  {
  	 
  	 //console.log(oGrid_event);
//  	 var fields = [];
//		fields.push(oGrid_event.field);
//	var values = [];
//       	values.push(oGrid_event.value);
// 
//	var encoded_array_f = Ext.encode(fields);
//	var encoded_array_v = Ext.encode(values);
//   Ext.Ajax.request({   
//      waitMsg: 'Por favor espere...',
//      url: CARPETA+'/guardar_edicion',
//      params: {
//		 id: oGrid_event.record.data.id_objetivo,     
//		 campos : encoded_array_f,
//		 valores : encoded_array_v
//      }, 
//      success: function(response){              
//         var result=eval(response.responseText);
//         switch(result){
//         case 1:
//            usuariosDataStore.commitChanges();
//            usuariosDataStore.reload();
//            break;
//         case 10:
//            Ext.MessageBox.alert('Error','Usuario existente...');
//            usuariosDataStore.reload();
//            break;  
//         case 11:
//            Ext.MessageBox.alert('Error','Email existente...');
//            usuariosDataStore.reload();
//            break;          
//         default:
//            Ext.MessageBox.alert('Uh uh...','No se pudo actualizar...');
//            break;
//         }
//      },
//      failure: function(response){
//         var result=response.responseText;
//         Ext.MessageBox.alert('error','No se pudo conectar a la Base de Datos. Intente mas tarde');    
//      }                      
//   });  
  }
  
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
       
function verPermiso(){
//    console.log("x"+permiso_modificar_obj);
    return true;
}
function showBtnLiberar(value,metaData,record){
    var e=record.json.id_estado;
    var val=record.json.real1;
    if(val!=null)
    {
        if (e==9 || e==13)
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
    var e=record.json.id_estado;
    if (e==12)
    {
        return 'x-grid-center-icon';                
    }
    else
            return 'x-hide-display';  

};
function showBtnEditar(value,metaData,record){
    var e=record.json.id_estado;
    if (e==6 ||e==9 || e==12)
    {
            return 'x-grid-center-icon';                
    }
    else
            return 'x-hide-display'; 
};
function clickBtnEditObj1raEv(grid,rowIndex,colIndex,item,event){
      var id=grid.getStore().getAt(rowIndex).json.id_objetivo;
        dFW_ddpEditObjEv1(id);
};
function clickBtnLiberarObj1raEv  (grid,rowIndex,colIndex,item,event){
      msgProcess('Liberando objetivo...');
      var id=grid.getStore().getAt(rowIndex).json.id_objetivo;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/liberar_eval1',
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
            Ext.MessageBox.alert('Operación OK','El objetivo ha sido liberado para ser revisado por su supervisor');
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
function clickBtnAprobarObj1raEvetivo (grid,rowIndex,colIndex,item,event){
      msgProcess('Aprobando objetivo...');
      var id=grid.getStore().getAt(rowIndex).json.id_objetivo;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/aprobar_eval1',
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
function showCeldaEvalReal1 (value,metaData,superData){
    if(superData.json)
    {
        var val=superData.json.real1;
        if (val==null)
            value="";
    }
    metaData.attr = 'style="background-color:#C5C5C5  ; color:#000;"';
    return value;
    
}
function showPeso (value,metaData,superData){
        metaData.attr = 'style="background-color:#C5C5C5  ; color:#000;"';
        return value;
}
function showPorcentaje (value,metaData,superData){
        metaData.attr = 'style="background-color:#F9F90F  ; color:#000;"';
        return value;
}
