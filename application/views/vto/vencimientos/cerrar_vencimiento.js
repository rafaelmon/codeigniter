
function clickBtnArchivoVencimiento (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnArchivoVencimiento(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnArchivoVencimiento (grid,rowIndex,colIndex,item,event)
{
    var id_vencimiento      = grid.getStore().getAt(rowIndex).json.id_vencimiento;
    var id_estado_actual    = grid.getStore().getAt(rowIndex).json.id_estado;
    var fecha_vto           = grid.getStore().getAt(rowIndex).json.fecha_vto;
    
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
    function isCerrarVencimientoFormValid(){	  
        var v1 = comentarioField.isValid();
        var v2 = renovacionCheckField.isValid();
        
        if(renovacionCheckField.getValue() == true)
        {
            var v3 = fechaVtoField.isValid();
            var v4 = diasAvisoField.isValid();
            var v5 = cantidadAvisosField.isValid();
            return( v1 && v2 && v3 && v4 && v5 );
        }
        else
            return( v1 && v2 );       
    }
    
    comentarioField = new Ext.form.TextArea({
        id: 'comentarioField',
        fieldLabel: 'Comentario sobre el vencimiento',
        maxLength: 2000,
        maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 1
    });
    
    renovacionCheckField = new Ext.form.Checkbox ({
        fieldLabel: '',
        name: 'renovacionCheckField',
        checked: false
    });
    renovacionCheckField.on('check', selectRenovacionCheckField); 
    renovacionComposite=new Ext.form.CompositeField({
        fieldLabel: '',
        combineErrors: false,
        items: [
            {xtype: 'displayfield', value: '¿Renueva Vencimiento?'},
            renovacionCheckField,
        ]
    });
    
    fechaVtoField = new Ext.form.DateField({
        id:'fechaVtoField',
        allowBlank: false,
        tabIndex: 3,
        fieldLabel:'Fecha Vencimiento',
        anchor : '95%',
        editable: true,
        minValue:fecha_vto,
        format:'d/m/Y',
        hidden:true
    });
    
    diasAvisoField = new Ext.form.NumberField({
      id: 'diasAvisoField',
      fieldLabel: 'Dias aviso',
      maxLength: 3,
      allowBlank: false,
      tabIndex:5,
      anchor : '95%',
      allowDecimals: false ,
      hidden:true,
      listeners: {
            'render': function(c) {
              c.getEl().on('change', function() {
                var fechaVto = new Date(Ext.getCmp("fechaVtoField").getValue());
                var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
                var today = new Date(TODAY);
                var diffDays = Math.round((Math.abs((fechaVto.getTime() - today.getTime()))/(oneDay)));
                var qavisos = Ext.getCmp("diasAvisoField").getValue();
                if(qavisos > diffDays)
                {
                    Ext.MessageBox.alert('Error','Los cantidad de dias previos al aviso no pueden ser mayor a la diferencia de dias entre la fecha de vencimiento y hoy.');
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
      allowDecimals: false,
      hidden:true
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
                width: 400
        },
        items:[{
            id: 'file',
            name: 'file',
            allowBlank: false,
            emptyText: 'Tipo de archivo permitido: ',
        }]
    });
    
    archivoVencimientoCreateForm = new Ext.FormPanel({
        id:'archivoVencimientoCreateForm',
    //    title: 'Subir archivos',
        url: CARPETA+'/cerrarVencimiento',
        width: 350, 
        buttonAlign: 'center',
        frame: true, 
        fileUpload: true, 
        labelAlign: 'top',
        waitTitle:'Espere Por favor...',
        labelWidth:100,
        style: 'margin: 0 auto;',
        items: [
            comentarioField
            ,renovacionComposite
            ,fechaVtoField
            ,diasAvisoField
            ,cantidadAvisosField
            ,upload
            ]
    });
    
    archivoVencimientoCreateWindow= new Ext.Window({
        id: 'archivoVencimientoCreateWindow',
        title: 'Cerrar vencimiento',
        modal:true,
        width: 500,
        height: 500,
        plain:true,
        layout: 'fit',
        items: archivoVencimientoCreateForm,
        closable:true,
        closeAction: 'close',
        resizable:false,
        buttons: [{
        text: 'Guardar',
        handler: function() {
//            console.log('BtnSubir-fcion handler');
//            console.log(Ext.getCmp("archivoVencimientoCreateForm").getForm());
            if(isCerrarVencimientoFormValid){
                Ext.getCmp("archivoVencimientoCreateForm").getForm().submit({
                    waitMsg: 'Subiendo archivo...',
                    timeout:300,
                    params: { 
                       id_vencimiento       : id_vencimiento,
                       comentario           : comentarioField.getValue(),
                       fecha_vencimiento    : fechaVtoField.getValue(),
                       renovacion           : renovacionCheckField.getValue(),
                       id_estado_actual     : id_estado_actual,
                       dias_avisos          : diasAvisoField.getValue(),
                       q_avisos             : cantidadAvisosField.getValue()
                    },
                    success: function(response){
    //                    var result=eval(action.response.responseText);
                        var result = eval(response.responseText);
    //                    console.log(action);
                        console.log(response.responseText);
                        console.log(result);
                        switch(result){
                            case 1:
                                Ext.MessageBox.alert('Operación OK','El vencimiento se cerro correctamente.');
                                vencimientosDataStore.reload();
                                cerrarVencimientoCreateWindow.close();
                                break;
                            case 2:
                                Ext.MessageBox.alert('Error','Error al intentar cerrar el vencimiento. Intente mas tarde.');
                                break;
                            case 3:
                                Ext.MessageBox.alert('Operación OK','El vencimiento se renovo correctamente.');
                                vencimientosDataStore.reload();
                                cerrarVencimientoCreateWindow.close();
                                break;
                            case 4:
                                Ext.MessageBox.alert('Error','Error al intentar renovar el vencimiento. Intente mas tarde.');
                                break;
                            case 5:
                                Ext.MessageBox.alert('Error','Error no puede realizar esta acción sobre el vencimiento.');
                                break;
                            case 6:
                                Ext.MessageBox.alert('Error','Error el archivo es muy grande.');
                                break;
                            case 7:
                                Ext.MessageBox.alert('Error','Error el tipo de archivo no esta permitido.');
                                break;
                            case 8:
                                Ext.MessageBox.alert('Error','Error al intentar subir el archivo.');
                                break;
                            case 9:
                                Ext.MessageBox.alert('Error','La cantidad de alertas no puede ser superior a los dias de aviso.');
                                break;
                            case 10:
                                Ext.MessageBox.alert('Error','Falta completar campos requeridos.');
                                break;
                            case 11:
                                Ext.MessageBox.alert('Error','La candidad de días precios para el aviso debe ser menor.');
                                break;
                            default:
                                Ext.MessageBox.alert('Error','Error.');
                                break;
                        };
                    },
                    failure: function(form, action){
                        var result=eval(action.response.responseText);
    //                console.log(result);
                        msg('Error', result.error);
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
            archivoVencimientoCreateForm.destroy();
            archivoVencimientoCreateWindow.close();
        }
    },{
        text: 'Reset',
        handler: function() {
            archivoVencimientoCreateForm.getForm().reset();
        }
    }]
    });
    archivoVencimientoCreateWindow.on('afterrender', selectRenovacionCheckField); 
    function selectRenovacionCheckField(cb,checked)
    {
        var fecha = Ext.getCmp('fechaVtoField');
        var dias = Ext.getCmp('diasAvisoField');
        var cantidad = Ext.getCmp('cantidadAvisosField');
        var upload = Ext.getCmp('upload');
        
        if(checked == true)
        {
            fecha.setVisible(true);
            dias.setVisible(true);
            cantidad.setVisible(true);
            Ext.getCmp('upload').setVisible(true);
        }
        else
        {
            fecha.reset();
            dias.reset();
            cantidad.reset();
            fecha.setVisible(false);
            dias.setVisible(false);
            cantidad.setVisible(false);
            Ext.getCmp('upload').setVisible(false);
        }
    }
    
    archivoVencimientoCreateWindow.show();

  
 }


