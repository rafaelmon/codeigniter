//grilla_plan
edUsuarioCompCuantDataStore = new Ext.data.GroupingStore({
    id: 'edUsuarioCompCuantDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/listado_competencias_cuant', 
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
        {name: 'valor',             type: 'int',         mapping: 'valor'},
    ]),
    sortInfo:{field: 'competencia', direction: "asc"},
    groupField:'competencia',
    remoteSort : true
});
var arrayEnteros=[0,1,2,3,4,5,6,7,8,9,10];  
    enterosCombo = new Ext.form.ComboBox({
        id:'enterosCombo',
        forceSelection : false,
        store: arrayEnteros,
        editable : true,
        allowBlank: false,
        blankText:'campo requerido',
        anchor:'95%',
        mode:'local',
        triggerAction: 'all'
//        width: 300
    });

edUsuarioCompCuantColumnModel = new Ext.grid.ColumnModel([
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
        width:  800,
        fixed:false,
        readOnly: true
      },{
        header: 'Valor',
        dataIndex: 'valor',
        align:'center',
        sortable: true,
        width:  150,
        fixed:false,
        readOnly: true,
        renderer:showEditable,
        editor: enterosCombo
//        editor: new Ext.form.NumberField({
//            disabled: !permiso_modificar,
//            allowBlank: false,
//            })
      }
]);
edUsuarioCompCuantEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'edUsuarioCompCuantEditorGrid',
    store: edUsuarioCompCuantDataStore,
    cm: edUsuarioCompCuantColumnModel,
    view: new Ext.grid.GroupingView({
            forceFit:false,
            headersDisabled :true,
            groupTextTpl: '{group} ({[values.rs.length]} {[values.rs.length > 1 ? "subcompetencias" : "subcompetencia"]})'
        }),
    enableColLock:false,
//    renderTo: 'grillita',
    renderTo: 'edTab1',
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
edUsuarioCompCuantEditorGrid.on('afteredit', guardarCambiosGrillaCompCuant);
edUsuarioCompCuantDataStore.load({params: {start: 0}});
var altura=Ext.getBody().getSize().height - 130;
edUsuarioCompCuantEditorGrid.setHeight(altura);

Ext.getCmp('edTab0').on('resize',function(comp){
        edUsuarioCompCuantEditorGrid.setWidth(this.getSize().width);
        edUsuarioCompCuantEditorGrid.setHeight(Ext.getBody().getSize().height - 130);

});
function showEditable(v, p, record){
       if (v=="" || v==null)
       {
            v="complete aquí"
            p.attr = 'style="background-color:#F3F781; color:#6E6E6E;"';
           
       }
       else
            p.attr = 'style="background-color:#F3F781; color:#000;"';
           
       return v;
  }
function guardarCambiosGrillaCompCuant(oGrid_event){
     Ext.Ajax.request({   
      waitMsg: 'Por favor espere...',
      url: CARPETA+'/insertar_comp_cuant',
      params: {
		 id_ec: oGrid_event.record.data.id_ec,     
		 valor: oGrid_event.value     
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result.success){
         case true:
            edUsuarioCompCuantDataStore.commitChanges();
            edUsuarioCompCuantDataStore.reload();
            Ext.MessageBox.alert('OK',result.msg);
            break;
         case false:
            Ext.MessageBox.alert('Error',result.msg);
            edUsuarioCompCuantDataStore.commitChanges();
            edUsuarioCompCuantDataStore.reload();
            break;          
         default:
            Ext.MessageBox.alert('Error','Error inesperado...');
            break;
         }
      },
      failure: function(response){
         var result=response.responseText;
         Ext.MessageBox.alert('Uh uh...','No hay conexión con la base de datos. Intenta otra vez');    
      }                      
   });  
}

//-->FIN PANEL SUPERIOR