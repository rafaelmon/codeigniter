// JavaScript Document

  // inserta usuario en DB
  function editUsuario(){
     if(isUsuarioFormValidEditor()){

	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/update_user',
        params: {
          nombre  : nombreUsrFieldEditor.getValue(),
          email: emailFieldEditor.getValue(),
          usuario : usuarioFieldEditor.getValue(),
          password: passwordFieldEditor.getValue(),
          apellido  : apellidoUsrFieldEditor.getValue(),
          tipo  : tipoFieldEditor.getValue(),
          perfil_id  : perfilFieldEditor.getValue(),
          estado  : estadoFieldEditor.getValue(),
          usuario_twitter  : twitterFieldEditor.getValue(),
          descripcion  : descripcionFieldEditor.getValue(),
          ciudad  : ciudadFieldEditor.getValue(),
          domicilio  : domicilioFieldEditor.getValue(),
          telefono  : telefonoFieldEditor.getValue(),
          provincia_id  : combo_provincias.getValue(),
          usuario_id : USER_ID
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case 1:
            Ext.MessageBox.alert('Edit OK','El usuario fue modificado satisfactoriamente.');
            UsuariosDataStore.reload();
            UsuarioCreateWindowEditor.close();
            break;
		  case 2:
		  	Ext.MessageBox.alert('Error','El nombre de usuario ya existe.');
		  break;
		  case 3:
		  	Ext.MessageBox.alert('Error','El mail ya existe.');
		  break;	
		  case 4:
		  	Ext.MessageBox.alert('Error','No tiene Permisos');
		  	UsuarioCreateWindowEditor.close();
		  break;	
          default:
            Ext.MessageBox.alert('Error','No se pudo crear el usuario.');
            break;
          }        
        },
        failure: function(response){
          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
    } else {
      Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
    }
  }

  
  // check if the form is valid
  function isUsuarioFormValidEditor(){	  
	  var ve1 = nombreUsrFieldEditor.isValid();
	  var ve2 = emailFieldEditor.isValid();
	  var ve3 = usuarioFieldEditor.isValid();
	  var ve4 = passwordFieldEditor.isValid();
	  var ve6 = apellidoUsrFieldEditor.isValid();
	  return( ve1 && ve2 && ve3 && ve4 && ve6 );
  }
   
   var USER_ID=0;
  // display or bring forth the form
  function displayFormWindowEditor(usuario_id){

	USER_ID=usuario_id;
		  
  // reset the Form before opening it
  function resetPresidentForm(){
    codigoFieldEditor.setValue('');
	nombreUsrFieldEditor.setValue('');
    emailFieldEditor.setValue('');
    usuarioFieldEditor.setValue('');
    passwordFieldEditor.setValue('');
  }	
  
  
  nombreUsrFieldEditor = new Ext.form.TextField({
    id: 'nombreUsrFieldEditor',
    fieldLabel: 'Nombre',
    maxLength: 30,
    allowBlank: false,
    anchor : '95%'
      });
      
  apellidoUsrFieldEditor = new Ext.form.TextField({
    id: 'apellidoUsrFieldEditor',
    fieldLabel: 'Apellido',
    maxLength: 30,
    allowBlank: false,
    anchor : '95%'
      });
   
    
  emailFieldEditor = new Ext.form.TextField({
    id: 'emailFieldEditor',
    fieldLabel: 'Email',
    maxLength: 50,
    allowBlank: false,
    anchor : '95%',    
    //maskRe: /([a-z._@A-Z0-9\s]+)$/
	vtype:'email' 
      });
  
 
  usuarioFieldEditor = new Ext.form.TextField({
    id:'usuarioFieldEditor',
    fieldLabel: 'Usuario',
    maxLength: 30,
    allowBlank: false,
    anchor : '95%',    
    maskRe: /([a-zA-Z0-9\s]+)$/  
      });
      
   descripcionFieldEditor = new Ext.form.TextArea({
   		xtype:'textarea',
        fieldLabel: 'Descripci&oacute;n',
        name: 'descripcion',
        anchor:'97%',
        id:'descripcion',
	});
 	
  passwordFieldEditor = new Ext.form.TextField({
    id:'passwordFieldEditor',
    fieldLabel: 'Contrase&ntilde;a',
    maxLength: 30,
    allowBlank: true,
    anchor : '95%',    
    maskRe: /([a-zA-Z0-9\s]+)$/  
      });
      
  twitterFieldEditor = new Ext.form.TextField({
    id: 'twitterFieldEditor',
    fieldLabel: 'Usuario Twitter',
    maxLength: 30,
    allowBlank: true,
    anchor : '95%',
    maskRe: /([a-zA-Z0-9\s]+)$/
      });
      
      ciudadFieldEditor = new Ext.form.TextField({
    id: 'ciudadFieldEditor',
    fieldLabel: 'Ciudad',
    maxLength: 70,
    allowBlank: true,
    anchor : '95%'
      });
      
      domicilioFieldEditor = new Ext.form.TextField({
    id: 'domicilioFieldEditor',
    fieldLabel: 'Domicilio',
    maxLength: 70,
    allowBlank: true,
    anchor : '95%'
      });
      
       telefonoFieldEditor = new Ext.form.TextField({
    id: 'telefonoFieldEditor',
    fieldLabel: 'Telefono',
    maxLength: 70,
    allowBlank: true,
    anchor : '95%'
      });
      
      array_provincias = new Ext.data.JsonStore({
	url: LINK_GENERICO+'/provincias',
	baseParams:{id_pais:13},
	root: 'rows',
	fields: ['id_provincia', 'provincia']
});
array_provincias.load();

combo_provincias = new Ext.form.ComboBox({
	id: 'id_provincias_id',
	name: 'provincia_id_n',
	hiddenName :'provincia_id',
	allowBlank: true,
	fieldLabel: 'Provincia',
	store: array_provincias,
	displayField: 'provincia',
	valueField: 'id_provincia',
	mode: 'local',
	width: 110,
	editable: false,
	emptyText: 'Seleccione una Provincia...',
	triggerAction: 'all',
	anchor:'95%',
	tabIndex: 10
});
  //*********
   array_tipos_usuarios = new Ext.data.JsonStore({
	url: CARPETA+'/tiposUsuarios',
	root: 'data',
	baseParams:{tarea: "LISTAR_TIPO_SECCION"},
	fields: ['id', 'tipo']
});
array_tipos_usuarios.load();
	
array_tipos_usuarios.on('load' , function(  js , records, options ){
											   
	var tRecord = Ext.data.Record.create(
		{name: 'id', type: 'int'},
		{name: 'tipo', type: 'string'}
	);
} );
	
tipoFieldEditor = new Ext.form.ComboBox({
			 id:'tipoFieldEditor',
			 forceSelection : false,
			 fieldLabel: 'Tipo de Usuario',
			 value: '',
			 store: array_tipos_usuarios,
			 editable : false,
			 displayField: 'tipo',
			 emptyText: 'Seleccione un tipo...',
			 valueField:'id',
			 allowBlank: false,
			 selectOnFocus:true,
			 triggerAction: 'all'
			});	
  //*********
  //*********
   array_perfiles_usuarios = new Ext.data.JsonStore({
	url: CARPETA+'/perfilesUsuarios',
	root: 'data',
	fields: ['id', 'nombre']
});
array_perfiles_usuarios.load();
	
array_perfiles_usuarios.on('load' , function(  js , records, options ){
											   
	var tRecord = Ext.data.Record.create(
		{name: 'id', type: 'int'},
		{name: 'nombre', type: 'string'}
	);
	var myNewT = new tRecord({
		id: 0,
		nombre: 'Ninguno'
	});
	array_perfiles_usuarios.insert( 0, myNewT);	
} );
	
perfilFieldEditor = new Ext.form.ComboBox({
			 id:'perfilFieldEditor',
			 forceSelection : false,
			 fieldLabel: 'Perfil de Usuario',
			 value: '',
			 store: array_perfiles_usuarios,
			 editable : false,
			 displayField: 'nombre',
			 valueField:'id',
			 emptyText: 'Seleccione un perfil...',
			 allowBlank: false,
			 selectOnFocus:true,
			 triggerAction: 'all'
			});	
  //*********
  //*********
   array_estados_usuarios = new Ext.data.JsonStore({
	url: CARPETAESTADO+'/listado',
	root: 'rows',
	fields: ['id', 'descripcion']
});
array_estados_usuarios.load();
	
array_estados_usuarios.on('load' , function(  js , records, options ){
											   
	var tRecord = Ext.data.Record.create(
		{name: 'id', type: 'int'},
		{name: 'descripcion', type: 'string'}
	);
} );
	
estadoFieldEditor = new Ext.form.ComboBox({
			 id:'estadoFieldEditor',
			 forceSelection : false,
			 fieldLabel: 'Estado',
			 value: '',
			 store: array_estados_usuarios,
			 editable : false,
			 displayField: 'descripcion',
			 emptyText: 'Seleccione un estado...',
			 valueField:'id',
			 allowBlank: false,
			 selectOnFocus:true,
			 triggerAction: 'all'
			});	
  //*********

  
  var UsuarioCreateFormEditor = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width: 600,        
        items: [{
			id:'FieldEditorset_form',
            layout:'column',
            border:false,
            items:[{
                columnWidth:0.5,
                layout: 'form',
                border:false,
                items: [apellidoUsrFieldEditor, usuarioFieldEditor,emailFieldEditor,combo_provincias,domicilioFieldEditor]
            },{
                columnWidth:0.5,
                layout: 'form',
                border:false,
                items: [nombreUsrFieldEditor,passwordFieldEditor,twitterFieldEditor,ciudadFieldEditor,telefonoFieldEditor]//
            },{
            	columnWidth:0.3,
            	layout: 'form',
            	border:false,
            	items:[tipoFieldEditor]
            },{
            	columnWidth:0.3,
            	layout: 'form',
            	border:false,
            	items:[perfilFieldEditor]
            },{
            	columnWidth:0.4,
            	layout: 'form',
            	border:false,
            	items:[estadoFieldEditor]
            },{
            
            },{
            	columnWidth:1,
            	layout: 'form',
            	border:false,
            	items:[descripcionFieldEditor]
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: editUsuario
		},{
		  text: 'Cancelar',
		  handler: function()
		  {
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			UsuarioCreateWindowEditor.close();
			if(UsuarioCreateFormEditor.findById('fieldsetid'))
			{
		 		//get the fieldset
				var oldfieldseteditor = UsuarioCreateFormEditor.findById('fieldset_form');
		 		//var oldfieldseteditor = UsuarioCreateFormEditor.items;
		 
				//iterate trough each of the component in the fieldset
				oldfieldseteditor.items.each(function(collection,item,length)
				{
					var i = item;
					//destroy the object within the fieldset
					for(i=item; i<length; i++){oldfieldseteditor.items.get(i).destroy();}
				});
			}
			//alert('borrando');
			UsuarioCreateFormEditor.destroy();
			UsuarioCreateWindowEditor.destroy();
		  }
		}]
    });
	
	 UsuarioCreateFormEditor.getForm().load(
				{
					url: CARPETA+'/traer_datos_usuario',
					params: {id_usuario:USER_ID },
					waitMsg: 'Cargando Nota...',
					success:function(response)
					{
						//Ext.getCmp('sbs_secciones1').setValue('1,2');
						//Ext.getCmp('sbs_temas1').setValue('1,2');			
						//alert(USER_ID);			
					}
				});
 
  UsuarioCreateWindowEditor= new Ext.Window({
      id: 'UsuarioCreateWindowEditor',
      title: 'Editar usuario',
      closable:false,
	  modal:true,
      width: 610,
      height: 460,
      plain:true,
      layout: 'fit',
      items: UsuarioCreateFormEditor,
      closeAction: 'close'
    });		
		
		
	    UsuarioCreateWindowEditor.show();
  }