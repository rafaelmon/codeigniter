// JavaScript Document
function clickBtnHacerObsoleta (grid,rowIndex,colIndex,item,event){
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
                    go_clickBtnHacerObsoleta(grid,rowIndex,colIndex,item,event);
                    break;
            }
      },
        failure: function(response) {
            location.assign(URL_BASE_SITIO+"admin");
        }
    });
}
function go_clickBtnHacerObsoleta (grid, rowIndex)
{
//     var id=grid.getStore().getAt(rowIndex).json.id_Tarea;
    var record = grid.getStore().getAt(rowIndex); 
    var id_tarea=record.data.id_tarea;

    textoHacerObsoletaTareaField = new Ext.form.TextArea({
        id: 'textoHacerObsoletaTareaField',
        name:'texto',
        fieldLabel: 'Descripción el motivo por lo que se hace obsoleta la tarea Nro '+id_tarea,
        maxLength: 1024,
        maxLengthText:'supera el máximo de caracteres permitidos',
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        height: 120,
        maskRe: /([a-zñáéíóúüA-ZÑÁÉÍÓÚÜ0-9,¿!¡;:\.\?\-\s]+)$/
    });
    
    hacerObsoletaTareaCreateForm = new Ext.FormPanel({
        id:'hacerObsoletaDoc-form',
        labelAlign: 'top',
//        labelWidth:150,
        bodyStyle:'padding:5px',
        width: 550,        
        items: [textoHacerObsoletaTareaField],
	buttons: [{
            text: 'Guardar',
            handler: hacerObsoletaTarea
	},{
            text: 'Cancelar',
            handler: function(){
                hacerObsoletaTareaCreateForm.destroy();
                hacerObsoletaTareaCreateWindow.close();
            }
            }]
    });
function hacerObsoletaTarea (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_hacerObsoletaTarea();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

    function go_hacerObsoletaTarea(){
     if(ishacerObsoletaTareaFormValid()){
         msgProcess('Guardando...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/hacer_obsoleta',
        method: 'POST',
        params: {
          id        :id_tarea,  
          texto     : textoHacerObsoletaTareaField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case -1:
          case 0:
            Ext.MessageBox.alert('Error','El usuario no tiene permisos para realizar la accion');
            break;
          case 1:
            Ext.MessageBox.alert('Operación OK','La Tarea se ha pasado a obsoleta');
            tareasDataStore.reload();
            hacerObsoletaTareaCreateWindow.close();
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
  function ishacerObsoletaTareaFormValid(){	  
        var v1  = textoHacerObsoletaTareaField.isValid();
      return( v1);
  }


    hacerObsoletaTareaCreateWindow= new Ext.Window({
        id: 'hacerObsoletaTareaCreateWindow',
        title: 'Hacer obsoleta la tarea',
        closable:true,
        modal:true,
        width: 600,
        height: 250,
        plain:true,
        resizable:false,
        layout: 'fit',
        items: hacerObsoletaTareaCreateForm,
        closeAction: 'close'
    });		
    hacerObsoletaTareaCreateWindow.show();

  
 }//fin 