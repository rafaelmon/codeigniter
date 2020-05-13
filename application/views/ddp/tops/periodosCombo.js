
periodosTopsDS = new Ext.data.Store({
    id: 'periodosTopsDS',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/periodos_combo', 
        method: 'POST'
    }),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total'
        },[
        {name: 'id_periodo', type: 'int'},        
        {name: 'periodo', type: 'string'},
    ])
});
periodosTopsCombo = new Ext.form.ComboBox({
        id:'periodosTopsCombo',
        forceSelection : false,
        fieldLabel: 'periodo',
        store: periodosTopsDS,
        editable : false,
        displayField: 'periodo',
        allowBlank: false,
        blankText:'campo requerido',
        valueField: 'id_periodo',
//        anchor:'95%',
        tabIndex:30,
        triggerAction: 'all',
        width: 200
    });
    periodosTopsCombo.on ("select",traerUsuarios);
    
    function traerUsuarios(combo,record,index){
        
        idPeriodo=combo.getValue();
        var usuariosCombo=Ext.getCmp('usuariosTopsCombo');
        usuariosCombo.enable();
        var usuariosDS=usuariosCombo.getStore();
        usuariosDS.setBaseParam("id_periodo",idPeriodo);
        var dimPanel=Ext.getCmp('dimensiones-grid-panel2');
        var dimDS=dimPanel.getStore();
        dimDS.setBaseParam("id_periodo",idPeriodo);
        var objPanel=Ext.getCmp('objetivosGridPanel2');
        var objDS=objPanel.getStore();
        objDS.setBaseParam("id_periodo",idPeriodo);
//        usuariosDS.load({params: {id:idPeriodo}});
    };