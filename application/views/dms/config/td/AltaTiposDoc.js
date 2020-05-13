  // inserta TipoDoc en DB
function createTipoDoc(){
    if(isTipoDocFormValid()){
	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insert',
        params: {
          td        : tipoDocField.getValue(),
          abv       : abvTdField.getValue(),
          detalle   : detalleField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case 0:
                Ext.MessageBox.alert('Error','No se pudo crear el registro, ya existe un tipo de documento con ese nombre.');
                break;
            case 1:
                Ext.MessageBox.alert('Alta OK','El Registro fue creado satisfactoriamente.');
                TiposDocsDataStore.reload();
                TipoDocCreateWindow.close();
                break;
            default:
                Ext.MessageBox.alert('Error','No se pudo crear el registro, por favor vuelva a intentarlo o contacte con el administrador.');
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
  }// END function createTipoDoc

  
// Verifico que los campos del formulario sean válidos
function isTipoDocFormValid(){	  
    var v1 = tipoDocField.isValid();
    var v2 = abvTdField.isValid();
    var v3 = detalleField.isValid();
    return( v1 && v2 && v3 );
}
   
// display or bring forth the form
function displayFormWindow(){
    if(TipoDocCreateForm){
        if(TipoDocCreateForm.findById('fieldset_form')) {
            //get the fieldset
            var oldfieldset = TipoDocCreateForm.findById('fieldset_form');
            //var oldfieldset = TipoDocCreateForm.items;
                //iterate trough each of the component in the fieldset
                oldfieldset.items.each(function(collection,item,length){
                        var i = item;
                        //destroy the object within the fieldset
                        for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
                });
        }
        TipoDocCreateForm.destroy();
        TipoDocCreateWindow.destroy()
    }
		
    tipoDocField = new Ext.form.TextField({
        id: 'tipoDocField',
        fieldLabel: 'Tipo de documento',
        maxLength: 64,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 1
    });
    
    detalleField = new Ext.form.TextArea({
        id: 'detalleField',
        fieldLabel: 'Destalle/Descripción',
        maxLength: 1024,
        allowBlank: true,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 3
    });
      
    abvTdField = new Ext.form.TextField({
        id: 'abvTdField',
        fieldLabel: 'Nombre abreviado',
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '15%',
        tabIndex: 2,
        maxLength:4,
        minLength :2,
        maxLengthText:'M&aacute;ximo 4 caracteres',
        minLengthText:'M&iacute;nimo 2 caracteres'
    });
    
		
  function resetPresidentForm(){
    tipoDocField.setValue('');
    detalleField.setValue('');
    abvTdField.setValue('');
  }	
  
    TipoDocCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[tipoDocField,abvTdField,detalleField]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createTipoDoc
            },{
            text: 'Cancelar',
            handler: function(){
                TipoDocCreateWindow.close();
            }
            }]
    });//END FormPanel
	
 
    TipoDocCreateWindow= new Ext.Window({
        id: 'TipoDocCreateWindow',
        title: 'Alta nuevo Tipo de documento',
        closable:false,
        modal:true,
        width: 610,
        height: 300,
        plain:true,
        layout: 'fit',
        items: TipoDocCreateForm,
        closeAction: 'close'
    });		
    TipoDocCreateWindow.show();
}//END function displayFormWindow