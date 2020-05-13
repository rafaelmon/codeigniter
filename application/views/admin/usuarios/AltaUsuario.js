// JavaScript Document

  // inserta usuario en DB
function createUsuario(){
    if(isUsuarioFormValid()){

	 
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert',
            params: {
                usuario       : usuarioField.getValue(),
                email         : emailField.getValue(),
                password      : passwordField.getValue(),
                id_perfil     : perfilCombo.getValue(),
                id_persona    : personaCombo.getValue(),
//                id_supervisor : supervisorCombo.getValue()
      //          id_departamento: departamentoCombo.getValue()
            }, 
        success: function(response){              
            var result=eval(response.responseText);
            switch(result){
                case 1:
                        Ext.MessageBox.alert('Alta OK','El usuario fue creado satisfactoriamente.');
                        UsuariosDataStore.reload();
                        UsuarioCreateWindow.close();
                        UsuarioCreateForm.close();
                        break;
                case 2:
		  	Ext.MessageBox.alert('Error','El nombre de usuario ya existe.');
                        break;
                case 3:
		  	Ext.MessageBox.alert('Error','El mail ya existe.');
                         break;	
                case 5:
		  	Ext.MessageBox.alert('Error','La persona ya cuenta con usuario registrado.');
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
    } 
    else 
    {
      Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
    }
}
  
  // check if the form is valid
  function isUsuarioFormValid(){	  
	  var v1 = usuarioField.isValid();
	  var v2 = emailField.isValid();
	  var v3 = passwordField.isValid();
	  var v4 = perfilCombo.isValid();
	  var v5 = personaCombo.isValid();
//	  var v6 = supervisorCombo.isValid();
	  return( v1 && v2 && v3 && v4 && v5);
  }
   
  // display or bring forth the form
  function displayAltaUsuarioFormWindow()
  {
        if(UsuarioCreateForm)
        {
            if(UsuarioCreateForm.findById('fieldset_form')) {
                //get the fieldset
                var oldfieldset = UsuarioCreateForm.findById('fieldset_form');
                //var oldfieldset = UsuarioCreateForm.items;

                //iterate trough each of the component in the fieldset
                oldfieldset.items.each(function(collection,item,length){
                        var i = item;
                        //destroy the object within the fieldset
                        for(i=item; i<length; i++){
//                            console.log(item);
                            oldfieldset.items.get(i).destroy();
                        }
                        });
            }
            UsuarioCreateForm.destroy();
            UsuarioCreateWindow.destroy();
        }
		
        // reset the Form before opening it
        function resetPresidentForm(){
            console.log('entra');
            usuarioField.setValue('');
            emailField.setValue('');
            passwordField.setValue('');
            perfilCombo.setValue('');
            supervisorCombo.setValue('');
            personaCombo.setValue('');
        }	
  
   usuarioField = new Ext.form.TextField({
        id:'usuarioField',
        fieldLabel: 'Usuario',
        maxLength: 30,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',    
        tabIndex: 2,
//        maskRe: /([a-zA-Z0-9\s]+)$/  
    });
   
    
  emailField = new Ext.form.TextField({
    id: 'emailField',
    fieldLabel: 'Email',
    blankText:'campo requerido y en formato email: ejemplo@dominio.com',
    maxLength: 50,
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',    
    //maskRe: /([a-z._@A-Z0-9\s]+)$/
	vtype:'email',
	tabIndex: 4 
      });
  
  passwordField = new Ext.form.TextField({
    id:'passwordField',
    fieldLabel: 'Contrase&ntilde;a',
    maxLength: 30,
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',    
    inputType:'password',
    tabIndex: 3,
    maskRe: /([a-zA-Z0-9\s]+)$/  
      });

      perfilesUsuariosJS = new Ext.data.JsonStore({
	url: CARPETA+'/perfilesUsuarios',
	root: 'data',
	fields: ['id_perfil', 'perfil']
    });
	
    perfilCombo = new Ext.form.ComboBox({
        id:'perfilCombo',
        blankText:'campo requerido',
        forceSelection : false,
        fieldLabel: 'Perfil de Usuario',
        store: perfilesUsuariosJS,
        editable : false,
        displayField: 'perfil',
        valueField:'id_perfil',
        anchor : '95%',
        allowBlank: false,
        selectOnFocus:true,
        triggerAction: 'all',
        tabIndex: 5
    });		
    
    personasDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/personasCombo',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_persona'
        }, [
            {name: 'id_persona', mapping: 'id_persona'},
            {name: 'nomape', mapping: 'nomape'},
            {name: 'documento', mapping: 'documento'},
        ])
    });

    // Custom rendering Template
    var resultTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({documento})</span>',
        '</div></tpl>'
    );
    
    personaCombo = new Ext.form.ComboBox({
        store: personasDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Seleccione la persona',
        displayField:'nomape',
        valueField:'id_persona',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '95%',
        minChars:3,
        labelStyle: 'font-weight:bold;',
        pageSize:10,
         tabIndex: 1,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
//        hideTrigger:true,
        tpl: resultTpl,
//        applyTo: 'search',
        itemSelector: 'div.search-item'
//        onSelect: function(record){ // override default onSelect to do redirect
//            window.location =
//                String.format('http://extjs.com/forum/showthread.php?t={0}&p={1}', record.data.topicId, record.id);
//        }
    });
    

//    SupervisorDS = new Ext.data.Store({
//        proxy: new Ext.data.HttpProxy({
//            url: CARPETA+'/supervisorCombo',
//            method: 'POST'
//        }),
//        reader: new Ext.data.JsonReader({
//            root: 'rows',
//            totalProperty: 'total',
//            id: 'id_usuario'
//        }, [
//            {name: 'id_usuario', mapping: 'id_usuario'},
//            {name: 'nomape', mapping: 'nomape'},
//        ])
//    });
//    
//    supervisorCombo = new Ext.form.ComboBox({
//        id:'supervisorCombo',
//        store: SupervisorDS,
//        blankText:'campo requerido',
//        allowBlank: false,
//        fieldLabel: 'Supervisor ED',
//        displayField: 'nomape',
//        valueField:'id_usuario',
//        typeAhead: false,
//        loadingText: 'Buscando...',
//        anchor : '95%',
//        minChars:3,
//        pageSize:10,
//        tabIndex: 6,
//        emptyText:'Ingresa caracteres para buscar',
//        valueNotFoundText:"",
//    });
//  
//    resetPresidentForm();
      
  UsuarioCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'column',
            border:false,
            items:[{
                columnWidth:0.5,
                layout: 'form',
                border:false,
                items: [personaCombo,passwordField,perfilCombo]
            },{
                columnWidth:0.5,
                layout: 'form',
                border:false,
                items: [usuarioField,emailField]//,empresaCombo,gerenciaCombo,departamentoCombo,supervisorCombo
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: createUsuario
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
                      UsuarioCreateWindow.close();
		  }
		}]
    });
 
    
 
  UsuarioCreateWindow= new Ext.Window({
      id: 'UsuarioCreateWindow',
      title: 'Crear nuevo usuario',
      closable:false,
      modal:true,
      width: 610,
      height: 250,
      plain:true,
      layout: 'fit',
      items: UsuarioCreateForm,
      closeAction: 'close'
    });		
    UsuarioCreateWindow.show();
  }