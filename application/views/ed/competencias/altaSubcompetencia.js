// JavaScript Document

  // inserta usuario en DB
  function createSubcompetencia(){
//     console.log(competenciasListingEditorGrid.getStore());
     if(isSubcompetenciaFormValid()){

      if (IDCOMPETENCIA != null)
      {
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insertar_sub',
        method: 'POST',
        params: {
          id_competencia : IDCOMPETENCIA,
          subcompetencia  : subcompetenciaField.getValue(),
          tipo          : tiposSubCompetenciaRadios.getValue().inputValue,
        }, 
        success: function(response){              
            var result=eval(response.responseText);
            switch(result){
                case 1:
                    Ext.MessageBox.alert('Alta OK','La subcompetencia fue creada satisfactoriamente.');
                    IDCOMPETENCIA = 0;
                    subcompetenciasDataStore.reload();
                    subcompetenciaCreateWindow.hide();
                    break;
                case 2:
                    Ext.MessageBox.alert('Error','El campo subcompetencia es obligatorio.');
                    break;
                case 3:
                    Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
                    break;
                case 4:
                    Ext.MessageBox.alert('Error','Debe seleccionar una competencia.');
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
        }
      else
      {
          Ext.MessageBox.alert('Error','Debe seleccionar una competencia.');
      }
    } else {
      Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
    }
  }

  
  // check if the form is valid
  function isSubcompetenciaFormValid(){	  
	  var v1 = subcompetenciaField.isValid();
      return( v1 );
  }
   
  // display or bring forth the form
  function altaSubcompetencia(){


	 if(subcompetenciaCreateForm){
	 	//if(SeccionCreateForm.findById('fieldsetid')) {
		 //get the fieldset
	
	
		 var oldfieldset = subcompetenciaCreateForm.findById('fieldset_form1');
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
		subcompetenciaCreateForm.destroy();
		subcompetenciaCreateWindow.destroy()
	}
		
  // reset the Form before opening it
  function resetPresidentForm(){
    subcompetenciaField.setValue('');
  }	
  tiposSubCompetenciaRadios = new Ext.form.RadioGroup({ 
    id:'tiposSubCompetenciaRadios',
    fieldLabel: '¿Es Obligatoria?',
    anchor:'95%',
    tabIndex:1,
    columns: 2,
    items: [ 
          {boxLabel: 'Si', name: 'tipo_comp', inputValue: '1', checked: true}, 
          {boxLabel: 'No', name: 'tipo_comp', inputValue: '0'}
     ] 
});
  subcompetenciaField = new Ext.form.TextField({
    id: 'subcompetenciaField',
    fieldLabel: 'Subcompetencia',
    tabIndex:2,
    maxLength: 200,
    allowBlank: false,
    anchor : '95%'
      });
      
  subcompetenciaCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width: 600,        
        items: [{
                id:'fieldset_form1',
                layout:'column',
                border:false,
                items:[{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [tiposSubCompetenciaRadios,subcompetenciaField]
                }]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createSubcompetencia
        },{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                subcompetenciaCreateWindow.close();
            }
        }]
    });
	
 
  subcompetenciaCreateWindow = new Ext.Window({
      id: 'subcompetenciaCreateWindow',
      title: 'Crear nueva subcompetencia',
      closable:false,
	  modal:true,
      width: 400,
      height: 200,
      plain:true,
      layout: 'fit',
      items: subcompetenciaCreateForm,
      closeAction: 'close'
    });		
		
		
	    subcompetenciaCreateWindow.show();
  }