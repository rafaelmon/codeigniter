function clickBtnEventosExcel (){
//    var store = Ext.getCmp('edDataStore').getStore();
//    console.log(store);
    Ext.Ajax.request({ 
        url: LINK_GENERICO+'/sesion',
        method: 'POST',
        waitMsg: 'Por favor espere...',
        success: function(response, opts) {
            var result=parseInt(response.responseText);
            switch (result)
            {
                case 0:
                case '0':
                    location.assign(URL_BASE_SITIO+"admin");
                    break;
                case 1:
                case '1':
                    go_clickBtnEventosExcel();
                    break;
            }
        },
        failure: function(response) {
            location.assign(URL_BASE_SITIO+"admin");
        }
    });
}

function go_clickBtnEventosExcel(){
    var txt='¿Confirma la descarga del archivo?';
    Ext.MessageBox.confirm('Confirmar',txt, function(btn, text){
        if(btn=='yes'){
            msgProcess('Generando...');
            var body = Ext.getBody();
            var eventoDownloadFrame = body.createChild({
                 tag: 'iframe',
                 cls: 'x-hidden',
                 id: 'ed-admin-app-upload-frame',
                 name: 'uploadframe'
             });
            var eventoDownloadForm = body.createChild({
                 tag: 'form',
                 cls: 'x-hidden',
                 id: 'ed-admin-app-upload-form',
                 target: 'ed-admin-app-upload-frame'
             });
            Ext.Ajax.request({
                url: CARPETA+'/datos_excel', 
                timeout:10000,
                scope :this,
                params: {
                    filtros:cppEventosDataStore.baseParams.filtros,
                    query:cppEventosDataStore.baseParams.query,
                    fields:cppEventosDataStore.baseParams.fields,
                    sort:cppEventosDataStore.baseParams.sort,
                    dir:cppEventosDataStore.baseParams.dir,
                },
                form: eventoDownloadForm,
                callback:function (){
                Ext.Msg.alert('Status', 'Datos generados correctamente.');
            },
                isUpload: true,
                 success: function(response, opts) {
                 },
                failure: function(response, opts) {
                 }
            });
            Ext.Msg.alert('Descarga de archivo', 'Descarga en proceso. Por Favor aguarde a que se abra la ventana de descarga...');
        }
    });
}

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


