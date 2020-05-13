//grilla_plan
edUsuarioPlanDataStore = new Ext.data.GroupingStore({
    id: 'edUsuarioPlanDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/listado_plan', 
        method: 'POST'
    }),
    baseParams:{limit: TAM_PAGINA, id:ID_ED}, 
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_ec'
    },[         
        {name: 'id_ec',             type: 'int',            mapping: 'id_ec'},
        {name: 'competencia',       type: 'string',         mapping: 'competencia'},
        {name: 'subcompetencia',    type: 'string',         mapping: 'subcompetencia'},
        {name: 'accion',            type: 'string',         mapping: 'accion'},
        {name: 'plazo',             type: 'date', dateFormat: 'Y-m-d',           mapping: 'plazo'},
        {name: 'resp_tarea',        type: 'int',            mapping: 'resp_tarea'},
        {name: 'id_evaluacion',     type: 'int',            mapping: 'id_evaluacion'},
        {name: 'responsable',       type: 'string',         mapping: 'responsable'}
    ]),
    sortInfo:{field: 'competencia', direction: "asc"},
    groupField:'competencia',
    remoteSort : true
});

var responsableDS = new Ext.data.ArrayStore({
        fields: ['resp_tarea', 'responsable'],
        data : [[1,'Usuario'],[2,'Supervisor']]
    });

responsableTareaCombo = new Ext.form.ComboBox({
        id:'responsableTareaCombo',
        store: responsableDS,
        blankText:'campo requerido',
        allowBlank: false,
        displayField:'responsable',
        valueField:'resp_tarea',
        triggerAction: 'all',
        typeAhead: true,
        mode: 'local',
        selectOnFocus: true,
//        forceSelection : true,
});  

edUsuarioPlanColumnModel = new Ext.grid.ColumnModel([
    {
        header: '#',
        readOnly: true,
        dataIndex: 'id_ec',
        width: 40,
        hideable:false,
        hidden: false,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        }
    },{
        header: 'Competencia',
        dataIndex: 'competencia',
        sortable: true,
        hidden: true,
        width:  200,
        fixed:true,
        hideable:false,
        readOnly: true
      },{
        header: 'Competencia- Subcompetencia',
        dataIndex: 'subcompetencia',
        sortable: true,
        width:  400,
        fixed:false,
        renderer:showTooltip,
        readOnly: true
      },{
        header: 'Descripción del plan de mejora',
        dataIndex: 'accion',
        align:'center',
        sortable: true,
        width:  400,
        fixed:false,
        renderer:showEditable,
        readOnly: true,
        editor: new Ext.form.TextArea({
            disabled: !permiso_modificar,
            allowBlank: false,
            maxLength: 500,
//            maskRe: /([a-zA-Z \u00f1\u00d1\u00e1\u00e9\u00ed\u00f3\u00fa\u00c1\u00c9\u00cd\u00d3\u00da\u00c7\u00e7\u00dc\u00fc 0-9\s]+)$/
            })
      },{
        header: 'Plazo',
        dataIndex: 'plazo',
        align:'center',
        format: 'd/m/Y',
        sortable: true,
        width:  120,
        renderer:showEditableFecha,
//        fixed:false,
        readOnly: true,
        editor: new Ext.form.DateField({
            disabled: !permiso_modificar,
            allowBlank: false,
            })
      },{
        header: 'Responsable Tarea',
        align:'center',
        dataIndex: 'responsable',
        width:  150,
        sortable: true,
        readOnly: permiso_modificar,
        editor:responsableTareaCombo,
        renderer:showEditable
    }
]);
edUsuarioPlanEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'edUsuarioPlanEditorGrid',
    store: edUsuarioPlanDataStore,
    cm: edUsuarioPlanColumnModel,
    view: new Ext.grid.GroupingView({
            forceFit:false,
            headersDisabled :true,
            groupTextTpl: '{group} ({[values.rs.length]} {[values.rs.length > 1 ? "subcompetencias" : "subcompetencia"]})'
        }),
    enableColLock:false,
//    renderTo: 'grillita',
    renderTo: 'edTab3',
    layout:'form',
    stripeRows:true,
//    viewConfig: {
//        forceFit: true
//    },      
    autoScroll : true,	 
    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    height:500,
    plugins:[],
    bbar:[],
    tbar: []
});
edUsuarioPlanEditorGrid.on('afteredit', guardarCambiosGrillaPlan);
edUsuarioPlanDataStore.load({params: {start: 0}});
var altura=Ext.getBody().getSize().height - 130;
edUsuarioPlanEditorGrid.setHeight(altura);

Ext.getCmp('edTab3').on('resize',function(comp){
        edUsuarioPlanEditorGrid.setWidth(this.getSize().width);
        edUsuarioPlanEditorGrid.setHeight(Ext.getBody().getSize().height - 130);

});
function showMarca(value,metaData,record)
{
    if(value)
    {
        value="X";
        metaData.attr = 'style="background-color:#A1D6A2; color:#FFF;"';
    }            
    else
    {
        value="-";
        metaData.attr = 'style="background-color:#DCDEDC; color:#FFF;"';
    }
    return value;
};
function showEditable(v, p, record){
    if (v=="" || v==null)
    {
        p.attr = 'style="background-color:#F3F781; color:#FE2E2E;"';//#6E6E6E
        v="complete aquí"
    }
    else
        p.attr = 'style="background-color:#F3F781; color:#000;"';
        
    return v;
  }
function showEditableFecha(value, cell, record){
    if (value=="" || value==null)
    {
        cell.attr = 'style="background-color:#F3F781; color:#FE2E2E;"';//#6E6E6E
        value="complete aquí"
    }
    else
    {
        
       value = value.dateFormat('d/m/Y');
       cell.attr = 'style="background-color:#F3F781; color:#000;"';
    }
        
    return value;
  }
function guardarCambiosGrillaPlan(oGrid_event){
    msgWaitProcess('Guardando datos');
     Ext.Ajax.request({   
      waitMsg: 'Por favor espere...',
      url: CARPETA+'/insertar_plan',
      params: {
		 id   : ID_ED,     
		 id_ec: oGrid_event.record.data.id_ec,     
                 campo: oGrid_event.field,
		 valor: oGrid_event.value     
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result.success){
         case true:
            edUsuarioPlanDataStore.commitChanges();
            edUsuarioPlanDataStore.reload();
            Ext.MessageBox.hide();
//            Ext.MessageBox.alert('OK',result.msg);
            break;
         case false:
//            Ext.MessageBox.alert('Error',result.msg);
            edUsuarioPlanDataStore.commitChanges();
//            edUsuarioPlanDataStore.reload();
            break;          
         default:
//            Ext.MessageBox.alert('Error','Error inesperado...');
            break;
         }
      },
      failure: function(response){
         var result=response.responseText;
         Ext.MessageBox.alert('Uh uh...','No hay conexión con la base de datos. Intenta otra vez');    
      }                      
   });  
}
 function msgWaitProcess(titulo){
 Ext.MessageBox.show({
        title: titulo,
        msg: 'Guardando, por favor espere...',
        progress:true,
        progressText: 'Guardando...', 
        width:300, 
        wait:true, 
        waitConfig: {interval:200}
    });
}
function showTooltip (value,metaData,superData){
    var deviceDetail = value;
//    metaData.attr = 'style="background-color:#'+css_bc+'; color:#FFF;"';        
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
//-->FIN PANEL PLAN