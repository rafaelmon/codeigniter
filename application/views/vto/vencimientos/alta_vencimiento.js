
function clickBtnNuevoVencimiento (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnNuevoVencimiento();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevoVencimiento ()
{    
    var msg = function(title, msg){
        Ext.Msg.show({
            title: title,
            msg: msg,
            minWidth: 200,
            modal: true,
            icon: Ext.Msg.INFO,
            buttons: Ext.Msg.OK
        });
    };
    
     // check if the form is valid
   function isAltaVencimientoFormValid(){	  
          var v1 = vencimientoField.isValid();
          var v2 = descripcionField.isValid();
          var v3 = fechaVtoField.isValid();
          var v4 = responsablesCombo.isValid();
          var v5 = diasAvisoField.isValid();
          var v6 = cantidadAvisosField.isValid();
          var v7 = vencimientoRevisionRadios.isValid();
        return(v1 && v2 && v3 && v4 && v5 && v6 && v7);
    }
    
    vencimientoField = new Ext.form.TextField({
      id: 'vencimientoField',
      fieldLabel: 'Titulo del vencimiento',
      maxLength: 256,
      allowBlank: false,
      tabIndex:1,
      anchor : '95%'
    });
    
    descripcionField = new Ext.form.TextArea({
        id: 'descripcionField',
        fieldLabel: 'Descripcion sobre el vencimiento',
        maxLength: 2000,
        maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 2
    });
    
    fechaVtoField = new Ext.form.DateField({
        id:'fechaVtoField',
        allowBlank: false,
        tabIndex: 3,
        fieldLabel:'Fecha Vencimiento',
        anchor : '95%',
        blankText:'campo requerido',
        editable: true,
        minValue:MINDATE,
//            maxValue:,
        format:'d/m/Y',
        
    });
    
    usuariosDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_responsables',
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
    var responsableTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    responsablesCombo = new Ext.form.ComboBox({
        id:'responsablesCombo',
        store: usuariosDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Usuario',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '95%',
        minChars:3,
//        labelStyle: 'font-weight:bold;',
        pageSize:10,
        tabIndex: 4,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: responsableTpl,
        itemSelector: 'div.search-item'
    });
    
    diasAvisoField = new Ext.form.NumberField({
      id: 'diasAvisoField',
      fieldLabel: 'Dias aviso',
      maxLength: 3,
      allowBlank: false,
      tabIndex:5,
      anchor : '95%',
      allowDecimals: false,
      listeners: {
            'render': function(c) {
              c.getEl().on('change', function() {
                var fechaVto = new Date(Ext.getCmp("fechaVtoField").getValue());
                var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
                var today = new Date(TODAY);
                var diffDays = Math.round((Math.abs((fechaVto.getTime() - today.getTime()))/(oneDay)));
                var qavisos = Ext.getCmp("diasAvisoField").getValue();
                console.log(diffDays);
                if(qavisos > diffDays)
                {
                    Ext.MessageBox.alert('Error','La candidad de días precios para el aviso debe ser menor.');
                    Ext.getCmp("diasAvisoField").setValue("");
                }
              }, c);
            }
        }
    });
    
    cantidadAvisosField = new Ext.form.NumberField({
      id: 'cantidadAvisosField',
      fieldLabel: 'Cantidad de aviso/s',
      maxLength: 3,
      allowBlank: false,
      tabIndex:6,
      anchor : '95%',
      allowDecimals: false
    });
    
    upload = new Ext.form.FieldSet({
        id:'upload',
        title : 'Constancia de Renovación',
        anchor : '95%',
        growMin:100,
//        hidden: true,
        defaults:{
                xtype: 'fileuploadfield',
                buttonText: 'Explorar',
                width: 350
        },
        items:[{
                id: 'file',
                name: 'file',
                allowBlank: false,
                emptyText: 'Tipo de archivo permitido: ',
            }]
    });
    
    vencimientoRevisionRadios = new Ext.form.RadioGroup({ 
        id:'vencimientoRevisionRadios',
        fieldLabel: 'Entrada para revisión de la dirección',
        allowBlank: false,
        anchor : '95%',
        tabIndex:8,
        columns: 4,
        items: [ 
              {boxLabel: 'Si', name: 'vencimiento_revision', inputValue: '1'}, //, checked: true
              {boxLabel: 'No', name: 'vencimiento_revision', inputValue: '2'}
         ] 
    });
    
    altaVencimientoCreateForm = new Ext.FormPanel({
        id:'altaVencimientoCreateForm',
    //    title: 'Subir archivos',
        url: CARPETA+'/insert',
        width: 350, 
        buttonAlign: 'center',
        frame: true, 
        fileUpload: true, 
        labelAlign: 'top',
        waitTitle:'Espere Por favor...',
        labelWidth:100,
        style: 'margin: 0 auto;',
        items:[{
            layout:'column',
            border:false,
            items:[{
                columnWidth:0.5,
                layout: 'form',
                border:false,
                items: [vencimientoField,descripcionField,fechaVtoField,responsablesCombo]
            },{
                columnWidth:0.5,
                layout: 'form',
                border:false,
                items: [diasAvisoField,cantidadAvisosField,upload,vencimientoRevisionRadios]
            }]
        }] 
    });
    
    altaVencimientoCreateWindow= new Ext.Window({
        id: 'altaVencimientoCreateWindow',
        title: 'Alta nuevo vencimiento',
        modal:true,
        width: 850,
        height: 350,
        plain:true,
        layout: 'fit',
        items: altaVencimientoCreateForm,
        closable:true,
        closeAction: 'close',
        resizable:false,
        buttons: [{
        text: 'Guardar',
        handler: function() {
//            console.log('BtnSubir-fcion handler');
//            console.log(Ext.getCmp("altaVencimientoCreateForm").getForm());
            if(isAltaVencimientoFormValid()){
                Ext.getCmp("altaVencimientoCreateForm").getForm().submit({
                    waitMsg: 'Subiendo archivo...',
                    timeout:300,
                    params: { 
                       vencimiento            : vencimientoField.getValue(),
                       descripcion            : descripcionField.getValue(),
                       fecha_vto              : fechaVtoField.getValue(),
                       id_usuario_responsable : responsablesCombo.getValue(),
                       dias_avisos            : diasAvisoField.getValue(),
                       q_avisos               : cantidadAvisosField.getValue(),
                       rpd                    : vencimientoRevisionRadios.getValue().inputValue
                    },
                    success: function(response,msg){
                        var result=eval(msg.result.msg);
    //                    console.log(result);
    //                    console.log(msg.result.msg);
    //                    console.log(msg.result);
                        switch(result){
                            case 1:
                                Ext.MessageBox.alert('Operación OK','Registro agregado correctamente.');
                                vencimientosDataStore.reload();
                                altaVencimientoCreateWindow.close();
                                break;
                            case 2:
                                Ext.MessageBox.alert('Error','Falta completar campos requeridos.');
                                break;
                            case 3:
                                Ext.MessageBox.alert('Error','No tiene los permisos necesarios para realizar esta acción.');
                                break;
                            case 4:
                                Ext.MessageBox.alert('Error','La cantidad de alertas no puede ser superior a los dias de aviso.');
                                break;
                            case 5:
                                Ext.MessageBox.alert('Error','El archivo que intenta subir es muy grande.');
                                break;
                            case 6:
                            case 8:
                                Ext.MessageBox.alert('Error','El archivo no se pudo adjuntar.');
                                break;
                            case 7:
                                Ext.MessageBox.alert('Error','La extensión del archivo que intenta subir no esta permitida.');
                                break;
                            case 9:
                                Ext.MessageBox.alert('Error','La candidad de días precios para el aviso debe ser menor.');
                                break;
                            default:
                                Ext.MessageBox.alert('Error','No se pudo dar de alta el vencimiento.');
                                break;
                        }
                    },
                   failure: function(response,action){
                       console.log(action);
                       console.log(response);
                       console.log(response.success);
                        var result=eval(response.responseText);
                        Ext.MessageBox.alert('error','Falta completar campos requeridos.');      
                    }
                });
            }
            else
            {
                Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
            }
        }
        },{
            text: 'Cancelar',
            handler: function(){
                altaVencimientoCreateForm.destroy();
                altaVencimientoCreateWindow.close();
            }
        },{
            text: 'Reset',
            handler: function() {
                altaVencimientoCreateForm.getForm().reset();
            }
        }]
    });
    altaVencimientoCreateWindow.show();
 }


