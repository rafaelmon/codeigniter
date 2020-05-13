// JavaScript Document

  // inserta usuario en DB
  function createPerfil(){
     if(isPerfilFormValid()){

	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insertar',
        method: 'POST',
        params: {		  
          perfil  : nombrePerfilField.getValue(),
          detalle : detallePerfilField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case 1:
            Ext.MessageBox.alert('Alta OK','El perfil fue creado satisfactoriamente.');
            PerfilesDataStore.reload();
            PerfilCreateWindow.hide();
            break;
          case 2:
            Ext.MessageBox.alert('Error','El campo nombre es obligatorio.');
            break;
          case 3:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            break;	
          default:
            Ext.MessageBox.alert('Error','No se pudo crear el perfil.');
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
  function isPerfilFormValid(){	  
	  var v1 = nombrePerfilField.isValid();
	  var v2 = detallePerfilField.isValid();
      return( v1&&v2 );
  }
   
  // display or bring forth the form
  function altaPerfil(){


	 if(PerfilCreateForm){
	 	//if(SeccionCreateForm.findById('fieldsetid')) {
		 //get the fieldset
	
	
		 var oldfieldset = PerfilCreateForm.findById('fieldset_form');
		 if (oldfieldset)
		 {
		 //var oldfieldset = UsuarioCreateForm.items;
		 
			//iterate trough each of the component in the fieldset
			oldfieldset.items.each(function(collection,item,length){
				var i = item;
				//destroy the object within the fieldset
				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
			});
		}
		PerfilCreateForm.destroy();
		PerfilCreateWindow.destroy()
	}
		
  // reset the Form before opening it
  function resetPresidentForm(){
    nombrePerfilField.setValue('');
    detallePerfilField.setValue('');
  }	
  
  nombrePerfilField = new Ext.form.TextField({
    id: 'nombrePerfilField',
    fieldLabel: 'Nombre Perfil',
    maxLength: 30,
    allowBlank: false,
    anchor : '95%'
    //maskRe: /([a-zA-Z0-9\s]+)$/
      });
  detallePerfilField = new Ext.form.TextArea({
    id: 'detallePerfilField',
    fieldLabel: 'Detalle',
    maxLength: 254,
    allowBlank: true,
    anchor : '95%'
    //maskRe: /([a-zA-Z0-9\s]+)$/
      });
      
  PerfilCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width: 600,        
        items: [{
                id:'fieldset_form',
                layout:'column',
                border:false,
                items:[{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [nombrePerfilField,detallePerfilField]
                }]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createPerfil
        },{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                PerfilCreateWindow.close();
            }
        }]
    });
	
 
  PerfilCreateWindow = new Ext.Window({
      id: 'PerfilCreateWindow',
      title: 'Crear nuevo Perfil de Usuario',
      closable:false,
	  modal:true,
      width: 400,
      height: 300,
      plain:true,
      layout: 'fit',
      items: PerfilCreateForm,
      closeAction: 'close'
    });		
		
		
	    PerfilCreateWindow.show();
  }