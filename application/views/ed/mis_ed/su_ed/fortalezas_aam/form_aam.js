aamForm = new Ext.FormPanel({
        id:'aamForm',
    	bodyStyle: 'padding: 5px;',
        labelAlign: 'top',
    	frame:false,
        border: false,
        height:150,
        defaults: {
    		anchor: '95%',
        	msgTarget: 'side'
    	},
        items: 
        [
        
            {
                xtype: 'textfield',
                name: 'id_ed_aam',
                id: 'id_ed_aam',
                value: ID_ED,
                hidden : true,
                hideLabel : true,
                cls:'invisible'
            },{
                xtype:'textarea',
                id:'aam',
                name:'aam',
                fieldLabel:'A continuación resuma los aspectos que a su juicio deben mejorarse para el poróximo período de evaluación',
                allowBlank: false,
                anchor:'95%',
                labelStyle: 'font-size:11px;'   
            }
        ],
        buttons: 
        [{
            text: 'Agregar',
            handler: function(){
                if(aamForm.getForm().isValid())
                {
                    aamForm.getForm().submit({
                        url: CARPETA+'/insertar_aam',
                        waitMsg: 'Agregando...',
                        success: function(aamForm, o)
                        {
                            switch(o.result.success)
                            {
                                case true:
                                case 1:
                                    aamDataStore.reload();
                                    aamForm.reset();
                                    Ext.MessageBox.alert('OK', o.result.msg);
                                    break;
                                case false:
                                case 0:
                                    Ext.MessageBox.alert('Error', o.result.msg);
                                    break;
                                default:
                                    Ext.MessageBox.alert('Error','Error esperando confirmación...');
                                    break;
                            }
                        },
                        failure: function(){
                            Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');
                        }
                    });
                }
            }
        }
    ]
    });