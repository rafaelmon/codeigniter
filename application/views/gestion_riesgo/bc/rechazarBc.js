//console.log('Hola');
function clickBtnRechazarBc (grid,rowIndex,colIndex,item,event){
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
                    go_clickBtnRechazarBc(grid,rowIndex,colIndex,item,event);
                    break;
            }
      },
        failure: function(response) {
            location.assign(URL_BASE_SITIO+"admin");
        }
    });
}
function go_clickBtnRechazarBc (grid, rowIndex)
{
//     var id=grid.getStore().getAt(rowIndex).json.id_Bc;
    var record = grid.getStore().getAt(rowIndex); 
    var id_bc=record.data.id_bc;

    textoRechazarBcField = new Ext.form.TextArea({
        id: 'textoRechazarBcField',
        name:'textoRechazarBcField',
        fieldLabel: 'Indique el motivo por el cual rechaza la BC',
        maxLength: 2000,
        maxLengthText:'supera el máximo de caracteres permitidos',
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        height: 120,
        maskRe: /([a-zñáéíóúüA-ZÑÁÉÍÓÚÜ0-9,¿!¡;:\.\?\-\s]+)$/
    });
    
    rechazarBcCreateForm = new Ext.FormPanel({
        id:'rechazarDoc-form',
        labelAlign: 'top',
//        labelWidth:150,
        bodyStyle:'padding:5px',
        width: 550,        
        items: [textoRechazarBcField],
	buttons: [{
            text: 'Guardar',
            handler: rechazarBc
	},{
            text: 'Cancelar',
            handler: function(){
                rechazarBcCreateForm.destroy();
                rechazarBcCreateWindow.close();
            }
            }]
    });
function rechazarBc (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_rechazarBc();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

    function go_rechazarBc(){
     if(isrechazarBcFormValid()){
         msgProcess('Guardando...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/rechazar',
        method: 'POST',
        params: {
          id        :id_bc,  
          texto     : textoRechazarBcField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case -1:
          case 0:
            Ext.MessageBox.alert('Error','El usuario no tiene permisos para rechazar esta BC');
            break;
          case 1:
            Ext.MessageBox.alert('Operación OK','La BC ha sido rechazada');
            bcDataStore.reload();
            rechazarBcCreateForm.destroy();
            rechazarBcCreateWindow.close();
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
  function isrechazarBcFormValid(){	  
        var v1  = textoRechazarBcField.isValid();
      return( v1);
  }


    rechazarBcCreateWindow= new Ext.Window({
        id: 'rechazarBcCreateWindow',
        title: 'Rechazar BC',
        closable:true,
        modal:true,
        width: 600,
        height: 250,
        plain:true,
        resizable:false,
        layout: 'fit',
        items: rechazarBcCreateForm,
        closeAction: 'close'
    });		
    rechazarBcCreateWindow.show();

 }//fin 
