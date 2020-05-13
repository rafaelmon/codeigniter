// JavaScript Document

  // inserta usuario en DB
  function createCompetencia(){
     if(isCompetenciaFormValid()){

	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insertar',
        method: 'POST',
        params: {		  
          tipo          : tiposCompetenciaRadios.getValue().inputValue,
          competencia   : competenciaField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case 1:
            Ext.MessageBox.alert('Alta OK','La competencia fue creada satisfactoriamente.');
            competenciasDataStore.reload();
            competenciaCreateWindow.hide();
            break;
          case 2:
            Ext.MessageBox.alert('Error','El campo competencia es obligatorio.');
            break;
          case 3:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            break;	
          default:
            Ext.MessageBox.alert('Error','No se pudo crear la competencia.');
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
  function isCompetenciaFormValid(){	  
	  var v1 = competenciaField.isValid();
      return( v1 );
  }
   
  // display or bring forth the form
  function altaCompetencia(){


	 if(competenciaCreateForm){
	 	//if(SeccionCreateForm.findById('fieldsetid')) {
		 //get the fieldset
	
	
		 var oldfieldset = competenciaCreateForm.findById('fieldset_form');
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
		competenciaCreateForm.destroy();
		competenciaCreateWindow.destroy()
	}
		
  // reset the Form before opening it
  function resetPresidentForm(){
    competenciaField.setValue('');
  }	
   tiposCompetenciaRadios = new Ext.form.RadioGroup({ 
    id:'tiposCompetenciaRadios',
    fieldLabel: 'Tipo',
    anchor:'95%',
    tabIndex:1,
    columns: 2,
    items: [ 
          {boxLabel: 'Cualitativa', name: 'tipo_comp', inputValue: '1', checked: true}, 
          {boxLabel: 'Cuantitativa', name: 'tipo_comp', inputValue: '2'}
     ] 
});
  competenciaField = new Ext.form.TextField({
    id: 'competenciaField',
    fieldLabel: 'Competencia',
    tabIndex:2,
    maxLength: 512,
    allowBlank: false,
    anchor : '95%'
      });
      
  competenciaCreateForm = new Ext.FormPanel({
        labelAlign: 'left',
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
                    items: [tiposCompetenciaRadios,competenciaField]
                }]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createCompetencia
        },{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                competenciaCreateWindow.close();
            }
        }]
    });
	
 
  competenciaCreateWindow = new Ext.Window({
      id: 'competenciaCreateWindow',
      title: 'Crear nueva competencia',
      closable:false,
	  modal:true,
      width: 600,
      height: 200,
      plain:true,
      layout: 'fit',
      items: competenciaCreateForm,
      closeAction: 'close'
    });		
		
		
	    competenciaCreateWindow.show();
  }