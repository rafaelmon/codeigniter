// JavaScript Document
function clickBtnNoAplica (grid,rowIndex,colIndex,item,event){
    Ext.Ajax.request({ 
        url: LINK_GENERICO+'/sesion',
        method: 'POST',
        success: function(response, opts) {
            var result=parseInt(response.responseText);
            switch (result)
            {
                case 0:
                case '0':
                    location.assign(URL_BASE_SITIO+"admin");
                    break;
                case 1:
                case '1':
                    go_clickBtnRechazarTarea(grid,rowIndex,colIndex,item,event);
                    break;
            }
      },
        failure: function(response) {
            location.assign(URL_BASE_SITIO+"admin");
        }
    });
}
function go_clickBtnRechazarTarea (grid, rowIndex)
{
//     var id=grid.getStore().getAt(rowIndex).json.id_Tarea;
    var record = grid.getStore().getAt(rowIndex); 
    var id_tarea=record.data.id_tarea;

    textoRechazarTareaField = new Ext.form.TextArea({
        id: 'textoRechazarTareaField',
        name:'texto',
        fieldLabel: 'Indique el motivo por el cual no aplica la bajada en cascada',
        maxLength: 1024,
        maxLengthText:'supera el máximo de caracteres permitidos',
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        height: 120,
        maskRe: /([a-zñáéíóúüA-ZÑÁÉÍÓÚÜ0-9,¿!¡;:\.\?\-\s]+)$/
    });
    
    rechazarTareaCreateForm = new Ext.FormPanel({
        id:'rechazarDoc-form',
        labelAlign: 'top',
//        labelWidth:150,
        bodyStyle:'padding:5px',
        width: 550,        
        items: [textoRechazarTareaField],
	buttons: [{
            text: 'Guardar',
            handler: rechazarTarea
	},{
            text: 'Cancelar',
            handler: function(){
                rechazarTareaCreateForm.destroy();
                rechazarTareaCreateWindow.close();
            }
            }]
    });
function rechazarTarea (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_rechazarTarea();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

    function go_rechazarTarea(){
     if(isrechazarTareaFormValid()){
         msgProcess('Guardando...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/rechazar',
        method: 'POST',
        params: {
          id        :id_tarea,  
          texto     : textoRechazarTareaField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case -1:
          case 0:
            Ext.MessageBox.alert('Error','El usuario no tiene permisos para rechazar esta tarea');
            break;
          case 1:
            Ext.MessageBox.alert('Operación OK','La Tarea ha sido modificada');
            tareasDataStore.reload();
            rechazarTareaCreateWindow.close();
            break;
          case 3:
            Ext.MessageBox.alert('Error','Operación no permitida');
            break;
           default:
            Ext.MessageBox.alert('Error','No se ha podido realizar la accion, por favor reintente');
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
  function isrechazarTareaFormValid(){	  
        var v1  = textoRechazarTareaField.isValid();
      return( v1);
  }


    rechazarTareaCreateWindow= new Ext.Window({
        id: 'rechazarTareaCreateWindow',
        title: 'Rechazar Tarea',
        closable:true,
        modal:true,
        width: 600,
        height: 250,
        plain:true,
        resizable:false,
        layout: 'fit',
        items: rechazarTareaCreateForm,
        closeAction: 'close'
    });		
    rechazarTareaCreateWindow.show();

  
 }//fin 