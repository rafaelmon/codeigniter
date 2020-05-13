function dFW_ddpNuevoObetivo(id_obj)
{
    if (!id_obj)
    {
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/controlar_q_obj',
            params: {
              id_dimension  : ID_DIM,
              id_top        : ID_TOP
            }, 
            success: function(response){              
              var result=eval(response.responseText);
//              console.log(result);
              switch(result){
                case 0:
                case "0":
                    switch(ID_DIM)
                    {
                        case 1:
                            Ext.MessageBox.alert('Error','Ya completo los 3 objetivos para definir el Que');
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','Solo 1 objetivo es necesario para definir el Como');
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Solo 1 objetivo Organizacional es necesario');
                            break;
                        
                    }
                    break;
                case 1:
                case "1":
                    altaObjetivo(id_obj);
                    break;
                default:
                    Ext.MessageBox.alert('Error','No se pude crear un objetivo, por favor vuelva a intentarlo o contacte con el administrador.');
                    break;
              }        
            },
            failure: function(response){
              var result=response.responseText;
              Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
            }                      
        });
    }
    else
        altaObjetivo(id_obj);
}
    
function altaObjetivo(id_obj)
{

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

    function createDdpObjetivo(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
    go_createDdpObjetivo();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

    function go_createDdpObjetivo(){
        if(isDdpObjetivoFormValid())
        {
           msgProcess('Guardando Objetivo');
          Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert_obj',
            params: {
              id                : id_obj,
              id_dimension      : ID_DIM,
    //          obj_e         : ddpObjetivoEmpresaField.getValue(),
              obj               : ddpObjetivoField.getValue(),
              indicador         : ddpIndicadorObjetivoField.getValue(),
              valor_ref         : ddpValorRefObjetivoField.getValue(),
              fd                : ddpFuenteDatosObjetivoField.getValue(),
    //          peso              : 20,//ddpPesoObjetivoField.getValue()
              fecha_evaluacion  : ddpFechaEvaluacionField.getValue(),
              id_top            : ID_TOP
            }, 
            success: function(response){              
              var result=eval(response.responseText);
              switch(result){
                case 0:
                    Ext.MessageBox.alert('Error','No se pudo crear el registro, ya existe el objetivo que intenta crear.');
                    ddpObjetivoCreateWindow.close();
                    break;
                case 1:
                    var obj_GP=Ext.getCmp('miTopObjetivosGridPanel');
                    var obj_DS=obj_GP.getStore();
                    obj_DS.load({params: {start: 0, id_top: ID_TOP}});
                    ddpObjetivoCreateWindow.close();
                    SUM_DIM=0;
                    if (id_obj>0)
                        {
                            Ext.MessageBox.alert('Alta OK..','El Objetivo  fue modificado correctamente');
                        }
                    else
                    {
                        Ext.MessageBox.alert('Alta OK..','El Objetivo  fue creado satisfactoriamente');
                    }
                    break;
                case 2:
    //                ddpObjetivoCreateForm.destroy();
                    ddpObjetivoCreateWindow.close();
                    Ext.MessageBox.alert('Aviso','No se realizaron cambios en el objetivo');         
                    break;
                case 3:
    //                ddpObjetivoCreateForm.destroy();
                    ddpObjetivoCreateWindow.close();
                    Ext.MessageBox.alert('Aviso','Error al insertar auditoria. Comuniquese con el administrador del sistema.');         
                    break;
                default:
                    if (result.error !="")
                        Ext.MessageBox.alert('Error',result.error);
                    else
                        Ext.MessageBox.alert('Error','No se pudo crear el objetivo, por favor vuelva a intentarlo o contacte con el administrador.');
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
      }// END function createDdpObjetivo

  
    // Verifico que los campos del formulario sean válidos
    function isDdpObjetivoFormValid(){	  
        var v1 = ddpObjetivoField.isValid();
        var v2 = ddpFechaEvaluacionField.isValid();
        return( v1 && v2);
    }
   
    var ddpObjetivoField = new Ext.form.TextArea({
        id: 'ddpObjetivoField',
        fieldLabel: 'Objetivo',
        maxLength: 2000,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        maxLengthText:'M&aacute;ximo 2000 caracteres',
        tabIndex: 1
    });
      
    var ddpIndicadorObjetivoField = new Ext.form.TextField({
        id: 'ddpIndicadorObjetivoField',
        fieldLabel: 'Indicador',
        allowBlank: true,
        anchor : '95%',
        tabIndex: 3
    });
    var ddpFuenteDatosObjetivoField = new Ext.form.TextField({
        id: 'ddpFuenteDatosObjetivoField',
        fieldLabel: 'Fuente de datos',
        allowBlank: true,
        anchor : '95%',
        tabIndex: 4
    });
var ddpValorRefObjetivoField = new Ext.form.TextField({
    id: 'ddpValorRefObjetivoField',
    fieldLabel: 'Valor de Referencia',
    allowBlank: true,
    anchor : '95%',
    tabIndex: 5
    });
var ddpPesoObjetivoField = new Ext.form.TextField({
        id: 'ddpPesoObjetivoNumberField',
        fieldLabel: 'Peso',
        allowDecimals:true,
        allowNegative:false,
        allowBlank: false,
        blankText:'campo requerido',
        value:'20%',
        disabled: true,
        anchor : '95%',
    });
  
  var ddpFechaEvaluacionField = new Ext.form.DateField({
    id: 'ddpFechaEvaluacionField',
    fieldLabel: 'Fecha revisión',
    allowBlank: false,
    minText : 'seleccione una fecha de evaluación',
    blankText:'campo requerido',
    minValue:MINDATE,
    tabIndex:2,
    anchor : '95%'
});
  
    var ddpObjetivoCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:700,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[ ddpObjetivoField
                    ,ddpFechaEvaluacionField
                    ,ddpIndicadorObjetivoField
                    ,ddpFuenteDatosObjetivoField
                    ,ddpValorRefObjetivoField
                    ,ddpPesoObjetivoField
            ]
        }],
        buttons: [{
            text: 'Guardar',
            tabIndex: 7,
            handler: createDdpObjetivo
            },{
            text: 'Cancelar',
            tabIndex: 8,
            handler: function(){
                ddpObjetivoCreateWindow.close();
            }
            }]
    });//END FormPanel

 
    var ddpObjetivoCreateWindow= new Ext.Window({
        id: 'ddpObjetivoCreateWindow',
        title: 'Nuevo Objetivo  para la dimension '+DIM,
        closable:true,
        modal:true,
        width: 650,
        height: 450,
        plain:true,
        layout: 'fit',
        items: ddpObjetivoCreateForm,
        closeAction: 'close'
    });
    if (id_obj > 0)
    {
            ddpObjetivoCreateForm.getForm().load({
            waitMsg: "Cargando...",
            url: CARPETA+'/datos_obj',
            params: {id: id_obj},
            success:function(response)
            {

            },
            failure:function(response){

            }
            });
    } 
    ddpObjetivoCreateWindow.show();
}//END function displayFormWindow