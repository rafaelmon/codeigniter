// JavaScript Document

  // inserta usuario en DB
  function createCalificacion(){
     if(isCalificacionFormValid()){

    Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insertar',
        method: 'POST',
        params: {		  
          periodo          : periodoCombo.getValue(),
          calificacion   : calificacionField.getValue(),
          valor   : valorField.getValue()
        }, 
        success: function(response){              
            var result=eval(response.responseText);
            switch(result){
                case 1:
                    Ext.MessageBox.alert('Alta OK','La competencia fue creada satisfactoriamente.');
                    calificacionesDataStore.reload();
                    calificacionCreateWindow.hide();
                    break;
                case 2:
                    Ext.MessageBox.alert('Error','El campo periodo es obligatorio.');
                    break;
                case 3:
                    Ext.MessageBox.alert('Error','El campo valor es obligatorio.');
                    break;
                case 4:
                    Ext.MessageBox.alert('Error','El campo calificacion es obligatorio.');
                    break;
                case 5:
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
  function isCalificacionFormValid(){	  
	  var v1 = calificacionField.isValid();
          var v2 = valorField.isValid();
          var v3 = periodoCombo.isValid();
      return(  v1 && v2 && v3 );
  }
   
  // display or bring forth the form
function altaCalificacion(){
    if(calificacionCreateForm){
        //if(SeccionCreateForm.findById('fieldsetid')) {
         //get the fieldset
        var oldfieldset = calificacionCreateForm.findById('fieldset_form');
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
       calificacionCreateForm.destroy();
       calificacionCreateWindow.destroy()
    }
    
    periodosJS = new Ext.data.JsonStore({
        url: CARPETA+'/combo_periodos',
        root: 'rows',
        fields: ['id_periodo', 'periodo']
    });
    periodosJS.load();
    periodosJS.on('load' , function(  js , records, options )
    {
        var tRecord = Ext.data.Record.create(
            {name: 'id_periodo', type: 'int'},
            {name: 'periodo', type: 'string'}
        );
        var myNewT = new tRecord({
            id_periodo: -1,
            periodo   : 'Todos'
        });
        periodosJS.insert( 0, myNewT);	
    });

    // reset the Form before opening it
    function resetPresidentForm(){
        calificacionField.setValue('');
        valorField.setValue('');
        periodoCombo.setValue(-1);
    }	
    periodoCombo = new Ext.form.ComboBox({
        id:'periodoCombo',
        forceSelection : true,
        value: 'Todos',
        fieldLabel: 'Periodo',
        store: periodosJS,
        editable : false,
        displayField: 'periodo',
        valueField:'id_periodo',
        allowBlank: false,
        width:  200,
        selectOnFocus:true,
        triggerAction: 'all'
    });
    calificacionField = new Ext.form.TextField({
        id: 'calificacionField',
        fieldLabel: 'Calificacion',
        tabIndex:2,
        maxLength: 512,
        allowBlank: false,
        anchor : '95%'
    });
    valorField = new Ext.form.TextField({
        id: 'valorField',
        fieldLabel: 'Valor',
        tabIndex:2,
        maxLength: 512,
        allowBlank: false,
        anchor : '95%'
    });
      
  calificacionCreateForm = new Ext.FormPanel({
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
                    items: [periodoCombo,calificacionField,valorField]
                }]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createCalificacion
        },{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                calificacionCreateWindow.close();
            }
        }]
    });
	
 
  calificacionCreateWindow = new Ext.Window({
      id: 'competenciaCreateWindow',
      title: 'Crear nueva competencia',
      closable:false,
	  modal:true,
      width: 600,
      height: 200,
      plain:true,
      layout: 'fit',
      items: calificacionCreateForm,
      closeAction: 'close'
    });		
		
		
    calificacionCreateWindow.show();
  }