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

function clickBtnEditarRevisionTarea (grid,rowIndex,colIndex,item,event,id){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnEditarRevisionTarea(grid,rowIndex,colIndex,item,event,id);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnEditarRevisionTarea (grid,rowIndex,colIndex,item,event,id)
{
//    console.log(id);
    function editarRevisionTarea(){
        if(isModuloFormValid())
        {
            var record = grid.getStore().getAt(rowIndex);
            var id_ec=record.data.id_ec;
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/update_revision', 
                params: { 
                   id_tarea           : id,
                   rpd                : tareaRevisionRadios.getValue().inputValue
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                        case 1:
                            Ext.MessageBox.alert('Operación OK','Registro agregado correctamente');
                            tareasDataStore.reload();
                            tareaRevisionCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','No tiene los permisos necesarios para realizar esta acción');
                            break;
                        case 4:
                            Ext.MessageBox.alert('Error','No se pudo modificar la tarea.');
                            tareaRevisionCreateWindow.close()
                            break;

                        default:
                            Ext.MessageBox.alert('Error','No se pudo dar de alta la capacitación.');
                            break;
                    }
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
          var v1 = tareaRevisionRadios.isValid();
        return( v1 );
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          tareaRevisionRadios.setValue('');
    }

    tareaRevisionRadios = new Ext.form.RadioGroup({ 
    id:'tareaRevisionRadios',
    fieldLabel: 'Entrada para revisión de la dirección',
    allowBlank: false,
    anchor : '95%',
    tabIndex:3,
    columns: 4,
    items: [ 
            {boxLabel: 'Si', name: 'tarea_revision', inputValue: '1'}, //, checked: true
            {boxLabel: 'No', name: 'tarea_revision', inputValue: '2'}
        ] 
    });
    
    tareaRevisionCreateForm = new Ext.FormPanel({
        id:'tareaRevisionCreateForm',
        labelAlign: 'top',
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
                    items: [tareaRevisionRadios]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: editarRevisionTarea
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                tareaRevisionCreateWindow.close();
            }
            }]
    });
	
 
    tareaRevisionCreateWindow= new Ext.Window({
        id: 'tareaRevisionCreateWindow',
        title: 'Editar revisión',
        closable:false,
        modal:true,
        width: 400,
        height: 150,
        plain:true,
        layout: 'fit',
        items: tareaRevisionCreateForm,
        closeAction: 'close'
    });		
    
    if (id > 0)
    {
        tareaRevisionCreateWindow.setTitle('Editar revisión de evento Nro '+id);
        
        var formPanel=Ext.getCmp('tareaRevisionCreateForm');
        formPanel.getForm().load({
            waitMsg: "Cargando...",
            url: CARPETA+'/dato_revision',
            params: {id: id},
            success:function(response){
            },
            failure:function(response){
                   Ext.MessageBox.alert('Error','No se pudo cargar el objetivo');
                   tareaRevisionCreateWindow.close();

            }
        });
    }
    tareaRevisionCreateWindow.show();
    
 }//fin 