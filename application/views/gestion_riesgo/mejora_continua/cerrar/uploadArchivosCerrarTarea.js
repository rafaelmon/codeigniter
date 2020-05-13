// JavaScript Document

function clickBtnArchivosCerrarTarea ()
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

    archivosCerrarTareaCreateForm = new Ext.FormPanel({
        id:'archivosCerrarTareaCreateForm',
    //    title: 'Subir archivos',
        url: CARPETA_UPLOAD+'/upload_cerrar_tarea',
        width: 320, 
        buttonAlign: 'center',
        frame: true, 
        fileUpload: true, 
        labelAlign: 'left',
        waitTitle:'Espere Por favor...',
        labelWidth:100,
        style: 'margin: 0 auto;',
        defaults: {
            xtype: 'fileuploadfield',
            buttonText: 'Explorar',
            width: 300
    //        buttonCfg: {
    //            iconCls: 'icon-file-upload'
    //        }
        },
        items: [{
            id: 'file',
            name: 'file',
            allowBlank: false,
            emptyText: 'Tipo de archivo permitido: ',
            fieldLabel: 'Archivo'
        }]
    });
    archivosCerrarTareaCreateWindow= new Ext.Window({
        id: 'archivosCerrarTareaCreateWindow',
        title: 'Subir Archivo',
        modal:true,
        width: 450,
        height: 120,
        plain:true,
        layout: 'fit',
        items: archivosCerrarTareaCreateForm,
        closable:true,
        closeAction: 'close',
        resizable:false,
        buttons: [{
        text: 'Subir',
        handler: function() {
//            console.log('BtnSubir-fcion handler');
//            console.log(Ext.getCmp("archivosCerrarTareaCreateForm").getForm());
            Ext.getCmp("archivosCerrarTareaCreateForm").getForm().submit({
                waitMsg: 'Subiendo archivo...',
                timeout:300,
                params: { 
                   id: TAREA.id_tarea,
                },
                success: function(form, action){
                    archivosCerrarTareaCreateWindow.close();
                    var panelUpld=Ext.getCmp('archivosCerrarTareaListingEditorGrid');
                    var archivosDS=panelUpld.getStore();
                    archivosDS.reload();
                    msg('Success', 'Archivo agregado correctamente');
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
            archivosCerrarTareaCreateForm.destroy();
            archivosCerrarTareaCreateWindow.close();
        }
    },{
        text: 'Reset',
        handler: function() {
            archivosCerrarTareaCreateForm.getForm().reset();
        }
    }]
    });		
    archivosCerrarTareaCreateWindow.show();

  
 }//fin clickBtnDelegar()