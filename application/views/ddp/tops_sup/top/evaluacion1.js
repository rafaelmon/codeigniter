
supTop1raEvObjetivosBotonesAction = new Ext.grid.ActionColumn({
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
            getClass:showBtnSupTopEditar,
//            hidden: (!permiso_alta||!rol_editor),
            handler: clickBtnEditObj1raEvSupTop
        },{
            icon:URL_BASE+'images/edit-find.png',
            iconCls :'col_accion',
            tooltip:'Ver historial',
            handler: clickBtnVerHistorial
        }
        ,{
            icon:URL_BASE+'images/1_check.png',
            iconCls :'col_accion',
            tooltip:'Liberar',
            getClass:showBtnSupTopLiberar,
            handler: clickBtnLiberarObj1raEvSupTop
        },{
            icon:URL_BASE+'images/2_check.png',
            iconCls :'col_accion',
            tooltip:'Aprobar',
            getClass:showBtnSupTopAprobar,
            handler: clickBtnAprobarObj1raEvSupTopetivo
        }
    ]
});

supTop1raEvObjetivosColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),
        {
        header: 'Dimension',
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
//        summaryRenderer: function(v, params, data){
//                    return  '<div style="text-align:right;">Subtotal:</div>';
//                },
        sortable: false,
        renderer: showQtipVRef
        },{
        header: 'Peso',
//        tooltip:'Pesos',
        dataIndex: 'peso',
        width: 50,
        align:'center',
//        summaryType: 'sum',
        sortable: false
        },{
        header: '% Alcanzado',
//        tooltip:'Pesos',
        dataIndex: 'real1',
        width: 50,
        align:'center',
        renderer: showCeldaEvalReal1,
//         editor: new Ext.form.NumberField({
//            allowBlank: true,
//            disabled: false
//          }),
//        summaryType: 'sum',
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
//        summaryType: 'sum',
        sortable: false
        },{
            header: 'F. Revisión',
            dataIndex: 'fecha_evaluacion',
            width: 50,
            align:'center',
//            summaryType: 'sum',
            sortable: false
        },
        supTop1raEvObjetivosBotonesAction
    ]
);
supTop1raEvObjetivosGridPanel =  new Ext.grid.EditorGridPanel({
        id: 'supTop1raEvObjetivosGridPanel',
//        title:'objetivos',
        store: supTopObjetivosDS,
//        store: objetivos1raEvSupTopDS,
        cm: supTop1raEvObjetivosColumnModel,
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
        plugins:[/*summaryObj1raEvSupTop*/],
        clicksToEdit:3,
        height:500,
        autoScroll :true,
//        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: [],
        tbar: []
});

var altura=Ext.getBody().getSize().height - 180;
supTop1raEvObjetivosGridPanel.setHeight(altura);
Ext.getCmp('browser').on('resize',function(comp){
    var dimPanel=Ext.getCmp('supTopDimensionesGridPanel');
//    var widthDimPanel=dimPanel.getInnerWidth();
//    console.log(this.getSize().width-widthDimPanel);
    supTop1raEvObjetivosGridPanel.setWidth(this.getSize().width);
    supTop1raEvObjetivosGridPanel.doLayout();
    supTop1raEvObjetivosGridPanel.setHeight(altura);
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
       
function verPermiso(){
//    console.log("x"+permiso_modificar_obj);
    return true;
}
function showBtnSupTopLiberar(value,metaData,record){
    var e=record.json.id_estado;
    var val=record.json.real1;
    if(val!=null)
    {
        if (e==11)
        {
            return 'x-grid-center-icon';                
        }
        else
            return 'x-hide-display';  
        
    }
    else
        return 'x-hide-display';  

};
function showBtnSupTopAprobar(value,metaData,record){
    var e=record.json.id_estado;
    if (e==10)
    {
        return 'x-grid-center-icon';                
    }
    else
            return 'x-hide-display';  

};
function showBtnSupTopEditar(value,metaData,record){
    var e=record.json.id_estado;
    if (e==10 || e==11)
    {
            return 'x-grid-center-icon';                
    }
    else
            return 'x-hide-display'; 
};
function clickBtnEditObj1raEvSupTop(grid,rowIndex,colIndex,item,event){
      var id=grid.getStore().getAt(rowIndex).json.id_objetivo;
        ddpSupTopEv1EditObj_DFW(id);
};
function clickBtnLiberarObj1raEvSupTop  (grid,rowIndex,colIndex,item,event){
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
            Ext.MessageBox.alert('Operación OK','El objetivo ha sido liberado para ser revisado por el editor');
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
function clickBtnAprobarObj1raEvSupTopetivo (grid,rowIndex,colIndex,item,event){
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
function showCeldaEvalReal1 (value,metaData,superData){
    if(superData.json)
    {
        var val=superData.json.real1;
        if (val==null)
            value="";
    }
    return value;
    
}