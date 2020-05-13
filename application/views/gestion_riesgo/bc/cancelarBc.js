//console.log('Hola');
function clickBtnCancelarBc (grid,rowIndex,colIndex,item,event){
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
                    go_clickBtnCancelarBc(grid,rowIndex,colIndex,item,event);
                    break;
            }
      },
        failure: function(response) {
            location.assign(URL_BASE_SITIO+"admin");
        }
    });
}
function go_clickBtnCancelarBc (grid, rowIndex)
{
//     var id=grid.getStore().getAt(rowIndex).json.id_Bc;
    var record = grid.getStore().getAt(rowIndex); 
    var id_bc=record.data.id_bc;

    textoCancelarBcField = new Ext.form.TextArea({
        id: 'textoCancelarBcField',
        name:'textoCancelarBcField',
        fieldLabel: 'Indique el motivo por el cual detiene y cancela la BC',
        maxLength: 2000,
        maxLengthText:'supera el máximo de caracteres permitidos',
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        height: 120,
        maskRe: /([a-zñáéíóúüA-ZÑÁÉÍÓÚÜ0-9,¿!¡;:\.\?\-\s]+)$/
    });
    
    cancelarBcCreateForm = new Ext.FormPanel({
        id:'cancelarDoc-form',
        labelAlign: 'top',
//        labelWidth:150,
        bodyStyle:'padding:5px',
        width: 550,        
        items: [textoCancelarBcField],
	buttons: [{
            text: 'Guardar',
            handler: cancelarBc
	},{
            text: 'Cancelar',
            handler: function(){
                cancelarBcCreateForm.destroy();
                cancelarBcCreateWindow.close();
            }
            }]
    });
function cancelarBc (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_cancelarBc();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

    function go_cancelarBc(){
     if(iscancelarBcFormValid()){
         msgProcess('Guardando...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/cancelar',
        method: 'POST',
        params: {
          id        :id_bc,  
          texto     : textoCancelarBcField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case -1:
          case 0:
            Ext.MessageBox.alert('Error','El usuario no tiene permisos para cancelar esta BC');
            break;
          case 1:
            Ext.MessageBox.alert('Operación OK','La BC ha sido cancelada');
            bcDataStore.reload();
            cancelarBcCreateForm.destroy();
            cancelarBcCreateWindow.close();
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
  function iscancelarBcFormValid(){	  
        var v1  = textoCancelarBcField.isValid();
      return( v1);
  }


    cancelarBcCreateWindow= new Ext.Window({
        id: 'cancelarBcCreateWindow',
        title: 'Cancelar BC',
        closable:true,
        modal:true,
        width: 600,
        height: 250,
        plain:true,
        resizable:false,
        layout: 'fit',
        items: cancelarBcCreateForm,
        closeAction: 'close'
    });		
    cancelarBcCreateWindow.show();

 }//fin 
