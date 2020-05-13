// JavaScript Document
function clickBtnNuevoProducto (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnNuevoProducto(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevoProducto (grid, rowIndex)
{
    function altaProducto(){
        if(isModuloFormValid())
        {
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/insert', 
                params: { 
                   producto             : productoField.getValue(),
                   descripcion          : descripcionField.getValue()
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                        case 1:
                            Ext.MessageBox.alert('Operación OK','Registro agregado correctamente');
                            productosDataStore.reload();
                            altaProductoCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo dar de alta el cliente.');
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
          var v1  = productoField.isValid();
          var v2  = descripcionField.isValid();
        return( v1 && v2);
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          productoField.setValue('');
          descripcionField.setValue('');
    }	

    productoField = new Ext.form.TextField({
      id: 'productoField',
      fieldLabel: 'Producto',
      maxLength: 30,
      allowBlank: false,
      anchor : '80%'
    });
    descripcionField = new Ext.form.TextArea({
      id: 'descripcionField',
      fieldLabel: 'Descripción',
      maxLength: 1028,
      allowBlank: true,
      anchor : '80%'
    });
    
    altaProductoCreateForm = new Ext.FormPanel({
        id:'altaProductoCreateForm',
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
                    items: [productoField,descripcionField]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: altaProducto
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                altaProductoCreateWindow.close();
            }
            }]
    });
	
 
    altaProductoCreateWindow= new Ext.Window({
        id: 'altaProductoCreateWindow',
        title: 'Alta producto',
        closable:false,
        modal:true,
        width: 600,
        height: 200,
        plain:true,
        layout: 'fit',
        items: altaProductoCreateForm,
        closeAction: 'close'
    });		
    altaProductoCreateWindow.show();
    
 }//fin 