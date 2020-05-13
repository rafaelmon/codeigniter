function ddpSupTopEditObjetivo_DFW(id_obj){
function msgProcess(titulo){
 Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:400, 
        wait:true, 
        waitConfig: {interval:200}
    });
}

function editDdpSupTopObjetivo(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_editDdpSupTopObjetivo();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_editDdpSupTopObjetivo(){
    if(isDdpObjetivoFormValid()){
       msgProcess('Guardando Objetivo');
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/edit_obj',
        params: {
          id            : id_obj,
//          id_dimension  : ID_DIM,
          obj               : ddpObjetivoField.getValue(),
          indicador         : ddpSupTopIndicadorObjetivoField.getValue(),
          valor_ref         : ddpSupTopValorRefObjetivoField.getValue(),
          fd                : ddpSupTopFuenteDatosObjetivoField.getValue(),
//          peso              : 20,//ddpSupTopPesoObjetivoField.getValue(),
          fecha_evaluacion  : ddpFechaEvaluacionField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case '1':
            case 1:
                var objetivos_DS=Ext.getCmp('supTopObjetivosGridPanel').getStore();
//                var dimensionesGridPanel=Ext.getCmp('supTopDimensionesGridPanel');
//                var dimensionesDS=dimensionesGridPanel.getStore();
                objetivos_DS.reload();
//                var rowdim=dimensionesGridPanel.selModel.rowNav.scope.lastActive;
//                dimensionesDS.load();
//                dimensionesDS.on('load',function(){
//                    dimensionesGridPanel.selModel.selectRow(rowdim,false,false);
//                });
//                dimensionesGridPanel.selModel.selectRow(rowdim);
                ddpSupTopObjetivoEditForm .destroy();
                ddpSupTopObjetivoEditWindow.close();
                SUM_DIM=0;
//                clickBtnVerHistorial();
                Ext.MessageBox.alert('Acción OK..','El Objetivo  fue modificado correctamente');
                break;
            case '2':
            case 2:
                ddpSupTopObjetivoEditForm .destroy();
                ddpSupTopObjetivoEditWindow.close();
                Ext.MessageBox.alert('Aviso','No se realizaron cambios en el objetivo');         
                break;
            case '3':
            case 3:
                Ext.MessageBox.alert('Error','Ocurrio un error en la gestión del objetivo, por favor notifique al administrador');         
                break;
            case '4':
            case 4:
                Ext.MessageBox.alert('Error','Debe completar campos requeridos');         
                break;
            default:
                Ext.MessageBox.alert('Error','Error inesperado, por favor notifique al administrador');
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
  }// END function editDdpSupTopObjetivo

  
// Verifico que los campos del formulario sean válidos
function isDdpObjetivoFormValid(){	  
    var v1 = ddpObjetivoField.isValid();
    //var v2 = ddpSupTopObjetivoPersonalField.isValid();
    var v2 = ddpFechaEvaluacionField.isValid();
//    var v3 = ddpSupTopIndicadorObjetivoField.isValid();
//    var v4 = ddpSupTopValorRefObjetivoField.isValid();
//    var v5 = ddpSupTopFuenteDatosObjetivoField.isValid();
//    var v6 = ddpSupTopPesoObjetivoField.isValid();
//    return( v1 && v2 && v3 && v4 && v5 &&v6);
    return( v1 && v2);
}
   
// display or bring forth the form
//    console.log(ID_DIM);

    
 ddpObjetivoField = new Ext.form.TextArea({
        id: 'ddpObjetivoField',
        fieldLabel: 'Objetivo',
        maxLength: 2000,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        maxLengthText:'M&aacute;ximo 2000 caracteres',
        tabIndex: 1
    });
      
 ddpSupTopIndicadorObjetivoField = new Ext.form.TextField({
        id: 'ddpSupTopIndicadorObjetivoField',
        fieldLabel: 'Indicador',
        allowBlank: true,
//        blankText:'campo requerido',
//        width: 50,
        anchor : '95%',
        tabIndex: 2
    });
ddpSupTopFuenteDatosObjetivoField = new Ext.form.TextField({
        id: 'ddpSupTopFuenteDatosObjetivoField',
        fieldLabel: 'Fuente de datos',
        allowBlank: true,
//        blankText:'campo requerido',
//        width: 50,
        anchor : '95%',
        tabIndex: 3
    });
ddpSupTopValorRefObjetivoField = new Ext.form.TextField({
        id: 'ddpSupTopValorRefObjetivoField',
        fieldLabel: 'Valor de Referencia',
        allowBlank: true,
//        blankText:'campo requerido',
//        width: 50,
        anchor : '95%',
        tabIndex: 4
    });
ddpSupTopPesoObjetivoField = new Ext.form.TextField({
        id: 'ddpSupTopPesoObjetivoField',
        fieldLabel: 'Peso',
        invalidText:'Solo valores entre 0 y 100',
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 5,
        disabled: true
    });
    
  ddpFechaEvaluacionField = new Ext.form.DateField({
        id: 'ddpFechaEvaluacionField',
        fieldLabel: 'Fecha evaluación',
        allowBlank: false,
        //vtype: 'daterange',
        minText : 'seleccione una fecha de evaluación',
        blankText:'campo requerido',
//        minValue:MINDATE,
        tabIndex:6,
        anchor : '95%'
    });
  
ddpSupTopObjetivoEditForm = new Ext.FormPanel({
        id:'ddpSupTopObjetivoEditForm',
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:700,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[ ddpObjetivoField
                    ,ddpSupTopIndicadorObjetivoField
                    ,ddpSupTopFuenteDatosObjetivoField
                    ,ddpSupTopValorRefObjetivoField
                    ,ddpSupTopPesoObjetivoField
                    ,ddpFechaEvaluacionField
            ]
        }],
        buttons: [{
            text: 'Guardar',
            tabIndex: 7,
            handler: editDdpSupTopObjetivo
            },{
            text: 'Cancelar',
            tabIndex: 8,
            handler: function(){
                ddpSupTopObjetivoEditWindow.close();
            }
            }]
    });//END FormPanel

 
ddpSupTopObjetivoEditWindow= new Ext.Window({
        id: 'ddpSupTopObjetivoEditWindow',
        title: 'Editando Objetivo  nro '+id_obj,
        closable:true,
        modal:true,
        width: 700,
        height: 500,
        plain:true,
        layout: 'fit',
        items: ddpSupTopObjetivoEditForm ,
        closeAction: 'close'
    });
                ddpSupTopObjetivoEditWindow.show();
if (id_obj > 0)
    {
            var formPanel=Ext.getCmp('ddpSupTopObjetivoEditForm');
            var winPanel=Ext.getCmp('ddpSupTopObjetivoEditWindow');
//        console.log(winPanel);
            formPanel.getForm().load({
                waitMsg: "Cargando...",
                url: CARPETA+'/datos_obj',
                params: {id: id_obj},
                success:function(response){
        //                var result=eval(response.responseText)

                },
                failure:function(response){
                       Ext.MessageBox.alert('Error','No se pudo cargar el objetivo');
                       winPanel.close();

                }
            });
    } 
    
    
}//END function displayFormWindow