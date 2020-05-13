// JavaScript Document
function msgProcess(titulo){
 Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:400, 
        wait:true, 
        waitConfig: {interval:200}
    });
}

function clickBtnMonto (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnMonto(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnMonto (grid, rowIndex)
{
    function monto(){
        if(isModuloFormValid())
        {
            var record = grid.getStore().getAt(rowIndex);
            var id_ec=record.data.id_ec;
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/update_monto', 
                params: { 
                   id_ec                : id_ec,
                   monto                : montoField.getValue()
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    if(result.success){
                        cppEventosDataStore.reload();
                        consecuenciasDataStore.reload();
                        montoCreateWindow.close();
                        Ext.MessageBox.alert('OK',result.msg);
                    }
                    else
                        Ext.MessageBox.alert('Error',result.msg);
                },
                failure: function(response){
                    var result=eval(response.responseText);
                    Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
                }
           });

       } else {
         Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
       }
     }

  
    // check if the form is valid
    function isModuloFormValid(){	  
          var v1 = montoField.isValid();
        return( v1 );
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          montoField.setValue('');
    }

    montoField = new Ext.form.NumberField({
      id: 'montoField',
      fieldLabel: 'Monto (U$S)',
      maxLength: 30,
      allowBlank: false,
      anchor : '80%'
    });
    
    montoCreateForm = new Ext.FormPanel({
        id:'montoCreateForm',
        labelAlign: 'left',
        labelWidth:150,
        bodyStyle:'padding:5px',
        width: 400,        
        items: [{
                id:'fieldset_form',
                layout:'column',
                border:false,
                items:[{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [montoField]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: monto
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                montoCreateWindow.close();
            }
            }]
    });
	
 
    montoCreateWindow= new Ext.Window({
        id: 'montoCreateWindow',
        title: 'Definir monto perdido',
        closable:false,
        modal:true,
        width: 400,
        height: 120,
        plain:true,
        layout: 'fit',
        items: montoCreateForm,
        closeAction: 'close'
    });		
    montoCreateWindow.show();
    
 }//fin 