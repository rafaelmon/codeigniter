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

function clickBtnsetCriticidadEvento (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnsetCriticidadEvento(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnsetCriticidadEvento (grid, rowIndex)
{
    function setCriticidadEvento(){
        if(isModuloFormValid())
        {
            var record = grid.getStore().getAt(rowIndex);
            var id = record.data.id;
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/set_criticidad', 
                params: { 
                   id_evento            : id,
                   id_criticidad        : criticidadEventoRadios.getValue().inputValue,
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    if(result.success){
                        cppEventosDataStore.reload();
                        setCriticidadEventoCreateWindow.close();
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
          var v1 = criticidadEventoRadios.isValid();
        return( v1 );
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          criticidadEventoRadios.setValue('');
    }

    criticidadEventoRadios = new Ext.form.RadioGroup({ 
        id:'criticidadEventoRadios',
        fieldLabel: 'Criticidad',
        tabIndex:1,
        columns: 1,
        anchor : '95%',
        autoWidth: true,
//        boxMaxWidth:100,
        allowBlank: false,
        blankText:'Debe seleccionar una opción',
        items: [ 
            {boxLabel: '&nbsp;Critica&nbsp;',  name: 'rgCriticidad', inputValue: '1'},
            {boxLabel: '&nbsp;Alta&nbsp;',     name: 'rgCriticidad', inputValue: '2'},  
            {boxLabel: '&nbsp;Menor&nbsp;',    name: 'rgCriticidad', inputValue: '3'}
            
            
        ] 
    });
    
    setCriticidadEventoCreateForm = new Ext.FormPanel({
        id:'setCriticidadEventoCreateForm',
        labelAlign: 'left',
        labelWidth:80,
        bodyStyle:'padding:5px',
        width: 600,        
        items: [{
                id:'fieldset_form',
                layout:'column',
                border:false,
                items:[{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [criticidadEventoRadios]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: setCriticidadEvento
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                setCriticidadEventoCreateWindow.close();
            }
            }]
    });
	
 
    setCriticidadEventoCreateWindow= new Ext.Window({
        id: 'setCriticidadEventoCreateWindow',
        title: 'Definir criticidad',
        closable:false,
        modal:true,
        width: 300,
        height: 200,
        plain:true,
        layout: 'fit',
        items: setCriticidadEventoCreateForm,
        closeAction: 'close'
    });		
    setCriticidadEventoCreateWindow.show();
    
 }//fin 