// JavaScript Document

function clickBtnRechazar (grid, rowIndex)
{
//     var id=grid.getStore().getAt(rowIndex).json.id_documento;
    var record = grid.getStore().getAt(rowIndex); 
    var id_documento=record.data.id_documento;

    textorechazarField = new Ext.form.TextArea({
        id: 'textorechazarField',
        name:'texto',
        fieldLabel: 'Motivo por el que lo rechaza',
        maxLength: 4048,
        maxLengthText:'supera el máximo de caracteres permitidos',
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        height: 140,
        maskRe: /([a-zñáéíóúüA-ZÑÁÉÍÓÚÜ0-9,¿!¡;:\.\?\-\s]+)$/
    });
    
    rechazarCreateForm = new Ext.FormPanel({
        id:'rechazarDoc-form',
        labelAlign: 'left',
//        labelWidth:150,
        bodyStyle:'padding:5px',
        width: 550,        
        items: [textorechazarField],
	buttons: [{
            text: 'Guardar',
            handler: rechazarDocumento
	},{
            text: 'Cancelar',
            handler: function(){
                rechazarCreateForm.destroy();
                rechazarDocumentoCreateWindow.close();
            }
            }]
    });
    function rechazarDocumento(){
     if(isrechazarFormValid()){
        var codigo=grid.getStore().getAt(rowIndex).json.codigo;
        var txt ='¿Confirma que rechaza el documento código '+codigo+'? Será devuelto al editor...';
        Ext.MessageBox.confirm('Confirmar',txt, function(btn, text){
            if(btn=='yes'){ 
                msgProcess('Guardando...');
                Ext.Ajax.request({   
                waitMsg: 'Por favor espere...',
                url: CARPETA+'/rechazar',
                method: 'POST',
                params: {
                  id:id_documento,  
                  texto     : textorechazarField.getValue()
                }, 
                success: function(response){              
                  var result=eval(response.responseText);
                  switch(result.success){
                  case 'true':
                  case true:
                    Ext.MessageBox.alert('Confirmación','El documento ha sido rechazado y devuelto al editor');
                    workflowDataStore.reload();
                    rechazarDocumentoCreateWindow.close();
                    break;
                  case 'false':
                  case false:
                    Ext.MessageBox.alert('Error',result.error);
                    break;
                   default:
                    Ext.MessageBox.alert('Error','Error inesperado, por favor comunique a sistemas!');         
                    break;
                  }        
                },
                failure: function(response){
                  var result=response.responseText;
                  Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
                }                      
                });
            }
        });
    } else {
      Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
    }
  }
  // check if the form is valid
  function isrechazarFormValid(){	  
        var v1  = textorechazarField.isValid();
      return( v1);
  }


    rechazarDocumentoCreateWindow= new Ext.Window({
        id: 'rechazarDocumentoCreateWindow',
        title: 'Rechazar documento',
        closable:true,
        modal:true,
        width: 600,
        height: 300,
        plain:true,
        resizable:false,
        layout: 'fit',
        items: rechazarCreateForm,
        closeAction: 'close'
    });		
    rechazarDocumentoCreateWindow.show();

  
 }//fin 