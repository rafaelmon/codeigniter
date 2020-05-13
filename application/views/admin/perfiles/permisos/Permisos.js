PermisosDataStore = new Ext.data.Store({
	id: 'PermisosDataStore',
        proxy: new Ext.data.HttpProxy({
    	url: CARPETA+'/listado_permisos', 
        method: 'POST'
	}),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_permiso'
    	},[ 
        {name: 'id_permiso',    type: 'int',    mapping: 'id_permiso'},
        {name: 'id_perfil',     type: 'int',    mapping: 'id_perfil'},
        {name: 'id_modulo',     type: 'int',    mapping: 'id_modulo'},
        {name: 'modulo',        type: 'string', mapping: 'modulo'},
        {name: 'perfil',        type: 'string', mapping: 'perfil'},
        {name: 'alta',          type: 'bool',   mapping: 'alta'},
        {name: 'baja',          type: 'bool',   mapping: 'baja'},
        {name: 'modificacion',  type: 'bool',   mapping: 'modificacion'},
        {name: 'listar',        type: 'bool',   mapping: 'listar'}
     ]),
     sortInfo:{field: 'modulo', direction: "ASC"}
});	

	
	var checkAlta = new Ext.grid.CheckColumn({
        id:'Alta',
        header: "Alta",
        dataIndex: 'alta',
        width: 75,
        sortable: false,
        menuDisabled:true,
        tabla: 'sys_permisos',
        campo_id: 'id_permiso'
//        ,sortable:  true
    });
    var checkBaja = new Ext.grid.CheckColumn({
        id:'Baja',
        header: "Baja",
        dataIndex: 'baja',
        width: 75,
        sortable: false,
        menuDisabled:true,
        tabla: 'sys_permisos',
	campo_id: 'id_permiso'
//        ,sortable:  true
    });
	var checkModi = new Ext.grid.CheckColumn({
        id:'Modificacion',
        header: "Modificaci&oacute;n",
        dataIndex: 'modificacion',
        width: 75,
        sortable: false,
        menuDisabled:true,
        tabla: 'sys_permisos',
	campo_id: 'id_permiso'
//        ,sortable:  true
    });   
	var checkList = new Ext.grid.CheckColumn({
        id:'Listar',
        header: "Listado",
        dataIndex: 'listar',
        width: 75,
        sortable: false,
        menuDisabled:true,
        tabla: 'sys_permisos',
        campo_id: 'id_permiso'
//        ,sortable:  true
    });
	
	
	PermisosColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_permiso',
        width: 30,        
        renderer: function(value, cell){ 
            cell.css = "readonlycell";
            return value;
        },
        hidden: true
      },{
        header: 'Modulo',
        dataIndex: 'modulo',
        width: 140,
        readOnly: true,
        renderer: function(value, cell){ 
            cell.css = "coolcell";
            return value;
        }

      },checkAlta,checkBaja,checkModi,checkList
	]);
	
	// grid de los permisos de usuario
	PermisosListingEditorGrid =  new Ext.grid.EditorGridPanel({
      id: 'PermisosListingEditorGrid',
      store: PermisosDataStore,
      cm: PermisosColumnModel,
      enableColLock:false,
      plugins:[checkAlta,checkModi,checkList,checkBaja],
      selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
      tbar: [
          {
            text: 'Agregar Permiso',
            tooltip: 'Agregar nuevo permiso para al perfil',
            iconCls:'add',                      // reference to our css
            handler: crearPermiso,
            hidden: !permiso_permiso_alta,
          }, '-', { 
            text: 'Eliminar Permisos',
            tooltip: 'Eliminar permisos seleccionados',
            handler: confirmDeletePermisos,   // Confirm before deleting
            iconCls:'remove',
            hidden: !permiso_permiso_eliminar,
          }
        ]
     });
	
	// Ventana de Permisos de usuario
	PermisosListingWindow = new Ext.Window({
      id: 'PermisosListingWindow',
      title: 'Administrar permisos para el perfil',
      closable:false,
	  modal:true,
      width:500,
      height:450,
      plain:true,
      layout: 'fit',
      items: PermisosListingEditorGrid,
      buttons: [{
	      text: 'Cerrar',
	      handler: function(){
	        // because of the global vars, we can only instantiate one window... so let's just hide it.
	        PermisosListingWindow.hide();
	      }
	    }]
    });
    
        moduloPermisoStore = new Ext.data.Store({
        id: 'comboUuaa',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado_modulosXPerfil', 
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
        }, [
      	{name: 'id_modulo', type: 'int'},        
        {name: 'modulo', type: 'string'},
      ])
    });    
        
//    moduloPermisoStore = new Ext.data.JsonStore({
//		url: CARPETA+'/listado_modulosXPerfil',
//		root: 'rows',
////                params: {
////			  perfil_id : 1
////                },
//		fields: ['id', 'modulo']
//	});
    
    var moduloPermisoField = new Ext.form.ComboBox({
     		id:'moduloPermisoField',
                //forceSelection : true,
     		fieldLabel: 'M&oacute;dulo',
     		store: moduloPermisoStore,
                editable : false,
//                emptyText :
     		displayField: 'modulo',
     		allowBlank: false,
     		valueField: 'id_modulo',
     		anchor:'95%',
     		triggerAction: 'all',
	 		width: 300
    	});
    
	// Formulario de permiso nuevo ( modulo )
	altapermisoForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width: 300,        
        items: [{
			id:'fieldset_form',
            layout:'column',
            border:false,
            items:[{
                //columnWidth:0.5,
                layout: 'form',
                border:false,
                items: moduloPermisoField
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: agregarPermisoPerfil
		},{
		  text: 'Cancelar',
		  handler: function(){
			nuevopermisoWindow.hide();
		  }
		}]
    });
	
	// Ventana para agregar permisos
	
	var nuevopermisoWindow = new Ext.Window({
            id: 'nuevopermisoWindow',
            title: 'Agregar un permiso para el perfil',
            closable:false,
            modal:true,
            width:300,
            height:150,
            plain:true,
            modal : true ,
            layout: 'fit',
            items: altapermisoForm
            //closeAction: 'close'
        });
 
    
	// Abrir ventana de nuevo permiso
    function crearPermiso()
    {
        
    	//var seleccionado = PerfilesListingEditorGrid.selModel.getSelections();
		//var id_perfil = seleccionado[0].json.id;
                moduloPermisoField.reset();
		moduloPermisoStore.reload();
		 if(!nuevopermisoWindow.isVisible()){       
	       nuevopermisoWindow.show();
	     } else {
	       nuevopermisoWindow.toFront();
	     }	
	};
	
	
	// agregar modulos a un perfil. Permisos
	function agregarPermisoPerfil(){
		 var selections = PerfilesListingEditorGrid.selModel.getSelections();
		 var id_perfil = selections[0].json.id_perfil;// la primer seleccion, la unica
		Ext.Ajax.request({   
			waitMsg: 'Por favor espere...',
			url: CARPETA+'/agregar_modulo',
			params: {
			  tarea: "agregar_modulo",
			  id_modulo  : moduloPermisoField.getValue(),
			  id_perfil : id_perfil
			}, 
			success: function(response){              
			  var result=eval(response.responseText);
			  switch(result){
			  case 1:
				//Ext.MessageBox.alert('Alta OK','El M&oacute;dulo fue agregado.');
				PermisosDataStore.reload();
				nuevopermisoWindow.hide();
				break;
			  case 2:
				Ext.MessageBox.alert('Error','No tiene permisos para realizar la operaci&oacute;n solicitada.');
				nuevopermisoWindow.hide();
			  break;
			  case 3:
				Ext.MessageBox.alert('Error','Debe eleccionar un m&oacute;dulo.');
				nuevopermisoWindow.hide();
			  break;	
			  default:
				Ext.MessageBox.alert('Error','No se pudo agregar el m&oacute;dulo.');
				nuevopermisoWindow.hide();
				break;
			  }        
			},
			failure: function(response){
			  var result=response.responseText;
			  Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
			}                      
       });
	  
	}
	
	
	// eliminar permisos
	function confirmDeletePermisos(){
		if(PermisosListingEditorGrid.selModel.getCount() == 1) // only one president is selected here
		{
		  Ext.MessageBox.confirm('Confirmaci&oacute;n','Est&aacute; por borrar un permiso. Desea continuar?', function(btn){
		  	if(btn=='yes'){
				var selections = PermisosListingEditorGrid.selModel.getSelections();
		 		var id_pm = selections[0].json.id_permiso;// la primer seleccion, la unica
				
				Ext.Ajax.request({   
					waitMsg: 'Por favor espere...',
					url: CARPETA+'/eliminar_modulo_permiso',
					params: {
					  perfil_modulo_id  : id_pm
					}, 
					success: function(response){              
					  var result=eval(response.responseText);
					  switch(result){
					  case 1:
						PermisosDataStore.reload();
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