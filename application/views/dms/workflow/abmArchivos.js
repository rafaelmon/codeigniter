// JavaScript Document

function clickBtnArchivos (grid, rowIndex, colIndex)
{
    var col;
    var tipo;
//    switch (colIndex)
//    {
//        case 9:
            col='PDF';
            tipo='.pdf';
//            break;
//        case 10:
//            col='Fuente';
//            tipo='.doc o .docx';
//            break;
//    };
    
    var record = grid.getStore().getAt(rowIndex); 
    var id_documento=record.data.id_documento;
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

archivosDmsCreateForm = new Ext.FormPanel({
    id:'archivosDmsCreateForm',
//    title: 'Subir archivos',
    url: CARPETA+'/upload',
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
        id: 'archivo',
        name: 'archivo',
        allowBlank: false,
        emptyText: 'Tipo de archivo permitido: '+tipo,
        fieldLabel: 'Archivo '+col
    }]
});




    archivosDmsCreateWindow= new Ext.Window({
        id: 'archivosDmsCreateWindow',
        title: 'Subir Archivo '+col,
        modal:true,
        width: 450,
        height: 120,
        plain:true,
        layout: 'fit',
        items: archivosDmsCreateForm,
        closable:true,
        closeAction: 'close',
        resizable:false,
        buttons: [{
        text: 'Subir',
        handler: function() {
//            console.log('BtnSubir-fcion handler');
//            console.log(Ext.getCmp("archivosDmsCreateForm").getForm());
            Ext.getCmp("archivosDmsCreateForm").getForm().submit({
                waitMsg: 'Subiendo archivo...',
                timeout:300,
                params: { 
                   id_documento: id_documento,
                   col:col
                },
                success: function(form, action){
                    msg('Success', 'Archivo subido correctamente');
                    workflowDataStore.reload();
                    archivosDmsCreateWindow.close();
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
            archivosDmsCreateForm.destroy();
            archivosDmsCreateWindow.close();
        }
    },{
        text: 'Reset',
        handler: function() {
            archivosDmsCreateForm.getForm().reset();
        }
    }]
    });		
    archivosDmsCreateWindow.show();

  
 }//fin clickBtnDelegar()