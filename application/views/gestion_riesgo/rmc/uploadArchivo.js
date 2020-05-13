
function clickBtnUploadArchivoRi (grid, rowIndex, colIndex)
{
    var record = grid.getStore().getAt(rowIndex); 
    var id_rmc=record.data.id_rmc;
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
rmcArchivoTituloField = new Ext.form.TextField({
        id: 'rmcArchivoTituloField',
        name: 'rmcArchivoTituloField',
        fieldLabel: 'Título',
        allowBlank: true,
        blankText:'campo requerido',
        disabled:false,
        tabIndex:1,
        anchor : '95%'
    });
rmcArchivoDescField = new Ext.form.TextArea({
    id: 'rmcTareaField',
    fieldLabel: 'Descripción',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: true,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 2
});

archivosRiCreateForm = new Ext.FormPanel({
    id:'archivosRiCreateForm',
//    title: 'Subir archivos',
    url: CARPETA_UPLOAD+'/upload_ri',
    width: 320, 
    buttonAlign: 'center',
    frame: true, 
    fileUpload: true, 
    labelAlign: 'left',
    waitTitle:'Espere Por favor...',
    labelWidth:100,
    style: 'margin: 0 auto;',
    items: [
        rmcArchivoTituloField,
        rmcArchivoDescField,
        {
            id: 'file',
            name: 'file',
            xtype: 'fileuploadfield',
            buttonText: 'Explorar',
            width: 390,
            allowBlank: false,
            emptyText: 'Seleccione...',
            fieldLabel: 'Archivo '
        }
]
});

    archivosRiCreateWindow= new Ext.Window({
        id: 'archivosRiCreateWindow',
        title: 'Subir Archivo',
        modal:true,
        width: 550,
        height: 250,
        plain:true,
        layout: 'fit',
        items: archivosRiCreateForm,
        closable:true,
        closeAction: 'close',
        resizable:false,
        buttons: [{
        text: 'Subir',
        handler: function() {
            var form=Ext.getCmp("archivosRiCreateForm").getForm();
            console.log(form);
            form.submit({
                waitMsg: 'Subiendo archivo...',
                timeout:300,
                params: { 
                   id       : id_rmc,
                   titulo   : rmcArchivoTituloField.getValue(),
                   descr    : rmcArchivoDescField.getValue()
                },
                success: function(form, action){
                    msg('Success', 'Archivo subido correctamente');
                    rmcDataStore.reload();
                    rmcArchivosDataStore.reload();
                    archivosRiCreateWindow.close();
                },
                failure: function(form, action){
                    var result=eval(action.response.responseText);
//                console.log(result);
                    msg('Error', result.error);
                }
            });
        }
    },{
        text: 'Cancelar',
        handler: function(){
            archivosRiCreateForm.destroy();
            archivosRiCreateWindow.close();
        }
    },{
        text: 'Reset',
        handler: function() {
            archivosRiCreateForm.getForm().reset();
        }
    }]
    });		
    archivosRiCreateWindow.show();
 }//fin clickBtnDelegar()
