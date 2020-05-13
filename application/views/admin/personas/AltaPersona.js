  // inserta Persona en DB
function createPersona(){
    if(isPersonaFormValid()){
	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insert',
        params: {
          nombre    : nombrePersonaField.getValue(),
          apellido  : apellidoPersonaField.getValue(),
          id_td     : tdCombo.getValue(),
          documento : documentoPersonaField.getValue(),
          genero    : generoRadios.getValue().inputValue,
          id_empresa: empresaCombo.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case 0:
                Ext.MessageBox.alert('Error','No se pudo crear el registro, ya existe una persona con el numero de documento ingresado.');
                break;
            case 1:
                Ext.MessageBox.alert('Alta OK','El Registro fue creado satisfactoriamente.');
                PersonasDataStore.reload();
                PersonaCreateWindow.close();
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
  }// END function createPersona

  
// Verifico que los campos del formulario sean válidos
function isPersonaFormValid(){	  
    var v1 = nombrePersonaField.isValid();
    var v2 = apellidoPersonaField.isValid();
    var v3 = tdCombo.isValid();
    var v4 = documentoPersonaField.isValid();
    var v5 = generoRadios.isValid();
    var v6 = empresaCombo.isValid();
    return( v1 && v2 && v3 && v4 && v5 && v6);
}
   
// display or bring forth the form
function displayFormWindow(){
    if(PersonaCreateForm){
        if(PersonaCreateForm.findById('fieldsetid')) {
            //get the fieldset
            var oldfieldset = PersonaCreateForm.findById('fieldset_form');
            //var oldfieldset = PersonaCreateForm.items;
                //iterate trough each of the component in the fieldset
                oldfieldset.items.each(function(collection,item,length){
                        var i = item;
                        //destroy the object within the fieldset
                        for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
                });
        }
        PersonaCreateForm.destroy();
        PersonaCreateWindow.destroy()
    }
		
    nombrePersonaField = new Ext.form.TextField({
        id: 'nombrePersonaField',
        fieldLabel: 'Nombre/s',
        maxLength: 30,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 1
    });
      
    apellidoPersonaField = new Ext.form.TextField({
        id: 'apellidoPersonaField',
        fieldLabel: 'Apellido/s',
        maxLength: 30,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 2
    });
    tdDS = new Ext.data.Store({
        id: 'tdDS',
        proxy: new Ext.data.HttpProxy({
        url: LINK_GENERICO+'/tipos_documentos', 
        method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_td', type: 'int'},        
            {name: 'td', type: 'string'},
        ])
    });
    tdCombo = new Ext.form.ComboBox({
            id:'tdCombo',
            forceSelection : false,
            fieldLabel: 'Tipo Documento',
            store: tdDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'td',
            valueField: 'id_td',
            anchor:'95%',
            triggerAction: 'all',
            width: 300,
            tabIndex: 3
    });
    documentoPersonaField = new Ext.form.TextField({
        id: 'documentoPersonaField',
        fieldLabel: 'Nro Documento',
        maxLength: 30,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 4
    });
    generoRadios = new Ext.form.RadioGroup({ 
        id:'generoRadios',
        fieldLabel: 'Genero',
        allowBlank: false,
        anchor : '95%',
        tabIndex:5.1,
        columns: 4,
        items: [ 
              {boxLabel: 'Femenino',    name: 'genero', inputValue: 'F'}, //, checked: true
              {boxLabel: 'Masculino',   name: 'genero', inputValue: 'M'}
         ] 
    });
    
    empresaArray = [
        [2, 'Sales de Jujuy'],
        [3, 'Borax Argentina']        
    ];
    
    empresaStore = new Ext.data.ArrayStore({
        fields: ['id_empresa', 'empresa'],
        data : empresaArray
    });
    
    empresaCombo = new Ext.form.ComboBox({
        id:'empresaCombo',
        store: empresaStore,
        fieldLabel: 'Empresa',
	tooltip:'Empresa con la que esta relacionado la persona',
        displayField:'empresa',
        valueField:'id_empresa',
        typeAhead: true,
        mode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        emptyText:'Seleccione...',
        selectOnFocus:true,
        anchor : '95%',
        tabIndex: 5
    });
   
		
  function resetPresidentForm(){
    nombrePersonaField.setValue('');
    apellidoPersonaField.setValue('');
    tdCombo.setValue('');
    documentoPersonaField.setValue('');
    empresaCombo.setValue('');
  }	
  
    PersonasCreateForm = new Ext.FormPanel({
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
                items: [nombrePersonaField,tdCombo]
            },{
                columnWidth:0.5,
                layout: 'form',
                border:false,
                items: [apellidoPersonaField,documentoPersonaField]//
            },{
                columnWidth:1,
                layout: 'form',
                border:false,
                items: [generoRadios]//
            },{
                columnWidth:0.5,
                layout: 'form',
                border:false,
                items: [empresaCombo]//
            }]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createPersona
            },{
            text: 'Cancelar',
            handler: function(){
                PersonaCreateWindow.close();
            }
            }]
    });//END FormPanel
	
 
    PersonaCreateWindow= new Ext.Window({
        id: 'PersonaCreateWindow',
        title: 'Alta nueva persona',
        closable:false,
        modal:true,
        width: 610,
        height: 320,
        plain:true,
        layout: 'fit',
        items: PersonasCreateForm,
        closeAction: 'close'
    });		
    PersonaCreateWindow.show();
}//END function displayFormWindow