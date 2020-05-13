  // inserta Gerencia en DB
function createGerencia(){
    if(isGerenciaFormValid()){
	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insert',
        params: {
          gerencia      : nombreGerenciaField.getValue(),
          abv           : abvGerenciaField.getValue(),
          id_empresa    : empresasCombo.getValue(),
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case 0:
                Ext.MessageBox.alert('Error','No se pudo crear el registro, ya existe una gerencia con ese nombre para esa empresa.');
                break;
            case 1:
                Ext.MessageBox.alert('Alta OK','El Registro fue creado satisfactoriamente.');
                GerenciasDataStore.reload();
                GerenciaCreateWindow.close();
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
  }// END function createGerencia

  
// Verifico que los campos del formulario sean válidos
function isGerenciaFormValid(){	  
    var v1 = nombreGerenciaField.isValid();
    var v2 = abvGerenciaField.isValid();
    var v3 = empresasCombo.isValid();
    return( v1 && v2 && v3 );
}
   
// display or bring forth the form
function displayFormWindow(){
    if(GerenciaCreateForm){
        if(GerenciaCreateForm.findById('fieldsetid')) {
            //get the fieldset
            var oldfieldset = GerenciaCreateForm.findById('fieldset_form');
            //var oldfieldset = GerenciaCreateForm.items;
                //iterate trough each of the component in the fieldset
                oldfieldset.items.each(function(collection,item,length){
                        var i = item;
                        //destroy the object within the fieldset
                        for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
                });
        }
        GerenciaCreateForm.destroy();
        GerenciaCreateWindow.destroy()
    }
		
    nombreGerenciaField = new Ext.form.TextField({
        id: 'nombreGerenciaField',
        fieldLabel: 'Gerencia',
        maxLength: 30,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 1
    });
      
    abvGerenciaField = new Ext.form.TextField({
        id: 'apellidoGerenciaField',
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
    empresasDS = new Ext.data.Store({
        id: 'empresasDS',
        proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/empresas_combo', 
        method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_empresa', type: 'int'},        
            {name: 'empresa', type: 'string'},
        ])
    });
    empresasCombo = new Ext.form.ComboBox({
            id:'empresasCombo',
            forceSelection : false,
            fieldLabel: 'Seleccione la Empresa a la que pertenece la Gerencia',
            store: empresasDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'empresa',
            valueField: 'id_empresa',
            anchor:'95%',
            triggerAction: 'all',
            width: 300,
            tabIndex: 3
    });
		
  function resetPresidentForm(){
    nombreGerenciaField.setValue('');
    abvGerenciaField.setValue('');
    empresasCombo.setValue('');
  }	
  
    GerenciaCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[empresasCombo,nombreGerenciaField,abvGerenciaField]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createGerencia
            },{
            text: 'Cancelar',
            handler: function(){
                GerenciaCreateWindow.close();
            }
            }]
    });//END FormPanel
	
 
    GerenciaCreateWindow= new Ext.Window({
        id: 'GerenciaCreateWindow',
        title: 'Alta nueva Gerencia',
        closable:false,
        modal:true,
        width: 610,
        height: 250,
        plain:true,
        layout: 'fit',
        items: GerenciaCreateForm,
        closeAction: 'close'
    });		
    GerenciaCreateWindow.show();
}//END function displayFormWindow