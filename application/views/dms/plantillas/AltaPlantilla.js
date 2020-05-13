// JavaScript Document

  // inserta Plantilla en DB
  function createPlantilla(){
     if(isPlantillaFormValid()){
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/upload',
            method: 'POST',
            params: {
                nombre        : nombrePlantillaField.getValue(),
                descripcion   : descripcionField.getValue(),
                archivo       : archivoField.getValue(),
                habilitado    : habilitadoField.getValue(),
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 1:
                            Ext.MessageBox.alert('Alta OK','La Plantilla fue creado satisfactoriamente.');
                            PlantillasDataStore.reload();
                            PlantillaCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','El nombre de Plantilla ya existe.');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo crear la Plantilla.');
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
  function isPlantillaFormValid(){	  
	  var v1 = nombrePlantillaField.isValid();
	  var v2 = descripcionField.isValid();
	  var v3 = archivoField.isValid();
	  var v4 = habilitadoField.isValid();
	  return( v1 && v2 && v3 && v4);
  }
   
  // display or bring forth the form
  function displayFormWindow(){

	
	 if(PlantillaCreateForm){
	 	if(PlantillaCreateForm.findById('fieldsetid')) {
		 //get the fieldset
	
	
		 var oldfieldset = PlantillaCreateForm.findById('fieldset_form');
		 //var oldfieldset = PlantillaCreateForm.items;
		 
			//iterate trough each of the component in the fieldset
			oldfieldset.items.each(function(collection,item,length){
				var i = item;
				//destroy the object within the fieldset
				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
			});
		}
		PlantillaCreateForm.destroy();
		PlantillaCreateWindow.destroy()
	}
		

		
		
		
	  
  // reset the Form before opening it
  function resetPresidentForm(){
    nombrePlantillaField.setValue('');
    descripcionField.setValue('');
    archivoField.setValue('');
    habilitadoField.setValue('');
  }	
  
  
nombrePlantillaField = new Ext.form.TextField({
    id: 'nombrePlantillaField',
    fieldLabel: 'Nombre de la Plantilla',
    maxLength: 150,
    allowBlank: false,
    anchor : '95%',
    tabIndex: 2
});
descripcionField = new Ext.form.TextArea({
    id: 'descripcionField',
    fieldLabel: 'Descripción',
    maxLength: 1000,
    allowBlank: true,
    anchor : '95%',
    tabIndex: 3
});
archivoField = new Ext.form.FileUploadField({
    id: 'archivoField',
    fieldLabel: 'Archivo',
    anchor : '95%',
    tabIndex: 4,
    emptyText: 'Seleccione el archivo para subir',
    buttonText: '',
    buttonCfg: {
        iconCls: 'upload-icon'
    }
});
habilitadoField = new Ext.form.Checkbox({
    id: 'habilitadoField',
    fieldLabel: '',
    boxLabel: 'Habilitada',
    checked: true,
    anchor : '95%',
    tabIndex: 5
});
      

  
 
  PlantillaCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'column',
            border:false,
            items:[{
                columnWidth:1,
                layout: 'form',
                border:false,
                items: [nombrePlantillaField,descripcionField,archivoField,habilitadoField]
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: createPlantilla
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			PlantillaCreateWindow.close();
		  }
		}]
    });
	
 
  PlantillaCreateWindow= new Ext.Window({
      id: 'PlantillaCreateWindow',
      title: 'Crear nuevo Plantilla',
      closable:false,
      modal:true,
      width: 500,
      height: 300,
      plain:true,
      layout: 'fit',
      items: PlantillaCreateForm,
      closeAction: 'close'
    });		
		
		
	    PlantillaCreateWindow.show();
  }