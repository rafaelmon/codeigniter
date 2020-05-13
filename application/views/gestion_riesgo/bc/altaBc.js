function msgProcess(titulo){
 Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:300, 
        wait:true, 
        waitConfig: {interval:200}
    });
}

function createBc (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createBc();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_createBc(){
     if(isBcFormValid()){
        msgProcess('Guardando RMC');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert',
            method: 'POST',
            params: {
                usuario  : usuarioInicioBcCombo.getValue(),
                desc     : descAltaBcTextfield.getValue()
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 0:
                            Ext.MessageBox.alert('Error','BC repetido...');
                        case 1:
                            Ext.MessageBox.alert('Alta OK','BC creada satisfactoriamente.');
                            bcDataStore.reload();
                            bcAltaCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','El Registro no se pudo guardar en la Base de datos');
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo crear el registro');
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
  function isBcFormValid(){	  
	  var v1 = usuarioInicioBcCombo.isValid();
	  var v2 = descAltaBcTextfield.isValid();
	  return( v1 && v2);
  }
  
  function bcAltaDisplayFormWindow (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_bcAltaDisplayFormWindow();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
   
  // display or bring forth the form
  function go_bcAltaDisplayFormWindow(){
	 if(bcAltaCreateForm){
	 	if(bcAltaCreateForm.findById('fieldsetid')) {
		 //get the fieldset
	
		 var oldfieldset = bcAltaCreateForm.findById('fieldset_form');
		 
			//iterate trough each of the component in the fieldset
			oldfieldset.items.each(function(collection,item,length){
				var i = item;
				//destroy the object within the fieldset
				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
			});
		}
		bcAltaCreateForm.destroy();
		bcAltaCreateWindow.destroy()
	}
		

  // reset the Form before opening it
  function resetPresidentForm(){
    usuarioInicioBcCombo.setValue('');
    descAltaBcTextfield.setValue('');
  }	

usuarioInicioBcDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_usuarios',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_usuario'
        }, [
            {name: 'id_usuario', mapping: 'id_usuario'},
            {name: 'nomape', mapping: 'nomape'},
            {name: 'usuario', mapping: 'usuario'},
            {name: 'puesto', mapping: 'puesto'},
        ])
    });

    // Custom rendering Template
    var usuarioInicioBcTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
             '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
usuarioInicioBcCombo = new Ext.form.ComboBox({
        id:'usuarioInicioBcCombo',
        store: usuarioInicioBcDS,
        blankText:'campo requerido',
        invalidText:'error',
        allowBlank: false,
        fieldLabel: 'Usuario donde se debe iniciar la Bajada en Cascada',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '95%',
        minChars:3,
    //        labelStyle: 'font-weight:bold;',
        pageSize:10,
        tabIndex: 11,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: usuarioInicioBcTpl,
        itemSelector: 'div.search-item',
        hidden:false,
        disabled:false
    });
    
  descAltaBcTextfield = new Ext.form.TextArea({
    id: 'descAltaBcTextfield',
    fieldLabel: 'Descripción',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    height :170,
    maskRe: /([a-zñáéíóúüA-ZÑÁÉÍÓÚÜ0-9,@+*¨¿!¡;:=°\|\.\$\%\&\/\?\(\)\{\}\[\]\>\~\-\s]+)$/,
    tabIndex: 3
});
 
   bcAltaCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        labelWidth:110,
        bodyStyle:'padding:5px',
//        width:500,        
        items: [{
                layout:'column',
                border:false,
                items:[{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [usuarioInicioBcCombo,descAltaBcTextfield]
                    }
                ]
                }
        ],
		buttons: [{
		  text: 'Guardar',
		  handler: createBc
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			bcAltaCreateWindow.close();
		  }
		}]
    });
	
 
    bcAltaCreateWindow= new Ext.Window({
        id: 'bcAltaCreateWindow',
        title: 'Crear nueva  Bajada en Cascada',
        closable:false,
        modal:true,
        width: 500,
        height: 350,
        plain:true,
        layout: 'fit',
        items: bcAltaCreateForm,
        closeAction: 'close'
    });		


    bcAltaCreateWindow.show();
    
}