// JavaScript Document
function clickBtnNuevoSector (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnNuevoSector(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevoSector (grid, rowIndex)
{
    function altaSector(){
        if(isModuloFormValid())
        {
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/insert', 
                params: { 
                   sector             : sectorField.getValue(),
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                        case 1:
                            Ext.MessageBox.alert('Operación OK','Registro agregado correctamente');
                            sectoresDataStore.reload();
                            altaSectorCreateWindow.close();
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
          var v1  = sectorField.isValid();
        return( v1 );
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          sectorField.setValue('');
    }	

    sectorField = new Ext.form.TextField({
      id: 'sectorField',
      fieldLabel: 'Sector',
      maxLength: 30,
      allowBlank: false,
      anchor : '80%'
    });
    
    altaSectorCreateForm = new Ext.FormPanel({
        id:'altaSectorCreateForm',
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
                    items: [sectorField]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: altaSector
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                altaSectorCreateWindow.close();
            }
            }]
    });
	
 
    altaSectorCreateWindow= new Ext.Window({
        id: 'altaSectorCreateWindow',
        title: 'Alta sector',
        closable:false,
        modal:true,
        width: 600,
        height: 150,
        plain:true,
        layout: 'fit',
        items: altaSectorCreateForm,
        closeAction: 'close'
    });		
    altaSectorCreateWindow.show();
    
 }//fin 