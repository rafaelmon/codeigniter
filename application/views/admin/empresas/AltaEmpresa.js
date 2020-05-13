  // inserta Empresa en DB
function createEmpresa(){
    if(isEmpresaFormValid()){
	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insert',
        params: {
          empresa    : nombreEmpresaField.getValue(),
          abv       : abvEmpresaField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
//            case 0:
//                Ext.MessageBox.alert('Error','No se pudo crear el registro, ya existe una empresa con el numero de documento ingresado.');
//                break;
            case 1:
                Ext.MessageBox.alert('Alta OK','El Registro fue creado satisfactoriamente.');
                EmpresasDataStore.reload();
                EmpresaCreateWindow.close();
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
  }// END function createEmpresa

  
// Verifico que los campos del formulario sean válidos
function isEmpresaFormValid(){	  
    var v1 = nombreEmpresaField.isValid();
    var v2 = abvEmpresaField.isValid();
    return( v1 && v2 );
}
   
// display or bring forth the form
function displayFormWindow(){
    if(EmpresaCreateForm){
        if(EmpresaCreateForm.findById('fieldsetid')) {
            //get the fieldset
            var oldfieldset = EmpresaCreateForm.findById('fieldset_form');
            //var oldfieldset = EmpresaCreateForm.items;
                //iterate trough each of the component in the fieldset
                oldfieldset.items.each(function(collection,item,length){
                        var i = item;
                        //destroy the object within the fieldset
                        for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
                });
        }
        EmpresaCreateForm.destroy();
        EmpresaCreateWindow.destroy()
    }
		
    nombreEmpresaField = new Ext.form.TextField({
        id: 'nombreEmpresaField',
        fieldLabel: 'Nombre Empresa',
        maxLength: 30,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 1
    });
      
    abvEmpresaField = new Ext.form.TextField({
        id: 'apellidoEmpresaField',
        fieldLabel: 'Nombre Abreviado',
        maxLength: 30,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '15%',
        tabIndex: 2,
        maxLength:4,
        minLength :2,
        maxLengthText:'M&aacute;ximo 4 caracteres',
        minLengthText:'M&iacute;nimo 2 caracteres'
    });
//    logoEmpresaField = new Ext.form.TextField({
//        id: 'logoEmpresaField',
//        fieldLabel: 'Nro Documento',
//        maxLength: 30,
//        allowBlank: false,
//        blankText:'campo requerido',
//        anchor : '95%',
//        tabIndex: 4
//    });
   
		
  function resetPresidentForm(){
    nombreEmpresaField.setValue('');
    abvEmpresaField.setValue('');
  }	
  
    EmpresasCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:400,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[nombreEmpresaField,abvEmpresaField]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createEmpresa
            },{
            text: 'Cancelar',
            handler: function(){
                EmpresaCreateWindow.close();
            }
            }]
    });//END FormPanel
	
 
    EmpresaCreateWindow= new Ext.Window({
        id: 'EmpresaCreateWindow',
        title: 'Alta nueva empresa',
        closable:false,
        modal:true,
        width: 410,
        height: 200,
        plain:true,
        layout: 'fit',
        items: EmpresasCreateForm,
        closeAction: 'close'
    });		
    EmpresaCreateWindow.show();
}//END function displayFormWindow