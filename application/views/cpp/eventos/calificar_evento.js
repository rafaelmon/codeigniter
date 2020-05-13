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

function clickBtnCalificarEvento (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnCalificarEvento(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnCalificarEvento (grid, rowIndex)
{
    function calificarEvento(){
        if(isModuloFormValid())
        {
            var record = grid.getStore().getAt(rowIndex);
            var evento=record.data.id;
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/insert_cons', 
                params: { 
                   evento             : evento,
                   consecuencia       : consecuenciasCombo.getValue(),
                   descripcion        : descripcionField.getValue(),
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    if(result.success){
                        cppEventosDataStore.reload();
                        consecuenciasDataStore.reload();
                        calificarEventoCreateWindow.close();
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
          var v1 = consecuenciasCombo.isValid();
          var v2  = descripcionField.isValid();
        return( v1 && v2 );
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          consecuenciasCombo.setValue('');
          descripcionField.setValue('');
    }
consecuenciasDS = new Ext.data.Store({
    id: 'consecuenciasDS',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/combo_consecuencias', 
        method: 'POST'
    }),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total'
        },[
        {name: 'id_consecuencia', type: 'int'},        
        {name: 'consecuencia', type: 'string'},
    ])
});
consecuenciasCombo = new Ext.form.ComboBox({
    id:'consecuenciasCombo',
    allQuery:'',
    fieldLabel: 'Consecuencia',
    store: consecuenciasDS,
    editable : true,
    forceSelection : true,
    displayField: 'consecuencia',
    allowBlank: false,
    blankText:'campo requerido',
    valueField: 'id_consecuencia',
    anchor:'95%',
    tabIndex:1,
    pageSize:15,
    triggerAction: 'all',
    width: 300
});

    descripcionField = new Ext.form.TextArea({
      id: 'descripcionField',
      fieldLabel: 'Descripción',
      maxLength: 2048,
      allowBlank: true,
      tabIndex:3,
      height : 200,
      anchor : '95%'
    });
    
    calificarEventoCreateForm = new Ext.FormPanel({
        id:'calificarEventoCreateForm',
        labelAlign: 'left',
        labelWidth:110,
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
                    items: [
                         consecuenciasCombo
                        ,descripcionField
                    ]
                }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: calificarEvento
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                calificarEventoCreateWindow.close();
            }
            }]
    });
	
 
    calificarEventoCreateWindow= new Ext.Window({
        id: 'calificarEventoCreateWindow',
        title: 'Asignar consecuencias',
        closable:false,
        modal:true,
        width: 650,
        height: 400,
        plain:true,
        layout: 'fit',
        items: calificarEventoCreateForm,
        closeAction: 'close'
    });		
    calificarEventoCreateWindow.show();
    
 }//fin 