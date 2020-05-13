
publicadosGrid.on('cellclick',function(grid, rowIndex, columnIndex){
    var id_estado=grid.store.data.items[rowIndex].data.id_estado;
//    if(columnIndex<(grid.colModel.config.length)-2 && id_estado!=1)
    if(columnIndex==4 && id_estado!=1)
    {
        var id_documento=grid.store.data.items[rowIndex].data.id_documento;
        var codigo=grid.store.data.items[rowIndex].data.codigo;
        gestionesDocsPublicadosDataStore.load({params: {id:id_documento,start: 0}});
        publicadosInferiorPanel.setTitle('Historial del Documento Código:'+codigo);
        publicadosInferiorPanel.show();
        publicadosInferiorPanel.expand(false);
//        return iDTramite;
    }
    else
    {
        publicadosInferiorPanel.collapse(true);
        publicadosInferiorPanel.setTitle('Historial del Documento Código:...');
        gestionesDocsPublicadosDataStore.removeAll();
    }
        
});

publicadosPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [publicadosSuperiorPanel,publicadosInferiorPanel],
    renderTo: 'grillita'

}); 

altura=Ext.getBody().getSize().height - 60;
publicadosPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    publicadosPanel.setWidth(this.getSize().width);
    publicadosPanel.setHeight(Ext.getBody().getSize().height - 60);
});

//ANULAR TRAMITE
//function clickBtnAnular (grid, rowIndex,colIndex)
//{
//    var id_tramite = grid.store.getAt(rowIndex).id
//    Ext.MessageBox.confirm('Confirmation','Confirma que desea anular este trámite?', function(btn){
//        if(btn=='yes'){
//            Ext.Ajax.request({  
//                waitMsg: 'Por favor espere',
//                url: CARPETA+'/anular', 
//                params: { 
//                    id_tramite:  id_tramite
//                }, 
//                success: function(response){
//                    var result=eval(response.responseText);
////                    console.log(result);
//                    switch(result){
//                    case 1:  // Success : simply reload
//                        grid.store.reload();
//                        break;
//                    case 2:  
//                        Ext.MessageBox.alert('Error','Ha ocurrido un error comuníquese con el Administrador.');
//                        break;
//                    case 3:  
//                        Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
//                        break;
//                    default:
//                        Ext.MessageBox.alert('Warning','No se pudo eliminar el registro seleccionado.');
//                        break;
//                    }
//                },
//                failure: function(response){
////                var result=response.responseText;
//                Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
//                }
//            });
//        }
//        
//    });
//    
//}//-->FIN ANULAR TRAMITE  
//
////RECHAZAR TRAMITE
//function clickBtnRechazar (grid, rowIndex,colIndex)
//{
//    var id_tramite = grid.store.getAt(rowIndex).id
//    Ext.MessageBox.confirm('Confirmation','Confirma que desea rechazar este trámite?', function(btn){
//        if(btn=='yes'){
//            Ext.Ajax.request({  
//                waitMsg: 'Por favor espere',
//                url: CARPETA+'/rechazar', 
//                params: { 
//                    id_tramite:  id_tramite
//                }, 
//                success: function(response){
//                    var result=eval(response.responseText);
////                    console.log(result);
//                    switch(result){
//                    case 1:  // Success : simply reload
//                        grid.store.reload();
//                        break;
//                    case 2:  
//                        Ext.MessageBox.alert('Error','Ha ocurrido un error comuníquese con el Administrador.');
//                        break;
//                    case 3:  
//                        Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
//                        break;
//                    default:
//                        Ext.MessageBox.alert('Warning','No se pudo rechazar el registro seleccionado.');
//                        break;
//                    }
//                },
//                failure: function(response){
////                var result=response.responseText;
//                Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
//                }
//            });
//        }
//        
//    });
//    
//}//-->FIN RECHAZAR TRAMITE
////CERRAR TRAMITE
//function clickBtnCerrar (grid, rowIndex,colIndex)
//{
//    var id_tramite = grid.store.getAt(rowIndex).id
//    Ext.MessageBox.confirm('Confirmation','Confirma que desea cerrar este trámite?', function(btn){
//        if(btn=='yes'){
//            Ext.Ajax.request({  
//                waitMsg: 'Por favor espere',
//                url: CARPETA+'/cerrar', 
//                params: { 
//                    id_tramite:  id_tramite
//                }, 
//                success: function(response){
//                    var result=eval(response.responseText);
////                    console.log(result);
//                    switch(result){
//                    case 1:  // Success : simply reload
//                        grid.store.reload();
//                        break;
//                    case 2:  
//                        Ext.MessageBox.alert('Error','Ha ocurrido un error comuníquese con el Administrador.');
//                        break;
//                    case 3:  
//                        Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
//                        break;
//                    default:
//                        Ext.MessageBox.alert('Warning','No se pudo cerrar el tramite seleccionado.');
//                        break;
//                    }
//                },
//                failure: function(response){
////                var result=response.responseText;
//                Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
//                }
//            });
//        }
//        
//    });
    
//}//-->FIN Cerrar TRAMITE  
