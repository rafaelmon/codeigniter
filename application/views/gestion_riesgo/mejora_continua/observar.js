// JavaScript Document
function clickBtnObservarTarea (grid,rowIndex,colIndex,item,event){
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
                    go_clickBtnObservarTarea(grid,rowIndex,colIndex,item,event);
                    break;
            }
      },
        failure: function(response) {
            location.assign(URL_BASE_SITIO+"admin");
        }
    });
}
function go_clickBtnObservarTarea (grid, rowIndex)
{
//     var id=grid.getStore().getAt(rowIndex).json.id_Tarea;
    var record = grid.getStore().getAt(rowIndex); 
    var id_tarea=record.data.id_tarea;

    textoObservarTareaField = new Ext.form.TextArea({
        id: 'textoObservarTareaField',
        name:'texto',
        fieldLabel: 'Indique el motivo por el cual observa la tarea informada y se la devuelve al responsable',
        maxLength: 1024,
        maxLengthText:'supera el máximo de caracteres permitidos',
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        height: 120,
        maskRe: /([a-zñáéíóúüA-ZÑÁÉÍÓÚÜ0-9,¿!¡;:\.\?\-\s]+)$/
    });
    
    observarTareaCreateForm = new Ext.FormPanel({
        id:'observarDoc-form',
        labelAlign: 'top',
//        labelWidth:150,
        bodyStyle:'padding:5px',
        width: 550,        
        items: [textoObservarTareaField],
	buttons: [{
            text: 'Guardar',
            handler: observarTarea
	},{
            text: 'Cancelar',
            handler: function(){
                observarTareaCreateForm.destroy();
                observarTareaCreateWindow.close();
            }
            }]
    });
function observarTarea (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_observarTarea();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

    function go_observarTarea(){
     if(isobservarTareaFormValid()){
         msgProcess('Guardando...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/observar',
        method: 'POST',
        params: {
          id        :id_tarea,  
          texto     : textoObservarTareaField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case -1:
          case 0:
            Ext.MessageBox.alert('Error','El usuario no tiene permisos para observar esta tarea');
            break;
          case 1:
            Ext.MessageBox.alert('Operación OK','La Tarea ha sido observada');
            tareasDataStore.reload();
            observarTareaCreateWindow.close();
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
  function isobservarTareaFormValid(){	  
        var v1  = textoObservarTareaField.isValid();
      return( v1);
  }


    observarTareaCreateWindow= new Ext.Window({
        id: 'observarTareaCreateWindow',
        title: 'Observar Tarea',
        closable:true,
        modal:true,
        width: 600,
        height: 250,
        plain:true,
        resizable:false,
        layout: 'fit',
        items: observarTareaCreateForm,
        closeAction: 'close'
    });		
    observarTareaCreateWindow.show();

  
 }//fin 