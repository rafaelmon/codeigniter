// JavaScript Document
function clickBtnObs (grid, rowIndex)
{
    var record = grid.getStore().getAt(rowIndex); 
    var id_documento=record.data.id_documento;
    console.log(id_documento);


    textoObsField = new Ext.form.TextArea({
        id: 'textoObsField',
        name:'texto',
        fieldLabel: 'Observación',
        maxLength: 4048,
        maxLengthText:'supera el máximo de caracteres permitidos',
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        height: 100,
        maskRe: /([a-zñáéíóúüA-ZÑÁÉÍÓÚÜ0-9,¿!¡;:\.\?\-\s]+)$/
    });
    
    observarCreateForm = new Ext.FormPanel({
        id:'observarDoc-form',
        labelAlign: 'left',
//        labelWidth:150,
        bodyStyle:'padding:5px',
        width: 450,        
        items: [textoObsField],
	buttons: [{
            text: 'Guardar',
            handler: observarDocumento
	},{
            text: 'Cancelar',
            handler: function(){
                observarCreateForm.destroy();
                obsDocumentoCreateWindow.close();
            }
            }]
    });
    function observarDocumento(){
     if(isObsFormValid()){	
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/grabar_obs',
        method: 'POST',
        params: {
          id_documento:id_documento,  
          texto     : textoObsField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case 1:
            Ext.MessageBox.alert('Confirmación','Su observacion fue grabada correctamente');
            obsDocumentoCreateWindow.close();
            break;
          case 2:
            Ext.MessageBox.alert('Error','Verifique los campos obligatorios.');
            break;
          case 3:
            Ext.MessageBox.alert('Error','no tiene permisos para realizar la operacion solicitada.');
            break;	
          default:
            Ext.MessageBox.alert('Error','Error al grabar por favor reintente o notifique al administrador.');
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
  function isObsFormValid(){	  
        var v1  = textoObsField.isValid();
      return( v1);
  }


    obsDocumentoCreateWindow= new Ext.Window({
        id: 'obsDocumentoCreateWindow',
        title: 'Ingresar observacion al documento',
        closable:true,
        modal:true,
        width: 400,
        height: 200,
        plain:true,
        resizable:false,
        layout: 'fit',
        items: observarCreateForm,
        closeAction: 'close'
    });		
    obsDocumentoCreateWindow.show();

  
 }//fin 