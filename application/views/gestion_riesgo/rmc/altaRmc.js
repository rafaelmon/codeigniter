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

function createRmc (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createRmc();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

var validar = 0;
  function go_createRmc(){
     if(isRmcFormValid()){
        msgProcess('Guardando Reporte');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert',
            method: 'POST',
            params: {
                desc                : descAltaRmcTextfield.getValue(),
                sector              : sectorAltaRmcCombo.getValue(),
                observacion_sector  : obserbacionSectoresField.getValue(),
                validar_observacion : validar
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 0:
                            Ext.MessageBox.alert('Error','Reporte repetido...');
                        case 1:
                            Ext.MessageBox.alert('Alta OK','Reporte creado satisfactoriamente.');
                            rmcDataStore.reload();
                            rmcAltaCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','El Registro no se pudo guardar en la Base de datos');
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo crear el Reporte.');
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
function isRmcFormValid()
{
    var v1 = descAltaRmcTextfield.isValid();
    var v2 = sectorAltaRmcCombo.isValid();
    if (validar == 1)
    {
        var v3 = obserbacionSectoresField.isValid();
        return ( v1 && v2 && v3);
    }
    else
        return (v1 && v2);
}
  
  function rmcAltaDisplayFormWindow (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_rmcAltaDisplayFormWindow();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
   
  // display or bring forth the form
  function go_rmcAltaDisplayFormWindow(){
    
	 if(rmcAltaCreateForm){
	 	if(rmcAltaCreateForm.findById('fieldsetid')) {
		 //get the fieldset
	
		 var oldfieldset = rmcAltaCreateForm.findById('fieldset_form');
		 
			//iterate trough each of the component in the fieldset
			oldfieldset.items.each(function(collection,item,length){
				var i = item;
				//destroy the object within the fieldset
				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
			});
		}
		rmcAltaCreateForm.destroy();
		rmcAltaCreateWindow.destroy()
	}
		

  // reset the Form before opening it
  function resetPresidentForm(){
    descAltaRmcTextfield.setValue('');
    sectorAltaRmcCombo.setValue('');
  }	

descAltaRmcTextfield = new Ext.form.TextArea({
    id: 'hallazgoField',
    fieldLabel: 'Describa el hecho o evento',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    height :170,
    maskRe: /([a-zñáéíóúüA-ZÑÁÉÍÓÚÜ0-9,@+*¨¿!¡;:=°\|\.\$\%\&\/\?\(\)\{\}\[\]\>\~\-\s]+)$/,
    tabIndex: 3
});

//descAltaRmcTextfield = new Ext.form.HtmlEditor({
//    id: 'hallazgoField',
//    fieldLabel: 'Describa el hecho o evento',
//    maxLength: 2000,
//    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
//    allowBlank: false,
//    blankText:'campo requerido',
//    anchor : '95%',
//    height :200,
//    tabIndex: 2,
//    enableFormat : false,
//    enableFontSize : false,
////    enableColors: false,
//    enableAlignments: false,
//    enableLists: false,
////    enableSourceEdit : false,
//    enableLinks : false,
//    enableFont : false,
//    createLinkText:'Ingrese la URL',
//    enableFont: false
//});

    empresasAltaRmcDS = new Ext.data.Store({
        id: 'empresasAltaRmcDS',
        proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/empresas_combo', 
        method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_empresa', type: 'int'},        
            {name: 'empresa', type: 'string'},
        ])
    });
    
    empresaAltaRmcCombo = new Ext.form.ComboBox({
            id:'empresaAltaRmcCombo',
            forceSelection : false,
            fieldLabel: 'Empresa',
            store: empresasAltaRmcDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'empresa',
            valueField: 'id_empresa',
            anchor:'95%',
            triggerAction: 'all',
            width: 300,
            tabIndex: 3
    });
    
    
    sectorAltaRmcDS = new Ext.data.Store({
        id: 'sectorAltaRmcDS',
        proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/sectores_combo', 
        method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_sector',             type: 'int'},        
            {name: 'sector',                type: 'string'},
            {name: 'validar_observacion',   type: 'int'}
        ])
    });
    sectorAltaRmcDS.load();
    
    sectorAltaRmcCombo = new Ext.form.ComboBox({
            id:'sectorAltaRmcCombo',
            forceSelection : false,
            hidden:false,
//            disabled:true,
            fieldLabel: 'Sector',
            store: sectorAltaRmcDS,
//            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'sector',
            valueField: 'id_sector',
            //tpl: '<tpl for="."><div class="x-combo-list-item">{validar_observacion}</div></tpl>',
            anchor:'95%',
            hiddenValue: 'validar_observacion',
            triggerAction: 'all',
            width: 300,
            tabIndex: 1
    });
    sectorAltaRmcCombo.on('select', selectSector);
    
    obserbacionSectoresField = new Ext.form.TextField({
        id: 'obserbacionSectoresField',
        name: 'obserbacionSectoresField',
        fieldLabel: 'Observacion sectores',
        allowBlank: false,
        blankText:'campo requerido',
        disabled:false,
        tabIndex:2,
        anchor : '95%'
    });
       
    sectorAltaRmcFieldSet = new Ext.form.FieldSet({
        id:'sectorAltaRmcFieldSet',
        title : 'Sector involucrado',
        anchor : '95%',
        growMin:100,
        items:[sectorAltaRmcCombo,obserbacionSectoresField]
    }); 


  
 
   rmcAltaCreateForm = new Ext.FormPanel({
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
                    items: [sectorAltaRmcFieldSet,descAltaRmcTextfield]
                    }
                ]
                }
        ],
		buttons: [{
		  text: 'Guardar',
		  handler: createRmc
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			rmcAltaCreateWindow.close();
		  }
		}]
    });
	
 
    rmcAltaCreateWindow= new Ext.Window({
        id: 'rmcAltaCreateWindow',
        title: 'Crear nuevo Reporte',
        closable:false,
        modal:true,
        width: 700,
        height: 470,
        plain:true,
        layout: 'fit',
        items: rmcAltaCreateForm,
        closeAction: 'close'
    });		


    rmcAltaCreateWindow.show();
    
    function selectSector( combo, record, index ){
        validar = record.data.validar_observacion;
    }
}