PerfilesDataStore = new Ext.data.Store({
      id: 'PerfilesDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listado', 
                method: 'POST'
            }),
      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id'
      },[ 
        {name: 'id_perfil', type: 'int', mapping: 'id_perfil'},        
        {name: 'perfil', type: 'string', mapping: 'perfil'},
        {name: 'detalle', type: 'string', mapping: 'detalle'},
        {name: 'habilitado', type: 'bool', mapping: 'habilitado'}
      ])
    });

habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitado',
        header: "Habilitada",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
         disabled: false, //-->NO FUNCIONA
        tabla: 'sys_perfiles',
        align:'center',
        campo_id: 'id_perfil'
    });
    	
 PerfilesColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_perfil',
        width: 30,        
        renderer: function(value, cell){ 
                cell.css = "readonlycell";
        	return value;		 
        },
        hidden: false
      },{
        header: 'Perfil',
        dataIndex: 'perfil',
        width:  230,
        readOnly: !permiso_modificar,
        sortable: true,
        editor: new Ext.form.TextField({
            disabled: !permiso_modificar,
            allowBlank: false,
            maxLength: 50,
            maskRe: /([a-zA-Z \u00f1\u00d1\u00e1\u00e9\u00ed\u00f3\u00fa\u00c1\u00c9\u00cd\u00d3\u00da\u00c7\u00e7\u00dc\u00fc 0-9\s]+)$/
        }),
        renderer: function(value, cell){ 
         cell.css = "coolcell";
         return value;
        }
      },{
        header: 'Detalle',
        dataIndex: 'detalle',
        width:  450,
        readOnly: true,
        sortable: true,
        editor: new Ext.form.TextArea({
            disabled: !permiso_modificar,
            allowBlank: true,
            maxLength: 254
        }),
        align:'left'
      },habilitadaCheck
      ]
    );
 PerfilesColumnModel.defaultSortable= true;
 
 PerfilesListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'PerfilesListingEditorGrid',
    title: 'Perfiles de Usuarios',
    store: PerfilesDataStore,
    cm: PerfilesColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
     plugins:[habilitadaCheck],
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    bbar: new Ext.PagingToolbar({
        pageSize: TAM_PAGINA,
        store: PerfilesDataStore,
        displayMsg: 'Mostrando {0} - {1} de {2}',				
        displayInfo: true
    }),
    tbar: [
          {
            text: 'Nuevo Perfil',
            tooltip: 'Crear un nuevo perfil de usuario',
            iconCls:'add',                      // reference to our css
            handler: altaPerfil,
			hidden: !permiso_alta
          }, '-', 
          { 
            text: 'Permisos',
            tooltip: 'Asignar permisos al perfil',
            handler: mostrarpermisosWindow,   // Confirm before deleting
            iconCls:'permisos',
			hidden: !permiso_permiso_listar
          }, '-', 
          { 
            text: 'Eliminar',
            tooltip: 'Eliminar el perfil seleccionado',
            handler: confirmDeletePerfiles,
            iconCls:'remove',
			hidden: !permiso_eliminar
          }
      ]
    });
     
   PerfilesDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
   
   PerfilesListingEditorGrid.on('afteredit', guardarPerfil);
   
   // guarda los cambios en los datos del modulo luego de la edicion
  function guardarPerfil(oGrid_event){
   Ext.Ajax.request({   
      waitMsg: 'Por favor espere...',
      url: CARPETA+'/modificar',
      params: {
		 id_perfil: oGrid_event.record.data.id_perfil,     
		 perfil : oGrid_event.record.data.perfil,
                 detalle:oGrid_event.record.data.detalle
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            PerfilesDataStore.commitChanges();
            PerfilesDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            PerfilesDataStore.reload();
            break;
         case 3:
            Ext.MessageBox.alert('Error','El campo nombre es obligatorio.');
            PerfilesDataStore.reload();
            break;          
         default:
            Ext.MessageBox.alert('Error','No hay conexión con la base de datos. Asegurese de tener conexion');
            break;
         }
      },
      failure: function(response){
         var result=response.responseText;
         Ext.MessageBox.alert('Uh uh...','No hay conexión con la base de datos. Intenta otra vez');    
      }                      
   });   
  }
  
  function confirmDeletePerfiles(){
    if(PerfilesListingEditorGrid.selModel.getCount() == 1) // only one president is selected here
    {
      Ext.MessageBox.confirm('Confirmation','Est&aacute; por borrar un perfil. Desea continuar?', deletePerfil);
    } else if(PerfilesListingEditorGrid.selModel.getCount() > 1){
      Ext.MessageBox.confirm('Confirmation','Desea borrar estos perfiles seleccionados?', deletePerfil);
    } else {
      Ext.MessageBox.alert('Uh oh...','Para borrar un perfil debe seleccionar alguno del listado');
    }
  }  
  
  function deletePerfil(btn){
    if(btn=='yes'){
         var selections = PerfilesListingEditorGrid.selModel.getSelections();
         var prez = [];
         for(i = 0; i< PerfilesListingEditorGrid.selModel.getCount(); i++){
          prez.push(selections[i].json.id);
		 
         }
         var encoded_array = Ext.encode(prez);
		  //alert(encoded_array);
         Ext.Ajax.request({  
            waitMsg: 'Por favor espere',
            url: CARPETA+'/borrar', 
            params: { 
               ids:  encoded_array
              }, 
            success: function(response){
              var result=eval(response.responseText);
              switch(result){
              case 1:  // Success : simply reload
                PerfilesDataStore.reload();
                break;
              case 2:  // Success : simply reload
                Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
                break;
              case 3:  // Success : simply reload
                Ext.MessageBox.alert('Error','Alguno de los perfiles seleccionados est&aacute; asociado a uno o mas usuarios.');
                break;
              default:
                Ext.MessageBox.alert('Warning','No se pudo eliminar el registro seleccionado.');
                break;
              }
            },
            failure: function(response){
              var result=response.responseText;
              Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
              }
         });
      }  
  }
  
  function mostrarpermisosWindow()
  {
  	if(PerfilesListingEditorGrid.selModel.getCount() == 1) // only one president is selected here
    {
		var selections = PerfilesListingEditorGrid.selModel.getSelections();
		var id_perfil_sel = selections[0].json.id_perfil;
		var perfil_nombre    = selections[0].json.perfil;

                PermisosDataStore.load({params: {id_perfil: id_perfil_sel }});
		moduloPermisoStore.setBaseParam('id_perfil',id_perfil_sel);
		PermisosListingWindow.setTitle('Administrar permisos para el perfil  "'+perfil_nombre+'"');
                moduloPermisoField.reset();
                moduloPermisoStore.load();
		if(!PermisosListingWindow.isVisible()){       
                    PermisosListingWindow.show();
                } else {
                    PermisosListingWindow.toFront();
                }
		
		//Combo modulos para el formulario de alta en permisos
		//moduloPermisoStore.load({params: {perfil_id: perfil_id_sel}});
        
    } 
    else
    {
      Ext.MessageBox.alert('Error','Debe seleccionar un perfil');
    }
  }
  
	var altura=Ext.getBody().getSize().height - 60;
	PerfilesListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		
		PerfilesListingEditorGrid.setWidth(Ext.getCmp('browser').getSize().width);
		
		PerfilesListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});