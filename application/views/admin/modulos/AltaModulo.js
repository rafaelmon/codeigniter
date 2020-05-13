// JavaScript Document

  // inserta usuario en DB
  function createModulo(){
     if(isModuloFormValid()){
	
        var hijosCheck = Ext.getCmp('hijosField');	 
        var menuCheck = Ext.getCmp('menuField');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insertar',
        method: 'POST',
        params: {		  
          modulo  : moduloField.getValue(),
          accion: accionField.getValue(),
          icono : iconoField.getValue(),
          padre_id: padreField.getValue(),
          orden: ordenField.getValue(),
          hijos: hijosCheck.getValue(),
          menu: menuCheck.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
          case 1:
            Ext.MessageBox.alert('Alta OK','El modulo fue creado satisfactoriamente.');
            array_modulos_padres.reload();
            comboPadreData.reload();
            ModulosDataStore.reload();
            ModuloCreateWindow.close();
            break;
          case 2:
            Ext.MessageBox.alert('Error','El campo Titulo y el campo Orden son obligatorios.');
            break;
          case 3:
            Ext.MessageBox.alert('Error','no tiene permisos para realizar la operacion solicitada.');
            break;	
          default:
            Ext.MessageBox.alert('Error','No se pudo crear el modulo.');
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
  function isModuloFormValid(){	  
	  var v1 = moduloField.isValid();
	  var v3 = ordenField.isValid();
      return( v1 && v3 );
  }
   
  // display or bring forth the form
  function displayFormWindow(){		
		
	  
  // reset the Form before opening it
  function resetPresidentForm(){
    moduloField.setValue('');
    accionField.setValue('');
    iconoField.setValue('');
    ordenField.setValue('');
  }	
 
 comboPadreData = new Ext.data.Store({
	id: 'comboPadreData',
	proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/padres', 
                method: 'POST'
            }),
    reader: new Ext.data.JsonReader({
        root: 'data',
        totalProperty: 'num'
      }, [
      	{name: 'id_padre', type: 'int'},        
        {name: 'padre', type: 'string'},
      ])
});

comboPadreData.on('load' , function(  js , records, options ){
											   
	var tRecord = Ext.data.Record.create(
		{name: 'id_padre', type: 'int'},
		{name: 'padre', type: 'string'}
	);
	var myNewT = new tRecord({
		id: 0,
		titulo: 'No tiene padre'
	});
	comboPadreData.insert( 0, myNewT);	
} );
 
    padreField = new Ext.form.ComboBox({
     id:'padreField',
	 forceSelection : false,
     fieldLabel: 'M&oacute;dulo Padre',
     store: comboPadreData,
	 editable : false,
     displayField: 'padre',
     allowBlank: true,
     valueField: 'id_padre',
     anchor:'95%',
     triggerAction: 'all',
	 width: 300,
	 value: 'No tiene padre'
    });
 
 
  
  
  moduloField = new Ext.form.TextField({
    id: 'moduloField',
    fieldLabel: 'T&iacute;tulo',
    maxLength: 60,
    maxLengthText:'El máximo de caracteres es 60',
    allowBlank: false,
    anchor : '95%',
    maskRe: /([a-zA-Z0-9\s]+)$/
      });
   
    
  accionField = new Ext.form.TextField({
    id: 'accionField',
    fieldLabel: 'Acci&oacute;n',
    maxLength: 50,
    allowBlank: true,
    anchor : '95%',    
    maskRe: /([a-z._@A-Z0-9/\s]+)$/ 
      });
  
 
  iconoField = new Ext.form.TextField({
    id:'iconoField',
    fieldLabel: 'Icono',
    maxLength: 30,
    allowBlank: true,
    anchor : '95%'    
    //maskRe: /([a-zA-Z0-9\_s]+)$/  
      });
 
 
  ordenField = new Ext.form.TextField({
    id:'ordenField',
    fieldLabel: 'Orden',
    maxLength: 30,
    allowBlank: false,
    anchor : '95%',    
    maskRe: /([a-zA-Z0-9\s]+)$/  
      });
  
  hijosField = {
  		xtype: 'checkbox',
  		fieldLabel: 'Hijos',
  		name: 'hijosField',
        id:'hijosField'
    };
    
   menuField = {
  		xtype: 'checkbox',
  		fieldLabel: 'Men&uacute;',
  		name: 'menuField',
        id:'menuField',
        checked: true
    };

  
  ModuloCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width: 600,        
        items: [{
			id:'fieldset_form',
            layout:'column',
            border:false,
            items:[{
                columnWidth:0.5,
                layout: 'form',
                border:false,
                items: [moduloField, accionField, iconoField]
            },{
                columnWidth:0.5,
                layout: 'form',
                border:false,
                items: [ordenField, padreField, hijosField, menuField]
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: createModulo
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			ModuloCreateWindow.close();
		  }
		}]
    });
	
 
  ModuloCreateWindow= new Ext.Window({
      id: 'ModuloCreateWindow',
      title: 'Crear nuevo modulo',
      closable:false,
	  modal:true,
      width: 610,
      height: 255,
      plain:true,
      layout: 'fit',
      items: ModuloCreateForm,
      closeAction: 'close'
    });		
		
		
	    ModuloCreateWindow.show();
  }