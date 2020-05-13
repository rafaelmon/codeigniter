  usuariosGDRDataStore = new Ext.data.Store({
      id: 'usuariosGDRDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listado', 
                method: 'POST'
            }),
//      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id'
      },[ 
        {name: 'id_usuario',    type: 'int',    mapping: 'id_usuario'},        
        {name: 'persona',       type: 'string', mapping: 'persona'},
        {name: 'puesto',        type: 'string', mapping: 'puesto'},
        {name: 'gdr',           type: 'bool',   mapping: 'gdr'},
      ]),
      sortInfo:{field: 'persona', direction: "asc"},
      remoteSort: true
    });
    paginador.bindStore(usuariosGDRDataStore);
    usuariosGDRDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
      
   gdrCheck = new Ext.grid.CheckColumn({
        id:'gdrCheck',
        header: "GDR",
        dataIndex: 'gdr',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
        tabla: 'gr_usuarios',
        align:'center',
        campo_id: 'id_usuario'
    });
    
	
  usuariosGDRColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_usuario',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
				cell.css = "readonlycell";
         		return value;
        },
        hidden: false
      },
      {
        header: 'Usuario',
        dataIndex: 'persona',
        width: 150,
        sortable: true,
        editor: false,
        renderer: function(value, cell){ 
            cell.css = "coolcell";
            return value;
        }
      },{
        header: 'Puesto',
        dataIndex: 'puesto',
        width:  180,
        sortable: false,
        readOnly: true
      },gdrCheck]
    );
    
    buscadorUsuarioGDR= new Ext.ux.grid.Search({
        iconCls:'icon-zoom',
        readonlyIndexes:['usuario','persona'],
        disableIndexes:['id_usuario','gdr'],
        align:'right',
        minChars:3
    });
  
   usuariosGDRListingEditorGrid =  new Ext.grid.EditorGridPanel({
        id: 'usuariosGDRListingEditorGrid',
        title: 'Listado de usuarios y rol gestión de riesgo',
        store: usuariosGDRDataStore,
        cm: usuariosGDRColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[buscadorUsuarioGDR,gdrCheck],
        clicksToEdit:2,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: [paginador],
        tbar: []
    });   

    var altura=Ext.getBody().getSize().height - 60;
    usuariosGDRListingEditorGrid.setHeight(altura);

    Ext.getCmp('browser').on('resize',function(comp){
            usuariosGDRListingEditorGrid.setWidth(this.getSize().width);
            usuariosGDRListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

    });