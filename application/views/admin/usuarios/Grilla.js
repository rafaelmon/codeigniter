usuariosDataStore = new Ext.data.Store({
      id: 'usuariosDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listado', 
                method: 'POST'
            }),
      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_usuario'
      },[ 
        {name: 'id_usuario', type: 'int', mapping: 'id_usuario'},        
        {name: 'persona', type: 'string', mapping: 'persona'},
        {name: 'email', type: 'string', mapping: 'email'},
        {name: 'usuario', type: 'string', mapping: 'usuario'},
        {name: 'password', type: 'string', mapping: ''},        
        {name: 'id_perfil', type: 'string', mapping: 'id_perfil'},
        {name: 'habilitado', type: 'bool', mapping: 'habilitado'}
      ]),
      sortInfo:{field: 'id_usuario', direction: "ASC"},
      remoteSort: true
    });
    paginador.bindStore(usuariosDataStore);
    
   habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitado',
        header: "Habilitada",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
         disabled: false, //-->NO FUNCIONA
        tabla: 'sys_usuarios',
        align:'center',
        campo_id: 'id_usuario'
    });
    
	comboPerfilData = new Ext.data.Store({
		id: 'comboPerfilData',
		proxy: new Ext.data.HttpProxy({
        	url: CARPETA+'/perfilesUsuarios', 
            method: 'POST'
       	}),
    	reader: new Ext.data.JsonReader({
        	root: 'data',
        	totalProperty: 'num'
      }, [
      	{name: 'id_perfil', type: 'int'},        
        {name: 'perfil', type: 'string'},
      ])
	});
	
    
  UsuariosColumnModel = new Ext.grid.ColumnModel(
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
      },{
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
      },/*{
	  	header: 'Avatar',
		dataIndex: 'imagen',
		width:  69,
		renderer: function(value, cell, record ){ 
			 img_estado = record.get('imagen_estado');
			 estilo='border-left:2px #00FF00 solid';
			 titulo = 'imagen habilitada';
			 if(img_estado==3){
				 estilo='border-left:2px #FF0000 solid';
				 titulo = 'imagen deshabilitada';
			}
        	 tmp = value.toString();
			 if(tmp!=''){
				 return '<div class="controlBtn" ><img title="'+titulo+'" src="'+URL_BASE_FILE+'fotos/avatar_lector/'+tmp+'?'+Math.random()+'" width="69" class="control_delete" /></div>';
			}
			else
			{
				return '<div class="controlBtn">&nbsp;</div>';
			}
        	 
        }
	  },*/{
        header: 'Persona',
        dataIndex: 'persona',
        width:  130,
        sortable: true,
        readOnly: permiso_modificar,
        renderer: function(value, cell){ 
            cell.css = "coolcell";
            return value;
        }
      },{
        header: 'Email',
        dataIndex: 'email',
        width: 230,
		sortable: true,
        editor: new Ext.form.TextField({
		  disabled: !permiso_modificar,
          allowBlank: false,
          maxLength: 50,
          //maskRe: /([a-z._@A-Z0-9\s]+)$/
          vtype: 'email'
          }),
        renderer: function(value, cell){ 
         cell.css = "coolcell";
         return value;
        }
      },{
      	id:'id_perfil',
      	header:'Perfil',
      	sortable: true,
      	dataIndex: 'id_perfil',
      	editor: new Ext.grid.GridEditor(new Ext.form.ComboBox
        ({
            disabled: !permiso_modificar,
            id:'cmperfil',
            listWidth : 300,
            hiddenName: 'cmtid',
            store: comboPerfilData,
            displayField:'perfil',
            valueField:'id_perfil',
            allowBlank: true,
            typeAhead: true,
            mode: 'remote',
            triggerAction: 'all' 
       	}))
      },/*{
        header: 'Empresa',
        dataIndex: 'empresa',
        width:  100,
        sortable: true,
        readOnly: true
      },{
        header: 'Gerencia',
        dataIndex: 'gerencia',
        width:  100,
        sortable: true,
        readOnly: true
      },{
        header: 'Departamento',
        dataIndex: 'departamento',
        width:  100,
        sortable: true,
        readOnly: true
      },*/{
        header: 'Cambiar contraseña',
        dataIndex: 'password',
        width: 120,
        editor: new Ext.form.TextField({
		  disabled: !permiso_modificar,
		  inputType:'password',
		  allowBlank: false,
          maxLength: 50
          }),
        renderer: function(value, cell){ 
         cell.css = "coolcell";
          //return value;
          return '';
        }
      },habilitadaCheck]
    );
    
    buscadorUsuario= new Ext.ux.grid.Search({
        iconCls:'icon-zoom',
    //    readonlyIndexes:['id_convocatoria'],
        disableIndexes:['id_usuario','password','id_perfil','habilitado'],
        align:'left',
        minChars:3
    });
  
   UsuariosListingEditorGrid =  new Ext.grid.EditorGridPanel({
        id: 'PermisosListingEditorGrid',
        title: 'Usuarios',
        store: usuariosDataStore,
        cm: UsuariosColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[buscadorUsuario,habilitadaCheck], 
        clicksToEdit:2,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
        tbar: [
          {
            text: 'Nuevo Usuario',
            tooltip: 'Crear un nuevo usuario...',
            iconCls:'add',                      // reference to our css
            handler: displayAltaUsuarioFormWindow,
            hidden: !permiso_alta
          }/*, '-', { 
            text: 'Eliminar',
            tooltip: 'Eliminar el usuario seleccionado',
            handler: confirmDeleteUsuarios,   // Confirm before deleting
            iconCls:'remove',
			hidden: !permiso_eliminar
          }*/
      ]
    });   

  usuariosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

  UsuariosListingEditorGrid.on('afteredit', guardarUsuario);
  
  
   // guarda los cambios en los datos del usuario luego de la edicion
  function guardarUsuario(oGrid_event)
  {
  	 
  	 //console.log(oGrid_event);
  	 var fields = [];
		fields.push(oGrid_event.field);
	var values = [];
       	values.push(oGrid_event.value);
 
	var encoded_array_f = Ext.encode(fields);
	var encoded_array_v = Ext.encode(values);
   Ext.Ajax.request({   
      waitMsg: 'Por favor espere...',
      url: CARPETA+'/modificar',
      params: {
		 id: oGrid_event.record.data.id_usuario,     
		 campos : encoded_array_f,
		 valores : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            usuariosDataStore.commitChanges();
            usuariosDataStore.reload();
            break;
         case 10:
            Ext.MessageBox.alert('Error','Usuario existente...');
            usuariosDataStore.reload();
            break;  
         case 11:
            Ext.MessageBox.alert('Error','Email existente...');
            usuariosDataStore.reload();
            break;          
         default:
            Ext.MessageBox.alert('Uh uh...','No se pudo actualizar...');
            break;
         }
      },
      failure: function(response){
         var result=response.responseText;
         Ext.MessageBox.alert('error','No se pudo conectar a la Base de Datos. Intente mas tarde');    
      }                      
   });  
  }
  
  
    // This was added in Tutorial 6
  function confirmDeleteUsuarios(){
    if(UsuariosListingEditorGrid.selModel.getCount() == 1) // only one president is selected here
    {
      Ext.MessageBox.confirm('Confirmation','Est&aacute; por borrar un usuario. Desea continuar?', deleteUsuarios);
    } else if(UsuariosListingEditorGrid.selModel.getCount() > 1){
      Ext.MessageBox.confirm('Confirmation','Desea borrar estos usuarios?', deleteUsuarios);
    } else {
      Ext.MessageBox.alert('Uh oh...','Para borrar un usuario debe seleccionar alguno del listado');
    }
  }  
   // This was added in Tutorial 6
  function deleteUsuarios(btn){
    if(btn=='yes'){
         var selections = UsuariosListingEditorGrid.selModel.getSelections();
         var prez = [];
         for(i = 0; i< UsuariosListingEditorGrid.selModel.getCount(); i++){
          prez.push(selections[i].json.id);
		 
         }
         var encoded_array = Ext.encode(prez);
		  //alert(encoded_array);
         Ext.Ajax.request({  
            waitMsg: 'Por favor espere',
            url: CARPETA+'/eliminar', 
            params: { 
               tarea: "borrar", 
			   tabla: "usuario",
			   campo_id: "id",
               ids:  encoded_array
              }, 
            success: function(response){
              var result=eval(response.responseText);
              switch(result){
              case 1:  // Success : simply reload
                usuariosDataStore.reload();
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
   
  	var altura=Ext.getBody().getSize().height - 60;
	UsuariosListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		UsuariosListingEditorGrid.setWidth(this.getSize().width);
		UsuariosListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});