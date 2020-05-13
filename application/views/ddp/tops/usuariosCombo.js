
usuariosTopsDS = new Ext.data.Store({
    id: 'usuariosTopsDS',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/dr_combo', 
        method: 'POST'
    }),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total'
        },[
        {name: 'id_usuario', type: 'int'},        
        {name: 'nomape', type: 'string'},
    ])
});
usuariosTopsCombo = new Ext.form.ComboBox({
        id:'usuariosTopsCombo',
        disabled :true,
        forceSelection : false,
        fieldLabel: 'usuario',
        store: usuariosTopsDS,
        editable : false,
        displayField: 'nomape',
        allowBlank: false,
        blankText:'campo requerido',
        valueField: 'id_usuario',
//        anchor:'95%',
        tabIndex:31,
        triggerAction: 'all',
        width: 200
    });
    usuariosTopsCombo.on ("select",traerTop);
  
    
    function traerTop(combo,record,index){
        
        var idUsuario=combo.getValue();
        var comboPeriodos=Ext.getCmp('periodosTopsCombo');
        var idPeriodo=comboPeriodos.getValue();
        var dimPanel=Ext.getCmp('dimensiones-grid-panel2');
        var dimDS=dimPanel.getStore();
        dimDS.setBaseParam("id_usuario",idUsuario);
//        dimDS.setBaseParam("id_periodo",idPeriodo);
        dimDS.load();
        var objPanel=Ext.getCmp('objetivosGridPanel2');
        var objDS=objPanel.getStore();
        objDS.setBaseParam("id_usuario",idUsuario);
//        objDS.destroy(true);
        objDS.load({params: {id_dimension:-1}});
        
        
        
    };