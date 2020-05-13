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
function createHallazgoAuditorias(a,b,id_auditoria){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createHallazgoAuditorias(a,b,id_auditoria);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_createHallazgoAuditorias(a,b,id_auditoria){
     if(isauditoriaHallazgoFormValid()){
        msgProcess('Guardando Hallazgo');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert_hallazgo',
            method: 'POST',
            params: {
                hallazgo        : auditoriaHallazgoField.getValue(),
                normas           : auditoriaHallazgoNormasSBS.getValue(),
                id_auditoria    : id_auditoria
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 1:
                            auditoriaHallazgoCreateWindow.close();
                            Ext.MessageBox.alert('Alta OK','El hallazgo fue creada satisfactoriamente');
                            auditoriaDataStore.reload();
                            auditoriasHallazgosDataStore.load({params: {id:id_auditoria,start: 0}});
                            colAuditoriasHallazgos.setTitle('listado de hallazgos de auditoria nro: '+id_auditoria);
//                            var row=auditoriaGridPanel.getView().getRow(1);
//                            console.log(Ext.get(row)); 
//                            Ext.get(row).highlight(); 
//                            auditoriaHallazgosPanel.show();
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case -1:
                            Ext.MessageBox.alert('Error','Verifique sus permisos');
                            break;
                        case -2:
                            Ext.MessageBox.alert('Error','Solo el auditor que dió de alta la auditoría puede dar de alta hallazgos');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo crear el hallazgo.');
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
  function isauditoriaHallazgoFormValid(){	  
	  var v1 = auditoriaHallazgoField.isValid();
	  var v2 = auditoriaHallazgoNormasSBS.isValid();
	  return( v1 && v2);
  }
  
  // display or bring forth the form
  function displayHallazgoAuditoriasFormWindow(id_auditoria){
//	 if(auditoriaHallazgoCreateForm){
//	 	if(auditoriaHallazgoCreateForm.findById('fieldsetid')) {
//		 //get the fieldset
//	
//	
//		 var oldfieldset = auditoriaHallazgoCreateForm.findById('fieldset_form');
//		 //var oldfieldset = auditoriaHallazgoCreateForm.items;
//		 
//			//iterate trough each of the component in the fieldset
//			oldfieldset.items.each(function(collection,item,length){
//				var i = item;
//				//destroy the object within the fieldset
//				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
//			});
//		}
//		auditoriaHallazgoCreateForm.destroy();
//		auditoriaHallazgoCreateWindow.destroy()
//	}
//		
//
  // reset the Form before opening it
  function resetPresidentForm(){
    auditoriaHallazgoField.setValue('');
    auditoriaHallazgoNormasSBS.setValue('');
  }	


auditoriaHallazgoField = new Ext.form.TextArea({
    id: 'auditoriaHallazgoField',
    fieldLabel: 'Describa el hallazgo',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    height:150, 
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 1
});
auditoriaHallazgoNormasDS = new Ext.data.Store({
        id:'auditoriaHallazgoNormasDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_normas',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_norma_punto'
        }, [
            {name: 'id_norma_punto', mapping: 'id_norma_punto'},
            {name: 'norma', mapping: 'norma'},
            {name: 'normadetalle', mapping: 'normadetalle'},
            {name: 'punto', mapping: 'punto'},
            {name: 'detalle', mapping: 'detalle'}
        ])
    });
//var revisoresTpl = new Ext.XTemplate(
//        '<tpl for="."><div class="search-item">',
//            '<h3><span>{nomape}</h3>({usuario})</span>',
//        '</div></tpl>'
//);
var auditoriaHallazgoNormasTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<b><span>{norma}</b> - {punto}) {detalle}</span>',
        '</div></tpl>'
    );
auditoriaHallazgoNormasSBS = new Ext.ux.form.SuperBoxSelect({
        id:'auditoriaHallazgoNormasSBS',
        forceSelection : false,
        fieldLabel: 'Seleccione el/las norma/s infringida/s',
        store: auditoriaHallazgoNormasDS,
//        editable : false,
        allowBlank: false,
        emptyText: 'Ingresa caracteres para buscar',
        blankText:'campo requerido',
        displayField: 'normadetalle',
        valueField: 'id_norma_punto',
        anchor : '95%',
//        displayFieldTpl: '{nomape} ({usuario})',
        mode: 'remote',
//        valueDelimiter:';',
        tpl: auditoriaHallazgoNormasTpl,
        itemSelector: 'div.search-item',
//        stackItems:true, //un item por línea
        triggerAction: 'all',
//        forceSelection : true,
        allowQueryAll : true,
        minChars:3,
        tabIndex:2
    });
auditoriaNormasFieldSet = new Ext.form.FieldSet({
        id:'analisisRiesgoFieldSet',
        title : 'Normas infringidas',
        anchor : '95%',
        growMin:100,
        items:[auditoriaHallazgoNormasSBS]
    }); 

 
  auditoriaHallazgoCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:750,        
        items: [{
            id:'fieldset_form',
            layout:'column',
            border:false,
            items:[{
                columnWidth:1,
                layout: 'form',
                border:false,
                items: [auditoriaHallazgoField,auditoriaNormasFieldSet]
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: createHallazgoAuditorias.createDelegate(this,id_auditoria,true)
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			auditoriaHallazgoCreateWindow.close();
		  }
		}]
    });
	
 
  auditoriaHallazgoCreateWindow= new Ext.Window({
      id: 'auditoriaHallazgoCreateWindow',
      title: 'Crear nuevo Hallazgo para la Auditoría Nro: '+id_auditoria,
      closable:false,
      modal:true,
      width: 850,
      height: 450,
      plain:true,
      layout: 'fit',
      items: auditoriaHallazgoCreateForm,
      closeAction: 'close'
    });		
    auditoriaHallazgoCreateWindow.show();
  }