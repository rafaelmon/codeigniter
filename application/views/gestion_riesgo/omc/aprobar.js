function clickBtnAprobar (grid, rowIndex){
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
                    go_clickBtnAprobar(grid, rowIndex);
                    break;
            }
        },
        failure: function(response) {
            location.assign(URL_BASE_SITIO+"admin");
        }
    });
}

function go_clickBtnAprobar(grid,rowIndex){
    var record=grid.getStore().getAt(rowIndex);
    var id=record.data.id_omc;
    Ext.MessageBox.confirm('Aprobar','¿Confirma que desea aprobar la OMC N°'+id+'?', function(btn, text){
        if(btn=='yes'){
            msgProcess('Procesando...');
                Ext.Ajax.request({   
                    waitMsg: 'Por favor espere...',
                    url: CARPETA+'/aprobar', 
                    timeout:10000,
                    scope :this,
                    params: {
                        id:id
                    }, 
                    success: function(response){              
                    var result=eval(response.responseText);
                    switch(result.error){
                    case 0:
                        Ext.MessageBox.alert('Operación OK','OMC N° '+id+' aprobada...');
                        omcDataStore.reload();
                        break;
                    default:
                        Ext.MessageBox.alert('Error',result.error);
                        break;
                    }        
                    },
                    failure: function(response){
                        Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
                    }                      
                });
        }
     });
  };