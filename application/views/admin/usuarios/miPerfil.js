var PassCreateForm = new Ext.FormPanel({
	labelAlign: 'top',
	frame:true,
	height: 100,
	autoScroll : false ,
	id:'form_pass',
	title: 'Mi Perfil',
	bodyStyle:'padding:5px 5px 0',
	items:
	[{
		layout:'column',
		items: 
		[
		{
			columnWidth:0.8,
			layout: 'form',
			items: 
			[
//			
                        {
				xtype:'displayfield',
				fieldLabel: 'Nombre',
				name: 'nombreUsrFieldEditor',
				id: 'user_nombre',
                                cls:'negritafield',
				anchor:'95%',
				labelStyle: 'font-size:11px;'
				
			},
                        {
				xtype:'displayfield',
				fieldLabel: 'Apellido',
				name: 'apellidoUsrFieldEditor',
				id: 'user_apellido',
                                cls:'negritafield',
				anchor:'95%',
				labelStyle: 'font-size:11px;'
				
			},
                        {
				xtype:'displayfield',
				fieldLabel: 'Nombre de Usuario',
				name: 'usuarioFieldEditor',
				id: 'user_nombreusr',
                                cls:'negritafield',
				anchor:'95%',
				labelStyle: 'font-size:11px;'
				
			},
                        {
				xtype:'textfield',
				inputType:'password',
				allowBlank: false,
                                blankText:'campo requerido',
				fieldLabel: 'Contrase&ntilde;a Actual',
				name: 'pass',
				id: 'pass',
				anchor:'95%',
				labelStyle: 'font-size:11px;'
				
			},
			{
				xtype:'textfield',
				inputType:'password',
				allowBlank: false,
                                blankText:'campo requerido',
				fieldLabel: 'Contrase&ntilde;a Nueva',
                                minLength:6,
                                minLengthText:'Por Favor utlice más de 6 caracteres',
				name: 'nuevopass',
				id: 'nuevopass',
				anchor:'95%',
				labelStyle: 'font-size:11px;'
				
			},
			{
				xtype:'textfield',
				inputType:'password',
				allowBlank: false,
                                blankText:'campo requerido',
				fieldLabel: 'Repetir Contrase&ntilde;a Nueva',
                                minLength:6,
                                minLengthText:'Por Favor utlice más de 6 caracteres',
				name: 'repass',
				id: 'repass',
				anchor:'95%',
				labelStyle: 'font-size:11px;'
				
			}
			]
		}
		]
	}],
	buttons: 
	[{
		text: 'Cambiar',
		handler: enviar_datos_form,
	}]
});

PassCreateForm.load(
	{
		url: CARPETA+'/traer_datos_usuario',
		params: {id_usuario: USER_ID },
		waitMsg: 'Cargando Datos...',
		success:function(response)
		{
			//Ext.getCmp('sbs_secciones1').setValue('1,2');
			//Ext.getCmp('sbs_temas1').setValue('1,2');			
			//alert(USER_ID);			
		}
	});


/*
$this->load->model('usuario','usuario',true);
$jcode = $this->usuario->traer_datos_usuario($user_id);
echo $jcode;
*/			
function enviar_datos_form()
{
	var ff = Ext.getCmp('form_pass').form;	
//        console.log(Ext.getCmp('form_pass'));	
		
	if (ff.isValid() )
	{
		var pass = Ext.getCmp('pass');
		var nuevo_pass = Ext.getCmp('nuevopass');
	 	var repass = Ext.getCmp('repass');
		var user_nombre = Ext.getCmp('user_nombre');
		var user_apellido = Ext.getCmp('user_apellido');
		
		Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/cambiar_pass_ad',
        method: 'POST',
        params: {		  
            pass 		: pass.getValue(),
            nuevopass           : nuevo_pass.getValue(),
            repass 		: repass.getValue()
//            nombre 		: user_nombre.getValue(),
//            apellido 		: user_apellido.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case 0:
            Ext.MessageBox.alert('Error','Debe completar correctamente los campos obligatorios');
            //ff.reset();
            pass.focus();
            break;
          case 1:
            Ext.MessageBox.alert('OK','La contrase&ntilde;a fue modificada con &eacute;xito.');
            ff.reset();
            window.location=URL+'/admin/salir';
            break;
          case 2:
            Ext.MessageBox.alert('Error','La contrase&ntilde;a actual ingresada es incorrecta.');
            //ff.reset();
            repass.focus();
            break;
          case 3:
            Ext.MessageBox.alert('Error','Las nuevas contrase&ntilde;as no coinciden.');
            repass.reset();
            break;	
          default:
            Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');
            break;
          }        
        },
        failure: function(response){
          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
				
	}else{
		Ext.MessageBox.alert('Atenci&oacute;n','Debe completar correctamente los campos obligatorios');
	}
}