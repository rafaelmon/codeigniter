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

function clickBtnToneladasPerdidas (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnToneladasPerdidas(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnToneladasPerdidas (grid, rowIndex)
{
    function toneladasPerdidas(){
        if(isModuloFormValid())
        {
            var record = grid.getStore().getAt(rowIndex);
            var id_ec=record.data.id_ec;
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/update_toneladas', 
                params: { 
                   id_ec                : id_ec,
                   unidades_perdidas    : toneladasField.getValue()
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    if(result.success){
                        cppEventosDataStore.reload();
                        consecuenciasDataStore.reload();
                        toneladasPerdidasCreateWindow.close();
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
          var v1 = toneladasField.isValid();
        return( v1 );
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          toneladasField.setValue('');
    }

    toneladasField = new Ext.form.NumberField({
      id: 'toneladasField',
      fieldLabel: 'Toneladas',
      allowDecimals : true,
      maxLength: 30,
      allowBlank: false,
      anchor : '80%'
    });
    
    toneladasPerdidasCreateForm = new Ext.FormPanel({
        id:'toneladasPerdidasCreateForm',
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
                    items: [toneladasField]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: toneladasPerdidas
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                toneladasPerdidasCreateWindow.close();
            }
            }]
    });
	
 
    toneladasPerdidasCreateWindow= new Ext.Window({
        id: 'toneladasPerdidasCreateWindow',
        title: 'Definir toneladas perdidas',
        closable:false,
        modal:true,
        width: 400,
        height: 120,
        plain:true,
        layout: 'fit',
        items: toneladasPerdidasCreateForm,
        closeAction: 'close'
    });		
    toneladasPerdidasCreateWindow.show();
    
 }//fin 