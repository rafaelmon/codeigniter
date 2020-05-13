//cerrar ED de usuario y grabar observacion
function clickBtnCerrarEdConObs (grid, rowIndex,colIndex,item ,event)
{
 function msgProcess(titulo){
 Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:300, 
        wait:true, 
        waitConfig: {interval:200}
    });
}
//     var id=grid.getStore().getAt(rowIndex).json.id_Tarea;
    var record = grid.getStore().getAt(rowIndex); 
    var id_ed=record.data.id_evaluacion;
    var tipo=grid.getStore().getAt(rowIndex).json.tipo;
    textoCerrarEdConObsField = new Ext.form.TextArea({
        id: 'textoCerrarEdConObsField',
        name:'texto',
        fieldLabel: 'Comentario final de mi evaluación de desempeño',
        maxLength: 2048,
        maxLengthText:'supera el máximo de caracteres permitidos',
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        height: 120,
        maskRe: /([a-zñáéíóúüA-ZÑÁÉÍÓÚÜ0-9,¿!¡;:\.\?\-\s]+)$/
    });
    
    cerrarEdConObsCreateForm = new Ext.FormPanel({
        id:'rechazarDoc-form',
        labelAlign: 'top',
//        labelWidth:150,
        bodyStyle:'padding:5px',
        width: 550,        
        items: [textoCerrarEdConObsField],
	buttons: [{
            text: 'Guardar',
            handler: cerrarEdConObs
	},{
            text: 'Cancelar',
            handler: function(){
                cerrarEdConObsCreateForm.destroy();
                cerrarEdConObsCreateWindow.close();
            }
            }]
    });

    function cerrarEdConObs(){
     sesionControl();
     if(iscerrarEdConObsFormValid()){
        msgProcess('Guardando...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/cerrar',
        method: 'POST',
        params: {
          id        :id_ed, 
          tipo      : tipo,
          txt       : textoCerrarEdConObsField.getValue()
        }, 
        success: function(response){   
            var result=eval(response.responseText);
                 switch(result.success){
                 case true:
                    Ext.MessageBox.alert('OK',result.msg);
                    misEeddDataStore.commitChanges();
                    misEeddDataStore.reload();
                    cerrarEdConObsCreateWindow.close();
                    break;
                 case false:
                    Ext.MessageBox.alert('Error',result.error);
//                  misEeddDataStore.commitChanges();
//                  misEeddDataStore.reload();
                    break;
                 default:
                    Ext.MessageBox.alert('Error','Error inesperado, por favor informe al área de IT');
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
  function iscerrarEdConObsFormValid(){	  
        var v1  = textoCerrarEdConObsField.isValid();
      return( v1);
  }


    cerrarEdConObsCreateWindow= new Ext.Window({
        id: 'cerrarEdConObsCreateWindow',
        title: 'Guardar observación y cerrar evaluación de desemeño',
        closable:true,
        modal:true,
        width: 600,
        height: 250,
        plain:true,
        resizable:false,
        layout: 'fit',
        items: cerrarEdConObsCreateForm,
        closeAction: 'close'
    });		
    cerrarEdConObsCreateWindow.show();

  
 }//fin 
 