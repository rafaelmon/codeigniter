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

function clickBtnNuevaCausa (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnNuevaCausa(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevaCausa (grid,rowIndex,colIndex,item,event)
{
    function nuevaCausa(){
        if(isModuloFormValid())
        {
            var record = grid.getStore().getAt(rowIndex);
            var evento = record.data.id;
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/insert_causa', 
                params: { 
                    id_evento         : evento,
                    id_ac             : areasCausanteCombo.getValue(),
                    causa_inmediata   : causaInmediataField.getValue(),
                    causa_raiz        : causaRaizField.getValue()
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    if(result.success){
                        cppEventosDataStore.reload();
                        cppCausasDataStore.reload();    
                        nuevaCausaCreateWindow.close();
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
          var v1  = areasCausanteCombo.isValid();
          var v2  = causaInmediataField.isValid();
          var v3  = causaRaizField.isValid();
        return( v1 && v2 && v3);
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          areasCausanteCombo.setValue('');
          causaInmediataField.setValue('');
          causaRaizField.setValue('');
    }
areasCausanteDS = new Ext.data.Store({
    id: 'areasCausanteDS',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/combo_areas_causante', 
        method: 'POST'
    }),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total'
        },[
        {name: 'id_ac', type: 'int'},        
        {name: 'ac',    type: 'string'},
    ])
});
areasCausanteCombo = new Ext.form.ComboBox({
    id:'areasCausanteCombo',
    allQuery:'',
    fieldLabel: 'Area Causante',
    store: areasCausanteDS,
    editable : true,
    forceSelection : true,
    displayField: 'ac',
    allowBlank: false,
    blankText:'campo requerido',
    valueField: 'id_ac',
    anchor:'95%',
    tabIndex:1,
    pageSize:15,
    triggerAction: 'all',
    width: 300
});

causaInmediataField = new Ext.form.TextArea({
    id: 'causaInmediataField',
    fieldLabel: 'Causa inmediata',
    maxLength: 2048,
    allowBlank: true,
    tabIndex:2,
    height : 150,
    anchor : '95%'
});
causaRaizField = new Ext.form.TextArea({
    id: 'causaRaizField',
    fieldLabel: 'Causa raíz',
    maxLength: 2048,
    allowBlank: true,
    tabIndex: 3,
    height : 150,
    anchor : '95%'
});
    
nuevaCausaCreateForm = new Ext.FormPanel({
    id:'nuevaCausaCreateForm',
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
                items: [areasCausanteCombo,causaInmediataField,causaRaizField]
                }]
    }],
    buttons: [{
        text: 'Guardar',
        handler: nuevaCausa
    },{
        text: 'Cancelar',
        handler: function(){
            // because of the global vars, we can only instantiate one window... so let's just hide it.
            nuevaCausaCreateWindow.close();
        }
        }]
});
	
 
    nuevaCausaCreateWindow= new Ext.Window({
        id: 'nuevaCausaCreateWindow',
        title: 'Nueva Causa',
        closable:false,
        modal:true,
        width: 700,
        height: 500,
        plain:true,
        layout: 'fit',
        items: nuevaCausaCreateForm,
        closeAction: 'close'
    });		
    nuevaCausaCreateWindow.show();
    
 }//fin 