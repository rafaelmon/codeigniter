  usuariosRolesDataStore = new Ext.data.Store({
      id: 'usuariosRolesDataStore',
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
        {name: 'id_permiso',    type: 'int',    mapping: 'id_permiso'},        
        {name: 'id_usuario',    type: 'int',    mapping: 'id_usuario'},        
        {name: 'usuario',       type: 'string', mapping: 'usuario'},
        {name: 'empresa',       type: 'string', mapping: 'empresa'},
        {name: 'puesto',        type: 'string', mapping: 'puesto'},
        {name: 'area',          type: 'string', mapping: 'area'},
        {name: 'editor',        type: 'bool',   mapping: 'editor'},
        {name: 'revisor',       type: 'bool',   mapping: 'revisor'},
        {name: 'aprobador',     type: 'bool',   mapping: 'aprobador'},
        {name: 'publicador',    type: 'bool',   mapping: 'publicador'},
        {name: 'auditor',       type: 'bool',   mapping: 'auditor'},
        {name: 'habilitado',    type: 'bool',   mapping: 'habilitado'}
      ]),
      sortInfo:{field: 'id_permiso', direction: "desc"},
      remoteSort: true
    });
    paginador.bindStore(usuariosRolesDataStore);
    usuariosRolesDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
      
   habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitadaCheck',
        header: "Habilitado",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
        tabla: 'gr_roles',
        align:'center',
        campo_id: 'id_usuario'
    });
   editorCheck = new Ext.grid.CheckColumn({
        id:'editorCheck',
        header: "Editor",
        dataIndex: 'editor',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:false,
        tabla: 'gr_roles',
        align:'center',
        campo_id: 'id_usuario'
    });
   revisorCheck = new Ext.grid.CheckColumn({
        id:'revisorCheck',
        header: "Revisor",
        dataIndex: 'revisor',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:false,
        tabla: 'gr_roles',
        align:'center',
        campo_id: 'id_usuario'
    });
   aprobadorCheck = new Ext.grid.CheckColumn({
        id:'aprobadorCheck',
        header: "Aprobador",
        dataIndex: 'aprobador',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:false,
        tabla: 'gr_roles',
        align:'center',
        campo_id: 'id_usuario'
    });
   publicadorCheck = new Ext.grid.CheckColumn({
        id:'publicadorCheck',
        header: "Publicador",
        dataIndex: 'publicador',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:false,
        tabla: 'gr_roles',
        align:'center',
        campo_id: 'id_usuario'
    });
   auditorCheck = new Ext.grid.CheckColumn({
        id:'auditorCheck',
        header: "Auditor",
        dataIndex: 'auditor',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:false,
        tabla: 'gr_roles',
        align:'center',
        campo_id: 'id_usuario'
    });
    
	
  usuariosRolesColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_permiso',
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
        dataIndex: 'usuario',
        width: 130,
        sortable: true,
        editor: new Ext.form.TextField({
		  disabled: !permiso_modificar,
		  allowBlank: false,
          maxLength: 150
          }),
        renderer: function(value, cell){ 
         cell.css = "coolcell";
         return value;
        }
      },{
        header: 'Empresa',
        dataIndex: 'empresa',
        width:  70,
        sortable: true,
        readOnly: true
      },{
        header: 'Puesto',
        dataIndex: 'puesto',
        width:  120,
        sortable: false,
        readOnly: true
      },{
        header: 'Area',
        dataIndex: 'area',
        width:  250,
        sortable: true,
        readOnly: true
      },editorCheck,revisorCheck,aprobadorCheck,publicadorCheck,auditorCheck,habilitadaCheck]
    );
    
    buscadorUsuarioRoles= new Ext.ux.grid.Search({
        iconCls:'icon-zoom',
        readonlyIndexes:['usuario','persona'],
        disableIndexes:['id_permiso','editor','revisor','aprobador','publicador','auditor','habilitado','empresa','puesto','area'],
        align:'right',
        minChars:3
    });
  
   usuariosRolesListingEditorGrid =  new Ext.grid.EditorGridPanel({
        id: 'usuariosRolesListingEditorGrid',
        title: 'Listado de usuarios y roles para gestión de documentos',
        store: usuariosRolesDataStore,
        cm: usuariosRolesColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[buscadorUsuarioRoles,habilitadaCheck,editorCheck,revisorCheck,aprobadorCheck,publicadorCheck,auditorCheck],
        clicksToEdit:2,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: [paginador],
        tbar: [
          {
            text: 'Agregar Usuario',
            tooltip: 'Agregar un nuevo usuario...',
            iconCls:'add',                      // reference to our css
            handler: displayUsuarioRolesFormWindow,
            hidden: !permiso_alta
          }, '-', { 
            text: 'Eliminar',
            tooltip: 'Eliminar el usuario seleccionado',
            handler: confirmDeleteusuariosRoles,   // Confirm before deleting
            iconCls:'remove',
            hidden: !permiso_eliminar
          }
      ]
    });   

//  usuariosRolesDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

   
  	var altura=Ext.getBody().getSize().height - 60;
	usuariosRolesListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		usuariosRolesListingEditorGrid.setWidth(this.getSize().width);
		usuariosRolesListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});
        
    
        
// eliminar permisos
	function confirmDeleteusuariosRoles(){
		if(usuariosRolesListingEditorGrid.selModel.getCount() == 1) // only one president is selected here
		{
		  Ext.MessageBox.confirm('Confirmaci&oacute;n','Est&aacute; por borrar un permiso. Desea continuar?', function(btn){
		  	if(btn=='yes'){
				var selections = usuariosRolesListingEditorGrid.selModel.getSelections();
		 		var id_pm = selections[0].json.id_permiso;// la primer seleccion, la unica
				
				Ext.Ajax.request({   
					waitMsg: 'Por favor espere...',
					url: CARPETA+'/eliminar_permiso',
					params: {
					  id_permiso  : id_pm
					}, 
					success: function(response){              
					  var result=eval(response.responseText);
					  switch(result){
					  case 1:
						usuariosRolesDataStore.reload();
						break;	
					  case 2:
					  	Ext.MessageBox.alert('Error','No tiene permisos para realizar la operaci&oacute;n solicitada.');
					  	break;
					  default:
						Ext.MessageBox.alert('Error','No se pudo eliminar el registro.');
						break;
					  }        
					},
					failure: function(response){
					  var result=response.responseText;
					  Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
					}                      
			   });
			   
		  	}
		  });
		} else {
		  Ext.MessageBox.alert('Uh oh...','Para eliminar un permiso debe seleccionar alguno del listado. Solo uno');
		}
	}